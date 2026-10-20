<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'thumbnail',
        'excerpt',
        'content',
        'category',
        'meta_title',
        'meta_description',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope: bài viết đã xuất bản
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Đường dẫn ảnh đại diện bài viết
     */
    public function getThumbnailUrlAttribute(): string
    {
        if (!$this->thumbnail) {
            return asset('wp-content/uploads/sites/186/2024/02/blog-vegan-food-usa-01.jpg');
        }
        if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
            return $this->thumbnail;
        }
        $path = ltrim($this->thumbnail, '/');
        return is_file(public_path($path)) ? asset($path) : asset('wp-content/uploads/sites/186/2024/02/blog-vegan-food-usa-01.jpg');
    }
}
