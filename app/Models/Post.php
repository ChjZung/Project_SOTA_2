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
        return asset(ltrim($this->thumbnail, '/'));
    }
}
