<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Danh sách khách hàng với bộ lọc, tìm kiếm và thống kê đơn/chi tiêu.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->where('status', 'completed')->where('payment_status', 'paid');
            }], 'total');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Chi tiết thông tin khách hàng và lịch sử đơn hàng của khách.
     */
    public function show(int|string $id): View
    {
        $customer = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->where('status', 'completed')->where('payment_status', 'paid');
            }], 'total')
            ->with(['orders' => function ($q) {
                $q->latest()->with('items');
            }])
            ->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Khóa hoặc Mở khóa tài khoản khách hàng.
     */
    public function toggleStatus(int|string $id): RedirectResponse
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        $customer->update(['is_active' => !$customer->is_active]);

        $statusStr = $customer->is_active ? 'Đã mở khóa hoạt động' : 'Đã khóa tài khoản';

        return back()->with('success', "Tài khoản khách hàng '{$customer->name}': {$statusStr}!");
    }
}
