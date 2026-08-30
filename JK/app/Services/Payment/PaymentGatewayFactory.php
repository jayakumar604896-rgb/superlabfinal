<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use Exception;

class PaymentGatewayFactory
{
    /**
     * Create a payment service driver instance for the given gateway slug.
     *
     * @param string $slug
     * @return PaymentServiceInterface
     * @throws Exception
     */
    public static function make(string $slug): PaymentServiceInterface
    {
        $gateway = PaymentGateway::where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!$gateway) {
            throw new Exception("Payment gateway '{$slug}' is either inactive or not configured.");
        }

        switch (strtolower($slug)) {
            case 'razorpay':
                return new RazorpayPaymentService($gateway);
            case 'stripe':
                return new StripePaymentService($gateway);
            case 'paypal':
                return new PayPalPaymentService($gateway);
            default:
                throw new Exception("Unsupported payment gateway: '{$slug}'");
        }
    }
}
