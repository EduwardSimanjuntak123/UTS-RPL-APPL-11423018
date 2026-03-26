<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|unique:users,email,' . $this->route('user'),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'specialty' => 'nullable|string',
            'insurance_provider' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,suspended',
        ];
    }
}
