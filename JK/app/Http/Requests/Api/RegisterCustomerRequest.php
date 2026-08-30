<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'mobile' => 'required|string|unique:customers,mobile',
            'password' => 'required|string|min:8|confirmed',
            'age' => 'nullable|integer|min:1|max:120',
            'gender' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:1000',
        ];
    }
}
