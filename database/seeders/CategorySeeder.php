<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Vegan Food & Drinks',
                'slug' => 'food-drinks',
                'image' => '/wp-content/uploads/sites/186/2023/12/vegan-food-drinks.jpg',
                'description' => 'Delicious, healthy 100% plant-based organic food, ready-to-eat meals, bakery, and fresh cold beverages.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Vegan Grocery Items',
                'slug' => 'grocery',
                'image' => '/wp-content/uploads/sites/186/2023/12/vegan-grocery-items.jpg',
                'description' => 'Pantry essentials, whole grains, organic pulses, fresh greens, and daily plant-based grocery goods.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Fitness & Nutrition',
                'slug' => 'fitness-nutrition',
                'image' => '/wp-content/uploads/sites/186/2023/12/vegan-diet-nutrition.jpg',
                'description' => 'Plant-powered proteins, organic vitamins, natural supplements, superfoods, and dried fruits for active health.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Vegan Beauty & Care',
                'slug' => 'beauty-care',
                'image' => '/wp-content/uploads/sites/186/2023/12/vegan-beauty-care.jpg',
                'description' => 'Cruelty-free cosmetics, botanical lotions, natural essential oils, and organic skin & hair treatments.',
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}