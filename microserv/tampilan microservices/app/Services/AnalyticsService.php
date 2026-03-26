<?php

namespace App\Services;

use App\Services\Api\ApiClient;
use Exception;
use Illuminate\Support\Facades\Log;
class AnalyticsService
{
    protected ApiClient $apiClient;

    public function __construct()
    {
        $this->apiClient = new ApiClient();
    }

    /**
     * Get service metrics
     */
    public function getServiceMetrics(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/metrics', $filters);
            return $response['data'] ?? [];
        } catch (Exception $e) {
                    Log::error('Failed to fetch service metrics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Record service metric
     */
    public function recordServiceMetric(array $data): array
    {
        try {
            $response = $this->apiClient->post('/api/v1/metrics', $data);
            return $response;
        } catch (Exception $e) {
                Log::error('Failed to record service metric: ' . $e->getMessage());
            throw new Exception('Failed to record service metric: ' . $e->getMessage());
        }
    }

    /**
     * Get health indicators
     */
    public function getHealthIndicators(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/health-indicators', $filters);
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to fetch health indicators: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get service health
     */
    public function getServiceHealth(string $serviceName): ?array
    {
        try {
            $response = $this->apiClient->get("/api/v1/health-indicators/{$serviceName}");
            return $response['data'] ?? null;
        } catch (Exception $e) {
            Log::error("Failed to fetch health for service {$serviceName}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get system alerts
     */
    public function getSystemAlerts(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/alerts', $filters);
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to fetch system alerts: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get alert by ID
     */
    public function getAlertById(int $id): ?array
    {
        try {
            $response = $this->apiClient->get("/api/v1/alerts/{$id}");
            return $response['data'] ?? null;
        } catch (Exception $e) {
            Log::error("Failed to fetch alert {$id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create system alert
     */
    public function createAlert(array $data): array
    {
        try {
            $response = $this->apiClient->post('/api/v1/alerts', $data);
            return $response;
        } catch (Exception $e) {
            Log::error('Failed to create alert: ' . $e->getMessage());
            throw new Exception('Failed to create alert: ' . $e->getMessage());
        }
    }

    /**
     * Resolve alert
     */
    public function resolveAlert(int $alertId): array
    {
        try {
            $response = $this->apiClient->put("/api/v1/alerts/{$alertId}/resolve", []);
            return $response;
        } catch (Exception $e) {
            Log::error("Failed to resolve alert {$alertId}: " . $e->getMessage());
            throw new Exception('Failed to resolve alert: ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard summary
     */
    public function getDashboardSummary(?string $date = null): array
    {
        try {
            $filters = [];
            if ($date) {
                $filters['date'] = $date;
            }
            $response = $this->apiClient->get('/api/v1/dashboard/summary', $filters);
            return $response;
        } catch (Exception $e) {
            Log::error('Failed to fetch dashboard summary: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user analytics
     */
    public function getUserAnalytics(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/user-analytics', $filters);
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to fetch user analytics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get appointment analytics
     */
    public function getAppointmentAnalytics(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/appointment-analytics', $filters);
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to fetch appointment analytics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get revenue analytics
     */
    public function getRevenueAnalytics(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/revenue-analytics', $filters);
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to fetch revenue analytics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get daily report
     */
    public function getDailyReport(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/reports/daily', $filters);
            return $response;
        } catch (Exception $e) {
            Log::error('Failed to fetch daily report: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get weekly report
     */
    public function getWeeklyReport(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/reports/weekly', $filters);
            return $response;
        } catch (Exception $e) {
            Log::error('Failed to fetch weekly report: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get monthly report
     */
    public function getMonthlyReport(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/reports/monthly', $filters);
            return $response;
        } catch (Exception $e) {
            Log::error('Failed to fetch monthly report: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get custom report
     */
    public function getCustomReport(string $startDate = '', string $endDate = '', array $metrics = [], array $filters = []): array
    {
        try {
            $params = array_merge($filters, [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'metrics' => $metrics,
            ]);
            $response = $this->apiClient->get('/api/v1/reports/custom', $params);
            return $response;
        } catch (Exception $e) {
            Log::error('Failed to fetch custom report: ' . $e->getMessage());
            return [];
        }
    }
}
