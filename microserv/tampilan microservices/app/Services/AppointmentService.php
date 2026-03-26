<?php

namespace App\Services;

use App\Models\ApiClient;
use Illuminate\Support\Facades\Log;

/**
 * AppointmentService - Microservices API Wrapper
 * 
 * All appointment operations are delegated to the Go Appointment Microservice
 * via the API Gateway. This service is purely a wrapper around HTTP calls.
 * 
 * ✅ MICROSERVICES ONLY - NO direct database access
 */
class AppointmentService
{
    protected ApiClient $apiClient;
    protected string $baseUrl = '/appointments';

    public function __construct()
    {
        $this->apiClient = new ApiClient();
    }

    /**
     * Get all appointments with optional filtering
     * GET /api/appointments?status=scheduled&doctor_id=...&date_from=...
     */
    public function getAllAppointments(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get($this->baseUrl, $filters);
            Log::info('Fetched all appointments', ['filters' => $filters, 'count' => count($response['data'] ?? [])]);
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch appointments', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get single appointment by ID
     * GET /api/appointments/{id}
     */
    public function getAppointmentById(string $id): ?array
    {
        try {
            $response = $this->apiClient->get("{$this->baseUrl}/{$id}");
            Log::info("Fetched appointment {$id}");
            return $response['data'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to fetch appointment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create new appointment
     * POST /api/appointments
     */
    public function createAppointment(array $data): array
    {
        try {
            $response = $this->apiClient->post($this->baseUrl, $data);
            Log::info('Appointment created', ['appointment_id' => $response['data']['id'] ?? 'unknown']);
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error('Failed to create appointment', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Update appointment
     * PUT /api/appointments/{id}
     */
    public function updateAppointment(string $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("{$this->baseUrl}/{$id}", $data);
            Log::info("Appointment {$id} updated");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to update appointment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete appointment
     * DELETE /api/appointments/{id}
     */
    public function deleteAppointment(string $id): array
    {
        try {
            $response = $this->apiClient->delete("{$this->baseUrl}/{$id}");
            Log::info("Appointment {$id} deleted");
            return $response;
        } catch (\Exception $e) {
            Log::error("Failed to delete appointment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Confirm appointment
     * PUT /api/appointments/{id}/confirm
     */
    public function confirmAppointment(string $id): array
    {
        try {
            $response = $this->apiClient->put("{$this->baseUrl}/{$id}/confirm", []);
            Log::info("Appointment {$id} confirmed");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to confirm appointment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Cancel appointment
     * PUT /api/appointments/{id}/cancel
     */
    public function cancelAppointment(string $id, ?string $reason = null): array
    {
        try {
            $data = [];
            if ($reason) {
                $data['reason'] = $reason;
            }
            $response = $this->apiClient->put("{$this->baseUrl}/{$id}/cancel", $data);
            Log::info("Appointment {$id} cancelled", ['reason' => $reason]);
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to cancel appointment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Complete appointment (mark as completed)
     * PUT /api/appointments/{id}/complete
     */
    public function completeAppointment(string $id, array $data = []): array
    {
        try {
            $response = $this->apiClient->put("{$this->baseUrl}/{$id}/complete", $data);
            Log::info("Appointment {$id} completed");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to complete appointment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get available slots for doctor
     * GET /api/slots?doctor_id={doctor_id}&date={date}
     */
    public function getAvailableSlots(string $doctorId, string $date): array
    {
        try {
            $response = $this->apiClient->get('/slots', [
                'doctor_id' => $doctorId,
                'date' => $date,
            ]);
            Log::info("Fetched available slots for doctor {$doctorId} on {$date}");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch available slots", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get patient's appointments
     * GET /api/patients/{patient_id}/appointments
     */
    public function getPatientAppointments(string $patientId): array
    {
        try {
            $response = $this->apiClient->get("/patients/{$patientId}/appointments");
            Log::info("Fetched appointments for patient {$patientId}");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient appointments", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get doctor schedule
     * GET /api/doctors/{doctor_id}/appointments
     */
    public function getDoctorSchedule(string $doctorId, array $filters = []): array
    {
        try {
            $response = $this->apiClient->get("/doctors/{$doctorId}/appointments", $filters);
            Log::info("Fetched schedule for doctor {$doctorId}");
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch doctor schedule", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Reschedule appointment
     * PUT /api/appointments/{id}/reschedule
     */
    public function rescheduleAppointment(string $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("{$this->baseUrl}/{$id}/reschedule", $data);
            Log::info("Appointment {$id} rescheduled");
            return $response['data'] ?? $response;
        } catch (\Exception $e) {
            Log::error("Failed to reschedule appointment {$id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}

