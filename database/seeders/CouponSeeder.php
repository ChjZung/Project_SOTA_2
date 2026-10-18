<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code'             => 'VEGAN10',
                'type'             => 'percent',
                'value'            => 10.00,
                'min_order_amount' => 20.00,
                'max_uses'         => 100,
                'used_count'       => 0,
                'starts_at'        => now()->subDays(5)->toDateString(),
                'expires_at'       => now()->addMonths(3)->toDateString(),
                'is_active'        => true,
            ],
            [
                'code'             => 'WELCOME5',
                'type'             => 'fixed',
                'value'            => 5.00,
                'min_order_amount' => 15.00,
                'max_uses'         => 500,
                'used_count'       => 0,
                'starts_at'        => now()->subDays(5)->toDateString(),
                'expires_at'       => now()->addMonths(6)->toDateString(),
                'is_active'        => true,
            ],
            [
                'code'             => 'SAVE20',
                'type'             => 'percent',
                'value'            => 20.00,
                'min_order_amount' => 50.00,
                'max_uses'         => 50,
                'used_count'       => 0,
                'starts_at'        => now()->subDays(1)->toDateString(),
                'expires_at'       => now()->addMonths(1)->toDateString(),
                'is_active'        => true,
            ],
        ];

        foreach ($coupons as $item) {
            Coupon::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}
