<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'age' => 'nullable',
            'gender' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'booking_date' => 'nullable',
            'payment_method' => 'nullable|string|max:100',
            'payment_status' => 'nullable|string|in:pending,paid,failed,refunded',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable',
            'items.*.name' => 'required_with:items|string|max:255',
            'items.*.price' => 'nullable|numeric|min:0',
            'total_price' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50',
        ];
    }
}
