<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Hiển thị bảng cấu hình website.
     */
    public function index(): View
    {
        $allSettings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact('allSettings'));
    }

    /**
     * Cập nhật toàn bộ cấu hình website theo từng nhóm.
     */
    public function update(Request $request): RedirectResponse
    {
        $group = $request->input('group', 'general');

        // 1. Nhóm Cài Đặt Chung & Liên Hệ
        if ($group === 'general') {
            $validated = $request->validate([
                'site_name' => ['required', 'string', 'max:255'],
                'site_tagline' => ['nullable', 'string', 'max:255'],
                'site_footer_about' => ['nullable', 'string', 'max:1000'],
                'site_phone' => ['nullable', 'string', 'max:50'],
                'site_email' => ['nullable', 'email', 'max:255'],
                'site_address' => ['nullable', 'string', 'max:255'],
                'site_working_hours' => ['nullable', 'string', 'max:100'],
                'site_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            ]);

            foreach ($validated as $key => $val) {
                if ($key !== 'site_logo') {
                    $settingGroup = str_starts_with($key, 'site_phone') || str_starts_with($key, 'site_email') || str_starts_with($key, 'site_address') || str_starts_with($key, 'site_working_hours') ? 'contact' : 'general';
                    Setting::set($key, $val, $settingGroup);
                }
            }

            if ($request->hasFile('site_logo')) {
                $file = $request->file('site_logo');
                $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/settings'), $filename);
                Setting::set('site_logo', 'uploads/settings/' . $filename, 'general');
            }
        }

        // 2. Nhóm Mạng Xã Hội
        if ($group === 'social') {
            $validated = $request->validate([
                'social_facebook' => ['nullable', 'url', 'max:255'],
                'social_instagram' => ['nullable', 'url', 'max:255'],
                'social_twitter' => ['nullable', 'url', 'max:255'],
                'social_youtube' => ['nullable', 'url', 'max:255'],
            ]);

            foreach ($validated as $key => $val) {
                Setting::set($key, $val, 'social');
            }
        }

        // 3. Nhóm Thanh Toán & Giao Hàng
        if ($group === 'payment') {
            $validated = $request->validate([
                'bank_name' => ['required', 'string', 'max:255'],
                'bank_account_number' => ['required', 'string', 'max:50'],
                'bank_account_name' => ['required', 'string', 'max:255'],
                'shipping_fee_default' => ['required', 'numeric', 'min:0'],
                'free_shipping_min_amount' => ['required', 'numeric', 'min:0'],
            ]);

            foreach ($validated as $key => $val) {
                Setting::set($key, (string) $val, 'payment');
            }
        }

        return back()->with('success', 'Lưu cấu hình hệ thống website thành công!');
    }
}
