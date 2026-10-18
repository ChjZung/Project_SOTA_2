<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Thêm sản phẩm vào giỏ hàng (hỗ trợ cả AJAX và Form Submit).
     */
    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $productId = (int) ($request->input('product_id') ?? $request->input('add-to-cart'));
        $quantity = max(1, (int) $request->input('quantity', 1));

        if (!$productId) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm.'], 400);
            }
            return back()->with('error', 'Không tìm thấy sản phẩm.');
        }

        $product = Product::find($productId);

        if (!$product) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại.'], 404);
            }
            return back()->with('error', 'Sản phẩm không tồn tại.');
        }

        $cart = session()->get('cart', []);

        $price = (float) $product->final_price;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $price,
                'formatted_price' => '$' . number_format($price, 2),
                'image' => $product->image_url,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_reduce($cart, fn ($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product has been added to your cart.',
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal,
                'formatted_total' => '$' . number_format($cartTotal, 2),
                'cart' => $cart,
            ]);
        }

        return back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng.');
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng.
     */
    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $productId = (int) $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId]['quantity'] = $quantity;
            }
            session()->put('cart', $cart);
        }

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_reduce($cart, fn ($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal,
                'formatted_total' => '$' . number_format($cartTotal, 2),
            ]);
        }

        return redirect()->route('cart')->with('success', 'Đã cập nhật giỏ hàng.');
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng.
     */
    public function remove(Request $request): JsonResponse|RedirectResponse
    {
        $productId = (int) $request->input('product_id');

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_reduce($cart, fn ($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal,
                'formatted_total' => '$' . number_format($cartTotal, 2),
            ]);
        }

        return redirect()->route('cart')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    /**
     * Lấy dữ liệu giỏ hàng hiện tại (Mini-cart AJAX).
     */
    public function getMiniCart(): JsonResponse
    {
        $cart = session()->get('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_reduce($cart, fn ($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'formatted_total' => '$' . number_format($cartTotal, 2),
            'items' => array_values($cart),
        ]);
    }
}
