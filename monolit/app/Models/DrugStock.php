<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugStock extends Model
{
    protected $fillable = [
        'pharmacy_id', 'drug_name', 'quantity', 'unit_price',
        'expiry_date', 'manufacturer', 'batch_number'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'unit_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Pharmacy relationship
     */
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
}
