<?php

namespace App\Services;

use App\Models\Pharmacy;
use App\Models\DrugStock;

class PharmacyService
{
    /**
     * Register new pharmacy
     */
    public function registerPharmacy(array $data)
    {
        $pharmacy = Pharmacy::create([
            'name' => $data['name'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'license_number' => $data['license_number'],
            'manager_id' => $data['manager_id'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'status' => 'active',
        ]);

        return $pharmacy;
    }

    /**
     * Add drug stock
     */
    public function addDrugToStock(Pharmacy $pharmacy, array $data)
    {
        // Cek jika drug sudah ada
        $existing = DrugStock::where('pharmacy_id', $pharmacy->id)
            ->where('drug_name', $data['drug_name'])
            ->where('batch_number', $data['batch_number'])
            ->first();

        if ($existing) {
            // Update quantity jika sudah ada
            $existing->increment('quantity', $data['quantity']);
            return $existing;
        }

        $stock = DrugStock::create([
            'pharmacy_id' => $pharmacy->id,
            'drug_name' => $data['drug_name'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'expiry_date' => $data['expiry_date'],
            'manufacturer' => $data['manufacturer'],
            'batch_number' => $data['batch_number'],
        ]);

        return $stock;
    }

    /**
     * Update drug stock quantity
     */
    public function updateDrugQuantity(DrugStock $stock, $newQuantity)
    {
        $stock->update(['quantity' => $newQuantity]);
        return $stock;
    }

    /**
     * Check drug availability
     */
    public function isDrugAvailable(Pharmacy $pharmacy, $drugName, $quantity = 1)
    {
        $stock = DrugStock::where('pharmacy_id', $pharmacy->id)
            ->where('drug_name', $drugName)
            ->where('quantity', '>=', $quantity)
            ->whereDate('expiry_date', '>', now())
            ->first();

        return $stock ? true : false;
    }

    /**
     * Get low stock items
     */
    public function getLowStockItems(Pharmacy $pharmacy, $threshold = 10)
    {
        return DrugStock::where('pharmacy_id', $pharmacy->id)
            ->where('quantity', '<=', $threshold)
            ->orderBy('quantity')
            ->get();
    }

    /**
     * Get expired items
     */
    public function getExpiredItems(Pharmacy $pharmacy)
    {
        return DrugStock::where('pharmacy_id', $pharmacy->id)
            ->whereDate('expiry_date', '<=', now())
            ->get();
    }

    /**
     * Remove expired items
     */
    public function removeExpiredItems(Pharmacy $pharmacy)
    {
        $expiredItems = $this->getExpiredItems($pharmacy);
        $count = $expiredItems->count();

        foreach ($expiredItems as $item) {
            $item->delete();
        }

        return [
            'removed_count' => $count,
            'items' => $expiredItems,
        ];
    }

    /**
     * Get pharmacy inventory value
     */
    public function getInventoryValue(Pharmacy $pharmacy)
    {
        $totalValue = DrugStock::where('pharmacy_id', $pharmacy->id)
            ->selectRaw('SUM(quantity * unit_price) as total')
            ->first();

        return $totalValue->total ?? 0;
    }

    /**
     * Get nearby pharmacies
     */
    public function getNearbyPharmacies($latitude, $longitude, $distance = 5)
    {
        // Menggunakan Haversine formula
        $pharmacies = Pharmacy::where('status', 'active')
            ->selectRaw(
                "id, name, address, phone, email, latitude, longitude,
                (6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians($longitude)) + 
                sin(radians($latitude)) * sin(radians(latitude)))) AS distance"
            )
            ->having('distance', '<', $distance)
            ->orderBy('distance')
            ->get();

        return $pharmacies;
    }
}
