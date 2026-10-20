@php($attributes = [])
@foreach($item['variations'] as $variation)
    @foreach($variation['attributes'] as $attribute => $value)
        @php($attributes[$attribute][$value] = $value)
    @endforeach
@endforeach
<form class="variations_form product-purchase" data-product_id="{{ $item['id'] }}" data-slug="{{ $item['slug'] }}" data-product_variations="{{ json_encode(array_map(fn ($v) => ['attributes' => $v['attributes'], 'display_price' => $v['price']], $item['variations'])) }}">
    <p class="stock-status">{{ $item['stock'] > 0 ? $item['stock'].' in stock' : 'Out of stock' }}</p>
    <fieldset @disabled($item['stock'] < 1)>
    @foreach($attributes as $attribute => $values)
        <label class="product-option">{{ ucfirst(str_replace(['attribute_pa_', 'attribute_', '-'], ['', '', ' '], $attribute)) }}
            <select name="{{ $attribute }}" required>
                <option value="">Choose an option</option>
                @foreach($values as $value)<option value="{{ $value }}">{{ ucfirst($value) }}</option>@endforeach
            </select>
        </label>
    @endforeach
    <div class="product-buy-row">
        <label class="product-quantity"><span class="screen-reader-text">Quantity</span><input aria-label="Quantity" type="number" name="quantity" value="1" min="1" max="99" required></label>
        <button type="submit" class="product-add" data-store-add><i class="awb-vegan-storecart" aria-hidden="true"></i> Add To Cart</button>
    </div>
    </fieldset>
</form>
