<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Danh sách đánh giá sản phẩm từ khách hàng.
     */
    public function index(Request $request): View
    {
        $query = Review::with(['user', 'product']);

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->input('rating'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();
        $products = Product::orderBy('name')->get();

        $stats = [
            'pending' => Review::where('is_approved', false)->count(),
            'approved' => Review::where('is_approved', true)->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'products', 'stats'));
    }

    /**
     * Duyệt hoặc Ẩn đánh giá và tự động tính lại avg_rating của sản phẩm.
     */
    public function toggleApprove(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => !$review->is_approved]);

        // Cập nhật lại điểm đánh giá trung bình cho sản phẩm
        if ($review->product) {
            $review->product->updateAvgRating();
        }

        $statusStr = $review->is_approved ? 'Đã duyệt hiển thị' : 'Đã ẩn khỏi website';

        return back()->with('success', "Đánh giá #{$review->id}: {$statusStr} và cập nhật điểm đánh giá sản phẩm thành công!");
    }

    /**
     * Xóa đánh giá và tự động tính lại avg_rating của sản phẩm.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $product = $review->product;
        $review->delete();

        if ($product) {
            $product->updateAvgRating();
        }

        return redirect()->route('admin.reviews.index')
            ->with('success', "Đã xóa đánh giá và cập nhật lại điểm đánh giá sản phẩm!");
    }
}
