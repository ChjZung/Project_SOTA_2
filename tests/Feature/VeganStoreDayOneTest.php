<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class VeganStoreDayOneTest extends TestCase
{
    /**
     * Test 1: Kiểm tra trang chủ và danh mục tải thành công
     */
    public function test_home_and_shop_pages_load_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $shop = $this->get('/shop');
        $shop->assertStatus(200);
    }

    /**
     * Test 2: Fix bug 1 - Phân trang danh mục và shop không bị 404
     */
    public function test_category_and_shop_pagination_does_not_return_404(): void
    {
        $shopPage2 = $this->get('/shop/page/2');
        $shopPage2->assertStatus(200);

        $catPage2 = $this->get('/product-category/food-drinks/page/2');
        $catPage2->assertStatus(200);
    }

    /**
     * Test 3: Fix bug 2 - Xem chi tiết sản phẩm hiển thị thông tin
     */
    public function test_single_product_detail_page_loads_with_data(): void
    {
        $product = Product::first();
        $this->assertNotNull($product, 'Cần có ít nhất 1 sản phẩm trong database');

        $response = $this->get('/product/' . $product->slug);
        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    /**
     * Test 4: Fix bug 3 - Thêm vào giỏ hàng hoạt động mượt mà
     */
    public function test_add_to_cart_functions_properly(): void
    {
        $product = Product::first();

        $response = $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'cart_count' => 2,
            ]);

        // Kiểm tra trang /cart
        $cartPage = $this->get('/cart');
        $cartPage->assertStatus(200);
        $cartPage->assertSee($product->name);
    }

    /**
     * Test 5: Quản trị viên chưa đăng nhập bị chặn khỏi /admin/dashboard
     */
    public function test_unauthenticated_user_cannot_access_admin(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test 6: Quản trị viên đăng nhập và truy cập các module CRUD
     */
    public function test_admin_can_access_dashboard_and_crud_modules(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin, 'Cần có tài khoản admin');

        $this->actingAs($admin);

        // Dashboard
        $dashboard = $this->get('/admin/dashboard');
        $dashboard->assertStatus(200);

        // Danh mục
        $categories = $this->get('/admin/categories');
        $categories->assertStatus(200);

        // Sản phẩm
        $products = $this->get('/admin/products');
        $products->assertStatus(200);

        // Đơn hàng
        $orders = $this->get('/admin/orders');
        $orders->assertStatus(200);

        // Bài viết
        $posts = $this->get('/admin/posts');
        $posts->assertStatus(200);
    }
}
