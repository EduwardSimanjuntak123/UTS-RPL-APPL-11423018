<?php

namespace App\Http\Controllers;

use App\Services\MedicalService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * MedicalRecordController - Updated untuk menggunakan MedicalService (Microservices)
 * 
 * Menangani semua medical records, prescriptions, lab results, dan clinical notes
 * melalui microservices API
 */
class MedicalRecordControllerMicroservices extends Controller
{
    public function __construct(private MedicalService $medicalService)
    {
    }

    /**
     * ==================== MEDICAL RECORDS ====================
     */

    /**
     * Get all medical records
     * GET /api/medical-records?patient_id=1&diagnosis=diabetes
     */
    public function indexMedicalRecords(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('patient_id')) {
                $filters['patient_id'] = $request->patient_id;
            }
            if ($request->has('diagnosis')) {
                $filters['diagnosis'] = $request->diagnosis;
            }
            if ($request->has('doctor_id')) {
                $filters['doctor_id'] = $request->doctor_id;
            }

            $records = $this->medicalService->getAllMedicalRecords($filters);

            return response()->json([
                'status' => 'success',
                'data' => $records,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch medical records: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch medical records',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single medical record
     * GET /api/medical-records/{id}
     */
    public function showMedicalRecord($id): JsonResponse
    {
        try {
            $record = $this->medicalService->getMedicalRecordById($id);

            if (!$record) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Medical record not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $record,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch medical record {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch medical record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create medical record
     * POST /api/medical-records
     */
    public function storeMedicalRecord(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|uuid',
                'doctor_id' => 'required|uuid',
                'diagnosis' => 'required|string|max:500',
                'symptoms' => 'required|array',
                'examination_findings' => 'nullable|string',
                'treatment_plan' => 'nullable|string',
                'medication_prescribed' => 'nullable|array',
                'notes' => 'nullable|string',
            ]);

            $record = $this->medicalService->createMedicalRecord($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Medical record created successfully',
                'data' => $record,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create medical record: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create medical record',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update medical record
     * PUT /api/medical-records/{id}
     */
    public function updateMedicalRecord(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'diagnosis' => 'nullable|string|max:500',
                'examination_findings' => 'nullable|string',
                'treatment_plan' => 'nullable|string',
                'medication_prescribed' => 'nullable|array',
                'notes' => 'nullable|string',
            ]);

            $record = $this->medicalService->updateMedicalRecord($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Medical record updated successfully',
                'data' => $record,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update medical record {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update medical record',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete medical record
     * DELETE /api/medical-records/{id}
     */
    public function destroyMedicalRecord($id): JsonResponse
    {
        try {
            $result = $this->medicalService->deleteMedicalRecord($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Medical record deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete medical record {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete medical record',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get patient medical records history
     * GET /api/patients/{patient_id}/medical-records
     */
    public function getPatientMedicalRecords($patientId): JsonResponse
    {
        try {
            $records = $this->medicalService->getPatientMedicalRecords($patientId);

            return response()->json([
                'status' => 'success',
                'data' => $records,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient {$patientId} medical records: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch patient medical records',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ==================== PRESCRIPTIONS ====================
     */

    /**
     * Get all prescriptions
     * GET /api/prescriptions?patient_id=1&status=active
     */
    public function indexPrescriptions(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('patient_id')) {
                $filters['patient_id'] = $request->patient_id;
            }
            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }
            if ($request->has('doctor_id')) {
                $filters['doctor_id'] = $request->doctor_id;
            }

            $prescriptions = $this->medicalService->getAllPrescriptions($filters);

            return response()->json([
                'status' => 'success',
                'data' => $prescriptions,
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
    public function showPrescription($id): JsonResponse
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
     * Create prescription
     * POST /api/prescriptions
     */
    public function storePrescription(Request $request): JsonResponse
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
    public function updatePrescription(Request $request, $id): JsonResponse
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
            Log::error("Failed to fetch patient {$patientId} prescriptions: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch patient prescriptions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ==================== LAB RESULTS ====================
     */

    /**
     * Get all lab results
     * GET /api/lab-results?patient_id=1&test_type=blood
     */
    public function indexLabResults(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('patient_id')) {
                $filters['patient_id'] = $request->patient_id;
            }
            if ($request->has('test_type')) {
                $filters['test_type'] = $request->test_type;
            }

            $results = $this->medicalService->getAllLabResults($filters);

            return response()->json([
                'status' => 'success',
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch lab results: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch lab results',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create lab result
     * POST /api/lab-results
     */
    public function storeLabResult(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|uuid',
                'test_type' => 'required|string|max:100',
                'test_name' => 'required|string|max:255',
                'result_value' => 'required|string',
                'unit' => 'nullable|string|max:50',
                'normal_range' => 'nullable|string|max:100',
                'test_date' => 'required|date',
                'notes' => 'nullable|string',
            ]);

            $result = $this->medicalService->createLabResult($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Lab result created successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create lab result: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create lab result',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * ==================== CLINICAL NOTES ====================
     */

    /**
     * Get all clinical notes
     * GET /api/clinical-notes?patient_id=1
     */
    public function indexClinicalNotes(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('patient_id')) {
                $filters['patient_id'] = $request->patient_id;
            }

            $notes = $this->medicalService->getAllClinicalNotes($filters);

            return response()->json([
                'status' => 'success',
                'data' => $notes,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch clinical notes: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch clinical notes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create clinical note
     * POST /api/clinical-notes
     */
    public function storeClinicalNote(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|uuid',
                'doctor_id' => 'required|uuid',
                'note_content' => 'required|string',
                'note_type' => 'required|in:progress,assessment,plan,observation,follow-up',
                'is_confidential' => 'nullable|boolean',
            ]);

            $note = $this->medicalService->createClinicalNote($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Clinical note created successfully',
                'data' => $note,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create clinical note: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create clinical note',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update clinical note
     * PUT /api/clinical-notes/{id}
     */
    public function updateClinicalNote(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'note_content' => 'nullable|string',
                'note_type' => 'nullable|in:progress,assessment,plan,observation,follow-up',
            ]);

            $note = $this->medicalService->updateClinicalNote($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Clinical note updated successfully',
                'data' => $note,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update clinical note {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update clinical note',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get patient clinical notes history
     * GET /api/patients/{patient_id}/clinical-notes
     */
    public function getPatientClinicalNotes($patientId): JsonResponse
    {
        try {
            $notes = $this->medicalService->getPatientClinicalNotes($patientId);

            return response()->json([
                'status' => 'success',
                'data' => $notes,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient {$patientId} clinical notes: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch patient clinical notes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
