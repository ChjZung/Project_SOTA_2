<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

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
        'avg_rating',
        'meta_title',
        'meta_description',
        'is_active',
        'variations',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'avg_rating' => 'decimal:2',
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'is_deal' => 'boolean',
        'is_active' => 'boolean',
        'variations' => 'array',
    ];

    /**
     * Quan hệ N-1: Sản phẩm thuộc về một Danh mục
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Quan hệ 1-N: Sản phẩm có nhiều dòng đơn hàng
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Quan hệ 1-N: Sản phẩm có nhiều đánh giá
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Quan hệ 1-N: Các đánh giá đã được duyệt hiển thị
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    /**
     * Quan hệ 1-N: Nằm trong danh sách yêu thích
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Cập nhật điểm đánh giá trung bình từ các review đã duyệt
     */
    public function updateAvgRating(): void
    {
        $avg = $this->approvedReviews()->avg('rating') ?: 0;
        $this->update(['avg_rating' => round((float) $avg, 2)]);
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
        $image = trim(str_replace('\\', '/', (string) $this->image));
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }
        $image = preg_replace('#^public/#', '', ltrim($image, '/'));
        if ($image !== '' && is_file(public_path($image))) {
            return asset($image);
        }
        $mapped = config('product_images.'.$this->slug);
        if ($mapped && is_file(public_path($mapped))) {
            return asset($mapped);
        }

        $categoryDefaults = [
            1 => 'wp-content/uploads/sites/186/2023/12/bakery-cake-1.jpg',
            2 => 'wp-content/uploads/sites/186/2023/12/vegan-grocery-items.jpg',
            3 => 'assets/images/products/organic-multivitamin-jar.webp',
            4 => 'wp-content/uploads/sites/186/2023/12/beauty-cosmetic-body-lotion.jpg',
            5 => 'wp-content/uploads/sites/186/2023/12/accessories-leather-bag-1.jpg',
            6 => 'wp-content/uploads/sites/186/2023/12/pet-dog-treats.jpg',
        ];

        $fallback = $categoryDefaults[$this->category_id ?? 1] ?? 'wp-content/uploads/sites/186/2023/12/bakery-cake-1.jpg';
        return is_file(public_path($fallback))
            ? asset($fallback)
            : asset('assets/images/product-placeholder.svg');
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
