<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FooterLocation;

class FooterLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['location_name' => 'Lab Test in Delhi', 'map_link' => 'https://maps.google.com/?q=Delhi'],
            ['location_name' => 'Lab Test in Gurgaon', 'map_link' => 'https://maps.google.com/?q=Gurgaon'],
            ['location_name' => 'Lab Test in Noida', 'map_link' => 'https://maps.google.com/?q=Noida'],
            ['location_name' => 'Lab Test in Ghaziabad', 'map_link' => 'https://maps.google.com/?q=Ghaziabad'],
            ['location_name' => 'Lab Test in Pune', 'map_link' => 'https://maps.google.com/?q=Pune'],
            ['location_name' => 'Lab Test in Mumbai', 'map_link' => 'https://maps.google.com/?q=Mumbai'],
            ['location_name' => 'Lab Test in Bengaluru', 'map_link' => 'https://maps.google.com/?q=Bengaluru'],
            ['location_name' => 'Lab Test in Dehradun', 'map_link' => 'https://maps.google.com/?q=Dehradun'],
            ['location_name' => 'Lab Test in Faridabad', 'map_link' => 'https://maps.google.com/?q=Faridabad'],
            ['location_name' => 'Lab Test in Thane', 'map_link' => 'https://maps.google.com/?q=Thane'],
            ['location_name' => 'Lab Test in Manesar', 'map_link' => 'https://maps.google.com/?q=Manesar'],
            ['location_name' => 'Lab Test in Zirakpur', 'map_link' => 'https://maps.google.com/?q=Zirakpur'],
            ['location_name' => 'Lab Test in Greater Noida', 'map_link' => 'https://maps.google.com/?q=Greater+Noida'],
            ['location_name' => 'Lab Test in Navi Mumbai', 'map_link' => 'https://maps.google.com/?q=Navi+Mumbai'],
            ['location_name' => 'Lab Test in Pimpri Chinchwad', 'map_link' => 'https://maps.google.com/?q=Pimpri+Chinchwad'],
            ['location_name' => 'Lab Test in Mohali', 'map_link' => 'https://maps.google.com/?q=Mohali'],
            ['location_name' => 'Lab Test in Jaipur', 'map_link' => 'https://maps.google.com/?q=Jaipur'],
            ['location_name' => 'Lab Test in Ahmedabad', 'map_link' => 'https://maps.google.com/?q=Ahmedabad'],
            ['location_name' => 'Lab Test in Rohtak', 'map_link' => 'https://maps.google.com/?q=Rohtak'],
            ['location_name' => 'Lab Test in Kolkata', 'map_link' => 'https://maps.google.com/?q=Kolkata'],
            ['location_name' => 'Lab Test in Chennai', 'map_link' => 'https://maps.google.com/?q=Chennai'],
            ['location_name' => 'Lab Test in Hyderabad', 'map_link' => 'https://maps.google.com/?q=Hyderabad'],
            ['location_name' => 'Lab Test in Hoshiarpur', 'map_link' => 'https://maps.google.com/?q=Hoshiarpur'],
            ['location_name' => 'Lab Test in Indore', 'map_link' => 'https://maps.google.com/?q=Indore'],
            ['location_name' => 'Lab Test in Khanna', 'map_link' => 'https://maps.google.com/?q=Khanna'],
            ['location_name' => 'Lab Test in Nashik', 'map_link' => 'https://maps.google.com/?q=Nashik'],
            ['location_name' => 'Lab Test in Sirsa', 'map_link' => 'https://maps.google.com/?q=Sirsa'],
            ['location_name' => 'Lab Test in Mathura', 'map_link' => 'https://maps.google.com/?q=Mathura'],
            ['location_name' => 'Lab Test in Agra', 'map_link' => 'https://maps.google.com/?q=Agra'],
            ['location_name' => 'Lab Test in Rudrapur', 'map_link' => 'https://maps.google.com/?q=Rudrapur'],
            ['location_name' => 'Lab Test in Hisar', 'map_link' => 'https://maps.google.com/?q=Hisar'],
            ['location_name' => 'Lab Test in Gohana', 'map_link' => 'https://maps.google.com/?q=Gohana'],
            ['location_name' => 'Lab Test in Chandigarh', 'map_link' => 'https://maps.google.com/?q=Chandigarh'],
            ['location_name' => 'Lab Test in Panchkula', 'map_link' => 'https://maps.google.com/?q=Panchkula'],
            ['location_name' => 'Lab Test in Jalandhar', 'map_link' => 'https://maps.google.com/?q=Jalandhar'],
            ['location_name' => 'Lab Test in Ludhiana', 'map_link' => 'https://maps.google.com/?q=Ludhiana'],
            ['location_name' => 'Lab Test in Amritsar', 'map_link' => 'https://maps.google.com/?q=Amritsar'],
            ['location_name' => 'Lab Test in Haridwar', 'map_link' => 'https://maps.google.com/?q=Haridwar'],
            ['location_name' => 'Lab Test in Rishikesh', 'map_link' => 'https://maps.google.com/?q=Rishikesh'],
            ['location_name' => 'Lab Test in Saharanpur', 'map_link' => 'https://maps.google.com/?q=Saharanpur'],
            ['location_name' => 'Lab Test in Lucknow', 'map_link' => 'https://maps.google.com/?q=Lucknow'],
            ['location_name' => 'Lab Test in Patna', 'map_link' => 'https://maps.google.com/?q=Patna'],
            ['location_name' => 'Lab Test in Nagpur', 'map_link' => 'https://maps.google.com/?q=Nagpur'],
            ['location_name' => 'Lab Test in Gwalior', 'map_link' => 'https://maps.google.com/?q=Gwalior'],
            ['location_name' => 'Lab Test in Moradabad', 'map_link' => 'https://maps.google.com/?q=Moradabad'],
            ['location_name' => 'Lab Test in Aligarh', 'map_link' => 'https://maps.google.com/?q=Aligarh'],
            ['location_name' => 'Lab Test in Bathinda', 'map_link' => 'https://maps.google.com/?q=Bathinda'],
            ['location_name' => 'Lab Test in Pathankot', 'map_link' => 'https://maps.google.com/?q=Pathankot'],
        ];

        foreach ($locations as $loc) {
            FooterLocation::firstOrCreate(
                ['location_name' => $loc['location_name']],
                ['map_link' => $loc['map_link'], 'status' => 'active']
            );
        }
    }
}
