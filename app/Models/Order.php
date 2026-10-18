<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'customer_note',
        'subtotal',
        'shipping_fee',
        'total',
        'payment_method',
        'payment_status',
        'status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-warning text-dark">Chờ xử lý</span>',
            'processing' => '<span class="badge bg-primary">Đang giao hàng</span>',
            'completed' => '<span class="badge bg-success">Hoàn thành</span>',
            'cancelled' => '<span class="badge bg-danger">Đã hủy</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getOrderNumberAttribute(): string
    {
        return $this->order_code ?? 'VS-'.$this->id;
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->total;
    }

    public function getNotesAttribute(): ?string
    {
        return $this->customer_note;
    }
}
