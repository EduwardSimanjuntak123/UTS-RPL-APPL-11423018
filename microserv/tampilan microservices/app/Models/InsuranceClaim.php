<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceClaim extends Model
{
    protected $fillable = [
        'patient_id', 'appointment_id', 'insurance_provider',
        'policy_number', 'claim_amount', 'approved_amount', 'status',
        'submission_date', 'approval_date'
    ];

    protected $casts = [
        'claim_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'submission_date' => 'datetime',
        'approval_date' => 'datetime',
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
     * Appointment relationship
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Payments related to this claim
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
