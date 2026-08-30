<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentType;
use Illuminate\Support\Str;

class BookingCreationService
{
    public function __construct(private CouponService $couponService)
    {
    }

    public function createFromCheckout(array $data, ?Customer $authenticatedCustomer = null): Booking
    {
        $lineItems = Booking::normalizeLineItems($data['items'] ?? []);
        $customerId = $this->resolveCustomerId($data, $authenticatedCustomer);
        $customer = $authenticatedCustomer ?: ($customerId ? Customer::find($customerId) : null);

        $subtotal = (int) round($data['subtotal'] ?? $data['total_price'] ?? 0);
        $discountAmount = 0;
        $couponId = null;
        $couponCode = null;
        $hasCustomPackage = Booking::hasCustomPackage($data['items'] ?? []);

        if (! $hasCustomPackage && ! empty($data['coupon_code'])) {
            $couponResult = $this->couponService->validateForCheckout(
                $data['coupon_code'],
                $subtotal,
                $customer
            );
            $discountAmount = $couponResult['discount_amount'];
            $couponId = $couponResult['coupon_id'];
            $couponCode = $couponResult['code'];
            $this->couponService->redeem(Coupon::findOrFail($couponId));
        }

        $finalTotal = max(0, $subtotal - $discountAmount);

        $notes = sprintf(
            'Address: %s | Age: %s | Gender: %s | Payment: %s',
            $data['address'] ?? 'N/A',
            $data['age'] ?? 'N/A',
            $data['gender'] ?? 'N/A',
            $data['payment_method'] ?? (($data['payment_status'] ?? null) === 'paid' ? 'Online' : 'COD')
        );

        if ($couponCode) {
            $notes .= sprintf(' | Coupon: %s (-₹%d)', $couponCode, $discountAmount);
        }

        $booking = Booking::create([
            'booking_number' => 'BK-' . strtoupper(Str::random(7)),
            'customer_id' => $customerId,
            'customer_name' => $data['name'],
            'customer_email' => $data['email'] ?? ($data['mobile'] . '@superlab.local'),
            'customer_phone' => $data['mobile'],
            'booking_date' => ! empty($data['booking_date'])
                ? date('Y-m-d', strtotime($data['booking_date']))
                : now()->toDateString(),
            'subtotal_amount' => $subtotal,
            'discount_amount' => $discountAmount,
            'total_price' => $finalTotal,
            'coupon_id' => $couponId,
            'coupon_code' => $couponCode,
            'status' => 'pending',
            'payment_status' => $data['payment_status'] ?? 'pending',
            'notes' => $notes,
            'line_items' => $lineItems,
        ]);

        $paymentType = $this->resolvePaymentType($data['payment_method'] ?? null, $data['payment_status'] ?? null);
        if ($paymentType) {
            $booking->update(['payment_type_id' => $paymentType->id]);
        }

        if (($data['payment_status'] ?? null) === 'paid') {
            Payment::create([
                'booking_id' => $booking->id,
                'payment_type_id' => $paymentType?->id,
                'transaction_id' => $data['razorpay_payment_id'] ?? ('TXN-' . strtoupper(Str::random(10))),
                'amount' => $booking->total_price,
                'status' => 'success',
                'payment_date' => now(),
                'remarks' => ! empty($data['mock'])
                    ? 'Razorpay Mock Payment Success'
                    : (isset($data['razorpay_payment_id']) ? 'Razorpay Payment Success' : 'Online Mock Payment Success'),
            ]);
        }

        return $booking->fresh();
    }

    private function resolveCustomerId(array $data, ?Customer $authenticatedCustomer): ?int
    {
        if ($authenticatedCustomer) {
            return $authenticatedCustomer->id;
        }

        $customer = Customer::where('mobile', $data['mobile'])->first();

        if (! $customer && ! empty($data['email'])) {
            $customer = Customer::where('email', $data['email'])->first();
        }

        return $customer?->id;
    }

    private function resolvePaymentType(?string $paymentMethod, ?string $paymentStatus): ?PaymentType
    {
        if ($paymentMethod && stripos($paymentMethod, 'BNPL') !== false) {
            return PaymentType::firstOrCreate(
                ['slug' => 'bnpl'],
                ['name' => 'Book Now Pay Later', 'status' => 'active']
            );
        }

        if ($paymentStatus === 'paid' || ($paymentMethod && stripos($paymentMethod, 'Online') !== false)) {
            return PaymentType::firstOrCreate(
                ['slug' => 'online-payment'],
                ['name' => 'Online Payment', 'status' => 'active']
            );
        }

        return PaymentType::firstOrCreate(
            ['slug' => 'cash-on-delivery'],
            ['name' => 'Cash on Delivery', 'status' => 'active']
        );
    }
}
