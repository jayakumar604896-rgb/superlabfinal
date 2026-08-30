<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('payment') ? $this->route('payment') : null;

        return [
            'booking_id' => 'required|exists:bookings,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'transaction_id' => 'nullable|string|max:255|unique:payments,transaction_id,' . $id,
            'amount' => 'required|integer|min:0',
            'status' => 'required|in:pending,success,failed,refunded',
            'payment_date' => 'nullable|date_format:Y-m-d\TH:i',
            'remarks' => 'nullable|string',
        ];
    }
}
