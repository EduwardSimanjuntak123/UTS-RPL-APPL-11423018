<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'role' => 'required|in:patient,doctor,pharmacist,admin',
            'address' => 'nullable|string',
            'specialty' => 'nullable|string|required_if:role,doctor',
            'license_number' => 'nullable|string|required_if:role,doctor',
            'insurance_provider' => 'nullable|string|required_if:role,patient',
        ];
    }
}
