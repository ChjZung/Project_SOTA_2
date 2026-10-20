<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\{AccountController, CustomerAuthController, CheckoutController};
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
Route::get('/search', [PageController::class, 'shop'])->name('search');
Route::get('/shop/page/{page}', [PageController::class, 'shop'])->whereNumber('page')->name('shop.page');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->middleware('throttle:5,1')->name('contact.send');
Route::get('/news-recipes', [PageController::class, 'news'])->name('news');
Route::get('/category/{slug}', [PageController::class, 'news'])->name('news.category');
Route::middleware('guest')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
    Route::get('/register', [CustomerAuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->middleware('throttle:5,1')->name('register.submit');
});
Route::middleware('auth')->group(function () {
    Route::get('/my-account', [AccountController::class, 'index'])->name('my-account');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    Route::put('/my-account/profile', [AccountController::class, 'profile'])->name('account.profile');
    Route::put('/my-account/password', [AccountController::class, 'password'])->name('account.password');
    Route::get('/my-account/orders/{order_code}', [AccountController::class, 'order'])->name('account.order');
    Route::post('/product/{slug}/reviews', [AccountController::class, 'review'])->name('product.review');
});
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'place'])->middleware('throttle:10,1')->name('checkout.place');
Route::get('/order-success/{order_code}', [CheckoutController::class, 'success'])->name('order.success');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

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
| Admin Routes - Hệ Thống Quản Trị Backend (Giai Đoạn 2 & 3)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Auth Admin
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Khu vực được bảo vệ bởi AdminMiddleware
    Route::middleware('admin')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Hồ sơ quản trị viên & Đổi mật khẩu
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');

        // CRUD Danh mục
        Route::resource('categories', AdminCategoryController::class);

        // CRUD Sản phẩm & Thùng rác (Soft Delete)
        Route::post('/products/{id}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
        Route::delete('/products/{id}/force', [AdminProductController::class, 'forceDelete'])->name('products.force-delete');
        Route::resource('products', AdminProductController::class);
        Route::post('/products/{product}/toggle', [AdminProductController::class, 'toggleActive'])->name('products.toggle');

        // Quản lý Đơn hàng (Vòng đời State Machine, In hóa đơn & Xác nhận TT)
        Route::post('/orders/{order}/confirm-payment', [AdminOrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
        Route::get('/orders/{order}/print', [AdminOrderController::class, 'print'])->name('orders.print');
        Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'destroy']);
        Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

        // CRUD Bài viết / Tin tức
        Route::resource('posts', AdminPostController::class);

        // Quản lý Khách hàng
        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');
        Route::post('/customers/{customer}/toggle', [AdminCustomerController::class, 'toggleStatus'])->name('customers.toggle');

        // Quản lý Yêu cầu Liên hệ
        Route::get('/inquiries', [AdminInquiryController::class, 'index'])->name('inquiries.index');
        Route::get('/inquiries/{inquiry}', [AdminInquiryController::class, 'show'])->name('inquiries.show');
        Route::put('/inquiries/{inquiry}', [AdminInquiryController::class, 'update'])->name('inquiries.update');
        Route::delete('/inquiries/{inquiry}', [AdminInquiryController::class, 'destroy'])->name('inquiries.destroy');

        // Quản lý Banner & Slider
        Route::resource('banners', AdminBannerController::class);
        Route::post('/banners/{banner}/toggle', [AdminBannerController::class, 'toggle'])->name('banners.toggle');

        // Quản lý Mã giảm giá (Coupons)
        Route::resource('coupons', AdminCouponController::class);
        Route::post('/coupons/{coupon}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');

        // Quản lý Đánh giá sản phẩm (Reviews)
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/toggle', [AdminReviewController::class, 'toggleApprove'])->name('reviews.toggle');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        // Cài đặt hệ thống (Settings)
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});

/*
|--------------------------------------------------------------------------
| Fallback SEO Slug Resolver
|--------------------------------------------------------------------------
*/
Route::get('/{slug}', [PageController::class, 'handleSlug'])->where('slug', '^[a-zA-Z0-9\-_]+$')->name('page.slug');
