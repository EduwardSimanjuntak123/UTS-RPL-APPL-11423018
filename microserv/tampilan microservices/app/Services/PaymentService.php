<?php

namespace App\Services;

use App\Models\ApiClient;
use Illuminate\Support\Facades\Log;

/**
 * PaymentService - Microservices API Wrapper
 * 
 * All payment operations are delegated to the Go Payment Microservice
 * via the API Gateway. This service is purely a wrapper around HTTP calls.
 * 
 * ✅ MICROSERVICES ONLY - NO direct database access
 */
class PaymentService
{
    protected ApiClient $apiClient;
    protected string $baseUrl = '/payments';

    public function __construct()
    {
        $this->apiClient = new ApiClient();
    }

    /**
     * Get all payments with optional filtering
     * GET /api/payments
     */
    public function getAllPayments(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get($this->baseUrl, $filters);
            Log::info('Fetched all payments', ['count' => count($response['data'] ?? [])]);
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch payments', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get single payment by ID
     * GET /api/payments/{id}
     */
    public function getPaymentById(string $id): ?array
    {
        try {
            $response = $this->apiClient->get("{$this->baseUrl}/{$id}");
            Log::info("Fetched payment {$id}");
            return $response['data'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to fetch payment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create payment
     * POST /api/payments
     */
    public function createPayment(array $data): array
    {
        try {
            $response = $this->apiClient->post($this->baseUrl, $data);
            Log::info('Payment created', ['payment_id' => $response['data']['id'] ?? 'unknown']);
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to create payment', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Process payment
     * POST /api/payments (using processPayment logic)
     */
    public function processPayment(array $data): array
    {
        return $this->createPayment($data);
    }

    /**
     * Update payment
     * PUT /api/payments/{id}
     */
    public function updatePayment(string $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("{$this->baseUrl}/{$id}", $data);
            Log::info("Payment {$id} updated");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to update payment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Confirm payment
     * POST /api/payments/{id}/confirm
     */
    public function confirmPayment(string $id): array
    {
        try {
            $response = $this->apiClient->post("{$this->baseUrl}/{$id}/confirm", []);
            Log::info("Payment {$id} confirmed");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to confirm payment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Verify payment
     * POST /api/payments/{id}/verify
     */
    public function verifyPayment(string $id): array
    {
        try {
            $response = $this->apiClient->post("{$this->baseUrl}/{$id}/verify", []);
            Log::info("Payment {$id} verified");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to verify payment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Refund payment
     * POST /api/refunds
     */
    public function refundPayment(string $paymentId, array $data = []): array
    {
        try {
            $data['payment_id'] = $paymentId;
            $response = $this->apiClient->post('/refunds', $data);
            Log::info("Payment {$paymentId} refunded", $data);
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to refund payment {$paymentId}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get patient payments
     * GET /api/patients/{patient_id}/payments
     */
    public function getPatientPayments(string $patientId): array
    {
        try {
            $response = $this->apiClient->get("/patients/{$patientId}/payments");
            Log::info("Fetched payments for patient {$patientId}");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient payments", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get all invoices
     * GET /api/invoices
     */
    public function getAllInvoices(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/invoices', $filters);
            Log::info('Fetched all invoices');
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch invoices', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get single invoice by ID
     * GET /api/invoices/{id}
     */
    public function getInvoiceById(string $id): ?array
    {
        try {
            $response = $this->apiClient->get("/invoices/{$id}");
            Log::info("Fetched invoice {$id}");
            return $response['data'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to fetch invoice {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create invoice
     * POST /api/invoices
     */
    public function createInvoice(array $data): array
    {
        try {
            $response = $this->apiClient->post('/invoices', $data);
            Log::info('Invoice created', ['invoice_id' => $response['data']['id'] ?? 'unknown']);
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to create invoice', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get all insurance claims
     * GET /api/insurance-claims
     */
    public function getAllInsuranceClaims(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/insurance-claims', $filters);
            Log::info('Fetched all insurance claims');
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch insurance claims', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get single insurance claim by ID
     * GET /api/insurance-claims/{id}
     */
    public function getInsuranceClaimById(string $id): ?array
    {
        try {
            $response = $this->apiClient->get("/insurance-claims/{$id}");
            Log::info("Fetched insurance claim {$id}");
            return $response['data'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to fetch insurance claim {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create insurance claim
     * POST /api/insurance-claims
     */
    public function createInsuranceClaim(array $data): array
    {
        try {
            $response = $this->apiClient->post('/insurance-claims', $data);
            Log::info('Insurance claim created', ['claim_id' => $response['data']['id'] ?? 'unknown']);
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to create insurance claim', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update insurance claim
     * PUT /api/insurance-claims/{id}
     */
    public function updateInsuranceClaim(string $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("/insurance-claims/{$id}", $data);
            Log::info("Insurance claim {$id} updated");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to update insurance claim {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Approve insurance claim
     * PUT /api/insurance-claims/{id} with approval logic
     */
    public function approveInsuranceClaim(string $id, array $approvalData): array
    {
        return $this->updateInsuranceClaim($id, array_merge($approvalData, ['status' => 'approved']));
    }

    /**
     * Reject insurance claim
     * PUT /api/insurance-claims/{id} with rejection logic
     */
    public function rejectInsuranceClaim(string $id, array $rejectionData): array
    {
        return $this->updateInsuranceClaim($id, array_merge($rejectionData, ['status' => 'rejected']));
    }

    /**
     * Get payment statistics
     * GET /api/reports/revenue
     */
    public function getPaymentStats(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/reports/revenue', $filters);
            Log::info('Fetched payment stats');
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to fetch payment stats', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get revenue by payment method
     * GET /api/reports/revenue (filtered by method)
     */
    public function getRevenueByMethod(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/reports/revenue', $filters);
            Log::info('Fetched revenue by method');
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch revenue by method', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Complete payment (mark as completed)
     * PUT /api/payments/{id}/complete
     */
    public function completePayment(string $id): array
    {
        try {
            $response = $this->apiClient->put("{$this->baseUrl}/{$id}/complete", []);
            Log::info("Payment {$id} completed");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to complete payment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete payment
     * DELETE /api/payments/{id}
     */
    public function deletePayment(string $id): array
    {
        try {
            $response = $this->apiClient->delete("{$this->baseUrl}/{$id}");
            Log::info("Payment {$id} deleted");
            return $response;
        } catch (\Exception $e) {
            Log::error("Failed to delete payment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}

