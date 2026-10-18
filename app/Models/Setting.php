<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Lấy giá trị cài đặt theo key (có cache 24h)
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::remember('setting_' . $key, 86400, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Lưu hoặc cập nhật cài đặt theo key (xóa cache)
     */
    public static function set(string $key, ?string $value, string $group = 'general'): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget('setting_' . $key);

        return $setting;
    }
}
