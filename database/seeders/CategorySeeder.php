<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Plastic Pet Toys',
            'Household Plastic Products',
            'Engineering Plastic Parts',
            'Textile Industry Plastics',
            'Woodworking Accessories',
            'Medical Industry Plastics',
            'Food Industry Plastics',
        ];

        foreach ($categories as $index => $name) {
            Category::create([
                'name'       => $name,
                'slug'       => Str::slug($name),
                'sort_order' => $index,
            ]);
        }
    }
}