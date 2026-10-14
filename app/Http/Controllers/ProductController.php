<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm (có thể lọc theo danh mục)
     */
    public function index(?string $categorySlug = null): View
    {
        $query = Product::active()->with('category')->latest();

        $currentCategory = null;

        if ($categorySlug) {
            $currentCategory = Category::where('slug', $categorySlug)->firstOrFail();
            $query->where('category_id', $currentCategory->id);
        }

        $products = $query->paginate(12);
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();

        return view('pages.products', compact('products', 'categories', 'currentCategory'));
    }

    /**
     * Chi tiết 1 sản phẩm
     */
    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)->active()->with('category')->firstOrFail();

        // Sản phẩm liên quan cùng danh mục
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('pages.product-detail', compact('product', 'relatedProducts'));
    }
}