<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'Razorpay',
                'slug' => 'razorpay',
                'api_key' => env('RAZORPAY_KEY_ID', 'rzp_test_TOzzv3iVr5Dkj8'),
                'api_secret' => env('RAZORPAY_KEY_SECRET', 'H91WpIkjxUx7Bz7yN0s67g3f'),
                'webhook_secret' => null,
                'environment' => 'sandbox',
                'status' => 'active',
                'additional_settings' => [
                    'theme_color' => '#3B82F6',
                    'company_name' => 'SuperLab Diagnostics',
                ],
            ],
            [
                'name' => 'Stripe',
                'slug' => 'stripe',
                'api_key' => env('STRIPE_KEY_ID', 'pk_test_stripe_mock_key'),
                'api_secret' => env('STRIPE_KEY_SECRET', 'sk_test_stripe_mock_secret'),
                'webhook_secret' => null,
                'environment' => 'sandbox',
                'status' => 'active',
                'additional_settings' => [
                    'theme_color' => '#635BFF',
                    'company_name' => 'SuperLab Diagnostics',
                ],
            ],
            [
                'name' => 'PayPal',
                'slug' => 'paypal',
                'api_key' => env('PAYPAL_KEY_ID', 'paypal_mock_client_id'),
                'api_secret' => env('PAYPAL_KEY_SECRET', 'paypal_mock_secret'),
                'webhook_secret' => null,
                'environment' => 'sandbox',
                'status' => 'active',
                'additional_settings' => [
                    'theme_color' => '#003087',
                    'company_name' => 'SuperLab Diagnostics',
                ],
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['slug' => $gateway['slug']],
                $gateway
            );
        }
    }
}
