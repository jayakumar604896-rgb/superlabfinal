<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\CustomerVital;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul.sharma@example.com',
                'mobile' => '9876543210',
                'password' => Hash::make('password123'),
                'age' => 32,
                'gender' => 'Male',
                'blood_group' => 'O+',
                'address' => '12 Park Street, Chennai',
                'status' => 'active',
            ],
            [
                'name' => 'Ananya Roy',
                'email' => 'ananya.roy@example.com',
                'mobile' => '9123456789',
                'password' => Hash::make('password123'),
                'age' => 28,
                'gender' => 'Female',
                'blood_group' => 'B+',
                'address' => '45 MG Road, Chennai',
                'status' => 'active',
            ],
        ];

        foreach ($customers as $cust) {
            $customer = Customer::updateOrCreate(
                ['mobile' => $cust['mobile']],
                $cust
            );

            // Create some realistic vitals/metrics
            CustomerVital::updateOrCreate(
                ['customer_id' => $customer->id, 'metric_name' => 'Haemoglobin'],
                [
                    'metric_value' => $customer->gender === 'Male' ? '14.5' : '12.8',
                    'unit' => 'g/dL',
                    'normal_range' => $customer->gender === 'Male' ? '13.5 - 17.5' : '12.0 - 15.5',
                    'status' => 'Normal',
                ]
            );

            CustomerVital::updateOrCreate(
                ['customer_id' => $customer->id, 'metric_name' => 'Blood Sugar (Fasting)'],
                [
                    'metric_value' => $customer->name === 'Rahul Sharma' ? '105' : '88',
                    'unit' => 'mg/dL',
                    'normal_range' => '70 - 100',
                    'status' => $customer->name === 'Rahul Sharma' ? 'High' : 'Normal',
                ]
            );

            CustomerVital::updateOrCreate(
                ['customer_id' => $customer->id, 'metric_name' => 'Thyroid Stimulating Hormone (TSH)'],
                [
                    'metric_value' => '2.4',
                    'unit' => 'µIU/mL',
                    'normal_range' => '0.4 - 4.5',
                    'status' => 'Normal',
                ]
            );

            CustomerVital::updateOrCreate(
                ['customer_id' => $customer->id, 'metric_name' => 'Total Cholesterol'],
                [
                    'metric_value' => '185',
                    'unit' => 'mg/dL',
                    'normal_range' => '< 200',
                    'status' => 'Normal',
                ]
            );

            // Create some past bookings
            $lineItems = [
                [
                    'id' => '1',
                    'name' => 'Complete Blood Count (CBC)',
                    'price' => 450,
                    'type' => 'service',
                ],
                [
                    'id' => '2',
                    'name' => 'Thyroid Profile (T3, T4, TSH)',
                    'price' => 600,
                    'type' => 'service',
                ]
            ];

            Booking::updateOrCreate(
                ['booking_number' => 'BK-' . $customer->id . '1001'],
                [
                    'customer_id' => $customer->id,
                    'booking_date' => now()->subDays(15),
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'total_price' => 1050,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'customer_phone' => $customer->mobile,
                    'line_items' => $lineItems,
                    'notes' => 'Routine general checkup.',
                ]
            );

            Booking::updateOrCreate(
                ['booking_number' => 'BK-' . $customer->id . '1002'],
                [
                    'customer_id' => $customer->id,
                    'booking_date' => now()->addDays(2),
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'total_price' => 850,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'customer_phone' => $customer->mobile,
                    'line_items' => [
                        [
                            'id' => '3',
                            'name' => 'HbA1c Test',
                            'price' => 850,
                            'type' => 'service',
                        ]
                    ],
                    'notes' => 'Home collection collection scheduled for 8:00 AM.',
                ]
            );
        }
    }
}
