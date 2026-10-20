<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'sort_order',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Quan hệ 1-N ngược: Danh mục này thuộc về 1 Danh mục cha
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Quan hệ 1-N: Danh mục này có nhiều Danh mục con
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Quan hệ 1-N: Danh mục này chứa nhiều Sản phẩm
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope chỉ lấy danh mục đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Đường dẫn ảnh danh mục
     */
    public function getImageUrlAttribute(): string
    {
        $image = trim(str_replace('\\', '/', (string) $this->image));
        if ($image === '') {
            return $this->fallback_image_url;
        }
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }
        $image = preg_replace('#^public/#', '', ltrim($image, '/'));
        return is_file(public_path($image)) ? asset($image) : $this->fallback_image_url;
    }

    public function getFallbackImageUrlAttribute(): string
    {
        $images = [
            'food-drinks' => 'vegan-food-drinks.jpg',
            'grocery' => 'vegan-grocery-items.jpg',
            'vegan-grocery-items' => 'vegan-grocery-items.jpg',
            'fitness-nutrition' => 'vegan-diet-nutrition.jpg',
            'beauty-care' => 'vegan-beauty-care.jpg',
            'pet-care-items' => 'vegan-pet-care-items.jpg',
            'clothes-accessories' => 'vegan-clothes-accessories.jpg',
        ];

        return isset($images[$this->slug])
            ? asset('wp-content/uploads/sites/186/2023/12/'.$images[$this->slug])
            : asset('assets/images/category-placeholder.svg');
    }
}
