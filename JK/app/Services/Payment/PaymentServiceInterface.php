<?php

namespace App\Services\Payment;

interface PaymentServiceInterface
{
    /**
     * Create a payment order/session.
     *
     * @param float $amount
     * @param array $options
     * @return array
     */
    public function createOrder(float $amount, array $options = []): array;

    /**
     * Verify payment status/signature.
     *
     * @param array $payload
     * @return bool
     */
    public function verifyPayment(array $payload): bool;
}
