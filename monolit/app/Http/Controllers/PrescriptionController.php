<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Http\Requests\StorePrescriptionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    /**
     * Get all prescriptions with filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Prescription::query();

        // Filter by patient
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by doctor
        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $prescriptions = $query->with(['patient', 'doctor', 'pharmacy', 'prescriptionOrders'])
                               ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $prescriptions,
        ]);
    }

    /**
     * Create new prescription
     */
    public function store(StorePrescriptionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['status'] = 'active';
        $validated['issue_date'] = now();

        $prescription = Prescription::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Prescription created successfully',
            'data' => $prescription->load(['patient', 'doctor', 'pharmacy']),
        ], 201);
    }

    /**
     * Get prescription by ID
     */
    public function show(Prescription $prescription): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $prescription->load(['patient', 'doctor', 'appointment', 'pharmacy', 'prescriptionOrders']),
        ]);
    }

    /**
     * Update prescription
     */
    public function update(Request $request, Prescription $prescription): JsonResponse
    {
        $validated = $request->validate([
            'medication' => 'nullable|string',
            'dosage' => 'nullable|string',
            'frequency' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
            'status' => 'nullable|in:active,completed,cancelled',
            'pharmacy_id' => 'nullable|exists:pharmacies,id',
        ]);

        $prescription->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Prescription updated successfully',
            'data' => $prescription,
        ]);
    }

    /**
     * Delete prescription
     */
    public function destroy(Prescription $prescription): JsonResponse
    {
        $prescription->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Prescription deleted successfully',
        ]);
    }

    /**
     * Get prescriptions by patient
     */
    public function getByPatient($patientId): JsonResponse
    {
        $prescriptions = Prescription::where('patient_id', $patientId)
                                     ->with(['doctor', 'pharmacy', 'prescriptionOrders'])
                                     ->get();

        return response()->json([
            'status' => 'success',
            'data' => $prescriptions,
        ]);
    }

    /**
     * Get prescriptions by doctor
     */
    public function getByDoctor($doctorId): JsonResponse
    {
        $prescriptions = Prescription::where('doctor_id', $doctorId)
                                     ->with(['patient', 'pharmacy', 'prescriptionOrders'])
                                     ->get();

        return response()->json([
            'status' => 'success',
            'data' => $prescriptions,
        ]);
    }

    /**
     * Mark prescription as completed
     */
    public function complete(Prescription $prescription): JsonResponse
    {
        $prescription->update(['status' => 'completed']);

        return response()->json([
            'status' => 'success',
            'message' => 'Prescription marked as completed',
            'data' => $prescription,
        ]);
    }

    /**
     * Cancel prescription
     */
    public function cancel(Prescription $prescription): JsonResponse
    {
        $prescription->update(['status' => 'cancelled']);

        return response()->json([
            'status' => 'success',
            'message' => 'Prescription cancelled',
            'data' => $prescription,
        ]);
    }
}
