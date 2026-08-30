<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('payment_type') ? $this->route('payment_type') : null;

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:payment_types,slug,' . $id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];
    }
}
