<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Setting;
use App\Policies\OrderPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Đăng ký Policy phân quyền đơn hàng
        Gate::policy(Order::class, OrderPolicy::class);

        // View Composers chia sẻ cấu hình store & danh mục toàn cục
        View::composer([
            'components.header',
            'components.footer',
            'components.storefront-drawers',
            'pages.checkout',
            'pages.order-success',
            'pages.contact'
        ], function ($view) {
            $view->with('storeSettings', Setting::pluck('value', 'key')->all());
            $view->with('storeCategories', Category::orderBy('sort_order')->get());
        });
    }
}
