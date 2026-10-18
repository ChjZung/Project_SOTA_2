<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'product_code',
        'price',
        'sale_price',
        'stock',
        'summary',
        'description',
        'image',
        'gallery',
        'is_featured',
        'is_deal',
        'sales_count',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'is_deal' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Quan hệ N-1: Sản phẩm thuộc về một Danh mục
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope: Lọc sản phẩm nổi bật
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Lọc sản phẩm khuyến mãi Flash Deal
     */
    public function scopeDeals(Builder $query): Builder
    {
        return $query->where('is_deal', true);
    }

    /**
     * Scope: Lọc sản phẩm đang hiển thị
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Tính giá bán cuối cùng (nếu có sale_price thì lấy sale_price)
     */
    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    /**
     * Tính phần trăm giảm giá (VD: 25% Off)
     */
    public function getDiscountPercentAttribute(): int
    {
        if ($this->sale_price && $this->price > 0 && $this->sale_price < $this->price) {
            return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    /**
     * Đường dẫn ảnh đại diện chuẩn
     */
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('wp-content/uploads/sites/186/2023/12/bakery-cake-1.jpg');
        }
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        return asset(ltrim($this->image, '/'));
    }

    /**
     * Định dạng giá tiền USD
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format((float) $this->price, 2);
    }

    public function getFormattedSalePriceAttribute(): ?string
    {
        return $this->sale_price ? '$' . number_format((float) $this->sale_price, 2) : null;
    }
}