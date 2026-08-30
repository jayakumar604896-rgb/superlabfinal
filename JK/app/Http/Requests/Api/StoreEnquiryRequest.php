<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'required|string|max:50',
            'message' => 'nullable|string|max:5000',
            'subject' => 'nullable|string|max:255',
        ];
    }
}
