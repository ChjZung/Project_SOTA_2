<?php

namespace App\Services;

use App\Models\{Coupon, Product, Setting};
use App\Support\StorefrontProduct;
use Illuminate\Validation\ValidationException;

final class StoreCart
{
    public function quote(bool $strict = false): array
    {
        $raw = session('cart', []);
        $products = Product::with('category')->whereIn('slug', array_filter(array_column($raw, 'slug')))->get()->keyBy('slug');
        $items = []; $subtotal = 0; $quantities = [];
        foreach ($raw as $key => $line) {
            $product = $products[$line['slug'] ?? ''] ?? null;
            if (!$product && is_numeric($line['id'] ?? null)) $product = Product::find($line['id']);
            $options = $line['options'] ?? [];
            $quantity = max(1, min(99, (int) ($line['quantity'] ?? 1)));
            $price = $product ? StorefrontProduct::price($product, $options) : null;
            $unavailable = !$product || !$product->is_active || $price === null;
            if ($strict && $unavailable) $this->fail(($line['name'] ?? 'Product').' is no longer available. Please remove it from the cart.');
            if ($product) {
                $quantities[$product->id] = ($quantities[$product->id] ?? 0) + $quantity;
                if ($strict && $quantities[$product->id] > $product->stock) $this->fail($product->name.' does not have enough stock.');
            }
            $unit = (int) round(($price ?? 0) * 100);
            $items[] = ['key' => (string) $key, 'id' => (string) $key, 'product_id' => $product?->id,
                'slug' => $product?->slug ?? ($line['slug'] ?? ''), 'name' => $product?->name ?? ($line['name'] ?? 'Unavailable product'),
                'image' => $product?->image_url ?? asset('assets/images/product-placeholder.svg'),
                'price' => $unit / 100, 'formatted_price' => '$'.number_format($unit / 100, 2),
                'quantity' => $quantity, 'options' => $options, 'unavailable' => $unavailable, 'line_total' => $unit * $quantity / 100];
            $subtotal += $unit * $quantity;
        }
        $coupon = null; $discount = 0;
        if ($code = session('coupon_code')) {
            $coupon = Coupon::where('code', $code)->first();
            if (!$coupon || !$coupon->isValid($subtotal / 100)) {
                if ($strict) $this->fail('The discount code is no longer valid for this order.');
                session()->forget('coupon_code'); $coupon = null;
            } else $discount = (int) round($coupon->calculateDiscount($subtotal / 100) * 100);
        }
        $shipping = 0;
        if ($items && $subtotal - $discount < (int) round((float) Setting::get('free_shipping_min_amount', '50') * 100)) {
            $shipping = (int) round((float) Setting::get('shipping_fee_default', '5') * 100);
        }
        return ['items' => $items, 'cart_count' => array_sum(array_column($items, 'quantity')),
            'subtotal' => $subtotal / 100, 'cart_total' => $subtotal / 100, 'formatted_total' => '$'.number_format($subtotal / 100, 2),
            'coupon' => $coupon, 'discount_amount' => $discount / 100, 'shipping_fee' => $shipping / 100, 'total' => ($subtotal - $discount + $shipping) / 100];
    }

    public function add(Product $product, int $quantity, array $options): array
    {
        if (!$product->is_active || $product->stock < 1) $this->fail('This product is out of stock.');
        if (StorefrontProduct::price($product, $options) === null) $this->fail('Please select valid product options.');
        ksort($options); $cart = session('cart', []); $total = $quantity;
        foreach ($cart as $line) if (($line['slug'] ?? '') === $product->slug) $total += (int) $line['quantity'];
        if ($total > min(99, $product->stock)) $this->fail('Not enough stock for this quantity.');
        $key = 'p'.$product->id.'-'.substr(hash('sha256', json_encode($options)), 0, 12);
        $cart[$key] = ['id' => $product->id, 'slug' => $product->slug, 'name' => $product->name, 'options' => $options, 'quantity' => ($cart[$key]['quantity'] ?? 0) + $quantity];
        session(['cart' => $cart]); return $this->quote();
    }

    public function fail(string $message): never
    {
        throw ValidationException::withMessages(['cart' => $message]);
    }
}
