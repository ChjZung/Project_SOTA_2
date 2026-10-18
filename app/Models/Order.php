<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coupon_id',
        'order_code',
        'idempotency_token',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'customer_note',
        'admin_note',
        'subtotal',
        'coupon_code',
        'discount_amount',
        'shipping_fee',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Danh sách trạng thái hợp lệ và các chuyển tiếp được phép
     * (Vòng đời đơn hàng - Order Lifecycle State Machine)
     */
    public const VALID_TRANSITIONS = [
        'pending'    => ['confirmed', 'cancelled'],
        'confirmed'  => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped'    => ['completed', 'processing'], // processing lại nếu giao thất bại
        'completed'  => [], // Trạng thái cuối
        'cancelled'  => [], // Trạng thái cuối
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Kiểm tra xem chuyển tiếp trạng thái có hợp lệ theo sơ đồ không
     */
    public function canTransitionTo(string $targetStatus): bool
    {
        $allowed = self::VALID_TRANSITIONS[$this->status] ?? [];
        return in_array($targetStatus, $allowed, true);
    }

    /**
     * Sinh mã đơn hàng duy nhất: VS-YYYYMMDD-XXXX (4 ký tự ngẫu nhiên)
     */
    public static function generateOrderCode(): string
    {
        do {
            $code = 'VS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('order_code', $code)->exists());

        return $code;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'    => '<span class="badge bg-warning text-dark">Chờ xác nhận</span>',
            'confirmed'  => '<span class="badge bg-info text-dark">Đã xác nhận</span>',
            'processing' => '<span class="badge bg-primary">Đang chuẩn bị</span>',
            'shipped'    => '<span class="badge bg-purple text-white" style="background:#6f42c1;">Đang giao hàng</span>',
            'completed'  => '<span class="badge bg-success">Hoàn thành</span>',
            'cancelled'  => '<span class="badge bg-danger">Đã hủy</span>',
            default      => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getOrderNumberAttribute(): string
    {
        return $this->order_code ?? ('VS-' . $this->id);
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
