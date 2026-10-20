<?php

namespace App\Http\Controllers;

use App\Models\{Coupon, Order, Product, Setting};
use App\Services\StoreCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(StoreCart $cart)
    {
        try { $quote = $cart->quote(true); }
        catch (\Illuminate\Validation\ValidationException $e) { return redirect()->route('cart')->withErrors($e->errors()); }
        if (!$quote['items']) return redirect()->route('cart');
        if (!session('checkout_token')) session(['checkout_token' => (string) Str::uuid()]);
        return view('pages.checkout', ['quote' => $quote, 'token' => session('checkout_token')]);
    }

    public function place(Request $request, StoreCart $cart)
    {
        $data = $request->validate([
            'idempotency_token' => 'required|uuid', 'customer_name' => 'required|string|max:120',
            'customer_email' => 'required|email|max:180', 'customer_phone' => 'required|string|max:30',
            'shipping_address' => 'required|string|min:10|max:1000', 'customer_note' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cod,bank_transfer',
        ]);
        $existing = Order::where('idempotency_token', $data['idempotency_token'])->first();
        if ($existing && $this->canView($existing)) return redirect()->route('order.success', $existing->order_code);
        abort_unless(hash_equals((string) session('checkout_token', ''), $data['idempotency_token']), 419);

        try {
            $order = DB::transaction(function () use ($data, $cart) {
                $slugs = array_column(session('cart', []), 'slug');
                Product::whereIn('slug', $slugs)->orderBy('id')->lockForUpdate()->get();
                if ($code = session('coupon_code')) Coupon::where('code', $code)->lockForUpdate()->first();
                $existing = Order::where('idempotency_token', $data['idempotency_token'])->first();
                if ($existing) return $existing;
                $quote = $cart->quote(true);
                if (!$quote['items']) $cart->fail('Your shopping cart is empty.');
                $order = Order::create([
                    'user_id' => auth()->id(), 'order_code' => Order::generateOrderCode(),
                    'idempotency_token' => $data['idempotency_token'],
                    'customer_name' => $data['customer_name'], 'customer_email' => $data['customer_email'],
                    'customer_phone' => $data['customer_phone'], 'shipping_address' => $data['shipping_address'],
                    'customer_note' => $data['customer_note'] ?? null,
                    'subtotal' => $quote['subtotal'], 'coupon_id' => $quote['coupon']?->id,
                    'coupon_code' => $quote['coupon']?->code, 'discount_amount' => $quote['discount_amount'],
                    'shipping_fee' => $quote['shipping_fee'], 'total' => $quote['total'],
                    'payment_method' => $data['payment_method'], 'payment_status' => 'unpaid', 'status' => 'pending',
                ]);
                foreach ($quote['items'] as $line) {
                    $changed = Product::where('id', $line['product_id'])->where('stock', '>=', $line['quantity'])->decrement('stock', $line['quantity']);
                    if (!$changed) $cart->fail($line['name'].' is out of stock.');
                    $order->items()->create([
                        'product_id' => $line['product_id'], 'product_name' => $line['name'], 'product_image' => $line['image'],
                        'price' => $line['price'], 'quantity' => $line['quantity'], 'total' => $line['line_total'], 'options' => $line['options'],
                    ]);
                }
                if ($quote['coupon']) $quote['coupon']->increment('used_count');
                return $order;
            }, 3);
        } catch (\Illuminate\Database\QueryException $e) {
            $order = Order::where('idempotency_token', $data['idempotency_token'])->first();
            if (!$order) throw $e;
        }
        session(['order_access.'.$order->order_code => true]);
        session()->forget(['cart', 'coupon_code', 'checkout_token']);
        return redirect()->route('order.success', $order->order_code);
    }

    public function success(string $order_code)
    {
        $order = Order::with('items')->where('order_code', $order_code)->firstOrFail();
        abort_unless($this->canView($order), 404);
        return view('pages.order-success', compact('order'));
    }

    private function canView(Order $order): bool
    {
        return (auth()->check() && $order->user_id === auth()->id()) || session('order_access.'.$order->order_code, false);
    }
}
