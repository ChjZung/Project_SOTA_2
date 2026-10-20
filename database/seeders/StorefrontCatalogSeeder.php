<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

/** Import the original demo catalogue once; admin changes stay authoritative. */
class StorefrontCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = json_decode(file_get_contents(resource_path('data/storefront-products.json')), true, 512, JSON_THROW_ON_ERROR);
        foreach ($catalog as $item) {
            $slug = $item['category_slug'] === 'vegan-grocery-items' ? 'grocery' : $item['category_slug'];
            $category = Category::firstOrCreate(['slug' => $slug], [
                'name' => $item['category'], 'sort_order' => (Category::max('sort_order') ?? 0) + 1, 'is_active' => true,
            ]);
            if (Product::withTrashed()->where('slug', $item['slug'])->exists()) continue;
            Product::create([
                'category_id' => $category->id, 'name' => $item['name'], 'slug' => $item['slug'],
                'product_code' => $item['sku'], 'price' => $item['price'], 'sale_price' => $item['sale_price'],
                'stock' => 30, 'summary' => $item['summary'], 'description' => $item['description'],
                'image' => $item['image'], 'gallery' => [$item['image']], 'variations' => $item['variations'],
                'is_active' => true, 'is_featured' => true, 'is_deal' => $item['sale_price'] !== null,
            ]);
        }
    }
}
