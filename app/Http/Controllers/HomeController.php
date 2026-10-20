<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\{Banner, Category, Post, Product};

class HomeController extends Controller
{
    public function index(): View
    {
        $base = Product::with('category')->where('is_active', true);
        return view('pages.home', [
            'heroBanners'=>Banner::where('is_active',true)->where('position','hero')->orderBy('sort_order')->get(),
            'promoBanner'=>Banner::where('is_active',true)->where('position','promo')->orderBy('sort_order')->first(),
            'sidebarBanner'=>Banner::where('is_active',true)->where('position','sidebar')->orderBy('sort_order')->first(),
            'homeCategories'=>Category::active()->orderBy('sort_order')->limit(4)->get(),
            'arrivals'=>(clone $base)->latest('id')->limit(8)->get(),
            'deals'=>(clone $base)->where('is_deal',true)->whereNotNull('sale_price')->limit(8)->get(),
            'beverages'=>(clone $base)->where(fn($q)=>$q->where('name','like','%tea%')->orWhere('name','like','%coffee%')->orWhere('name','like','%juice%'))->limit(3)->get(),
            'bestSellers'=>(clone $base)->orderByDesc('sales_count')->limit(6)->get(),
            'articles'=>Post::where('is_published',true)->latest()->limit(3)->get(),
        ]);
    }
}
