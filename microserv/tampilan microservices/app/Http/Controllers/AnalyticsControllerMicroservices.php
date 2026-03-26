<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * AnalyticsController - Updated untuk menggunakan AnalyticsService (Microservices)
 * 
 * Menangani semua analytics queries, metrics, health checks, dan reporting
 * melalui microservices API
 */
class AnalyticsControllerMicroservices extends Controller
{
    public function __construct(private AnalyticsService $analyticsService)
    {
    }

    /**
     * ==================== SERVICE METRICS ====================
     */

    /**
     * Get service metrics
     * GET /api/metrics?service=user-service&from=2024-02-01&to=2024-02-28
     */
    public function getServiceMetrics(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('service')) {
                $filters['service'] = $request->service;
            }
            if ($request->has('from')) {
                $filters['date_from'] = $request->from;
            }
            if ($request->has('to')) {
                $filters['date_to'] = $request->to;
            }

            $metrics = $this->analyticsService->getServiceMetrics($filters);

            return response()->json([
                'status' => 'success',
                'data' => $metrics,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch service metrics: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch service metrics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Record service metric
     * POST /api/metrics
     */
    public function recordServiceMetric(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'service_name' => 'required|string|max:100',
                'metric_type' => 'required|in:response_time,throughput,error_rate,cpu_usage,memory_usage',
                'value' => 'required|numeric',
                'timestamp' => 'nullable|date',
                'tags' => 'nullable|array',
            ]);

            $result = $this->analyticsService->recordServiceMetric($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Metric recorded successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to record service metric: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to record service metric',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * ==================== HEALTH INDICATORS ====================
     */

    /**
     * Get health indicators
     * GET /api/health-indicators?service=all
     */
    public function getHealthIndicators(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('service')) {
                $filters['service'] = $request->service;
            }

            $indicators = $this->analyticsService->getHealthIndicators($filters);

            return response()->json([
                'status' => 'success',
                'data' => $indicators,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch health indicators: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch health indicators',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get service health
     * GET /api/services/{service_name}/health
     */
    public function getServiceHealth($serviceName): JsonResponse
    {
        try {
            $health = $this->analyticsService->getServiceHealth($serviceName);

            return response()->json([
                'status' => 'success',
                'data' => $health,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch health for service {$serviceName}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch service health',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ==================== ALERTS ====================
     */

    /**
     * Get all alerts
     * GET /api/alerts?status=active&severity=high
     */
    public function getAlerts(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }
            if ($request->has('severity')) {
                $filters['severity'] = $request->severity;
            }

            $alerts = $this->analyticsService->getSystemAlerts($filters);

            return response()->json([
                'status' => 'success',
                'data' => $alerts,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch alerts: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch alerts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create alert
     * POST /api/alerts
     */
    public function createAlert(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'severity' => 'required|in:low,medium,high,critical',
                'alert_type' => 'required|string|max:100',
                'source' => 'nullable|string|max:100',
            ]);

            $alert = $this->analyticsService->createAlert($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Alert created successfully',
                'data' => $alert,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create alert: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create alert',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Resolve alert
     * PUT /api/alerts/{id}/resolve
     */
    public function resolveAlert($id): JsonResponse
    {
        try {
            $alert = $this->analyticsService->resolveAlert($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Alert resolved successfully',
                'data' => $alert,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to resolve alert {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to resolve alert',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * ==================== REPORTS ====================
     */

    /**
     * Get dashboard summary
     * GET /api/dashboard/summary?date=2024-02-01
     */
    public function getDashboardSummary(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date' => 'nullable|date',
            ]);

            $summary = $this->analyticsService->getDashboardSummary($validated['date'] ?? null);

            return response()->json([
                'status' => 'success',
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch dashboard summary: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch dashboard summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get daily report
     * GET /api/reports/daily?date=2024-02-01
     */
    public function getDailyReport(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
            ]);

            $report = $this->analyticsService->getDailyReport($validated['date']);

            return response()->json([
                'status' => 'success',
                'data' => $report,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch daily report: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch daily report',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get weekly report
     * GET /api/reports/weekly?start_date=2024-02-01
     */
    public function getWeeklyReport(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
            ]);

            $report = $this->analyticsService->getWeeklyReport($validated['start_date']);

            return response()->json([
                'status' => 'success',
                'data' => $report,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch weekly report: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch weekly report',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get monthly report
     * GET /api/reports/monthly?month=2024-02
     */
    public function getMonthlyReport(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'month' => 'required|date_format:Y-m',
            ]);

            $report = $this->analyticsService->getMonthlyReport($validated['month']);

            return response()->json([
                'status' => 'success',
                'data' => $report,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch monthly report: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch monthly report',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get custom report
     * POST /api/reports/custom
     */
    public function getCustomReport(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'metrics' => 'required|array',
                'metrics.*' => 'string',
                'filters' => 'nullable|array',
            ]);

            $report = $this->analyticsService->getCustomReport(
                $validated['start_date'],
                $validated['end_date'],
                $validated['metrics'],
                $validated['filters'] ?? []
            );

            return response()->json([
                'status' => 'success',
                'data' => $report,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch custom report: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch custom report',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ==================== ANALYTICS QUERIES ====================
     */

    /**
     * Get user analytics
     * GET /api/analytics/users?from=2024-02-01&to=2024-02-28
     */
    public function getUserAnalytics(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('from')) {
                $filters['date_from'] = $request->from;
            }
            if ($request->has('to')) {
                $filters['date_to'] = $request->to;
            }

            $analytics = $this->analyticsService->getUserAnalytics($filters);

            return response()->json([
                'status' => 'success',
                'data' => $analytics,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch user analytics: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get appointment analytics
     * GET /api/analytics/appointments?from=2024-02-01&to=2024-02-28
     */
    public function getAppointmentAnalytics(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('from')) {
                $filters['date_from'] = $request->from;
            }
            if ($request->has('to')) {
                $filters['date_to'] = $request->to;
            }

            $analytics = $this->analyticsService->getAppointmentAnalytics($filters);

            return response()->json([
                'status' => 'success',
                'data' => $analytics,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch appointment analytics: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch appointment analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get revenue analytics
     * GET /api/analytics/revenue?from=2024-02-01&to=2024-02-28
     */
    public function getRevenueAnalytics(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('from')) {
                $filters['date_from'] = $request->from;
            }
            if ($request->has('to')) {
                $filters['date_to'] = $request->to;
            }

            $analytics = $this->analyticsService->getRevenueAnalytics($filters);

            return response()->json([
                'status' => 'success',
                'data' => $analytics,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch revenue analytics: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch revenue analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
