<?php

namespace App\Services;

use App\Services\Api\ApiClient;
use Illuminate\Support\Facades\Log;

class MedicalRecordService
{
    private $apiClient;
    private $baseUrl = 'http://localhost:3000/api/v1/medical-records';
    private $cacheTime = 3600;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Get all medical records
     * GET /api/medical-records
     */
    public function getAllMedicalRecords(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get($this->baseUrl, $filters);
            Log::info('Fetched all medical records');
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch medical records', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get single medical record by ID
     * GET /api/medical-records/{id}
     */
    public function getMedicalRecordById(string $id): ?array
    {
        try {
            $response = $this->apiClient->get("{$this->baseUrl}/{$id}");
            Log::info("Fetched medical record {$id}");
            return $response['data'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to fetch medical record {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create new medical record
     * POST /api/medical-records
     */
    public function createMedicalRecord(array $data): array
    {
        try {
            $response = $this->apiClient->post($this->baseUrl, $data);
            Log::info('Medical record created');
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to create medical record', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update medical record
     * PUT /api/medical-records/{id}
     */
    public function updateMedicalRecord(string $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("{$this->baseUrl}/{$id}", $data);
            Log::info("Medical record {$id} updated");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to update medical record {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete medical record
     * DELETE /api/medical-records/{id}
     */
    public function deleteMedicalRecord(string $id): array
    {
        try {
            $response = $this->apiClient->delete("{$this->baseUrl}/{$id}");
            Log::info("Medical record {$id} deleted");
            return $response;
        } catch (\Exception $e) {
            Log::error("Failed to delete medical record {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get patient medical history
     * GET /api/patients/{patient_id}/medical-records
     */
    public function getPatientHistory(string $patientId): array
    {
        try {
            $response = $this->apiClient->get("/api/patients/{$patientId}/medical-records");
            Log::info("Fetched medical history for patient {$patientId}");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient medical history", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get patient medical summary
     * GET /api/patients/{patient_id}/medical-summary
     */
    public function getPatientSummary(string $patientId): array
    {
        try {
            $response = $this->apiClient->get("/api/patients/{$patientId}/medical-summary");
            Log::info("Fetched medical summary for patient {$patientId}");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient medical summary", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Export patient medical records
     * GET /api/patients/{patient_id}/medical-records/export
     */
    public function exportPatientRecords(string $patientId, string $format = 'json'): array
    {
        try {
            $response = $this->apiClient->get(
                "/api/patients/{$patientId}/medical-records/export",
                ['format' => $format]
            );
            Log::info("Exported medical records for patient {$patientId}");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to export medical records", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get patient prescriptions from medical records
     * GET /api/patients/{patient_id}/prescriptions
     */
    public function getPatientPrescriptions(string $patientId): array
    {
        try {
            $response = $this->apiClient->get("/api/patients/{$patientId}/prescriptions");
            Log::info("Fetched prescriptions for patient {$patientId}");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient prescriptions", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create medical record from appointment
     * POST /api/appointments/{appointment_id}/complete
     */
    public function createFromAppointment(string $appointmentId, array $data): array
    {
        try {
            $response = $this->apiClient->post(
                "/api/appointments/{$appointmentId}/complete",
                $data
            );
            Log::info("Medical record created from appointment {$appointmentId}");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to create medical record from appointment", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Check for duplicate diagnosis via API
     * POST /api/medical-records/check-duplicate-diagnosis
     */
    public function hasDuplicateDiagnosis(string $patientId, string $diagnosis, int $daysBack = 30): bool
    {
        try {
            $response = $this->apiClient->post(
                "{$this->baseUrl}/check-duplicate-diagnosis",
                [
                    'patient_id' => $patientId,
                    'diagnosis' => $diagnosis,
                    'days_back' => $daysBack
                ]
            );
            Log::info("Checked duplicate diagnosis for patient {$patientId}");
            return $response['exists'] ?? false;
        } catch (\Exception $e) {
            Log::error("Failed to check duplicate diagnosis", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get prescribed medications for patient
     * GET /api/patients/{patient_id}/medications
     */
    public function getPatientMedications(string $patientId): array
    {
        try {
            $response = $this->apiClient->get("/api/patients/{$patientId}/medications");
            Log::info("Fetched medications for patient {$patientId}");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient medications", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get all clinical notes with filters
     * GET /api/clinical-notes
     */
    public function getAllClinicalNotes(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('http://localhost:3000/api/clinical-notes', $filters);
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
            $response = $this->apiClient->post('http://localhost:3000/api/clinical-notes', $data);
            Log::info('Clinical note created');
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
            $response = $this->apiClient->put("http://localhost:3000/api/clinical-notes/{$id}", $data);
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
            $response = $this->apiClient->get("/api/patients/{$patientId}/clinical-notes");
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
            $response = $this->apiClient->delete("http://localhost:3000/api/clinical-notes/{$id}");
            Log::info("Clinical note {$id} deleted");
            return $response;
        } catch (\Exception $e) {
            Log::error("Failed to delete clinical note {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get all lab results
     * GET /api/lab-results
     */
    public function getAllLabResults(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('http://localhost:3000/api/lab-results', $filters);
            Log::info('Fetched all lab results');
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch lab results', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get lab result by ID
     * GET /api/lab-results/{id}
     */
    public function getLabResultById(string $id): ?array
    {
        try {
            $response = $this->apiClient->get("http://localhost:3000/api/lab-results/{$id}");
            Log::info("Fetched lab result {$id}");
            return $response['data'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to fetch lab result {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create lab result
     * POST /api/lab-results
     */
    public function createLabResult(array $data): array
    {
        try {
            $response = $this->apiClient->post('http://localhost:3000/api/lab-results', $data);
            Log::info('Lab result created');
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to create lab result', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update lab result
     * PUT /api/lab-results/{id}
     */
    public function updateLabResult(string $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("http://localhost:3000/api/lab-results/{$id}", $data);
            Log::info("Lab result {$id} updated");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to update lab result {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete lab result
     * DELETE /api/lab-results/{id}
     */
    public function deleteLabResult(string $id): array
    {
        try {
            $response = $this->apiClient->delete("http://localhost:3000/api/lab-results/{$id}");
            Log::info("Lab result {$id} deleted");
            return $response;
        } catch (\Exception $e) {
            Log::error("Failed to delete lab result {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get patient lab results
     * GET /api/patients/{patient_id}/lab-results
     */
    public function getPatientLabResults(string $patientId): array
    {
        try {
            $response = $this->apiClient->get("/api/patients/{$patientId}/lab-results");
            Log::info("Fetched lab results for patient {$patientId}");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient lab results", ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
