<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm với bộ lọc & phân trang.
     */
    public function index(Request $request): View
    {
        $trashedCount = Product::onlyTrashed()->count();

        if ($request->input('filter') === 'trashed') {
            $query = Product::onlyTrashed()->with('category');
        } else {
            $query = Product::with('category');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('filter') && $request->input('filter') !== 'trashed') {
            $filter = $request->input('filter');
            if ($filter === 'featured') {
                $query->where('is_featured', true);
            } elseif ($filter === 'deal') {
                $query->where('is_deal', true);
            } elseif ($filter === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            } elseif ($filter === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $products = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'trashedCount'));
    }

    /**
     * Form thêm sản phẩm mới.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Lưu sản phẩm mới.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'product_code' => ['nullable', 'string', 'max:50', 'unique:products,product_code'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'is_featured' => ['nullable', 'boolean'],
            'is_deal' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'category_id.required' => 'Vui lòng chọn danh mục cho sản phẩm.',
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            'stock.required' => 'Vui lòng nhập số lượng tồn kho.',
            'image.image' => 'Ảnh đại diện phải là định dạng hình ảnh hợp lệ.',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        // Đảm bảo slug duy nhất
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        // Tự động sinh mã sản phẩm nếu chưa có
        $productCode = $validated['product_code'] ?? 'VGN-' . strtoupper(Str::random(6));

        // Xử lý ảnh đại diện
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $imagePath = 'uploads/products/' . $filename;
        }

        // Xử lý thư viện ảnh (gallery)
        $galleryPaths = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $gFile) {
                $gFilename = time() . '_' . Str::random(5) . '.' . $gFile->getClientOriginalExtension();
                $gFile->move(public_path('uploads/products/gallery'), $gFilename);
                $galleryPaths[] = 'uploads/products/gallery/' . $gFilename;
            }
        }

        Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'product_code' => $productCode,
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'stock' => $validated['stock'],
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'gallery' => !empty($galleryPaths) ? $galleryPaths : null,
            'is_featured' => $request->boolean('is_featured'),
            'is_deal' => $request->boolean('is_deal'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Thêm sản phẩm '{$validated['name']}' thành công!");
    }

    /**
     * Form chỉnh sửa sản phẩm.
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Cập nhật thông tin sản phẩm.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $product->id],
            'product_code' => ['nullable', 'string', 'max:50', 'unique:products,product_code,' . $product->id],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'is_featured' => ['nullable', 'boolean'],
            'is_deal' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'category_id.required' => 'Vui lòng chọn danh mục cho sản phẩm.',
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            'stock.required' => 'Vui lòng nhập số lượng tồn kho.',
            'image.image' => 'Ảnh đại diện phải là hình ảnh hợp lệ.',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $imagePath = 'uploads/products/' . $filename;
        }

        // Gallery
        $galleryPaths = $product->gallery ?? [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $gFile) {
                $gFilename = time() . '_' . Str::random(5) . '.' . $gFile->getClientOriginalExtension();
                $gFile->move(public_path('uploads/products/gallery'), $gFilename);
                $galleryPaths[] = 'uploads/products/gallery/' . $gFilename;
            }
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'product_code' => $validated['product_code'] ?? $product->product_code,
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'stock' => $validated['stock'],
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'gallery' => !empty($galleryPaths) ? $galleryPaths : null,
            'is_featured' => $request->boolean('is_featured'),
            'is_deal' => $request->boolean('is_deal'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Cập nhật sản phẩm '{$product->name}' thành công!");
    }

    /**
     * Bật / tắt trạng thái hiển thị nhanh.
     */
    public function toggleActive(Product $product): RedirectResponse
    {
        $product->update(['is_active' => !$product->is_active]);
        $statusStr = $product->is_active ? 'Hiển thị' : 'Ẩn';

        return back()->with('success', "Đã chuyển trạng thái sản phẩm '{$product->name}' thành: {$statusStr}!");
    }

    /**
     * Xóa sản phẩm.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Đã chuyển sản phẩm '{$name}' vào thùng rác (xóa mềm)!");
    }

    /**
     * Khôi phục sản phẩm từ thùng rác.
     */
    public function restore(int|string $id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('admin.products.index', ['filter' => 'trashed'])
            ->with('success', "Khôi phục sản phẩm '{$product->name}' thành công!");
    }

    /**
     * Xóa vĩnh viễn sản phẩm khỏi hệ thống.
     */
    public function forceDelete(int|string $id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $name = $product->name;
        $product->forceDelete();

        return redirect()->route('admin.products.index', ['filter' => 'trashed'])
            ->with('success', "Đã xóa vĩnh viễn sản phẩm '{$name}' khỏi cơ sở dữ liệu!");
    }
}
