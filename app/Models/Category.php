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

    // Các cột được phép gán giá trị hàng loạt (Mass Assignment Protection)
    protected $fillable = ['parent_id', 'name', 'slug', 'description', 'image', 'sort_order'];

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
}