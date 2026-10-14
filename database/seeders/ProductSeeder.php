<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        $sampleProducts = [
            'Plastic Dog Chew Toy',
            'Stackable Storage Box',
            'Precision Gear Component',
            'Textile Bobbin Holder',
            'Furniture Edge Protector',
            'Medical Tray Container',
            'Food-Grade Container Lid',
            'Custom Injection Molded Part',
        ];

        foreach ($sampleProducts as $index => $name) {
            Product::create([
                'category_id'  => $categories->random()->id,
                'name'         => $name,
                'slug'         => Str::slug($name),
                'product_code' => 'SP-' . str_pad((string)($index + 1), 3, '0', STR_PAD_LEFT),
                'summary'      => 'High-quality ' . strtolower($name) . ' manufactured with precision injection molding.',
                'is_featured'  => $index < 4, // 4 sản phẩm đầu là nổi bật
                'is_active'    => true,
            ]);
        }
    }
}