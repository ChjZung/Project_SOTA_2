<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Frontend Khách Hàng
|--------------------------------------------------------------------------
*/

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Các trang chính
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/shop', [PageController::class, 'shop'])->name('shop');
Route::get('/shop/page/{page}', [PageController::class, 'shop'])->whereNumber('page')->name('shop.page');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/news-recipes', [PageController::class, 'news'])->name('news');
Route::get('/my-account', [PageController::class, 'myAccount'])->name('my-account');

// Giỏ hàng (Cart)
Route::get('/cart', [PageController::class, 'cart'])->name('cart');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/mini-cart', [CartController::class, 'getMiniCart'])->name('cart.mini');

// Danh mục sản phẩm (WooCommerce Category structure & Phân trang)
Route::get('/product-category/{slug}', [PageController::class, 'handleCategory'])->name('category.show');
Route::get('/product-category/{slug}/page/{page}', [PageController::class, 'handleCategory'])->whereNumber('page')->name('category.page');
Route::get('/product-tag/{slug}', [PageController::class, 'handleCategory'])->name('tag.show');
Route::get('/product-tag/{slug}/page/{page}', [PageController::class, 'handleCategory'])->whereNumber('page')->name('tag.page');

// Chi tiết sản phẩm (Single product)
Route::get('/product/{slug}', [PageController::class, 'handleProduct'])->name('product.show');

// Bài viết & công thức (Recipes / Posts)
Route::get('/recipe/{slug}', [PageController::class, 'handleRecipe'])->name('recipe.show');

/*
|--------------------------------------------------------------------------
| Admin Routes - Hệ Thống Quản Trị Backend
|--------------------------------------------------------------------------
*/

// Auth Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Khu vực được bảo vệ bởi AdminMiddleware
    Route::middleware('admin')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // CRUD Danh mục
        Route::resource('categories', AdminCategoryController::class);

        // CRUD Sản phẩm & thao tác nhanh
        Route::resource('products', AdminProductController::class);
        Route::post('/products/{product}/toggle', [AdminProductController::class, 'toggleActive'])->name('products.toggle');

        // Quản lý Đơn hàng
        Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'destroy']);
        Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

        // CRUD Bài viết / Tin tức
        Route::resource('posts', AdminPostController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Fallback SEO Slug Resolver
|--------------------------------------------------------------------------
*/
Route::get('/{slug}', [PageController::class, 'handleSlug'])->where('slug', '^[a-zA-Z0-9\-_]+$')->name('page.slug');