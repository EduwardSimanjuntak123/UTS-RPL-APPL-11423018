<?php

namespace App\Services;

use App\Services\Api\ApiClient;
use Exception;
use Illuminate\Support\Facades\Log;

class MedicalService
{
    protected ApiClient $apiClient;

    public function __construct()
    {
        $this->apiClient = new ApiClient();
    }

    /**
     * Get all medical records
     */
    public function getAllMedicalRecords(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/medical-records', $filters);
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to fetch medical records: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get medical record by ID
     */
    public function getMedicalRecordById(int $id): ?array
    {
        try {
            $response = $this->apiClient->get("/api/v1/medical-records/{$id}");
            return $response['data'] ?? null;
        } catch (Exception $e) {
            Log::error("Failed to fetch medical record {$id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create medical record
     */
    public function createMedicalRecord(array $data): array
    {
        try {
            $response = $this->apiClient->post('/api/v1/medical-records', $data);
            return $response;
        } catch (Exception $e) {
            Log::error('Failed to create medical record: ' . $e->getMessage());
            throw new Exception('Failed to create medical record: ' . $e->getMessage());
        }
    }

    /**
     * Update medical record
     */
    public function updateMedicalRecord(int $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("/api/v1/medical-records/{$id}", $data);
            return $response;
        } catch (Exception $e) {
            Log::error("Failed to update medical record {$id}: " . $e->getMessage());
            throw new Exception('Failed to update medical record: ' . $e->getMessage());
        }
    }

    /**
     * Get patient medical records
     */
    public function getPatientMedicalRecords(int $patientId): array
    {
        try {
            $response = $this->apiClient->get("/api/v1/patients/{$patientId}/medical-records");
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error("Failed to fetch patient medical records: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all prescriptions
     */
    public function getAllPrescriptions(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/prescriptions', $filters);
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to fetch prescriptions: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get prescription by ID
     */
    public function getPrescriptionById(int $id): ?array
    {
        try {
            $response = $this->apiClient->get("/api/v1/prescriptions/{$id}");
            return $response['data'] ?? null;
        } catch (Exception $e) {
            Log::error("Failed to fetch prescription {$id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create prescription
     */
    public function createPrescription(array $data): array
    {
        try {
            $response = $this->apiClient->post('/api/v1/prescriptions', $data);
            return $response;
        } catch (Exception $e) {
            Log::error('Failed to create prescription: ' . $e->getMessage());
            throw new Exception('Failed to create prescription: ' . $e->getMessage());
        }
    }

    /**
     * Get patient prescriptions
     */
    public function getPatientPrescriptions(int $patientId): array
    {
        try {
            $response = $this->apiClient->get("/api/v1/patients/{$patientId}/prescriptions");
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error("Failed to fetch patient prescriptions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all lab results
     */
    public function getAllLabResults(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/lab-results', $filters);
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to fetch lab results: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get lab result by ID
     */
    public function getLabResultById(int $id): ?array
    {
        try {
            $response = $this->apiClient->get("/api/v1/lab-results/{$id}");
            return $response['data'] ?? null;
        } catch (Exception $e) {
            Log::error("Failed to fetch lab result {$id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create lab result
     */
    public function createLabResult(array $data): array
    {
        try {
            $response = $this->apiClient->post('/api/v1/lab-results', $data);
            return $response;
        } catch (Exception $e) {
            Log::error('Failed to create lab result: ' . $e->getMessage());
            throw new Exception('Failed to create lab result: ' . $e->getMessage());
        }
    }

    /**
     * Get clinical notes
     */
    public function getClinicalNotes(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/clinical-notes', $filters);
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to fetch clinical notes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete medical record
     */
    public function deleteMedicalRecord(int $id): bool
    {
        try {
            $response = $this->apiClient->delete("/api/v1/medical-records/{$id}");
            Log::info("Medical record {$id} deleted successfully");
            return true;
        } catch (Exception $e) {
            Log::error("Failed to delete medical record {$id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update prescription
     */
    public function updatePrescription(int $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("/api/v1/prescriptions/{$id}", $data);
            Log::info("Prescription {$id} updated successfully");
            return $response['data'] ?? $response;
        } catch (Exception $e) {
            Log::error("Failed to update prescription {$id}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all clinical notes (alias for getClinicalNotes)
     */
    public function getAllClinicalNotes(array $filters = []): array
    {
        return $this->getClinicalNotes($filters);
    }

    /**
     * Create clinical note
     */
    public function createClinicalNote(array $data): array
    {
        try {
            $response = $this->apiClient->post('/api/v1/clinical-notes', $data);
            Log::info('Clinical note created successfully');
            return $response['data'] ?? $response;
        } catch (Exception $e) {
            Log::error('Failed to create clinical note: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update clinical note
     */
    public function updateClinicalNote(int $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("/api/v1/clinical-notes/{$id}", $data);
            Log::info("Clinical note {$id} updated successfully");
            return $response['data'] ?? $response;
        } catch (Exception $e) {
            Log::error("Failed to update clinical note {$id}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get patient clinical notes
     */
    public function getPatientClinicalNotes(int $patientId): array
    {
        try {
            $response = $this->apiClient->get("/api/v1/patients/{$patientId}/clinical-notes");
            Log::info("Fetched clinical notes for patient {$patientId}");
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error("Failed to fetch clinical notes for patient {$patientId}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete prescription
     */
    public function deletePrescription(int $id): bool
    {
        try {
            $response = $this->apiClient->delete("/api/v1/prescriptions/{$id}");
            Log::info("Prescription {$id} deleted successfully");
            return true;
        } catch (Exception $e) {
            Log::error("Failed to delete prescription {$id}: " . $e->getMessage());
            return false;
        }
    }
}
