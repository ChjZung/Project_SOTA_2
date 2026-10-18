<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Hiển thị bảng điều khiển trung tâm quản trị.
     */
    public function index(): View
    {
        $totalRevenue = (float) Order::where('status', 'completed')->sum('total');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalPosts = Post::count();

        // Đơn hàng gần đây
        $recentOrders = Order::with('items')->latest()->take(8)->get();

        // Sản phẩm mới nhất
        $latestProducts = Product::with('category')->latest()->take(5)->get();

        // Thống kê theo trạng thái đơn hàng
        $orderStats = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'totalProducts',
            'totalCategories',
            'totalPosts',
            'recentOrders',
            'latestProducts',
            'orderStats'
        ));
    }
}
