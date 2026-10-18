@extends('layouts.app')

@section('title', 'Cart - Avada Vegan Store')

@section('content')
<main id="main" class="clearfix width-100">
    <div class="fusion-row" style="max-width:100%;">
        <section id="content" class="full-width">
            <div id="post-11" class="post-11 page type-page status-publish hentry">
                <div class="post-content">
                    @if(empty($cart))
                        <!-- Empty Cart State -->
                        <div class="fusion-fullwidth fullwidth-box fusion-builder-row-4 fusion-flex-container has-pattern-background has-mask-background nonhundred-percent-fullwidth non-hundred-percent-height-scrolling fss-section-copy-1" style="--awb-padding-top:48px;--awb-flex-wrap:wrap;">
                            <div class="fusion-builder-row fusion-row fusion-flex-align-items-flex-start fusion-flex-content-wrap" style="max-width:1248px;margin-left: calc(-4% / 2 );margin-right: calc(-4% / 2 );">
                                <div class="fusion-layout-column fusion_builder_column fusion-builder-column-21 fusion_builder_column_1_1 1_1 fusion-flex-column" style="--awb-padding-top:64px;--awb-padding-right:80px;--awb-padding-bottom:64px;--awb-padding-left:80px;--awb-overflow:hidden;--awb-bg-size:cover;--awb-border-radius:8px;--awb-width-large:100%;">
                                    <div class="fusion-column-wrapper lazyload fusion-column-has-shadow fusion-flex-justify-content-flex-start fusion-content-layout-column fusion-column-has-bg-image" style="background-image: url(/wp-content/uploads/sites/186/2024/01/vegan-store-hero-1.jpg) !important; background-size: cover !important; background-position: center !important;">
                                        <div class="fusion-title title fusion-title-14 fusion-sep-none fusion-title-center fusion-title-text fusion-title-size-two" style="--awb-font-size:44px;">
                                            <h2 class="fusion-title-heading title-heading-center" style="margin:0;font-size:1em;color:#2d5a27;">your cart is empty.</h2>
                                        </div>
                                        <div class="fusion-text fusion-text-14" style="--awb-content-alignment:center;margin-top:12px;">
                                            <p>Add some vegan products to your cart by visiting our <a href="/shop" style="color:#44803f;font-weight:700;">Shop Page</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Cart With Items -->
                        <div class="fusion-fullwidth fullwidth-box fusion-flex-container nonhundred-percent-fullwidth" style="padding-top: 48px; padding-bottom: 80px;">
                            <div class="fusion-builder-row fusion-row" style="max-width: 1248px; margin: 0 auto; width: 100%;">
                                <div class="woocommerce" style="width: 100%;">
                                    <h2 style="font-weight: 800; color: #2d5a27; margin-bottom: 28px;">Shopping Cart</h2>

                                    <div class="row g-4">
                                        <!-- Items Table -->
                                        <div class="col-lg-8">
                                            <div style="background: #ffffff; border: 1px solid #e2ece1; border-radius: 10px; overflow: hidden; padding: 24px;">
                                                <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" style="width: 100%; border-collapse: collapse;">
                                                    <thead>
                                                        <tr style="border-bottom: 2px solid #edf2ed; text-align: left;">
                                                            <th style="padding: 12px 8px; width: 50px;">&nbsp;</th>
                                                            <th style="padding: 12px 8px; width: 70px;">&nbsp;</th>
                                                            <th style="padding: 12px 8px;">Product</th>
                                                            <th style="padding: 12px 8px;">Price</th>
                                                            <th style="padding: 12px 8px;">Quantity</th>
                                                            <th style="padding: 12px 8px; text-align: right;">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($cart as $item)
                                                            <tr style="border-bottom: 1px solid #edf2ed;">
                                                                <td style="padding: 16px 8px;">
                                                                    <form action="{{ route('cart.remove') }}" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                                        <button type="submit" style="background: none; border: none; color: #dc3545; font-size: 18px; cursor: pointer;" title="Remove this item">&times;</button>
                                                                    </form>
                                                                </td>
                                                                <td style="padding: 16px 8px;">
                                                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                                                                </td>
                                                                <td style="padding: 16px 8px;">
                                                                    <a href="/product/{{ $item['slug'] }}" style="color: #2d5a27; font-weight: 700; text-decoration: none;">
                                                                        {{ $item['name'] }}
                                                                    </a>
                                                                </td>
                                                                <td style="padding: 16px 8px; font-weight: 600;">
                                                                    {{ $item['formatted_price'] }}
                                                                </td>
                                                                <td style="padding: 16px 8px;">
                                                                    <form action="{{ route('cart.update') }}" method="POST" style="display: flex; align-items: center; gap: 6px;">
                                                                        @csrf
                                                                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="99" style="width: 65px; height: 38px; border: 1px solid #ccc; border-radius: 6px; text-align: center;">
                                                                        <button type="submit" style="background: #eef5ed; border: 1px solid #ccdacc; border-radius: 6px; padding: 7px 10px; font-size: 13px; font-weight: 600; cursor: pointer; color: #2d5a27;">Update</button>
                                                                    </form>
                                                                </td>
                                                                <td style="padding: 16px 8px; text-align: right; font-weight: 700; color: #2d5a27;">
                                                                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px;">
                                                    <a href="/shop" style="background: #ffffff; border: 1px solid #2d5a27; color: #2d5a27; font-weight: 700; padding: 12px 24px; border-radius: 6px; text-decoration: none;">&larr; Continue Shopping</a>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cart Summary -->
                                        <div class="col-lg-4">
                                            <div style="background: #f8faf8; border: 1px solid #e2ece1; border-radius: 10px; padding: 24px;">
                                                <h4 style="font-weight: 800; color: #2d5a27; margin-bottom: 20px;">Cart Totals</h4>

                                                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #edf2ed;">
                                                    <span style="color: #666;">Subtotal</span>
                                                    <strong style="color: #2d5a27;">${{ number_format((float) $cartTotal, 2) }}</strong>
                                                </div>
                                                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #edf2ed;">
                                                    <span style="color: #666;">Shipping</span>
                                                    <strong style="color: #44803f;">Free Shipping</strong>
                                                </div>
                                                <div style="display: flex; justify-content: space-between; padding: 16px 0; margin-top: 8px; font-size: 18px;">
                                                    <strong style="color: #2d5a27;">Total</strong>
                                                    <strong style="color: #2d5a27; font-size: 22px;">${{ number_format((float) $cartTotal, 2) }}</strong>
                                                </div>

                                                <button type="button" style="width: 100%; background: #2d5a27; color: #ffffff; font-weight: 700; font-size: 16px; padding: 14px; border: none; border-radius: 6px; cursor: pointer; margin-top: 16px;" onclick="alert('Cảm ơn bạn! Hệ thống đặt hàng checkout sẽ được hoàn thiện trong Giai đoạn Ngày 2 theo đúng kế hoạch.');">
                                                    Proceed to Checkout &rarr;
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</main>
@endsection
