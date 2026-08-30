<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'SuperLab Diagnostics',
                'type' => 'text',
                'group' => 'general'
            ],
            [
                'key' => 'site_tagline',
                'value' => 'Leading Pathological and Medical Testing Laboratory',
                'type' => 'text',
                'group' => 'general'
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'file',
                'group' => 'general'
            ],
            [
                'key' => 'site_footer_logo',
                'value' => null,
                'type' => 'file',
                'group' => 'general'
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@superlab.com',
                'type' => 'text',
                'group' => 'contact'
            ],
            [
                'key' => 'contact_phone',
                'value' => '+1 (555) 019-2834',
                'type' => 'text',
                'group' => 'contact'
            ],
            [
                'key' => 'contact_whatsapp',
                'value' => '+1 (555) 019-2834',
                'type' => 'text',
                'group' => 'contact'
            ],
            [
                'key' => 'contact_address',
                'value' => '123 Health Ave, Medical District, NY 10001',
                'type' => 'textarea',
                'group' => 'contact'
            ],
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/superlab',
                'type' => 'text',
                'group' => 'social'
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/superlab',
                'type' => 'text',
                'group' => 'social'
            ],
            [
                'key' => 'social_linkedin',
                'value' => 'https://linkedin.com/company/superlab',
                'type' => 'text',
                'group' => 'social'
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/superlab',
                'type' => 'text',
                'group' => 'social'
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
