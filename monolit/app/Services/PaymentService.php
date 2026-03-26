<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\InsuranceClaim;
use App\Models\Appointment;

class PaymentService
{
    /**
     * Process payment
     */
    public function processPayment(array $data)
    {
        $appointment = Appointment::findOrFail($data['appointment_id']);

        // Hitung biaya dari appointment
        $amount = $data['amount'] ?? 150000; // Default amount

        $payment = Payment::create([
            'appointment_id' => $data['appointment_id'],
            'patient_id' => $appointment->patient_id,
            'amount' => $amount,
            'method' => $data['method'],
            'status' => 'completed', // Asumsi payment langsung sukses
            'transaction_id' => 'TXN-' . uniqid(),
            'insurance_claim_id' => $data['insurance_claim_id'] ?? null,
        ]);

        return $payment;
    }

    /**
     * Refund payment
     */
    public function refundPayment(Payment $payment, $reason = null)
    {
        if ($payment->status !== 'completed') {
            throw new \Exception('Hanya pembayaran yang sudah selesai yang bisa di-refund');
        }

        $payment->update([
            'status' => 'refunded',
            'notes' => $reason
        ]);

        return $payment;
    }

    /**
     * Create insurance claim
     */
    public function createInsuranceClaim(array $data)
    {
        $claim = InsuranceClaim::create([
            'patient_id' => $data['patient_id'],
            'appointment_id' => $data['appointment_id'],
            'insurance_provider' => $data['insurance_provider'],
            'policy_number' => $data['policy_number'],
            'claim_amount' => $data['claim_amount'],
            'status' => 'pending',
            'submission_date' => now(),
        ]);

        return $claim;
    }

    /**
     * Approve insurance claim
     */
    public function approveInsuranceClaim(InsuranceClaim $claim, $approvedAmount)
    {
        $claim->update([
            'status' => 'approved',
            'approved_amount' => $approvedAmount,
            'approval_date' => now(),
        ]);

        return $claim;
    }

    /**
     * Reject insurance claim
     */
    public function rejectInsuranceClaim(InsuranceClaim $claim, $reason = null)
    {
        $claim->update([
            'status' => 'rejected',
            'notes' => $reason,
        ]);

        return $claim;
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats($dateFrom = null, $dateTo = null)
    {
        $query = Payment::query();

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        return [
            'total' => $query->sum('amount'),
            'completed' => $query->where('status', 'completed')->sum('amount'),
            'pending' => $query->where('status', 'pending')->sum('amount'),
            'failed' => $query->where('status', 'failed')->sum('amount'),
            'refunded' => $query->where('status', 'refunded')->sum('amount'),
            'transaction_count' => $query->count(),
        ];
    }

    /**
     * Get revenue by payment method
     */
    public function getRevenueByMethod($dateFrom = null, $dateTo = null)
    {
        $query = Payment::where('status', 'completed');

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        return $query->selectRaw('method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('method')
            ->get();
    }
}
