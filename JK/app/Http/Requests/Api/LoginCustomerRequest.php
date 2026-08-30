<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobile' => 'required_without:email|nullable|string|max:50',
            'email' => 'required_without:mobile|nullable|email|max:255',
            'password' => 'required|string',
        ];
    }
}
