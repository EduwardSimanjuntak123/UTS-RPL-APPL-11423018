<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Appointment;

class MedicalRecordService
{
    /**
     * Create medical record dari appointment
     */
    public function createFromAppointment(Appointment $appointment, array $data)
    {
        $record = MedicalRecord::create([
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'appointment_id' => $appointment->id,
            'diagnosis' => $data['diagnosis'],
            'treatment' => $data['treatment'] ?? null,
            'lab_results' => $data['lab_results'] ?? null,
            'medications' => $data['medications'] ?? null,
            'follow_up_date' => $data['follow_up_date'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Update appointment status to completed
        $appointment->update(['status' => 'completed']);

        return $record;
    }

    /**
     * Get patient medical history
     */
    public function getPatientHistory($patientId)
    {
        return MedicalRecord::where('patient_id', $patientId)
            ->with(['doctor', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get medical summary for patient
     */
    public function getPatientSummary($patientId)
    {
        $records = MedicalRecord::where('patient_id', $patientId)->get();
        $prescriptions = Prescription::where('patient_id', $patientId)
            ->where('status', 'active')
            ->get();

        return [
            'total_records' => $records->count(),
            'recent_diagnosis' => $records->first()?->diagnosis,
            'active_prescriptions' => $prescriptions->count(),
            'medications' => $prescriptions->pluck('medication')->unique(),
            'last_checkup' => $records->first()?->created_at,
        ];
    }

    /**
     * Export medical records
     */
    public function exportPatientRecords($patientId, $format = 'json')
    {
        $records = MedicalRecord::where('patient_id', $patientId)
            ->with(['doctor', 'appointment'])
            ->get();

        if ($format === 'json') {
            return $records->toJson();
        }

        // Can be extended for PDF, CSV, etc.
        return $records;
    }

    /**
     * Check for duplicate diagnosis
     */
    public function hasDuplicateDiagnosis($patientId, $diagnosis, $daysBack = 30)
    {
        return MedicalRecord::where('patient_id', $patientId)
            ->where('diagnosis', $diagnosis)
            ->where('created_at', '>=', now()->subDays($daysBack))
            ->exists();
    }
}
