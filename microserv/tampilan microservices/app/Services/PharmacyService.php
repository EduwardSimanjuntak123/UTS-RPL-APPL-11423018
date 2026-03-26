<?php

namespace App\Services;

use App\Services\Api\ApiClient;
use Illuminate\Support\Facades\Log;

/**
 * PharmacyService - Microservices API Wrapper
 * 
 * All pharmacy operations are delegated to the Go Pharmacy Microservice
 * via the API Gateway. This service is purely a wrapper around HTTP calls.
 * 
 * ✅ MICROSERVICES ONLY - NO direct database access
 */
class PharmacyService
{
    protected ApiClient $apiClient;
    protected string $drugBaseUrl = 'http://localhost:3000/api/v1/drugs';
    protected string $stockBaseUrl = 'http://localhost:3000/api/v1/stocks';
    protected string $orderBaseUrl = '/orders';

    public function __construct()
    {
        $this->apiClient = new ApiClient();
    }

    /**
     * Get all drugs
     * GET /api/drugs
     */
    public function getAllDrugs(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get($this->drugBaseUrl, $filters);
            Log::info('Fetched all drugs', ['count' => count($response['data'] ?? [])]);
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch drugs', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get single drug by ID
     * GET /api/drugs/{id}
     */
    public function getDrugById(string $id): ?array
    {
        try {
            $response = $this->apiClient->get("{$this->drugBaseUrl}/{$id}");
            Log::info("Fetched drug {$id}");
            return $response['data'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to fetch drug {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create drug
     * POST /api/drugs
     */
    public function createDrug(array $data): array
    {
        try {
            $response = $this->apiClient->post($this->drugBaseUrl, $data);
            Log::info('Drug created', ['drug_id' => $response['data']['id'] ?? 'unknown']);
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to create drug', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update drug
     * PUT /api/drugs/{id}
     */
    public function updateDrug(string $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("{$this->drugBaseUrl}/{$id}", $data);
            Log::info("Drug {$id} updated");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to update drug {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get all drug stocks
     * GET /api/stocks
     */
    public function getAllDrugStocks(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get($this->stockBaseUrl, $filters);
            Log::info('Fetched all drug stocks');
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch drug stocks', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get low stock items
     * GET /api/stocks/low
     */
    public function getLowStockDrugs(int $threshold = 10): array
    {
        try {
            $response = $this->apiClient->get("$this->stockBaseUrl/low", ['threshold' => $threshold]);
            Log::info("Fetched low stock items (threshold: {$threshold})");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch low stock items', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Add drug stock
     * POST /api/stocks
     */
    public function addDrugStock(array $data): array
    {
        try {
            $response = $this->apiClient->post($this->stockBaseUrl, $data);
            Log::info('Drug stock added', ['stock_id' => $response['data']['id'] ?? 'unknown']);
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to add drug stock', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update drug stock
     * PUT /api/stocks/{id}
     */
    public function updateDrugStock(string $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("{$this->stockBaseUrl}/{$id}", $data);
            Log::info("Drug stock {$id} updated");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to update drug stock {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get all drug orders
     * GET /api/orders
     */
    public function getAllDrugOrders(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get($this->orderBaseUrl, $filters);
            Log::info('Fetched all drug orders');
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch drug orders', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get single drug order by ID
     * GET /api/orders/{id}
     */
    public function getDrugOrderById(string $id): ?array
    {
        try {
            $response = $this->apiClient->get("{$this->orderBaseUrl}/{$id}");
            Log::info("Fetched drug order {$id}");
            return $response['data'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to fetch drug order {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create drug order
     * POST /api/orders
     */
    public function createDrugOrder(array $data): array
    {
        try {
            $response = $this->apiClient->post($this->orderBaseUrl, $data);
            Log::info('Drug order created', ['order_id' => $response['data']['id'] ?? 'unknown']);
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to create drug order', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update drug order
     * PUT /api/orders/{id}
     */
    public function updateDrugOrder(string $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("{$this->orderBaseUrl}/{$id}", $data);
            Log::info("Drug order {$id} updated");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to update drug order {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Confirm drug order
     * PUT /api/orders/{id}/confirm
     */
    public function confirmDrugOrder(string $id): array
    {
        try {
            $response = $this->apiClient->put("{$this->orderBaseUrl}/{$id}/confirm", []);
            Log::info("Drug order {$id} confirmed");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to confirm drug order {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Mark drug order as ready
     * PUT /api/orders/{id}/ready
     */
    public function markOrderReady(string $id): array
    {
        try {
            $response = $this->apiClient->put("{$this->orderBaseUrl}/{$id}/ready", []);
            Log::info("Drug order {$id} marked as ready");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to mark drug order ready {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Mark drug order as picked up
     * PUT /api/orders/{id}/pickup
     */
    public function markOrderPickedUp(string $id): array
    {
        try {
            $response = $this->apiClient->put("{$this->orderBaseUrl}/{$id}/pickup", []);
            Log::info("Drug order {$id} marked as picked up");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to mark drug order as picked up {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get patient drug orders
     * GET /api/patients/{patient_id}/orders
     */
    public function getPatientDrugOrders(string $patientId): array
    {
        try {
            $response = $this->apiClient->get("/patients/{$patientId}/orders");
            Log::info("Fetched drug orders for patient {$patientId}");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient drug orders", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get all pharmacies
     * GET /api/pharmacies
     */
    public function getAllPharmacies(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/pharmacies', $filters);
            Log::info('Fetched all pharmacies');
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch pharmacies', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get pharmacy by ID
     * GET /api/pharmacies/{id}
     */
    public function getPharmacyById(string $id): ?array
    {
        try {
            $response = $this->apiClient->get("/pharmacies/{$id}");
            Log::info("Fetched pharmacy {$id}");
            return $response['data'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to fetch pharmacy {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create pharmacy
     * POST /api/pharmacies
     */
    public function createPharmacy(array $data): array
    {
        try {
            $response = $this->apiClient->post('/pharmacies', $data);
            Log::info('Pharmacy created', ['pharmacy_id' => $response['data']['id'] ?? 'unknown']);
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to create pharmacy', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update pharmacy
     * PUT /api/pharmacies/{id}
     */
    public function updatePharmacy(string $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("/pharmacies/{$id}", $data);
            Log::info("Pharmacy {$id} updated");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to update pharmacy {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete pharmacy
     * DELETE /api/pharmacies/{id}
     */
    public function deletePharmacy(string $id): array
    {
        try {
            $response = $this->apiClient->delete("/pharmacies/{$id}");
            Log::info("Pharmacy {$id} deleted");
            return $response;
        } catch (\Exception $e) {
            Log::error("Failed to delete pharmacy {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get drug stock
     * GET /api/stocks
     */
    public function getDrugStock(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get($this->stockBaseUrl, $filters);
            Log::info('Fetched drug stocks');
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch drug stocks', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get drug orders
     * GET /api/orders
     */
    public function getDrugOrders(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get("{$this->orderBaseUrl}", $filters);
            Log::info('Fetched drug orders');
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch drug orders', ['error' => $e->getMessage()]);
            return [];
        }
    }
}

