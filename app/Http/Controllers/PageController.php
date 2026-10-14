<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function shop(): View
    {
        return view('pages.shop');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function news(): View
    {
        return view('pages.news');
    }

    public function cart(): View
    {
        return view('pages.cart');
    }

    public function myAccount(): View
    {
        return view('pages.my-account');
    }

    public function handleCategory(string $slug): View
    {
        if (view()->exists('pages.static.' . $slug)) {
            return view('pages.static.' . $slug);
        }
        return view('pages.shop');
    }

    public function handleProduct(string $slug): View
    {
        return view('pages.product-detail');
    }

    public function handleRecipe(string $slug): View
    {
        return view('pages.news');
    }

    public function handleSlug(string $slug): View
    {
        // 1. Core pages alias
        if (in_array($slug, ['about', 'about-us', 'about-store'])) {
            return $this->about();
        }
        if (in_array($slug, ['shop', 'all-products', 'store'])) {
            return $this->shop();
        }
        if (in_array($slug, ['contact', 'contact-us'])) {
            return $this->contact();
        }
        if (in_array($slug, ['news-recipes', 'news', 'blog', 'recipes'])) {
            return $this->news();
        }
        if (in_array($slug, ['cart', 'basket', 'checkout'])) {
            return $this->cart();
        }
        if (in_array($slug, ['my-account', 'account', 'profile', 'login'])) {
            return $this->myAccount();
        }
        if ($slug === 'vegan-store-home-alt') {
            return view('pages.home');
        }

        // 2. Check static view
        if (view()->exists('pages.static.' . $slug)) {
            return view('pages.static.' . $slug);
        }

        // 3. Fallback to product-detail if it looks like a product
        if (str_contains($slug, 'cake') || str_contains($slug, 'tea') || str_contains($slug, 'coffee') || 
            str_contains($slug, 'lotion') || str_contains($slug, 'bag') || str_contains($slug, 'vegan')) {
            return view('pages.product-detail');
        }

        // 4. Default fallback to shop
        return view('pages.shop');
    }
}