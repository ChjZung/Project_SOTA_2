<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Các trang chính
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/shop', [PageController::class, 'shop'])->name('shop');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/news-recipes', [PageController::class, 'news'])->name('news');
Route::get('/cart', [PageController::class, 'cart'])->name('cart');
Route::get('/my-account', [PageController::class, 'myAccount'])->name('my-account');

// Danh mục sản phẩm (WooCommerce Category structure)
Route::get('/product-category/{slug}', [PageController::class, 'handleCategory'])->name('category.show');
Route::get('/product-tag/{slug}', [PageController::class, 'handleCategory'])->name('tag.show');

// Chi tiết sản phẩm (Single product)
Route::get('/product/{slug}', [PageController::class, 'handleProduct'])->name('product.show');

// Bài viết & công thức (Recipes / Posts)
Route::get('/recipe/{slug}', [PageController::class, 'handleRecipe'])->name('recipe.show');

// Root slug fallback (Chống 404 cho mọi liên kết chuẩn SEO)
Route::get('/{slug}', [PageController::class, 'handleSlug'])->where('slug', '^[a-zA-Z0-9\-_]+$')->name('page.slug');