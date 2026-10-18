<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Hiển thị bảng điều khiển trung tâm quản trị.
     */
    public function index(): View
    {
        // 1. Thẻ Doanh thu: Chỉ tính đơn completed VÀ payment_status = paid
        $totalRevenue = (float) Order::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->sum('total');

        // 2. Thẻ Đơn hàng mới: Trạng thái pending
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalOrders = Order::count();

        // 3. Thẻ Sản phẩm trong kho
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalPosts = Post::count();

        // 4. Thẻ Khách hàng
        $totalCustomers = User::where('role', 'customer')->count();

        // 5 đơn hàng mới nhất
        $recentOrders = Order::with('items')->latest()->take(5)->get();

        // 5 sản phẩm sắp hết hàng (stock < 10)
        $lowStockProducts = Product::with('category')
            ->where('stock', '<', 10)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // Thống kê theo trạng thái đơn hàng đầy đủ theo sơ đồ vòng đời
        $orderStats = [
            'pending'    => Order::where('status', 'pending')->count(),
            'confirmed'  => Order::where('status', 'confirmed')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped'    => Order::where('status', 'shipped')->count(),
            'completed'  => Order::where('status', 'completed')->count(),
            'cancelled'  => Order::where('status', 'cancelled')->count(),
        ];

        // Biểu đồ doanh thu 7 ngày gần nhất (Chart.js)
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $chartLabels[] = $date->format('d/m');

            $dailyRevenue = (float) Order::where('status', 'completed')
                ->where('payment_status', 'paid')
                ->whereDate('paid_at', $dateStr)
                ->sum('total');

            // Dự phòng nếu paid_at null thì dùng created_at
            if ($dailyRevenue <= 0) {
                $dailyRevenue = (float) Order::where('status', 'completed')
                    ->where('payment_status', 'paid')
                    ->whereNull('paid_at')
                    ->whereDate('created_at', $dateStr)
                    ->sum('total');
            }

            $chartData[] = round($dailyRevenue, 2);
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'totalProducts',
            'totalCategories',
            'totalPosts',
            'totalCustomers',
            'recentOrders',
            'lowStockProducts',
            'orderStats',
            'chartLabels',
            'chartData'
        ));
    }
}
