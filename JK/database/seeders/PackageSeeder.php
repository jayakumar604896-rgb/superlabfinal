<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use Illuminate\Support\Str;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packagesData = [
            [
                'name' => 'Wellwise Total Profile',
                'slug' => 'wellwise-total-profile',
                'badge' => 'MOST BOOKED',
                'discount_percentage' => 35,
                'tests_included_count' => 91,
                'offer_price' => 2279,
                'original_price' => 3499,
                'description' => 'Comprehensive full body health checkup covering 91 essential health parameters including CBC, Lipid, KFT, LFT, Thyroid and Diabetes.',
                'status' => 'active',
            ],
            [
                'name' => 'WellWise Exclusive Profile',
                'slug' => 'wellwise-exclusive-profile',
                'badge' => 'BEST SELLER',
                'discount_percentage' => 38,
                'tests_included_count' => 95,
                'offer_price' => 3119,
                'original_price' => 4999,
                'description' => 'Exclusive full body checkup featuring 95 advanced parameters with Extended Lipid, HbA1c, Vitamin D & B12.',
                'status' => 'active',
            ],
            [
                'name' => 'Wellwise Platinum',
                'slug' => 'wellwise-platinum',
                'badge' => 'PREMIUM',
                'discount_percentage' => 36,
                'tests_included_count' => 103,
                'offer_price' => 4499,
                'original_price' => 6999,
                'description' => 'Platinum level health panel with 103 tests including Cardiac Markers, Cancer Screening, Immunity & Hormones.',
                'status' => 'active',
            ],
            [
                'name' => 'Wellwise Advanced Profile',
                'slug' => 'wellwise-advanced-profile',
                'badge' => 'POPULAR',
                'discount_percentage' => 40,
                'tests_included_count' => 81,
                'offer_price' => 1799,
                'original_price' => 2999,
                'description' => 'Advanced health checkup with 81 vital parameters for complete organ function monitoring.',
                'status' => 'active',
            ],
            [
                'name' => 'Super Fit Full Body Panel',
                'slug' => 'super-fit-full-body-panel',
                'badge' => 'VALUE DEAL',
                'discount_percentage' => 40,
                'tests_included_count' => 60,
                'offer_price' => 1499,
                'original_price' => 2499,
                'description' => 'Budget friendly full body panel covering 60 foundational health tests for all age groups.',
                'status' => 'active',
            ],
        ];

        foreach ($packagesData as $pkg) {
            Package::updateOrCreate(
                ['slug' => $pkg['slug']],
                $pkg
            );
        }
    }
}
