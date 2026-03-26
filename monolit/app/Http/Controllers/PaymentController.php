<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\InsuranceClaim;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Get all payments with filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::query();

        // Filter by patient
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by method
        // if ($request->has('method')) {
        //     $query->where('method', $request->method);
        // }

        $payments = $query->with(['appointment', 'patient', 'insuranceClaim'])
                          ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $payments,
        ]);
    }

    /**
     * Create new payment
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['status'] = 'pending';

        $payment = Payment::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment recorded successfully',
            'data' => $payment->load(['appointment', 'patient', 'insuranceClaim']),
        ], 201);
    }

    /**
     * Get payment by ID
     */
    public function show(Payment $payment): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $payment->load(['appointment', 'patient', 'insuranceClaim']),
        ]);
    }

    /**
     * Update payment status
     */
    public function updateStatus(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        $payment->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment status updated successfully',
            'data' => $payment,
        ]);
    }

    /**
     * Get payments by patient
     */
    public function getByPatient($patientId): JsonResponse
    {
        $payments = Payment::where('patient_id', $patientId)
                           ->with(['appointment', 'insuranceClaim'])
                           ->get();

        return response()->json([
            'status' => 'success',
            'data' => $payments,
        ]);
    }

    /**
     * Get payment by appointment
     */
    public function getByAppointment($appointmentId): JsonResponse
    {
        $payment = Payment::where('appointment_id', $appointmentId)
                          ->with('patient', 'insuranceClaim')
                          ->first();

        if (!$payment) {
            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'No payment found for this appointment',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $payment,
        ]);
    }

    /**
     * Refund payment
     */
    public function refund(Payment $payment): JsonResponse
    {
        if ($payment->status !== 'completed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only completed payments can be refunded',
            ], 400);
        }

        $payment->update(['status' => 'refunded']);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment refunded successfully',
            'data' => $payment,
        ]);
    }

    /**
     * Create insurance claim
     */
    public function createInsuranceClaim(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'appointment_id' => 'required|exists:appointments,id',
            'insurance_provider' => 'required|string',
            'policy_number' => 'required|string',
            'claim_amount' => 'required|numeric|min:0.01',
        ]);

        $validated['status'] = 'pending';
        $validated['submission_date'] = now();

        $claim = InsuranceClaim::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Insurance claim created successfully',
            'data' => $claim->load(['patient', 'appointment']),
        ], 201);
    }

    /**
     * Get insurance claims
     */
    public function getInsuranceClaims(Request $request): JsonResponse
    {
        $query = InsuranceClaim::query();

        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $claims = $query->with(['patient', 'appointment', 'payments'])
                        ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $claims,
        ]);
    }

    /**
     * Update insurance claim
     */
    public function updateInsuranceClaim(Request $request, InsuranceClaim $claim): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'nullable|in:pending,approved,rejected,paid',
            'approved_amount' => 'nullable|numeric|min:0',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'approved') {
            $validated['approval_date'] = now();
        }

        $claim->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Insurance claim updated successfully',
            'data' => $claim,
        ]);
    }

    /**
     * Get payment statistics
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $query = Payment::query();

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $totalPayments = $query->sum('amount');
        $completedPayments = $query->where('status', 'completed')->sum('amount');
        $pendingPayments = $query->where('status', 'pending')->sum('amount');
        $failedPayments = $query->where('status', 'failed')->sum('amount');

        $paymentsByMethod = Payment::groupBy('method')
                                   ->selectRaw('method, SUM(amount) as total, COUNT(*) as count')
                                   ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_amount' => $totalPayments,
                'completed_amount' => $completedPayments,
                'pending_amount' => $pendingPayments,
                'failed_amount' => $failedPayments,
                'by_method' => $paymentsByMethod,
            ],
        ]);
    }
}
