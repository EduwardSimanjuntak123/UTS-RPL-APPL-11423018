<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    // Get all appointments
    public function index()
    {
        try {
            $appointments = Appointment::with(['patient', 'doctor'])->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $appointments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch appointments: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get appointment by ID
    public function show(Appointment $appointment)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $appointment->load(['patient', 'doctor'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Appointment not found'
            ], 404);
        }
    }

    // Create appointment
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|exists:users,id',
                'doctor_id' => 'required|exists:users,id',
                'appointment_date' => 'required|date',
                'appointment_time' => 'required',
                'reason' => 'nullable|string',
                'notes' => 'nullable|string',
                'status' => 'nullable|in:scheduled,completed,cancelled,rescheduled'
            ]);

            $appointment = Appointment::create([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $validated['doctor_id'],
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'reason' => $validated['reason'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'] ?? 'scheduled'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment created successfully',
                'data' => $appointment->load(['patient', 'doctor'])
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update appointment
    public function update(Request $request, Appointment $appointment)
    {
        try {
            $validated = $request->validate([
                'appointment_date' => 'nullable|date',
                'appointment_time' => 'nullable',
                'reason' => 'nullable|string',
                'notes' => 'nullable|string',
                'status' => 'nullable|in:scheduled,completed,cancelled,rescheduled'
            ]);

            $appointment->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment updated successfully',
                'data' => $appointment->load(['patient', 'doctor'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete appointment
    public function destroy(Appointment $appointment)
    {
        try {
            $appointment->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get appointments by doctor
    public function getByDoctor($doctorId)
    {
        try {
            $appointments = Appointment::where('doctor_id', $doctorId)
                ->with(['patient', 'doctor'])
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $appointments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch appointments: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get appointments by patient
    public function getByPatient($patientId)
    {
        try {
            $appointments = Appointment::where('patient_id', $patientId)
                ->with(['patient', 'doctor'])
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $appointments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch appointments: ' . $e->getMessage()
            ], 500);
        }
    }

    // Cancel appointment
    public function cancel(Appointment $appointment)
    {
        try {
            $appointment->update(['status' => 'cancelled']);

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment cancelled successfully',
                'data' => $appointment
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to cancel appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    // Reschedule appointment
    public function reschedule(Request $request, Appointment $appointment)
    {
        try {
            $validated = $request->validate([
                'appointment_date' => 'required|date',
                'appointment_time' => 'required'
            ]);

            $appointment->update([
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'status' => 'rescheduled'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment rescheduled successfully',
                'data' => $appointment->load(['patient', 'doctor'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reschedule appointment: ' . $e->getMessage()
            ], 500);
        }
    }
}