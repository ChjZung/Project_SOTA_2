<?php

namespace App\Support;

use App\Models\Product;

final class StorefrontProduct
{
    public static function data(Product $product): array
    {
        return [
            'id' => $product->id, 'slug' => $product->slug, 'name' => $product->name,
            'image' => $product->image_url, 'gallery' => array_values(array_unique(array_map(fn ($image) => asset($image), array_filter($product->gallery ?? [], fn ($image) => str_starts_with($image, 'http') || is_file(public_path(ltrim($image, '/'))))))),
            'price' => (float) $product->price, 'sale_price' => $product->sale_price !== null ? (float) $product->sale_price : null,
            'summary' => strip_tags($product->summary ?? ''), 'description' => strip_tags($product->description ?? ''),
            'category' => $product->category?->name ?? 'Vegan Products', 'category_slug' => $product->category?->slug ?? '',
            'sku' => $product->product_code ?: 'VS-'.$product->id, 'variations' => $product->variations ?? [],
            'stock' => $product->stock, 'avg_rating' => (float) $product->avg_rating,
            'database_product' => true, 'is_featured' => $product->is_featured,
        ];
    }

    public static function price(Product $product, array $options): ?float
    {
        if (!$product->variations) return $options === [] ? $product->final_price : null;
        foreach ($product->variations as $variation) {
            if (($variation['attributes'] ?? []) == $options) return (float) $variation['price'];
        }
        return null;
    }
}
