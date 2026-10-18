<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'status',
        'admin_reply',
    ];

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'new'        => '<span class="badge bg-danger">Mới</span>',
            'contacted'  => '<span class="badge bg-warning text-dark">Đã liên hệ</span>',
            'resolved'   => '<span class="badge bg-success">Đã giải quyết</span>',
            default      => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}