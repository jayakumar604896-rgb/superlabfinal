<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            SettingSeeder::class,
            PaymentTypeSeeder::class,
            CouponSeeder::class,
            DummyDataSeeder::class,
            PackageSeeder::class,
            TestCategorySeeder::class,
            ServiceReviewSeeder::class,
            CustomerSeeder::class,
            PaymentGatewaySeeder::class,
            FooterLocationSeeder::class,
        ]);
    }
}
