<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
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
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'diagnosis' => 'required|string',
            'treatment' => 'nullable|string',
            'lab_results' => 'nullable|string',
            'medications' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after:now',
            'notes' => 'nullable|string',
        ];
    }
}
