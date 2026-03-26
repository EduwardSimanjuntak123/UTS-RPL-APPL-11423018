<?php

namespace App\Http\Controllers;

use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * AppointmentController - Updated untuk menggunakan AppointmentService (Microservices)
 * 
 * Menangani semua appointment/scheduling operations melalui microservices API
 */
class AppointmentControllerMicroservices extends Controller
{
    public function __construct(private AppointmentService $appointmentService)
    {
    }

    /**
     * Get all appointments dengan filtering
     * GET /api/appointments?status=scheduled&doctor_id=1&date_from=2024-01-01
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [];

            // Filter by status
            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }

            // Filter by doctor
            if ($request->has('doctor_id')) {
                $filters['doctor_id'] = $request->doctor_id;
            }

            // Filter by patient
            if ($request->has('patient_id')) {
                $filters['patient_id'] = $request->patient_id;
            }

            // Filter by date range
            if ($request->has('date_from')) {
                $filters['date_from'] = $request->date_from;
            }
            if ($request->has('date_to')) {
                $filters['date_to'] = $request->date_to;
            }

            // Available slots only
            if ($request->boolean('available')) {
                $filters['available'] = true;
            }

            $appointments = $this->appointmentService->getAllAppointments($filters);

            return response()->json([
                'status' => 'success',
                'data' => $appointments,
                'message' => 'Appointments fetched successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch appointments: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch appointments',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single appointment
     * GET /api/appointments/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $appointment = $this->appointmentService->getAppointmentById($id);

            if (!$appointment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Appointment not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $appointment,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch appointment {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch appointment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new appointment
     * POST /api/appointments
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|uuid',
                'doctor_id' => 'required|uuid',
                'appointment_date' => 'required|date|after:now',
                'appointment_time' => 'required|date_format:H:i',
                'reason' => 'required|string|max:500',
                'appointment_type' => 'required|in:checkup,followup,consultation,surgical',
                'duration_minutes' => 'nullable|integer|min:15|max:180',
                'notes' => 'nullable|string',
            ]);

            $result = $this->appointmentService->createAppointment($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment created successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create appointment: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create appointment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update appointment
     * PUT /api/appointments/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'appointment_date' => 'nullable|date|after:now',
                'appointment_time' => 'nullable|date_format:H:i',
                'reason' => 'nullable|string|max:500',
                'notes' => 'nullable|string',
            ]);

            $result = $this->appointmentService->updateAppointment($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment updated successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update appointment {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update appointment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete appointment
     * DELETE /api/appointments/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $result = $this->appointmentService->deleteAppointment($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment deleted successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete appointment {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete appointment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Confirm appointment
     * POST /api/appointments/{id}/confirm
     */
    public function confirm($id): JsonResponse
    {
        try {
            $result = $this->appointmentService->confirmAppointment($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment confirmed successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to confirm appointment {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to confirm appointment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Cancel appointment
     * POST /api/appointments/{id}/cancel
     * Body: { "reason": "optional cancellation reason" }
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'nullable|string|max:500',
            ]);

            $result = $this->appointmentService->cancelAppointment($id, $validated['reason'] ?? null);

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment cancelled successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to cancel appointment {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to cancel appointment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Complete appointment (mark as completed)
     * POST /api/appointments/{id}/complete
     */
    public function complete(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'notes' => 'nullable|string',
                'follow_up_required' => 'nullable|boolean',
            ]);

            $result = $this->appointmentService->completeAppointment($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment marked as completed',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to complete appointment {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to complete appointment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get available slots untuk doctor
     * GET /api/doctors/{doctor_id}/available-slots?date=2024-01-15
     */
    public function getAvailableSlots(Request $request, $doctorId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date|after:today',
            ]);

            $slots = $this->appointmentService->getAvailableSlots($doctorId, $validated['date']);

            return response()->json([
                'status' => 'success',
                'data' => $slots,
                'message' => 'Available slots fetched successfully',
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch available slots for doctor {$doctorId}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch available slots',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get patient appointments
     * GET /api/patients/{patient_id}/appointments
     */
    public function getPatientAppointments($patientId): JsonResponse
    {
        try {
            $appointments = $this->appointmentService->getPatientAppointments($patientId);

            return response()->json([
                'status' => 'success',
                'data' => $appointments,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient {$patientId} appointments: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch patient appointments',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get doctor schedule
     * GET /api/doctors/{doctor_id}/schedule?date_from=2024-01-01&date_to=2024-01-31
     */
    public function getDoctorSchedule(Request $request, $doctorId): JsonResponse
    {
        try {
            $filters = [
                'doctor_id' => $doctorId,
            ];

            if ($request->has('date_from')) {
                $filters['date_from'] = $request->date_from;
            }
            if ($request->has('date_to')) {
                $filters['date_to'] = $request->date_to;
            }

            $schedule = $this->appointmentService->getDoctorSchedule($doctorId, $filters);

            return response()->json([
                'status' => 'success',
                'data' => $schedule,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch doctor {$doctorId} schedule: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch doctor schedule',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get today's appointments
     * GET /api/appointments/today
     */
    public function getTodayAppointments(): JsonResponse
    {
        try {
            $today = Carbon::now()->toDateString();
            $appointments = $this->appointmentService->getAllAppointments([
                'date_from' => $today,
                'date_to' => $today,
                'status' => 'scheduled',
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $appointments,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch today appointments: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch today appointments',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reschedule appointment
     * PUT /api/appointments/{id}/reschedule
     */
    public function reschedule(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'appointment_date' => 'required|date|after:now',
                'appointment_time' => 'required|date_format:H:i',
                'reason' => 'nullable|string|max:500',
            ]);

            $result = $this->appointmentService->rescheduleAppointment($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment rescheduled successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to reschedule appointment {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reschedule appointment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
