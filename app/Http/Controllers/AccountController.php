<?php

namespace App\Http\Controllers;

use App\Models\{Order, Product, Review};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        return view('pages.account', ['orders' => auth()->user()->orders()->latest()->paginate(8)]);
    }
    public function profile(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:120', 'phone' => 'nullable|string|max:30', 'address' => 'nullable|string|max:1000']);
        $request->user()->update($data); return back()->with('success', 'Profile updated.');
    }
    public function password(Request $request)
    {
        $data = $request->validate(['current_password' => 'required|current_password', 'password' => 'required|string|min:6|confirmed']);
        $request->user()->update(['password' => $data['password']]); return back()->with('success', 'Password updated.');
    }
    public function order(string $order_code)
    {
        $order = auth()->user()->orders()->with('items')->where('order_code', $order_code)->firstOrFail();
        return view('pages.order-success', compact('order'));
    }
    public function review(Request $request, string $slug)
    {
        $product = Product::active()->where('slug', $slug)->firstOrFail();
        $data = $request->validate(['rating' => 'required|integer|min:1|max:5', 'comment' => 'required|string|min:10|max:2000']);
        Review::updateOrCreate(['user_id' => $request->user()->id, 'product_id' => $product->id], $data + ['is_approved' => false]);
        $product->updateAvgRating();
        return redirect()->route('product.show', $slug)->withFragment('reviews')->with('review_saved', 'Your review was saved and is waiting for store approval.');
    }
}
