<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(
            ['code' => 'SUPER25'],
            [
                'name' => 'Super Lab 25% Off',
                'discount_type' => 'percent',
                'discount_value' => 25,
                'min_order_amount' => null,
                'max_discount_amount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'guest_eligible' => true,
                'status' => 'active',
            ]
        );
    }
}
