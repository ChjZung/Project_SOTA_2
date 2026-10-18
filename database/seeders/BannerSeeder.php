<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title'       => 'Choose Healthy, Choose Vegan',
                'subtitle'    => 'Certified Organic & Fresh Goods Delivered To Your Door',
                'image'       => 'wp-content/uploads/sites/186/2023/12/hero-image-1.jpg',
                'button_text' => 'Shop Products',
                'button_link' => '/shop',
                'position'    => 'hero',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'title'       => 'Big Day Fresh Deals',
                'subtitle'    => 'Save up to 35% on selected organic vegetables and beverages',
                'image'       => 'wp-content/uploads/sites/186/2023/12/hero-image-1.jpg',
                'button_text' => 'Explore Deals',
                'button_link' => '/shop',
                'position'    => 'promo',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'title'       => '100% Organic Vegan Care',
                'subtitle'    => 'Natural nutrition for a vibrant, healthy lifestyle',
                'image'       => 'wp-content/uploads/sites/186/2023/12/hero-image-1.jpg',
                'button_text' => 'Learn More',
                'button_link' => '/about',
                'position'    => 'sidebar',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
        ];

        foreach ($banners as $item) {
            Banner::updateOrCreate(
                ['title' => $item['title'], 'position' => $item['position']],
                $item
            );
        }
    }
}
