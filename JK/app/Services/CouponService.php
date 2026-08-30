<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Customer;
use Illuminate\Validation\ValidationException;

class CouponService
{
    public function findByCode(string $code): ?Coupon
    {
        return Coupon::query()
            ->where('code', strtoupper(trim($code)))
            ->first();
    }

    /**
     * @return array{
     *     coupon_id:int,
     *     code:string,
     *     discount_type:string,
     *     discount_value:float,
     *     discount_amount:int,
     *     subtotal:int,
     *     final_total:int,
     *     label:string
     * }
     */
    public function validateForCheckout(string $code, int $subtotal, ?Customer $customer = null): array
    {
        $coupon = $this->findByCode($code);

        if (! $coupon) {
            throw ValidationException::withMessages([
                'code' => 'Invalid coupon code.',
            ]);
        }

        if ($coupon->status !== 'active') {
            throw ValidationException::withMessages([
                'code' => 'This coupon is not active.',
            ]);
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            throw ValidationException::withMessages([
                'code' => 'This coupon is not valid yet.',
            ]);
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            throw ValidationException::withMessages([
                'code' => 'This coupon has expired.',
            ]);
        }

        if ($coupon->usage_limit !== null && $coupon->usage_count >= $coupon->usage_limit) {
            throw ValidationException::withMessages([
                'code' => 'This coupon has reached its usage limit.',
            ]);
        }

        if ($coupon->min_order_amount !== null && $subtotal < $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'code' => 'Minimum order amount for this coupon is ₹' . number_format($coupon->min_order_amount) . '.',
            ]);
        }

        if (! $this->isEligible($coupon, $customer)) {
            throw ValidationException::withMessages([
                'code' => 'This coupon is not available for your account.',
            ]);
        }

        $discountAmount = $this->calculateDiscount($coupon, $subtotal);

        return [
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'discount_type' => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount_value,
            'discount_amount' => $discountAmount,
            'subtotal' => $subtotal,
            'final_total' => max(0, $subtotal - $discountAmount),
            'label' => $this->formatLabel($coupon),
        ];
    }

    public function isEligible(Coupon $coupon, ?Customer $customer): bool
    {
        if ($customer) {
            if ($coupon->customers()->where('customer_id', $customer->id)->exists()) {
                return true;
            }

            return (bool) $coupon->guest_eligible;
        }

        return (bool) $coupon->guest_eligible;
    }

    public function calculateDiscount(Coupon $coupon, int $subtotal): int
    {
        if ($subtotal <= 0) {
            return 0;
        }

        $discount = $coupon->discount_type === 'fixed'
            ? (int) round((float) $coupon->discount_value)
            : (int) round($subtotal * ((float) $coupon->discount_value / 100));

        if ($coupon->discount_type === 'percent' && $coupon->max_discount_amount !== null) {
            $discount = min($discount, (int) $coupon->max_discount_amount);
        }

        return min($discount, $subtotal);
    }

    public function redeem(Coupon $coupon): void
    {
        $coupon->increment('usage_count');
    }

    public function formatLabel(Coupon $coupon): string
    {
        if ($coupon->discount_type === 'fixed') {
            return '₹' . number_format((float) $coupon->discount_value) . ' off';
        }

        return rtrim(rtrim(number_format((float) $coupon->discount_value, 2), '0'), '.') . '% off';
    }

    /**
     * Coupons the storefront may show to the current visitor.
     * Assigned customers see their coupons; everyone else sees guest-eligible offers.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listVisibleForCustomer(?Customer $customer = null): array
    {
        $query = Coupon::query()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('usage_count', '<', 'usage_limit');
            });

        if ($customer) {
            $assignedIds = $customer->coupons()->pluck('coupons.id');

            if ($assignedIds->isNotEmpty()) {
                $query->whereIn('id', $assignedIds);
            } else {
                $query->where('guest_eligible', true);
            }
        } else {
            $query->where('guest_eligible', true);
        }

        return $query
            ->orderByDesc('id')
            ->get()
            ->map(fn (Coupon $coupon) => $this->formatPublicCoupon($coupon, $customer))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function formatPublicCoupon(Coupon $coupon, ?Customer $customer = null): array
    {
        $isAssigned = $customer
            ? $coupon->customers()->where('customer_id', $customer->id)->exists()
            : false;

        return [
            'code' => $coupon->code,
            'name' => $coupon->name,
            'label' => $this->formatLabel($coupon),
            'discount_type' => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount_value,
            'min_order_amount' => $coupon->min_order_amount,
            'max_discount_amount' => $coupon->max_discount_amount,
            'is_assigned' => $isAssigned,
            'guest_eligible' => (bool) $coupon->guest_eligible,
        ];
    }
}
