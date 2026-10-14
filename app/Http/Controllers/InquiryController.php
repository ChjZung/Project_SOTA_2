<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\InquiryStoreRequest;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;

class InquiryController extends Controller
{
    /**
     * Lưu yêu cầu báo giá từ khách hàng
     * Dữ liệu đã được validate sạch bởi InquiryStoreRequest
     * CSRF Token tự động được Laravel kiểm tra (chống Cross-Site Request Forgery)
     */
    public function store(InquiryStoreRequest $request): RedirectResponse
    {
        Inquiry::create($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Yêu cầu báo giá đã được gửi thành công! Nhị Bình Plastic sẽ liên hệ lại trong 24h.');
    }
}