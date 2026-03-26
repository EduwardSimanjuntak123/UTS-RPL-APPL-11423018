<?php

namespace App\Services;

use App\Models\Prescription;
use App\Models\PrescriptionOrder;
use App\Models\Pharmacy;
use App\Models\DrugStock;

class PrescriptionService
{
    /**
     * Create prescription dari medical record
     */
    public function createPrescription(array $data)
    {
        $prescription = Prescription::create([
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'appointment_id' => $data['appointment_id'] ?? null,
            'medication' => $data['medication'],
            'dosage' => $data['dosage'],
            'frequency' => $data['frequency'],
            'duration' => $data['duration'],
            'instructions' => $data['instructions'] ?? null,
            'status' => 'active',
            'issue_date' => now(),
            'expiry_date' => $data['expiry_date'],
            'pharmacy_id' => $data['pharmacy_id'] ?? null,
        ]);

        return $prescription;
    }

    /**
     * Order prescription dari pharmacy
     */
    public function orderPrescription(Prescription $prescription, Pharmacy $pharmacy)
    {
        // Cek ketersediaan stock
        $drugStock = DrugStock::where('pharmacy_id', $pharmacy->id)
            ->where('drug_name', $prescription->medication)
            ->first();

        if (!$drugStock) {
            throw new \Exception('Obat tidak tersedia di apotek ini');
        }

        if ($drugStock->quantity < $prescription->dosage) {
            throw new \Exception('Stok obat tidak mencukupi');
        }

        // Hitung harga
        $totalPrice = $drugStock->unit_price * $prescription->dosage;

        $order = PrescriptionOrder::create([
            'prescription_id' => $prescription->id,
            'pharmacy_id' => $pharmacy->id,
            'quantity' => $prescription->dosage,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        // Update prescription pharmacy
        $prescription->update(['pharmacy_id' => $pharmacy->id]);

        return $order;
    }

    /**
     * Get available pharmacies untuk medication
     */
    public function getAvailablePharmacies($medication)
    {
        return Pharmacy::where('status', 'active')
            ->whereHas('drugStock', function ($query) use ($medication) {
                $query->where('drug_name', $medication)
                      ->where('quantity', '>', 0);
            })
            ->with(['drugStock' => function ($query) use ($medication) {
                $query->where('drug_name', $medication);
            }])
            ->get();
    }

    /**
     * Complete prescription
     */
    public function completePrescription(Prescription $prescription)
    {
        $prescription->update(['status' => 'completed']);

        // Update drug stock jika ada order
        if ($prescription->pharmacy_id) {
            $drugStock = DrugStock::where('pharmacy_id', $prescription->pharmacy_id)
                ->where('drug_name', $prescription->medication)
                ->first();

            if ($drugStock) {
                $drugStock->decrement('quantity', $prescription->dosage);
            }
        }

        return $prescription;
    }

    /**
     * Cancel prescription
     */
    public function cancelPrescription(Prescription $prescription, $reason = null)
    {
        $prescription->update([
            'status' => 'cancelled',
        ]);

        // Cancel related orders
        if ($prescription->prescriptionOrders) {
            foreach ($prescription->prescriptionOrders as $order) {
                $order->update(['status' => 'cancelled']);
            }
        }

        return $prescription;
    }

    /**
     * Get prescription statistics
     */
    public function getPrescriptionStats($timeframe = 'month')
    {
        $query = Prescription::query();

        if ($timeframe === 'month') {
            $query->where('created_at', '>=', now()->subMonth());
        } elseif ($timeframe === 'year') {
            $query->where('created_at', '>=', now()->subYear());
        }

        return [
            'total' => $query->count(),
            'active' => $query->where('status', 'active')->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'cancelled' => $query->where('status', 'cancelled')->count(),
            'top_medications' => $query->selectRaw('medication, COUNT(*) as count')
                ->groupBy('medication')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
        ];
    }
}
