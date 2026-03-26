<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'appointment_date', 'status',
        'description', 'notes', 'type', 'location', 'duration'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Patient relationship
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Doctor relationship
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Medical records from this appointment
     */
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'appointment_id');
    }

    /**
     * Payment record
     */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'appointment_id');
    }

    /**
     * Prescriptions from this appointment
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'appointment_id');
    }
}