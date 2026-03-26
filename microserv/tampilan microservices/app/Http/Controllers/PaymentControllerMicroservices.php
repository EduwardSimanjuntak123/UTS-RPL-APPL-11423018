<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * PaymentController - Updated untuk menggunakan PaymentService (Microservices)
 * 
 * Menangani semua payment operations, invoicing, dan insurance claims
 * melalui microservices API
 */
class PaymentControllerMicroservices extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    /**
     * Get all payments dengan filtering
     * GET /api/payments?status=completed&patient_id=uuid
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('patient_id')) {
                $filters['patient_id'] = $request->patient_id;
            }
            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }
            if ($request->has('method')) {
                $filters['method'] = $request->input('method');
            }
            if ($request->has('date_from')) {
                $filters['date_from'] = $request->date_from;
            }
            if ($request->has('date_to')) {
                $filters['date_to'] = $request->date_to;
            }

            $payments = $this->paymentService->getAllPayments($filters);

            return response()->json([
                'status' => 'success',
                'data' => $payments,
                'message' => 'Payments fetched successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch payments: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch payments',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single payment
     * GET /api/payments/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $payment = $this->paymentService->getPaymentById($id);

            if (!$payment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch payment {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new payment
     * POST /api/payments
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|uuid',
                'appointment_id' => 'nullable|uuid',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:credit_card,debit_card,bank_transfer,cash,insurance',
                'description' => 'nullable|string|max:500',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ]);

            $result = $this->paymentService->createPayment($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment created successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create payment: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create payment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update payment
     * PUT /api/payments/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'nullable|in:pending,completed,failed,refunded',
                'paid_amount' => 'nullable|numeric|min:0',
                'payment_date' => 'nullable|date',
                'notes' => 'nullable|string',
            ]);

            $result = $this->paymentService->updatePayment($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment updated successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update payment {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update payment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Mark payment as completed
     * PUT /api/payments/{id}/complete
     */
    public function completePayment($id): JsonResponse
    {
        try {
            $result = $this->paymentService->completePayment($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment marked as completed',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to complete payment {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to complete payment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Refund payment
     * POST /api/payments/{id}/refund
     */
    public function refundPayment(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'refund_amount' => 'nullable|numeric|min:0.01',
                'reason' => 'required|string|max:500',
            ]);

            $result = $this->paymentService->refundPayment($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment refunded successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to refund payment {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to refund payment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get patient payments
     * GET /api/patients/{patient_id}/payments
     */
    public function getPatientPayments($patientId): JsonResponse
    {
        try {
            $payments = $this->paymentService->getPatientPayments($patientId);

            return response()->json([
                'status' => 'success',
                'data' => $payments,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch payments for patient {$patientId}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch patient payments',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ==================== INVOICES ====================
     */

    /**
     * Get all invoices
     * GET /api/invoices
     */
    public function getInvoices(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('patient_id')) {
                $filters['patient_id'] = $request->patient_id;
            }
            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }

            $invoices = $this->paymentService->getAllInvoices($filters);

            return response()->json([
                'status' => 'success',
                'data' => $invoices,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch invoices: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch invoices',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create invoice
     * POST /api/invoices
     */
    public function createInvoice(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|uuid',
                'appointment_id' => 'nullable|uuid',
                'items' => 'required|array',
                'items.*.description' => 'required|string',
                'items.*.amount' => 'required|numeric|min:0.01',
                'total_amount' => 'required|numeric|min:0.01',
                'due_date' => 'nullable|date',
                'notes' => 'nullable|string',
            ]);

            $invoice = $this->paymentService->createInvoice($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice created successfully',
                'data' => $invoice,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create invoice: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * ==================== INSURANCE CLAIMS ====================
     */

    /**
     * Get all insurance claims
     * GET /api/insurance-claims
     */
    public function getInsuranceClaims(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('patient_id')) {
                $filters['patient_id'] = $request->patient_id;
            }
            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }

            $claims = $this->paymentService->getAllInsuranceClaims($filters);

            return response()->json([
                'status' => 'success',
                'data' => $claims,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch insurance claims: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch insurance claims',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create insurance claim
     * POST /api/insurance-claims
     */
    public function createInsuranceClaim(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|uuid',
                'payment_id' => 'required|uuid',
                'insurance_provider' => 'required|string|max:255',
                'claim_amount' => 'required|numeric|min:0.01',
                'claim_date' => 'required|date',
                'description' => 'nullable|string',
            ]);

            $claim = $this->paymentService->createInsuranceClaim($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Insurance claim created successfully',
                'data' => $claim,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create insurance claim: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create insurance claim',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update insurance claim status
     * PUT /api/insurance-claims/{id}
     */
    public function updateInsuranceClaim(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'nullable|in:pending,approved,rejected,paid',
                'approved_amount' => 'nullable|numeric|min:0',
                'rejection_reason' => 'nullable|string|max:500',
                'notes' => 'nullable|string',
            ]);

            $claim = $this->paymentService->updateInsuranceClaim($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Insurance claim updated successfully',
                'data' => $claim,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update insurance claim {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update insurance claim',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
