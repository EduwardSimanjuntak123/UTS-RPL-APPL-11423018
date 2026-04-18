<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'address', 
        'specialty', 'license_number', 'status', 'insurance_provider'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Patient appointments
     */
    public function patientAppointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    /**
     * Doctor appointments
     */
    public function doctorAppointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    /**
     * Medical records for patient (when user is patient)
     */
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'patient_id');
    }

    /**
     * Medical records created by doctor
     */
    public function doctorMedicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id');
    }

    /**
     * Prescriptions assigned by doctor
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }

    /**
     * Prescriptions received by patient
     */
    public function patientPrescriptionRecords()
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }

    /**
     * Patient prescriptions
     */
    public function patientPrescriptions()
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }

    /**
     * Payments for patient
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'patient_id');
    }

    /**
     * Patients for doctor
     */
    public function patients()
    {
        return $this->hasManyThrough(
            User::class,
            Appointment::class,
            'doctor_id',
            'id',
            'id',
            'patient_id'
        );
    }
}