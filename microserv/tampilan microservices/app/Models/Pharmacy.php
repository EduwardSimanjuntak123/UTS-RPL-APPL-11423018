<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    protected $fillable = [
        'name', 'address', 'phone', 'email', 'license_number',
        'manager_id', 'status', 'latitude', 'longitude'
    ];

    /**
     * Manager (pharmacist) relationship
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Drug stock relationship
     */
    public function drugStock()
    {
        return $this->hasMany(DrugStock::class);
    }

    /**
     * Prescription orders
     */
    public function prescriptionOrders()
    {
        return $this->hasMany(PrescriptionOrder::class);
    }
}
