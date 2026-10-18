<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'Avada Vegan Store', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => '100% Organic & Healthy Plant-based Goodness', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => 'wp-content/uploads/sites/186/2023/12/vegan-logo-1.svg', 'group' => 'general'],
            ['key' => 'site_footer_about', 'value' => 'Avada Vegan Store brings you certified organic, plant-based products harvested with care and ethical love for our planet.', 'group' => 'general'],

            // Contact
            ['key' => 'site_phone', 'value' => '+1 (800) 234-5678', 'group' => 'contact'],
            ['key' => 'site_email', 'value' => 'contact@veganstore.vn', 'group' => 'contact'],
            ['key' => 'site_address', 'value' => '123 Green Valley Street, District 1, Ho Chi Minh City', 'group' => 'contact'],
            ['key' => 'site_working_hours', 'value' => 'Mon - Sat: 8:00 AM - 9:00 PM', 'group' => 'contact'],

            // Social
            ['key' => 'social_facebook', 'value' => 'https://facebook.com', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com', 'group' => 'social'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com', 'group' => 'social'],

            // Payment & Shipping
            ['key' => 'bank_name', 'value' => 'Vietcombank - Chi nhanh Ben Thanh', 'group' => 'payment'],
            ['key' => 'bank_account_number', 'value' => '0123456789999', 'group' => 'payment'],
            ['key' => 'bank_account_name', 'value' => 'CONG TY TNHH VEGAN STORE VIET NAM', 'group' => 'payment'],
            ['key' => 'shipping_fee_default', 'value' => '5.00', 'group' => 'payment'],
            ['key' => 'free_shipping_min_amount', 'value' => '50.00', 'group' => 'payment'],
        ];

        foreach ($settings as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                $item
            );
        }
    }
}
