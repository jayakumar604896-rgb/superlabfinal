<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use Illuminate\Support\Str;

class StripePaymentService implements PaymentServiceInterface
{
    private ?string $publishableKey;
    private ?string $secretKey;
    private bool $isSandbox;

    public function __construct(PaymentGateway $gateway)
    {
        $this->publishableKey = $gateway->api_key;
        $this->secretKey = $gateway->api_secret;
        $this->isSandbox = $gateway->environment === 'sandbox';
    }

    public function createOrder(float $amount, array $options = []): array
    {
        // Mock Stripe client/secret generation
        $clientSecret = 'pi_' . Str::random(24) . '_secret_' . Str::random(24);
        
        return [
            'status' => 'success',
            'client_secret' => $clientSecret,
            'publishable_key' => $this->publishableKey ?: 'pk_test_mock_stripe',
            'amount' => $amount,
            'currency' => 'usd',
            'mock' => true,
        ];
    }

    public function verifyPayment(array $payload): bool
    {
        // For simulation, if a token or payment intent ID is provided, verify it.
        return !empty($payload['stripe_payment_intent_id']) || !empty($payload['mock']);
    }
}
