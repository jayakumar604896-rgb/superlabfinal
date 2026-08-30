<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentGatewayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('payment_gateway') ? $this->route('payment_gateway') : null;

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:payment_gateways,slug,' . $id,
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'environment' => 'required|in:sandbox,live',
            'status' => 'required|in:active,inactive',
            'additional_settings' => 'nullable|array',
        ];
    }
}
