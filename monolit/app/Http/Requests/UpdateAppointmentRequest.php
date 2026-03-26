<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
            'appointment_date' => 'nullable|date|after:now',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:scheduled,completed,cancelled,no-show',
            'type' => 'nullable|in:consultation,follow-up,general-checkup',
            'location' => 'nullable|string',
            'duration' => 'nullable|integer|min:15',
        ];
    }
}
