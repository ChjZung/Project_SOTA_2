<?php

declare(strict_types=1);

namespace App\Support;

final class StorefrontCatalog
{
    public static function all(): array
    {
        return json_decode(file_get_contents(resource_path('data/storefront-products.json')), true, 512, JSON_THROW_ON_ERROR);
    }

    public static function find(string $slugOrId): ?array
    {
        foreach (self::all() as $product) {
            if ($product['slug'] === $slugOrId || (string) $product['id'] === $slugOrId) {
                return $product;
            }
        }

        return null;
    }

    public static function selection(array $product, array $options): ?float
    {
        if (empty($product['variations'])) {
            return $options === [] ? (float) ($product['sale_price'] ?? $product['price']) : null;
        }

        foreach ($product['variations'] as $variation) {
            if ($variation['attributes'] == $options) {
                return (float) $variation['price'];
            }
        }

        return null;
    }
}
