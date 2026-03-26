<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'appointment_id', 'medication',
        'dosage', 'frequency', 'duration', 'instructions', 'status',
        'issue_date', 'expiry_date', 'pharmacy_id'
    ];

    protected $casts = [
        'issue_date' => 'datetime',
        'expiry_date' => 'datetime',
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
     * Appointment relationship
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Pharmacy relationship
     */
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    /**
     * Prescription orders
     */
    public function prescriptionOrders()
    {
        return $this->hasMany(PrescriptionOrder::class);
    }
}
