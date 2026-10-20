<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\{Category, Inquiry, Post, Product};
use App\Services\StoreCart;
use App\Support\StorefrontProduct;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View { return view('pages.about'); }
    public function contact(): View { return view('pages.contact'); }
    public function sendContact(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:100','email'=>'required|email|max:180','phone'=>'required|string|max:30','subject'=>'required|string|max:200','message'=>'required|string|min:10|max:5000']);
        Inquiry::create($data + ['status'=>'new']);
        return back()->with('success', 'Thank you! Your message has been sent to the store.');
    }
    public function shop(Request $request, ?int $page = null): View
    {
        $filters = $request->validate(['q'=>'nullable|string|max:150','s'=>'nullable|string|max:150','category'=>'nullable|string|max:100','min_price'=>'nullable|numeric|min:0','max_price'=>'nullable|numeric|min:0','sort'=>'nullable|in:newest,price_asc,price_desc,bestselling']);
        $term = trim($filters['q'] ?? $filters['s'] ?? '');
        $category = $filters['category'] ?? $request->route('slug');
        $query = Product::with('category')->where('is_active', true);
        if ($term !== '') $query->where(fn($q) => $q->where('name','like', '%'.$term.'%')->orWhere('description','like','%'.$term.'%')->orWhere('summary','like','%'.$term.'%'));
        if ($category) $query->whereHas('category', fn($q) => $q->where('slug',$category));
        if (isset($filters['min_price'])) $query->whereRaw('COALESCE(sale_price, price) >= ?', [$filters['min_price']]);
        if (isset($filters['max_price'])) $query->whereRaw('COALESCE(sale_price, price) <= ?', [$filters['max_price']]);
        match ($filters['sort'] ?? 'newest') {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) DESC'),
            'bestselling' => $query->orderByDesc('sales_count'),
            default => $query->latest('id'),
        };
        $products = $query->orderBy('id')->paginate(12, ['*'], 'page', $page)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();
        return view('pages.catalog', compact('products','categories','term','category'));
    }
    public function handleCategory(Request $request, string $slug, ?int $page = null): View
    {
        $normalized = match ($slug) {
            'vegan-food-drinks' => 'food-drinks',
            'vegan-grocery-items' => 'grocery',
            'vegan-diet-nutrition' => 'fitness-nutrition',
            'vegan-beauty-care' => 'beauty-care',
            default => $slug,
        };
        Category::where('slug', $normalized)->firstOrFail();
        $request->route()->setParameter('slug', $normalized);
        return $this->shop($request, $page);
    }
    public function cart(StoreCart $cart): View { return view('pages.cart', ['quote'=>$cart->quote()]); }
    public function handleProduct(string $slug): View
    {
        $product = Product::with('category')->where('is_active',true)->where('slug',$slug)->firstOrFail();
        $item = StorefrontProduct::data($product);
        $related = Product::with('category')->where('is_active',true)->where('category_id',$product->category_id)->whereKeyNot($product->id)->limit(3)->get()->map(fn($p)=>StorefrontProduct::data($p));
        $reviews = $product->reviews()->where('is_approved',true)->with('user')->latest()->get()->map(fn($r)=>['name'=>$r->user?->name ?? 'Customer','date'=>$r->created_at->format('M d, Y'),'rating'=>(int)$r->rating,'comment'=>$r->comment])->all();
        $averageRating = count($reviews) ? array_sum(array_column($reviews,'rating')) / count($reviews) : 0;
        return view('pages.storefront-product',compact('item','related','reviews','averageRating','product'));
    }
    public function news(Request $request): View
    {
        $posts = Post::where('is_published',true)->when($request->route('slug'),fn($q,$slug)=>$q->where('category',$slug))->latest()->paginate(6);
        return view('pages.storefront-news',compact('posts'));
    }
    public function handleRecipe(string $slug): View
    {
        $post = Post::where('is_published',true)->where('slug',$slug)->firstOrFail();
        return view('pages.article',compact('post'));
    }

    public function sitemap(): \Illuminate\Http\Response
    {
        $products = Product::where('is_active', true)->select('slug', 'updated_at')->get();
        $categories = Category::where('is_active', true)->select('slug', 'updated_at')->get();
        $posts = Post::where('is_published', true)->select('slug', 'updated_at')->get();

        $content = view('pages.sitemap', compact('products', 'categories', 'posts'))->render();

        return response($content, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }
    public function handleSlug(string $slug)
    {
        if (in_array($slug,['about-us','about-store'])) return $this->about();
        if (in_array($slug,['all-products','store'])) return $this->shop(request());
        if ($slug==='contact-us') return $this->contact();
        if (in_array($slug,['news','blog','recipes'])) return $this->news(request());
        if (in_array($slug,['account','profile'])) return redirect()->route('my-account');
        if ($slug==='vegan-store-home-alt') return redirect()->route('home');
        if (Post::where('slug',$slug)->where('is_published',true)->exists()) return $this->handleRecipe($slug);
        if (Product::where('slug',$slug)->where('is_active',true)->exists()) return $this->handleProduct($slug);
        if (view()->exists('pages.static.'.$slug)) return view('pages.static.'.$slug);
        abort(404);
    }
}
