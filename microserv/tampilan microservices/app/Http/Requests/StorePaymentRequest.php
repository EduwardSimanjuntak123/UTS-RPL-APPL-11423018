<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'appointment_id' => 'required|exists:appointments,id',
            'patient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:credit_card,debit_card,bank_transfer,insurance',
            'insurance_claim_id' => 'nullable|exists:insurance_claims,id',
        ];
    }
}
