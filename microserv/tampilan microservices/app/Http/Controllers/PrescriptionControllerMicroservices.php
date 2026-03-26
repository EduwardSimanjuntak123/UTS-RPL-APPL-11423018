<?php

namespace App\Http\Controllers;

use App\Services\MedicalService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * PrescriptionController - Updated untuk menggunakan MedicalService (Microservices)
 * 
 * Menangani prescriptions yang diambil dari MedicalService
 */
class PrescriptionControllerMicroservices extends Controller
{
    public function __construct(private MedicalService $medicalService)
    {
    }

    /**
     * Get all prescriptions dengan filtering
     * GET /api/prescriptions?patient_id=uuid&status=active&doctor_id=uuid
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('patient_id')) {
                $filters['patient_id'] = $request->patient_id;
            }
            if ($request->has('doctor_id')) {
                $filters['doctor_id'] = $request->doctor_id;
            }
            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }

            $prescriptions = $this->medicalService->getAllPrescriptions($filters);

            return response()->json([
                'status' => 'success',
                'data' => $prescriptions,
                'message' => 'Prescriptions fetched successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch prescriptions: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch prescriptions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single prescription
     * GET /api/prescriptions/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $prescription = $this->medicalService->getPrescriptionById($id);

            if (!$prescription) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Prescription not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $prescription,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch prescription {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch prescription',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new prescription
     * POST /api/prescriptions
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|uuid',
                'doctor_id' => 'required|uuid',
                'medication_name' => 'required|string|max:255',
                'dosage' => 'required|string|max:100',
                'frequency' => 'required|string|max:100',
                'duration' => 'required|string|max:100',
                'instructions' => 'nullable|string',
                'is_refillable' => 'nullable|boolean',
                'refill_count' => 'nullable|integer|min:0',
                'expiry_date' => 'nullable|date',
            ]);

            $prescription = $this->medicalService->createPrescription($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Prescription created successfully',
                'data' => $prescription,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create prescription: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create prescription',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update prescription
     * PUT /api/prescriptions/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'dosage' => 'nullable|string|max:100',
                'frequency' => 'nullable|string|max:100',
                'duration' => 'nullable|string|max:100',
                'instructions' => 'nullable|string',
                'status' => 'nullable|in:active,inactive,refilled,expired,cancelled',
            ]);

            $prescription = $this->medicalService->updatePrescription($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Prescription updated successfully',
                'data' => $prescription,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update prescription {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update prescription',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete prescription
     * DELETE /api/prescriptions/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->medicalService->deletePrescription($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Prescription deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete prescription {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete prescription',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get patient prescriptions
     * GET /api/patients/{patient_id}/prescriptions
     */
    public function getPatientPrescriptions($patientId): JsonResponse
    {
        try {
            $prescriptions = $this->medicalService->getPatientPrescriptions($patientId);

            return response()->json([
                'status' => 'success',
                'data' => $prescriptions,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch prescriptions for patient {$patientId}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch patient prescriptions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get doctor prescriptions
     * GET /api/doctors/{doctor_id}/prescriptions
     */
    public function getDoctorPrescriptions($doctorId): JsonResponse
    {
        try {
            $prescriptions = $this->medicalService->getAllPrescriptions([
                'doctor_id' => $doctorId,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $prescriptions,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch prescriptions for doctor {$doctorId}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch doctor prescriptions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark prescription as refilled
     * PUT /api/prescriptions/{id}/refill
     */
    public function refillPrescription($id): JsonResponse
    {
        try {
            $prescription = $this->medicalService->updatePrescription($id, [
                'status' => 'refilled',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Prescription refilled successfully',
                'data' => $prescription,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to refill prescription {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to refill prescription',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Cancel prescription
     * DELETE /api/prescriptions/{id}/cancel
     */
    public function cancelPrescription($id): JsonResponse
    {
        try {
            $prescription = $this->medicalService->updatePrescription($id, [
                'status' => 'cancelled',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Prescription cancelled successfully',
                'data' => $prescription,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to cancel prescription {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to cancel prescription',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
