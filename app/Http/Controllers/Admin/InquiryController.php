<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    /**
     * Danh sách yêu cầu liên hệ từ khách hàng.
     */
    public function index(Request $request): View
    {
        $query = Inquiry::query();

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $inquiries = $query->latest()->paginate(15)->withQueryString();

        $statusCounts = [
            'new' => Inquiry::where('status', 'new')->count(),
            'contacted' => Inquiry::where('status', 'contacted')->count(),
            'resolved' => Inquiry::where('status', 'resolved')->count(),
        ];

        return view('admin.inquiries.index', compact('inquiries', 'statusCounts'));
    }

    /**
     * Xem chi tiết nội dung liên hệ.
     */
    public function show(Inquiry $inquiry): View
    {
        return view('admin.inquiries.show', compact('inquiry'));
    }

    /**
     * Cập nhật trạng thái và ghi chú/phản hồi của admin.
     */
    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,contacted,resolved'],
            'admin_reply' => ['nullable', 'string', 'max:2000'],
        ], [
            'status.required' => 'Vui lòng chọn trạng thái xử lý.',
            'status.in' => 'Trạng thái xử lý không hợp lệ.',
        ]);

        $inquiry->update([
            'status' => $validated['status'],
            'admin_reply' => $validated['admin_reply'] ?? $inquiry->admin_reply,
        ]);

        return back()->with('success', "Cập nhật yêu cầu liên hệ của '{$inquiry->name}' thành công!");
    }

    /**
     * Xóa yêu cầu liên hệ.
     */
    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $name = $inquiry->name;
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')
            ->with('success', "Đã xóa yêu cầu liên hệ của '{$name}' thành công!");
    }
}
