<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Danh sách tất cả đơn hàng.
     */
    public function index(Request $request): View
    {
        $query = Order::with('items.product');

        if ($request->filled('search')) {
            $search = $request->input('search');
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

        $orders = $query->latest()->paginate(12)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Chi tiết đơn hàng.
     */
    public function show(Order $order): View
    {
        $order->load('items.product', 'user');

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,completed,cancelled'],
            'payment_status' => ['required', 'in:unpaid,paid,refunded'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $order->update([
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status'],
            'customer_note' => $validated['notes'] ?? $order->customer_note,
        ]);

        return back()->with('success', "Đã cập nhật đơn hàng #{$order->order_code} thành công!");
    }

    /**
     * Xóa đơn hàng (dành cho đơn hủy/rác).
     */
    public function destroy(Order $order): RedirectResponse
    {
        $orderNumber = $order->order_code;
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', "Đã xóa đơn hàng #{$orderNumber} thành công!");
    }
}
