<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('booking') ? $this->route('booking') : null;

        return [
            'booking_number' => 'required|string|max:255|unique:bookings,booking_number,' . $id,
            'user_id' => 'nullable|exists:users,id',
            'customer_id' => 'nullable|exists:customers,id',
            'package_id' => 'nullable|exists:packages,id',
            'payment_type_id' => 'nullable|exists:payment_types,id',
            'booking_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'total_price' => 'required|integer|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
    }
}
