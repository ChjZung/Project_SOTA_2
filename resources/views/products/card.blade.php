@php($item=\App\Support\StorefrontProduct::data($product))
<article class="store-product db-product-card">
    <a class="db-product-photo" href="{{ route('product.show',$product->slug) }}"><img src="{{ $product->image_url }}" alt="{{ $product->name }}" width="400" height="320" loading="lazy">@if($product->sale_price !== null)<span class="db-sale">Sale!</span>@endif</a>
    <div class="db-product-info">@if($product->category)<a class="db-product-category" href="{{ route('category.show',$product->category->slug) }}">{{ $product->category->name }}</a>@else<span class="db-product-category">Vegan Products</span>@endif<h2><a href="{{ route('product.show',$product->slug) }}">{{ $product->name }}</a></h2>
    @if($product->avg_rating > 0)<span class="db-stars" aria-label="{{ $product->avg_rating }} out of 5">{{ str_repeat('★',(int)round($product->avg_rating)) }}</span>@endif
    <p class="db-price">@if($product->variations)From @endif ${{ number_format($product->variations ? min(array_column($product->variations,'price')) : $product->final_price,2) }} @if($product->sale_price !== null && !$product->variations)<del>${{ number_format($product->price,2) }}</del>@endif</p>
    @if($product->stock < 1)<span class="db-stock-out">Out of stock</span>
    @elseif($product->variations)<a class="db-card-button" href="{{ route('product.show',$product->slug) }}">Select options →</a>
    @else<form class="variations_form db-card-form" data-product_id="{{ $product->id }}" data-slug="{{ $product->slug }}"><button type="submit" data-store-add class="db-card-button"><i class="awb-vegan-storecart" aria-hidden="true"></i> Add To Cart</button></form>@endif
    </div>
</article>
