<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Http\Requests\StoreMedicalRecordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    /**
     * Get all medical records with filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = MedicalRecord::query();

        // Filter by patient
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by doctor
        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $records = $query->with(['patient', 'doctor', 'appointment'])
                         ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $records,
        ]);
    }

    /**
     * Create new medical record
     */
    public function store(StoreMedicalRecordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $record = MedicalRecord::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Medical record created successfully',
            'data' => $record->load(['patient', 'doctor', 'appointment']),
        ], 201);
    }

    /**
     * Get medical record by ID
     */
    public function show(MedicalRecord $medicalRecord): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $medicalRecord->load(['patient', 'doctor', 'appointment']),
        ]);
    }

    /**
     * Update medical record
     */
    public function update(Request $request, MedicalRecord $medicalRecord): JsonResponse
    {
        $validated = $request->validate([
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'lab_results' => 'nullable|string',
            'medications' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $medicalRecord->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Medical record updated successfully',
            'data' => $medicalRecord,
        ]);
    }

    /**
     * Delete medical record
     */
    public function destroy(MedicalRecord $medicalRecord): JsonResponse
    {
        $medicalRecord->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Medical record deleted successfully',
        ]);
    }

    /**
     * Get patient's medical history
     */
    public function getPatientHistory($patientId): JsonResponse
    {
        $records = MedicalRecord::where('patient_id', $patientId)
                                ->with(['doctor', 'appointment'])
                                ->orderBy('created_at', 'desc')
                                ->get();

        return response()->json([
            'status' => 'success',
            'data' => $records,
        ]);
    }

    /**
     * Export medical record
     */
    public function export($patientId)
    {
        $records = MedicalRecord::where('patient_id', $patientId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $records,
        ]);
    }
}
