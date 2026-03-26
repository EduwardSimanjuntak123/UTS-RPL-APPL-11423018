<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'appointment_id', 'patient_id', 'amount', 'status',
        'method', 'transaction_id', 'notes', 'insurance_claim_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Appointment relationship
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Patient relationship
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Insurance claim relationship
     */
    public function insuranceClaim()
    {
        return $this->belongsTo(InsuranceClaim::class);
    }
}
