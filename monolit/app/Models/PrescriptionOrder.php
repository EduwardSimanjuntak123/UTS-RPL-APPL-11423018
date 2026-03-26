<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionOrder extends Model
{
    protected $fillable = [
        'prescription_id', 'pharmacy_id', 'quantity', 'total_price',
        'status', 'pickup_date', 'notes'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'pickup_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Prescription relationship
     */
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    /**
     * Pharmacy relationship
     */
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
}
