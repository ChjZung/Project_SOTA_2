@extends('layouts.app')
@section('title', $item['name'].' - Avada Vegan Store')
@section('description', $item['summary'] ?? $item['name'])
@push('styles')<link rel="stylesheet" href="{{ asset('assets/css/product-detail.css') }}">@endpush
@section('content')
@php
    $prices = array_column($item['variations'], 'price');
    $minimum = count($prices) ? min($prices) : ($item['sale_price'] ?? $item['price']);
    $maximum = count($prices) ? max($prices) : $minimum;
    $discount = $item['sale_price'] && $item['price'] > 0 ? round(100 * (1 - $item['sale_price'] / $item['price'])) : 0;
    $isClothing = $item['category_slug'] === 'clothes-accessories';
@endphp
<main id="main" class="product-detail-page" data-product-slug="{{ $item['slug'] }}">
    <section class="product-detail-top product-container">
        <div class="product-gallery">
            @if(count($item['gallery']))<div class="product-thumbnails">@foreach(array_unique(array_merge([$item['image']], $item['gallery'])) as $image)<button type="button" data-gallery-image="{{ asset($image) }}" aria-label="View {{ $item['name'] }} image {{ $loop->iteration }}"><img src="{{ asset($image) }}" alt="{{ $item['name'] }}" width="72" height="72"></button>@endforeach</div>@endif
            <a class="product-main-image" href="{{ asset($item['image']) }}" target="_blank" rel="noopener" aria-label="View larger image of {{ $item['name'] }}">
                @if($discount)<span class="product-sale-badge">Sale!</span>@endif
                <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" width="800" height="600" fetchpriority="high">
            </a>
            @if($item['slug'] === 'fresh-organic-cake')<img class="product-organic-stamp" src="{{ asset('wp-content/uploads/sites/186/2024/01/organic-stamp-2.png') }}" alt="Organic Food" width="120" height="116">@endif
        </div>
        <div class="product-summary">
            <p class="product-intro">{{ $item['summary'] }}</p>
            <h1>{{ $item['name'] }}</h1>
            <a class="product-rating" href="#reviews"><span aria-label="{{ number_format($averageRating, 1) }} out of 5 stars">{{ $averageRating ? str_repeat('★', (int) round($averageRating)).str_repeat('☆', 5 - (int) round($averageRating)) : '☆☆☆☆☆' }}</span> <small>({{ count($reviews) }} {{ count($reviews) === 1 ? 'review' : 'reviews' }})</small></a>
            <hr>
            <p>{{ $item['summary'] }}</p>
            <p class="product-price"><strong>${{ number_format($minimum, 2) }}@if($maximum > $minimum) – ${{ number_format($maximum, 2) }}@endif</strong>@if($discount) <del>${{ number_format($item['price'], 2) }}</del><span class="product-discount">{{ $discount }}% Off</span>@endif</p>
            @include('products.purchase-form')
            <div class="product-meta"><p>Categories: <a href="/product-category/{{ $item['category_slug'] }}">{{ $item['category'] }}</a></p><p>SKU: {{ $item['sku'] }}</p></div>
            <div class="product-summary-actions"><a href="#description">Product Description ↓</a><a href="#reviews">Reviews ↓</a></div>
        </div>
    </section>
    <div class="product-guarantees"><div class="product-container"><span><b>✓</b> Money Back Guarantee</span><span><b>✓</b> Top Quality Vegan Item</span><span><b>✓</b> Fast & Secure Checkout</span></div></div>
    <section id="description" class="product-description-section"><div class="product-reading-width">
        <h2>Product Description</h2><p>{{ $item['description'] }}</p>
        <h3>{{ $isClothing ? 'Style & Product Details' : 'About This Vegan Product' }}</h3>
        <p>{{ $item['summary'] }}@if(count($item['variations'])) Select the available options above to find your preferred combination and its price.@endif</p>
        <ul class="product-benefits"><li>Part of our {{ $item['category'] }} collection</li><li>{{ count($item['variations']) ? 'Available in '.count($item['variations']).' product options' : 'A simple, ready-to-select product' }}</li><li>View your selected items and quantities in the shopping cart</li></ul>
        <div class="product-detail-icons">
            <div><span>@include('products.detail-icon', ['type' => 'leaf'])</span><p>Vegan Collection</p></div><div><span>@include('products.detail-icon', ['type' => 'options'])</span><p>Product Options</p></div><div><span>@include('products.detail-icon', ['type' => 'label'])</span><p>{{ $isClothing ? 'Materials & Care' : 'Label Information' }}</p></div><div><span>@include('products.detail-icon', ['type' => 'storage'])</span><p>Care & Storage</p></div>
        </div>
        <div class="product-accordions">
            <details><summary>Guidelines For Using This Product</summary><p>{{ $isClothing ? 'Choose your preferred size or color before ordering. Follow the garment or accessory label for washing, drying and storage.' : 'Refer to the product label for preparation, serving, application or feeding instructions appropriate to this item.' }}</p></details>
            <details><summary>{{ $isClothing ? 'Materials & Care Information' : 'Full Ingredients List' }}</summary><p>{{ $isClothing ? 'Check the attached product label for the complete material composition and care instructions.' : 'The complete ingredients and allergen information are provided on the product packaging. Contact the store if you need this information before ordering.' }}</p><a href="/contact">Ask about {{ $item['name'] }}</a></details>
            <details><summary>Product Shipping, Delivery & Returns</summary><p>Contact the store to confirm delivery availability, shipping charges and return eligibility for this product.</p><a href="/contact">Contact Vegan Store</a></details>
        </div>
        <h3>Additional Information</h3><dl class="product-information"><div><dt>Product</dt><dd>{{ $item['name'] }}</dd></div><div><dt>Category</dt><dd>{{ $item['category'] }}</dd></div><div><dt>SKU</dt><dd>{{ $item['sku'] }}</dd></div>@foreach($item['variations'] as $variation)<div><dt>Option</dt><dd>{{ implode(' / ', array_map('ucfirst', array_values($variation['attributes']))) }} — ${{ number_format($variation['price'], 2) }}</dd></div>@endforeach</dl>
    </div></section>
    @include('products.reviews')
    <section class="product-related"><div class="product-container"><h2>Related Vegan Products</h2><div class="product-related-grid">@foreach($related as $relatedItem)<article><a href="/product/{{ $relatedItem['slug'] }}"><img src="{{ asset($relatedItem['image']) }}" alt="{{ $relatedItem['name'] }}" width="120" height="90"></a><div><a class="product-related-category" href="/product-category/{{ $relatedItem['category_slug'] }}">{{ $relatedItem['category'] }}</a><h3><a href="/product/{{ $relatedItem['slug'] }}">{{ $relatedItem['name'] }}</a></h3><p>${{ number_format($relatedItem['sale_price'] ?? $relatedItem['price'], 2) }}</p></div></article>@endforeach</div></div></section>
</main>
@endsection
