<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var \App\Models\Customer|null $customer */
        $customer = $this->attributes->get('customer');
        $customerId = $customer?->id;

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customerId),
            ],
            'age' => 'nullable|integer|min:1|max:120',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:1000',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
        ];
    }
}
