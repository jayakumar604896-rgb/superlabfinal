<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Service;
use App\Models\ServiceReview;
use Illuminate\Database\Seeder;

class ServiceReviewSeeder extends Seeder
{
    public function run(): void
    {
        $service = Service::where('status', 'active')->orderBy('id')->first();
        $package = Package::where('status', 'active')->orderBy('id')->first();

        if ($service) {
            ServiceReview::updateOrCreate(
                [
                    'service_id' => $service->id,
                    'reviewer_name' => 'Priya Sharma',
                    'message' => 'Booking and home collection were smooth. Reports were clear and delivered on time.',
                ],
                [
                    'package_id' => null,
                    'rating' => 5,
                    'status' => 'active',
                ]
            );

            ServiceReview::updateOrCreate(
                [
                    'service_id' => $service->id,
                    'reviewer_name' => 'Rahul Mehta',
                    'message' => 'Good experience overall. Phlebotomist was professional and punctual.',
                ],
                [
                    'package_id' => null,
                    'rating' => 4,
                    'status' => 'active',
                ]
            );
        }

        if ($package) {
            ServiceReview::updateOrCreate(
                [
                    'package_id' => $package->id,
                    'reviewer_name' => 'Ananya Reddy',
                    'message' => 'Great value package. Easy booking and helpful support team.',
                ],
                [
                    'service_id' => null,
                    'rating' => 5,
                    'status' => 'active',
                ]
            );
        }
    }
}
