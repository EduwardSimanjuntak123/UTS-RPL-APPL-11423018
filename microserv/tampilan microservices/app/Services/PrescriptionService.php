<?php

namespace App\Services;

use App\Services\Api\ApiClient;
use Illuminate\Support\Facades\Log;

/**
 * PrescriptionService - Microservices API Wrapper
 * 
 * ✅ MICROSERVICES ONLY - NO direct database access
 * All prescription operations go through Go Microservice API
 */
class PrescriptionService
{
    protected ApiClient $apiClient;
    protected string $baseUrl = 'http://localhost:3000/api/v1/prescriptions';

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Create prescription
     * POST /api/prescriptions
     */
    public function createPrescription(array $data): array
    {
        try {
            $response = $this->apiClient->post($this->baseUrl, $data);
            Log::info('Prescription created', ['prescription_id' => $response['data']['id'] ?? 'unknown']);
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to create prescription', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get all prescriptions
     * GET /api/prescriptions
     */
    public function getAllPrescriptions(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get($this->baseUrl, $filters);
            Log::info('Fetched all prescriptions');
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch prescriptions', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get single prescription by ID
     * GET /api/prescriptions/{id}
     */
    public function getPrescriptionById(string $id): ?array
    {
        try {
            $response = $this->apiClient->get("{$this->baseUrl}/{$id}");
            Log::info("Fetched prescription {$id}");
            return $response['data'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to fetch prescription {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update prescription
     * PUT /api/prescriptions/{id}
     */
    public function updatePrescription(string $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("{$this->baseUrl}/{$id}", $data);
            Log::info("Prescription {$id} updated");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to update prescription {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete prescription
     * DELETE /api/prescriptions/{id}
     */
    public function deletePrescription(string $id): array
    {
        try {
            $response = $this->apiClient->delete("{$this->baseUrl}/{$id}");
            Log::info("Prescription {$id} deleted");
            return $response;
        } catch (\Exception $e) {
            Log::error("Failed to delete prescription {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get patient prescriptions
     * GET /api/patients/{patient_id}/prescriptions
     */
    public function getPatientPrescriptions(string $patientId): array
    {
        try {
            $response = $this->apiClient->get("/patients/{$patientId}/prescriptions");
            Log::info("Fetched prescriptions for patient {$patientId}");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient prescriptions", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get all clinical notes
     * GET /api/clinical-notes
     */
    public function getAllClinicalNotes(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/clinical-notes', $filters);
            Log::info('Fetched all clinical notes');
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch clinical notes', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create clinical note
     * POST /api/clinical-notes
     */
    public function createClinicalNote(array $data): array
    {
        try {
            $response = $this->apiClient->post('/clinical-notes', $data);
            Log::info('Clinical note created', ['note_id' => $response['data']['id'] ?? 'unknown']);
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to create clinical note', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update clinical note
     * PUT /api/clinical-notes/{id}
     */
    public function updateClinicalNote(string $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("/clinical-notes/{$id}", $data);
            Log::info("Clinical note {$id} updated");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to update clinical note {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get patient clinical notes
     * GET /api/patients/{patient_id}/clinical-notes
     */
    public function getPatientClinicalNotes(string $patientId): array
    {
        try {
            $response = $this->apiClient->get("/patients/{$patientId}/clinical-notes");
            Log::info("Fetched clinical notes for patient {$patientId}");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient clinical notes", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete clinical note
     * DELETE /api/clinical-notes/{id}
     */
    public function deleteClinicalNote(string $id): array
    {
        try {
            $response = $this->apiClient->delete("/clinical-notes/{$id}");
            Log::info("Clinical note {$id} deleted");
            return $response;
        } catch (\Exception $e) {
            Log::error("Failed to delete clinical note {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get clinical note by ID
     * GET /api/clinical-notes/{id}
     */
    public function getClinicalNoteById(string $id): ?array
    {
        try {
            $response = $this->apiClient->get("/clinical-notes/{$id}");
            Log::info("Fetched clinical note {$id}");
            return $response['data'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to fetch clinical note {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}

