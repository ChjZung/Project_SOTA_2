<?php
namespace App\Http\Controllers;
use App\Models\{Coupon, Product};
use App\Services\StoreCart;
use Illuminate\Http\Request;
class CartController extends Controller
{
    public function add(Request $request, StoreCart $cart)
    {
        $data=$request->validate(['quantity'=>'sometimes|integer|min:1|max:99','slug'=>'nullable|string|max:150','options'=>'sometimes|array','options.*'=>'string|max:80']);
        $product=$request->filled('slug')?Product::where('slug',$data['slug'])->firstOrFail():Product::findOrFail($request->input('product_id',$request->input('add-to-cart')));
        $quote=$cart->add($product,(int)($data['quantity']??1),$data['options']??[]);
        return $request->expectsJson()?response()->json(['success'=>true]+$quote):back()->with('success','Product added to cart.');
    }
    public function update(Request $request, StoreCart $service)
    {
        $data=$request->validate(['product_id'=>'required','quantity'=>'required|integer|min:0|max:99']);$cart=session('cart',[]);$key=(string)$data['product_id'];
        if(isset($cart[$key])){if((int)$data['quantity']===0)unset($cart[$key]);else $cart[$key]['quantity']=(int)$data['quantity'];$before=session('cart',[]);session(['cart'=>$cart]);try{$service->quote(true);}catch(\Illuminate\Validation\ValidationException $e){session(['cart'=>$before]);throw $e;}}
        return $request->expectsJson()?response()->json(['success'=>true]+$service->quote()):redirect()->route('cart');
    }
    public function remove(Request $request, StoreCart $service)
    {
        $cart=session('cart',[]);unset($cart[(string)$request->input('product_id')]);session(['cart'=>$cart]);return $request->expectsJson()?response()->json(['success'=>true]+$service->quote()):redirect()->route('cart');
    }
    public function getMiniCart(StoreCart $service){return response()->json(['success'=>true]+$service->quote());}
    public function applyCoupon(Request $request,StoreCart $service)
    {
        $data=$request->validate(['code'=>'required|string|max:60']);$quote=$service->quote();$coupon=Coupon::whereRaw('UPPER(code) = ?',[strtoupper(trim($data['code']))])->first();
        if(!$quote['items']||!$coupon||!$coupon->isValid($quote['subtotal']))return back()->withErrors(['coupon'=>'This code is invalid, expired, or the minimum order has not been reached.']);
        session(['coupon_code'=>$coupon->code]);return back()->with('success','Discount code applied.');
    }
    public function removeCoupon(){session()->forget('coupon_code');return back()->with('success','Discount code removed.');}
}
