<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds for default system settings.
     */
    public function run(): void
    {
        $settings = [
            // Media & Brand
            [
                'key'   => 'site_logo',
                'value' => 'assets/img/logo.png',
                'group' => 'media',
                'type'  => 'image',
            ],
            [
                'key'   => 'site_logo_white',
                'value' => 'assets/img/logo.png',
                'group' => 'media',
                'type'  => 'image',
            ],
            [
                'key'   => 'login_logo',
                'value' => 'assets/img/logo.png',
                'group' => 'media',
                'type'  => 'image',
            ],
            [
                'key'   => 'favicon',
                'value' => 'assets/img/logo.jpg',
                'group' => 'media',
                'type'  => 'image',
            ],

            // General Information
            [
                'key'   => 'site_name',
                'value' => 'Inoodex Inventory',
                'group' => 'general',
                'type'  => 'text',
            ],
            [
                'key'   => 'site_tagline',
                'value' => 'Enterprise ERP & Inventory Management',
                'group' => 'general',
                'type'  => 'text',
            ],
            [
                'key'   => 'footer_text',
                'value' => '© 2026 Inoodex Inventory. All rights reserved.',
                'group' => 'general',
                'type'  => 'text',
            ],
            [
                'key'   => 'contact_email',
                'value' => 'support@inoodex.com',
                'group' => 'general',
                'type'  => 'text',
            ],
            [
                'key'   => 'contact_phone',
                'value' => '+880 1700-000000',
                'group' => 'general',
                'type'  => 'text',
            ],
            [
                'key'   => 'address',
                'value' => 'Dhaka, Bangladesh',
                'group' => 'general',
                'type'  => 'textarea',
            ],

            // Localization
            [
                'key'   => 'currency_symbol',
                'value' => '৳',
                'group' => 'localization',
                'type'  => 'text',
            ],
            [
                'key'   => 'currency_code',
                'value' => 'BDT',
                'group' => 'localization',
                'type'  => 'text',
            ],
            [
                'key'   => 'date_format',
                'value' => 'd M, Y',
                'group' => 'localization',
                'type'  => 'text',
            ],
            [
                'key'   => 'timezone',
                'value' => 'Asia/Dhaka',
                'group' => 'localization',
                'type'  => 'text',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                    'type'  => $setting['type'],
                ]
            );
        }
    }
}
