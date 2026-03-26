<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\AnalyticsLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * Get patient outcomes analytics
     */
    public function getPatientOutcomes(Request $request): JsonResponse
    {
        // Total appointments completed
        $totalAppointmentsCompleted = Appointment::where('status', 'completed')->count();

        // Appointment statistics by status
        $appointmentsByStatus = Appointment::selectRaw('status, COUNT(*) as count')
                                           ->groupBy('status')
                                           ->get();

        // Average appointments per patient
        $totalPatients = User::where('role', 'patient')->count();
        $avgAppointmentsPerPatient = $totalPatients > 0 
            ? round(Appointment::count() / $totalPatients, 2) 
            : 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_appointments_completed' => $totalAppointmentsCompleted,
                'appointments_by_status' => $appointmentsByStatus,
                'avg_appointments_per_patient' => $avgAppointmentsPerPatient,
                'total_patients' => $totalPatients,
                'total_appointments' => Appointment::count(),
            ],
        ]);
    }

    /**
     * Get doctor performance analytics
     */
    public function getDoctorPerformance(Request $request): JsonResponse
    {
        $doctorPerformance = User::where('role', 'doctor')
            ->with(['doctorAppointments'])
            ->get()
            ->map(function ($doctor) {
                $appointments = $doctor->doctorAppointments;
                $completed = $appointments->where('status', 'completed')->count();
                $total = $appointments->count();
                $completionRate = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'specialty' => $doctor->specialty,
                    'total_appointments' => $total,
                    'completed_appointments' => $completed,
                    'completion_rate' => $completionRate . '%',
                    'average_rating' => 4.5, // Can be enhanced with ratings system
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $doctorPerformance,
        ]);
    }

    /**
     * Get drug usage trends
     */
    public function getDrugUsageTrends(Request $request): JsonResponse
    {
        $trends = Prescription::selectRaw('medication, COUNT(*) as usage_count, AVG(duration) as avg_duration')
                              ->where('status', 'active')
                              ->groupBy('medication')
                              ->orderByRaw('usage_count DESC')
                              ->get();

        return response()->json([
            'status' => 'success',
            'data' => $trends,
        ]);
    }

    /**
     * Get revenue analytics
     */
    public function getRevenueAnalytics(Request $request): JsonResponse
    {
        $dateFrom = $request->date_from ?? now()->subMonths(3);
        $dateTo = $request->date_to ?? now();

        $totalRevenue = Payment::whereBetween('created_at', [$dateFrom, $dateTo])
                               ->where('status', 'completed')
                               ->sum('amount');

        $revenueByMethod = Payment::whereBetween('created_at', [$dateFrom, $dateTo])
                                  ->where('status', 'completed')
                                  ->selectRaw('method, SUM(amount) as total, COUNT(*) as transaction_count')
                                  ->groupBy('method')
                                  ->get();

        $revenueByMonth = Payment::whereBetween('created_at', [$dateFrom, $dateTo])
                                 ->where('status', 'completed')
                                 ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total")
                                 ->groupBy('month')
                                 ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_revenue' => $totalRevenue,
                'revenue_by_method' => $revenueByMethod,
                'revenue_by_month' => $revenueByMonth,
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
            ],
        ]);
    }

    /**
     * Get user statistics
     */
    public function getUserStatistics(): JsonResponse
    {
        $userStats = [
            'total_patients' => User::where('role', 'patient')->count(),
            'total_doctors' => User::where('role', 'doctor')->count(),
            'total_pharmacists' => User::where('role', 'pharmacist')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'active_users' => User::where('status', 'active')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $userStats,
        ]);
    }

    /**
     * Get appointment statistics
     */
    public function getAppointmentStatistics(Request $request): JsonResponse
    {
        $dateFrom = $request->date_from ?? now()->subMonths(1);
        $dateTo = $request->date_to ?? now();

        $appointmentStats = Appointment::whereBetween('created_at', [$dateFrom, $dateTo])
                                       ->selectRaw("status, COUNT(*) as count")
                                       ->groupBy('status')
                                       ->get();

        $appointmentsByType = Appointment::whereBetween('created_at', [$dateFrom, $dateTo])
                                         ->selectRaw("type, COUNT(*) as count")
                                         ->groupBy('type')
                                         ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'by_status' => $appointmentStats,
                'by_type' => $appointmentsByType,
                'total_appointments' => Appointment::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            ],
        ]);
    }

    /**
     * Get prescription statistics
     */
    public function getPrescriptionStatistics(Request $request): JsonResponse
    {
        $dateFrom = $request->date_from ?? now()->subMonths(1);
        $dateTo = $request->date_to ?? now();

        $prescriptionStats = Prescription::whereBetween('created_at', [$dateFrom, $dateTo])
                                         ->selectRaw("status, COUNT(*) as count")
                                         ->groupBy('status')
                                         ->get();

        $topMedications = Prescription::whereBetween('created_at', [$dateFrom, $dateTo])
                                      ->selectRaw("medication, COUNT(*) as prescription_count")
                                      ->groupBy('medication')
                                      ->orderByRaw("prescription_count DESC")
                                      ->limit(10)
                                      ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'by_status' => $prescriptionStats,
                'top_medications' => $topMedications,
                'total_prescriptions' => Prescription::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            ],
        ]);
    }

    /**
     * Get insurance claim statistics
     */
    public function getInsuranceClaimStatistics(Request $request): JsonResponse
    {
        $dateFrom = $request->date_from ?? now()->subMonths(3);
        $dateTo = $request->date_to ?? now();

        $claimStats = Payment::where('method', 'insurance')
                             ->whereBetween('created_at', [$dateFrom, $dateTo])
                             ->selectRaw("status, COUNT(*) as count")
                             ->groupBy('status')
                             ->get();

        $totalClaimsAmount = Payment::where('method', 'insurance')
                                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                                    ->sum('amount');

        $claimsByProvider = Payment::where('method', 'insurance')
                                   ->whereBetween('created_at', [$dateFrom, $dateTo])
                                   ->selectRaw("insurance_claim_id")
                                   ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'by_status' => $claimStats,
                'total_claims_amount' => $totalClaimsAmount,
                'total_claims_count' => $claimStats->sum('count'),
            ],
        ]);
    }

    /**
     * Get activity logs
     */
    public function getActivityLogs(Request $request): JsonResponse
    {
        $query = AnalyticsLog::query();

        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->has('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        $logs = $query->with(['user'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(50);

        return response()->json([
            'status' => 'success',
            'data' => $logs,
        ]);
    }

    /**
     * Get system overview dashboard
     */
    public function getDashboardOverview(): JsonResponse
    {
        $users = [
            'patients' => User::where('role', 'patient')->count(),
            'doctors' => User::where('role', 'doctor')->count(),
            'pharmacists' => User::where('role', 'pharmacist')->count(),
        ];

        $appointments = [
            'total' => Appointment::count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'scheduled' => Appointment::where('status', 'scheduled')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
        ];

        $revenue = Payment::where('status', 'completed')->sum('amount');

        $prescriptions = [
            'total' => Prescription::count(),
            'active' => Prescription::where('status', 'active')->count(),
            'completed' => Prescription::where('status', 'completed')->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'users' => $users,
                'appointments' => $appointments,
                'revenue' => (float) $revenue,
                'prescriptions' => $prescriptions,
                'last_updated' => now(),
            ],
        ]);
    }
}
