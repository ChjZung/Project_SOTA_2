<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecurityAndStorefrontTest extends TestCase
{
    /**
     * Test 1: Khách hàng không thể xem đơn hàng của khách hàng khác (Phân quyền dữ liệu)
     */
    public function test_customer_cannot_view_another_customer_order(): void
    {
        $userA = User::create([
            'name' => 'Khach A ' . uniqid(),
            'email' => 'khacha_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        $userB = User::create([
            'name' => 'Khach B ' . uniqid(),
            'email' => 'khachb_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        $orderB = Order::create([
            'user_id' => $userB->id,
            'order_code' => Order::generateOrderCode(),
            'customer_name' => $userB->name,
            'customer_email' => $userB->email,
            'customer_phone' => '0912345678',
            'shipping_address' => '123 Le Loi, Q1, HCM',
            'subtotal' => 50.00,
            'shipping_fee' => 0.00,
            'total' => 50.00,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        // Kiểm tra qua Policy
        $this->assertFalse(Gate::forUser($userA)->allows('view', $orderB));
        $this->assertTrue(Gate::forUser($userB)->allows('view', $orderB));

        // Kiểm tra qua HTTP request của Khách A
        $response = $this->actingAs($userA)->get('/my-account/orders/' . $orderB->order_code);
        $response->assertStatus(404);
    }

    /**
     * Test 2: Khách vãng lai không xem được /order-success nếu không có session token hợp lệ
     */
    public function test_guest_cannot_view_order_success_without_session_token(): void
    {
        $order = Order::create([
            'user_id' => null,
            'order_code' => Order::generateOrderCode(),
            'customer_name' => 'Khach Vang Lai',
            'customer_email' => 'guest_' . uniqid() . '@example.com',
            'customer_phone' => '0933445566',
            'shipping_address' => '456 Tran Hung Dao, Q5, HCM',
            'subtotal' => 25.00,
            'shipping_fee' => 5.00,
            'total' => 30.00,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        // Người lạ không có session token truy cập -> bị chặn 404
        $response = $this->get('/order-success/' . $order->order_code);
        $response->assertStatus(404);

        // Khách có session token hợp lệ -> xem được
        $responseWithSession = $this->withSession(['order_access.' . $order->order_code => true])
            ->get('/order-success/' . $order->order_code);
        $responseWithSession->assertStatus(200);
    }

    /**
     * Test 3: Khách hàng thường tuyệt đối không thể truy cập vào trang Admin
     */
    public function test_regular_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::create([
            'name' => 'Khach Thuong ' . uniqid(),
            'email' => 'customer_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test 4: Máy chủ tự tính lại giá từ CSDL khi đặt hàng (Không tin dữ liệu gửi từ trình duyệt)
     */
    public function test_server_calculates_price_ignoring_client_manipulation(): void
    {
        $product = Product::create([
            'category_id' => 1,
            'name' => 'Banh Gia Chuan ' . uniqid(),
            'slug' => 'banh-gia-chuan-' . uniqid(),
            'price' => 20.00,
            'sale_price' => 15.00,
            'stock' => 50,
            'is_active' => true,
        ]);

        $token = (string) Str::uuid();

        $response = $this->withSession([
            'checkout_token' => $token,
            'cart' => [
                $product->slug => [
                    'slug' => $product->slug,
                    'quantity' => 2,
                    'options' => [],
                ]
            ]
        ])->post('/checkout', [
            'idempotency_token' => $token,
            'customer_name' => 'Nguoi Thu Mua',
            'customer_email' => 'buyer_' . uniqid() . '@example.com',
            'customer_phone' => '0901234567',
            'shipping_address' => '789 Nguyen Thi Minh Khai, Q3, HCM',
            'payment_method' => 'cod',
            // Co tinh gui gia sai hoac tong tien 0 dong tu client
            'total' => 0.00,
            'subtotal' => 0.00,
        ]);

        $response->assertRedirect();

        // Kiem tra trong CSDL: tong tien phai la 2 x 15 = 30.00 + 5.00 phi ship = 35.00
        $order = Order::where('idempotency_token', $token)->first();
        $this->assertNotNull($order);
        $this->assertEquals(30.00, (float) $order->subtotal);
        $this->assertEquals(35.00, (float) $order->total);
    }

    /**
     * Test 5: Sản phẩm hết hàng (stock = 0) không thể đặt mua
     */
    public function test_out_of_stock_product_cannot_be_ordered(): void
    {
        $product = Product::create([
            'category_id' => 1,
            'name' => 'San Pham Het Kho ' . uniqid(),
            'slug' => 'san-pham-het-kho-' . uniqid(),
            'price' => 20.00,
            'stock' => 0,
            'is_active' => true,
        ]);

        $token = (string) Str::uuid();

        $response = $this->withSession([
            'checkout_token' => $token,
            'cart' => [
                $product->slug => [
                    'slug' => $product->slug,
                    'quantity' => 1,
                    'options' => [],
                ]
            ]
        ])->post('/checkout', [
            'idempotency_token' => $token,
            'customer_name' => 'Nguoi Mua',
            'customer_email' => 'buyer2_' . uniqid() . '@example.com',
            'customer_phone' => '0901234567',
            'shipping_address' => 'Dia chi nhan hang',
            'payment_method' => 'cod',
        ]);

        // Đơn hàng KHÔNG được tạo
        $this->assertDatabaseMissing('orders', ['idempotency_token' => $token]);
    }

    /**
     * Test 6: Idempotency token ngăn ngừa việc đặt 2 đơn khi bấm nhanh hoặc reload trang
     */
    public function test_idempotency_token_prevents_duplicate_orders(): void
    {
        $product = Product::create([
            'category_id' => 1,
            'name' => 'SP Test Token ' . uniqid(),
            'slug' => 'sp-test-token-' . uniqid(),
            'price' => 12.00,
            'stock' => 20,
            'is_active' => true,
        ]);

        $token = (string) Str::uuid();

        // Lần gửi 1: tạo đơn thành công
        $this->withSession([
            'checkout_token' => $token,
            'cart' => [
                $product->slug => [
                    'slug' => $product->slug,
                    'quantity' => 1,
                    'options' => [],
                ]
            ]
        ])->post('/checkout', [
            'idempotency_token' => $token,
            'customer_name' => 'Khach Dat Trung',
            'customer_email' => 'dup_' . uniqid() . '@example.com',
            'customer_phone' => '0901234567',
            'shipping_address' => 'Dia chi nhan hang',
            'payment_method' => 'cod',
        ]);

        // Số đơn hàng với token này trong DB
        $orderCount = Order::where('idempotency_token', $token)->count();
        $this->assertEquals(1, $orderCount);

        // Lần gửi 2 cùng token: KHÔNG tạo thêm đơn thứ 2
        $this->withSession([
            'checkout_token' => $token,
            'order_access.' . Order::where('idempotency_token', $token)->first()->order_code => true,
        ])->post('/checkout', [
            'idempotency_token' => $token,
            'customer_name' => 'Khach Dat Trung',
            'customer_email' => 'dup_' . uniqid() . '@example.com',
            'customer_phone' => '0901234567',
            'shipping_address' => 'Dia chi nhan hang',
            'payment_method' => 'cod',
        ]);

        $this->assertEquals(1, Order::where('idempotency_token', $token)->count());
    }

    /**
     * Test 7: Hủy đơn hàng tự động hoàn lại tồn kho và lượt dùng mã giảm giá
     */
    public function test_cancelling_order_restores_product_stock_and_coupon_uses(): void
    {
        $product = Product::create([
            'category_id' => 1,
            'name' => 'SP Hoan Kho ' . uniqid(),
            'slug' => 'sp-hoan-kho-' . uniqid(),
            'price' => 25.00,
            'stock' => 8,
            'is_active' => true,
        ]);

        $coupon = Coupon::create([
            'code' => 'TESTRESTORE' . rand(100, 999),
            'type' => 'fixed',
            'value' => 5.00,
            'min_order_amount' => 10.00,
            'max_uses' => 10,
            'used_count' => 1,
            'is_active' => true,
        ]);

        $admin = User::where('role', 'admin')->first() ?? User::create([
            'name' => 'Admin Test',
            'email' => 'admin_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $order = Order::create([
            'user_id' => null,
            'coupon_id' => $coupon->id,
            'order_code' => Order::generateOrderCode(),
            'customer_name' => 'Nguoi Can Huy',
            'customer_email' => 'cancel_' . uniqid() . '@example.com',
            'customer_phone' => '0901234567',
            'shipping_address' => 'Dia chi',
            'subtotal' => 50.00,
            'discount_amount' => 5.00,
            'shipping_fee' => 0.00,
            'total' => 45.00,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => 25.00,
            'quantity' => 2,
            'total' => 50.00,
        ]);

        // Admin thực hiện hủy đơn
        $response = $this->actingAs($admin)->put('/admin/orders/' . $order->id . '/status', [
            'status' => 'cancelled',
        ]);

        $response->assertRedirect();

        // Kiểm tra tồn kho SP được cộng lại: 8 + 2 = 10
        $product->refresh();
        $this->assertEquals(10, $product->stock);

        // Kiểm tra lượt dùng coupon được giảm lại: 1 - 1 = 0
        $coupon->refresh();
        $this->assertEquals(0, $coupon->used_count);
    }

    /**
     * Test 8: Sitemap XML trả về đúng chuẩn XML với các liên kết
     */
    public function test_sitemap_xml_returns_valid_xml(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $this->assertStringContainsString('<urlset', $response->getContent());
        $this->assertStringContainsString(url('/shop'), $response->getContent());
    }

    /**
     * Test 9: Robots.txt chặn đúng các đường dẫn nhạy cảm
     */
    public function test_robots_txt_disallows_sensitive_paths(): void
    {
        $robotsPath = public_path('robots.txt');
        $this->assertFileExists($robotsPath);
        $content = file_get_contents($robotsPath);
        $this->assertStringContainsString('Disallow: /admin/', $content);
        $this->assertStringContainsString('Disallow: /checkout', $content);
        $this->assertStringContainsString('Sitemap: /sitemap.xml', $content);
    }
}
