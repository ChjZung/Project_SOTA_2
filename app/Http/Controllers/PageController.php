<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function shop(Request $request, ?int $page = 1): View
    {
        $currentPage = (int) ($page ?? $request->input('paged', 1));
        
        $products = Product::with('category')
            ->where('is_active', true)
            ->paginate(12, ['*'], 'page', $currentPage);

        $categories = Category::withCount('products')->get();

        return view('pages.shop', compact('products', 'categories', 'currentPage'));
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function news(): View
    {
        $posts = Post::where('is_published', true)->latest()->paginate(6);
        return view('pages.news', compact('posts'));
    }

    public function cart(): View
    {
        $cart = session()->get('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_reduce($cart, fn ($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);

        return view('pages.cart', compact('cart', 'cartCount', 'cartTotal'));
    }

    public function myAccount(): View
    {
        return view('pages.my-account');
    }

    /**
     * Xử lý hiển thị danh mục sản phẩm có hỗ trợ phân trang (page 1, 2, 3...)
     */
    public function handleCategory(Request $request, string $slug, ?int $page = 1): View
    {
        $currentPage = (int) ($page ?? $request->input('paged', 1));

        $category = Category::where('slug', $slug)->first();

        if ($category) {
            $products = Product::where('category_id', $category->id)
                ->where('is_active', true)
                ->paginate(12, ['*'], 'page', $currentPage);
        } else {
            $products = Product::where('is_active', true)
                ->paginate(12, ['*'], 'page', $currentPage);
        }

        $categories = Category::withCount('products')->get();

        if (view()->exists('pages.static.' . $slug)) {
            return view('pages.static.' . $slug, compact('category', 'products', 'categories', 'currentPage', 'slug'));
        }

        return view('pages.shop', compact('category', 'products', 'categories', 'currentPage', 'slug'));
    }

    /**
     * Hiển thị chi tiết sản phẩm.
     */
    public function handleProduct(string $slug): View
    {
        $product = Product::with('category')->where('slug', $slug)->first();

        if (!$product) {
            // Thử tìm theo ID hoặc tìm sản phẩm đầu tiên làm dữ liệu mẫu nếu slug chưa có trong CSDL
            if (is_numeric($slug)) {
                $product = Product::with('category')->find((int) $slug);
            }
            if (!$product) {
                $product = Product::with('category')->first();
            }
        }

        $relatedProducts = Product::where('id', '!=', $product?->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('pages.product-detail', compact('product', 'relatedProducts'));
    }

    /**
     * Chi tiết bài viết / công thức.
     */
    public function handleRecipe(string $slug): View
    {
        $post = Post::where('slug', $slug)->first();
        return view('pages.news', compact('post'));
    }

    public function handleSlug(string $slug): View
    {
        // 1. Core pages alias
        if (in_array($slug, ['about', 'about-us', 'about-store'])) {
            return $this->about();
        }
        if (in_array($slug, ['shop', 'all-products', 'store'])) {
            return $this->shop(request());
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
        $matchedProduct = Product::where('slug', $slug)->first();
        if ($matchedProduct) {
            return $this->handleProduct($slug);
        }

        if (str_contains($slug, 'cake') || str_contains($slug, 'tea') || str_contains($slug, 'coffee') || 
            str_contains($slug, 'lotion') || str_contains($slug, 'bag') || str_contains($slug, 'vegan')) {
            return $this->handleProduct($slug);
        }

        // 4. Default fallback to shop
        return $this->shop(request());
    }
}