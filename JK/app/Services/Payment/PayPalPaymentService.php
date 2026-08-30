<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use Illuminate\Support\Str;

class PayPalPaymentService implements PaymentServiceInterface
{
    private ?string $clientId;
    private ?string $clientSecret;
    private bool $isSandbox;

    public function __construct(PaymentGateway $gateway)
    {
        $this->clientId = $gateway->api_key;
        $this->clientSecret = $gateway->api_secret;
        $this->isSandbox = $gateway->environment === 'sandbox';
    }

    public function createOrder(float $amount, array $options = []): array
    {
        $paypalOrderId = 'PAYPAL-' . strtoupper(Str::random(12));
        
        return [
            'status' => 'success',
            'order_id' => $paypalOrderId,
            'client_id' => $this->clientId ?: 'mock_paypal_client_id',
            'amount' => $amount,
            'currency' => 'USD',
            'mock' => true,
        ];
    }

    public function verifyPayment(array $payload): bool
    {
        return !empty($payload['paypal_order_id']) || !empty($payload['mock']);
    }
}
