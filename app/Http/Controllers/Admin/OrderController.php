<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Danh sách tất cả đơn hàng với bộ lọc và tìm kiếm.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['items.product', 'user', 'coupon']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        $orders = $query->latest()->paginate(12)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Chi tiết đơn hàng.
     */
    public function show(Order $order): View
    {
        $order->load(['items.product', 'user', 'coupon']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng tuân thủ quy tắc Vòng Đời Đơn Hàng (State Machine).
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,shipped,completed,cancelled'],
            'payment_status' => ['nullable', 'in:unpaid,paid,refunded'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'status.required' => 'Vui lòng chọn trạng thái đơn hàng.',
            'status.in' => 'Trạng thái đơn hàng không hợp lệ.',
        ]);

        $currentStatus = $order->status;
        $targetStatus = $validated['status'];
        $newPaymentStatus = $validated['payment_status'] ?? $order->payment_status;

        // 1. Kiểm tra chuyển tiếp trạng thái
        if ($targetStatus !== $currentStatus) {
            // Nếu đã ở trạng thái kết thúc (completed hoặc cancelled) -> Không được đổi tiếp
            if (in_array($currentStatus, ['completed', 'cancelled'], true)) {
                return back()->with('error', "Đơn hàng đang ở trạng thái '{$currentStatus}' là trạng thái kết thúc, không thể thay đổi nữa.");
            }

            // Kiểm tra theo ma trận chuyển tiếp hợp lệ
            if (!$order->canTransitionTo($targetStatus)) {
                $allowed = implode(', ', Order::VALID_TRANSITIONS[$currentStatus] ?? []);
                return back()->with('error', "Không thể chuyển trạng thái từ '{$currentStatus}' sang '{$targetStatus}'. Các trạng thái tiếp theo hợp lệ: [{$allowed}].");
            }
        }

        // 2. Thực hiện cập nhật trong Transaction nguyên tử
        DB::transaction(function () use ($order, $currentStatus, $targetStatus, $newPaymentStatus, $validated) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $currentStatus = $order->status;
            if ($targetStatus !== $currentStatus && !$order->canTransitionTo($targetStatus)) {
                throw \Illuminate\Validation\ValidationException::withMessages(['status' => 'Trạng thái đơn đã thay đổi. Vui lòng tải lại trang.']);
            }
            // Khi hủy đơn hàng -> Hoàn kho + Hoàn coupon
            if ($targetStatus === 'cancelled' && $currentStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }
                }

                if ($order->coupon_id) {
                    Coupon::where('id', $order->coupon_id)
                        ->where('used_count', '>', 0)
                        ->decrement('used_count');
                }
            }

            // Khi hoàn thành đơn hàng -> Cập nhật thanh toán COD & Tăng sales_count
            if ($targetStatus === 'completed' && $currentStatus !== 'completed') {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)->increment('sales_count', $item->quantity);
                    }
                }

                // Nếu là COD hoặc chưa thanh toán, tự động chuyển sang paid khi completed
                if ($order->payment_method === 'cod' || $newPaymentStatus === 'paid') {
                    $order->payment_status = 'paid';
                    $order->paid_at = $order->paid_at ?? now();
                }
            }

            // Xử lý thời điểm thanh toán paid_at
            if ($newPaymentStatus === 'paid' && !$order->paid_at) {
                $order->paid_at = now();
            } elseif ($newPaymentStatus !== 'paid' && $targetStatus !== 'completed') {
                $order->paid_at = null;
            }

            $order->status = $targetStatus;
            $order->payment_status = ($targetStatus === 'completed' && $order->payment_method === 'cod') ? 'paid' : $newPaymentStatus;
            $order->admin_note = $validated['admin_note'] ?? $order->admin_note;
            $order->customer_note = $validated['notes'] ?? $order->customer_note;
            $order->save();
        });

        return back()->with('success', "Đã cập nhật trạng thái đơn hàng #{$order->order_code} thành công!");
    }

    /**
     * Xác nhận đã nhận tiền (Dành cho đơn chuyển khoản ngân hàng).
     */
    public function confirmPayment(Order $order): RedirectResponse
    {
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Không thể xác nhận thanh toán cho đơn đã hủy.');
        }
        if ($order->payment_status === 'paid') {
            return back()->with('info', "Đơn hàng #{$order->order_code} đã được thanh toán trước đó.");
        }

        $order->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', "Đã xác nhận nhận tiền thành công cho đơn hàng #{$order->order_code}!");
    }

    /**
     * In hóa đơn bán hàng (Print-friendly format).
     */
    public function print(Order $order): View
    {
        $order->load(['items.product', 'user', 'coupon']);

        return view('admin.orders.print', compact('order'));
    }

    /**
     * Xóa đơn hàng (dành cho đơn rác/hủy).
     */
    public function destroy(Order $order): RedirectResponse
    {
        if ($order->status !== 'cancelled') {
            return back()->with('error', 'Chỉ xóa đơn đã hủy. Hãy hủy đơn trước để hệ thống hoàn kho và mã giảm giá.');
        }
        $orderNumber = $order->order_code;
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', "Đã xóa đơn hàng #{$orderNumber} thành công!");
    }
}
