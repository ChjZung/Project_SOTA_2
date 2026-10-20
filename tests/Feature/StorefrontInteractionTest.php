<?php

namespace Tests\Feature;

use App\Support\StorefrontCatalog;
use Tests\TestCase;

class StorefrontInteractionTest extends TestCase
{
    public function test_required_options_and_unknown_options_are_rejected(): void
    {
        $this->postJson('/cart/add', ['slug' => 'vegan-diet-shake', 'quantity' => 1])->assertStatus(422);
        $this->postJson('/cart/add', ['slug' => 'vegan-diet-shake', 'options' => ['attribute_pa_flavor' => 'forged']])->assertStatus(422);
        $this->getJson('/cart/mini-cart')->assertJsonPath('cart_count', 0);
    }

    public function test_cart_prices_are_server_owned_and_variations_have_distinct_keys(): void
    {
        $variants = StorefrontCatalog::find('vegan-diet-shake')['variations'];
        foreach ($variants as $variant) {
            $this->postJson('/cart/add', [
                'slug' => 'vegan-diet-shake', 'options' => $variant['attributes'],
                'quantity' => 1, 'price' => 0.01,
            ])->assertOk();
        }
        $cart = $this->getJson('/cart/mini-cart')->assertJsonPath('cart_count', 2)->json();
        $this->assertCount(2, $cart['items']);
        $this->assertNotEquals($cart['items'][0]['key'], $cart['items'][1]['key']);
        $this->assertEquals($variants[0]['price'] + $variants[1]['price'], $cart['cart_total']);
        $key = $cart['items'][0]['key'];
        $this->postJson('/cart/update', ['product_id' => $key, 'quantity' => 3])->assertOk();
        $this->getJson('/cart/mini-cart')->assertJsonPath('cart_count', 4);
        $this->postJson('/cart/remove', ['product_id' => $key])->assertOk();
        $this->getJson('/cart/mini-cart')->assertJsonPath('cart_count', 1);
    }

    public function test_catalog_search_and_product_detail_work_without_database(): void
    {
        $this->get('/shop?s=cake')->assertOk()->assertSee('Fresh Organic Cake')->assertDontSee('Leather Vegan Bag</a>', false);
        $this->get('/shop?s=not-a-real-product')->assertOk()->assertSee('No products found');
        $this->get('/product/vegan-diet-shake')->assertOk()->assertSee('Vegan Diet Shake');
    }

    public function test_quantity_bounds_are_enforced(): void
    {
        $this->postJson('/cart/add', ['slug' => 'fresh-organic-cake', 'quantity' => 100])->assertStatus(422);
        $this->postJson('/cart/update', ['product_id' => 'anything', 'quantity' => -1])->assertStatus(422);
    }
}
