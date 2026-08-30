<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon');

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($couponId),
            ],
            'name' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|integer|min:0',
            'max_discount_amount' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
            'guest_eligible' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'integer|exists:customers,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $guestEligible = $this->boolean('guest_eligible');
            $customerIds = array_filter($this->input('customer_ids', []));

            if (! $guestEligible && empty($customerIds)) {
                $validator->errors()->add(
                    'guest_eligible',
                    'Enable guest eligibility or assign at least one customer.'
                );
            }

            if ($this->input('discount_type') === 'percent' && (float) $this->input('discount_value') > 100) {
                $validator->errors()->add('discount_value', 'Percent discount cannot exceed 100.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'guest_eligible' => $this->boolean('guest_eligible'),
            'customer_ids' => array_values(array_filter($this->input('customer_ids', []))),
        ]);
    }
}
