<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Cash on Delivery',
                'slug' => 'cash-on-delivery',
                'description' => 'Pay when the sample is collected or at the lab.',
                'status' => 'active',
            ],
            [
                'name' => 'Online Payment',
                'slug' => 'online-payment',
                'description' => 'Pay online via card, UPI, or net banking.',
                'status' => 'active',
            ],
            [
                'name' => 'Book Now Pay Later',
                'slug' => 'bnpl',
                'description' => 'Book now and pay after sample collection.',
                'status' => 'active',
            ],
        ];

        foreach ($types as $type) {
            PaymentType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
