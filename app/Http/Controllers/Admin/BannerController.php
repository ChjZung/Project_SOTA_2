<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BannerController extends Controller
{
    /**
     * Danh sách banner quảng cáo & hero slider.
     */
    public function index(Request $request): View
    {
        $query = Banner::query();

        if ($request->filled('position')) {
            $query->where('position', $request->input('position'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }

        $banners = $query->orderBy('position')->orderBy('sort_order', 'asc')->paginate(12)->withQueryString();

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Form thêm banner mới.
     */
    public function create(): View
    {
        return view('admin.banners.create');
    }

    /**
     * Lưu banner mới.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'in:hero,promo,sidebar'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề banner.',
            'position.required' => 'Vui lòng chọn vị trí hiển thị banner.',
            'image.image' => 'Tệp tải lên phải là định dạng hình ảnh hợp lệ.',
        ]);

        $imagePath = $validated['image_url'] ?? null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'banner_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $filename);
            $imagePath = 'uploads/banners/' . $filename;
        }

        Banner::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'position' => $validated['position'],
            'image' => $imagePath,
            'button_text' => $validated['button_text'] ?? null,
            'button_link' => $validated['button_link'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', "Thêm banner '{$validated['title']}' thành công!");
    }

    /**
     * Form sửa banner.
     */
    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Cập nhật thông tin banner.
     */
    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'in:hero,promo,sidebar'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề banner.',
            'position.required' => 'Vui lòng chọn vị trí hiển thị banner.',
        ]);

        $imagePath = $banner->image;

        if ($request->filled('image_url')) {
            $imagePath = $request->input('image_url');
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'banner_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $filename);
            $imagePath = 'uploads/banners/' . $filename;
        }

        $banner->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'position' => $validated['position'],
            'image' => $imagePath,
            'button_text' => $validated['button_text'] ?? null,
            'button_link' => $validated['button_link'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', "Cập nhật banner '{$banner->title}' thành công!");
    }

    /**
     * Bật / tắt hiển thị banner nhanh.
     */
    public function toggle(Banner $banner): RedirectResponse
    {
        $banner->update(['is_active' => !$banner->is_active]);
        $statusStr = $banner->is_active ? 'Hiển thị' : 'Ẩn';

        return back()->with('success', "Banner '{$banner->title}': {$statusStr}!");
    }

    /**
     * Xóa banner.
     */
    public function destroy(Banner $banner): RedirectResponse
    {
        $title = $banner->title;
        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', "Đã xóa banner '{$title}' thành công!");
    }
}
