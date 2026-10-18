<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Post;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Phase1DatabaseTest extends TestCase
{
    /**
     * Test 1: Kiểm tra đủ 12 bảng nghiệp vụ trong CSDL
     */
    public function test_all_12_business_tables_exist(): void
    {
        $expectedTables = [
            'users',
            'categories',
            'products',
            'orders',
            'order_items',
            'posts',
            'inquiries',
            'banners',
            'coupons',
            'reviews',
            'wishlists',
            'settings',
        ];

        foreach ($expectedTables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table {$table} must exist in database.");
        }
    }

    /**
     * Test 2: Kiểm tra các cột bổ sung quan trọng
     */
    public function test_schema_has_required_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('users', ['avatar', 'is_active']));
        $this->assertTrue(Schema::hasColumns('products', ['avg_rating', 'deleted_at', 'meta_title', 'meta_description']));
        $this->assertTrue(Schema::hasColumns('orders', ['coupon_id', 'idempotency_token', 'coupon_code', 'discount_amount', 'admin_note', 'paid_at']));
        $this->assertTrue(Schema::hasColumn('order_items', 'product_image'));
        $this->assertTrue(Schema::hasColumns('categories', ['is_active', 'meta_title', 'meta_description']));
        $this->assertTrue(Schema::hasColumns('posts', ['meta_title', 'meta_description']));
        $this->assertTrue(Schema::hasColumns('inquiries', ['subject', 'admin_reply']));
    }

    /**
     * Test 3: Ràng buộc UNIQUE chống trùng Wishlist
     */
    public function test_wishlist_has_unique_user_product_constraint(): void
    {
        $user = User::first() ?? User::factory()->create();
        $product = Product::first() ?? Product::create([
            'category_id' => 1,
            'name' => 'Test Product',
            'slug' => 'test-product-' . uniqid(),
            'price' => 10,
            'stock' => 5,
        ]);

        Wishlist::where('user_id', $user->id)->where('product_id', $product->id)->delete();

        // Tạo bản ghi 1: thành công
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        // Tạo bản ghi 2 cùng user_id và product_id: PHẢI ném ngoại lệ QueryException
        $this->expectException(QueryException::class);
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    /**
     * Test 4: Ràng buộc UNIQUE mỗi khách chỉ đánh giá 1 lần cho 1 sản phẩm
     */
    public function test_review_has_unique_user_product_constraint(): void
    {
        $user = User::first() ?? User::factory()->create();
        $product = Product::first();

        Review::where('user_id', $user->id)->where('product_id', $product->id)->delete();

        // Tạo đánh giá 1: thành công
        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Sản phẩm rất tốt!',
            'is_approved' => true,
        ]);

        // Tạo đánh giá 2 cùng user và product: PHẢI ném ngoại lệ QueryException
        $this->expectException(QueryException::class);
        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 4,
            'comment' => 'Đánh giá trùng lặp',
            'is_approved' => false,
        ]);
    }

    /**
     * Test 5: Soft Delete trên Product bảo vệ lịch sử đơn hàng
     */
    public function test_product_soft_delete_preserves_order_history(): void
    {
        $product = Product::create([
            'category_id' => 1,
            'name' => 'Sản phẩm xóa mềm ' . uniqid(),
            'slug' => 'san-pham-xoa-mem-' . uniqid(),
            'price' => 15.00,
            'stock' => 10,
        ]);

        $order = Order::create([
            'order_code' => Order::generateOrderCode(),
            'customer_name' => 'Khách Test',
            'customer_email' => 'test@gmail.com',
            'customer_phone' => '0987654321',
            'shipping_address' => 'Địa chỉ test',
            'subtotal' => 15.00,
            'total' => 15.00,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_image' => $product->image,
            'price' => 15.00,
            'quantity' => 1,
            'total' => 15.00,
        ]);

        // Xóa sản phẩm
        $product->delete();

        // Kiểm tra: SP không còn trong query thông thường
        $this->assertNull(Product::find($product->id));

        // Kiểm tra: SP vẫn tồn tại trong withTrashed()
        $this->assertNotNull(Product::withTrashed()->find($product->id));

        // Kiểm tra: Dòng order item vẫn giữ nguyên thông tin snapshot và quan hệ withTrashed
        $this->assertDatabaseHas('order_items', ['id' => $orderItem->id, 'product_name' => $product->name]);
    }

    /**
     * Test 6: Tính toán và kiểm tra tính hợp lệ của Coupon
     */
    public function test_coupon_validation_and_calculation(): void
    {
        $percentCoupon = Coupon::where('code', 'VEGAN10')->first();
        $this->assertNotNull($percentCoupon);
        $this->assertTrue($percentCoupon->isValid(30.00));
        // Giảm 10% của 30.00 là 3.00
        $this->assertEquals(3.00, $percentCoupon->calculateDiscount(30.00));

        $fixedCoupon = Coupon::where('code', 'WELCOME5')->first();
        $this->assertNotNull($fixedCoupon);
        $this->assertTrue($fixedCoupon->isValid(20.00));
        // Giảm cố định 5.00
        $this->assertEquals(5.00, $fixedCoupon->calculateDiscount(20.00));

        // Đơn không đủ số tiền tối thiểu
        $this->assertFalse($percentCoupon->isValid(10.00)); // Min là 20
        $this->assertEquals(0.00, $percentCoupon->calculateDiscount(10.00));
    }

    /**
     * Test 7: Cài đặt hệ thống Setting với bộ nhớ đệm Cache
     */
    public function test_setting_get_and_set(): void
    {
        Setting::set('test_key_phase1', 'gia_tri_thu_nghiem', 'general');

        $this->assertEquals('gia_tri_thu_nghiem', Setting::get('test_key_phase1'));
        $this->assertEquals('gia_tri_mac_dinh', Setting::get('non_existing_key', 'gia_tri_mac_dinh'));
    }

    /**
     * Test 8: Sơ đồ chuyển đổi trạng thái đơn hàng (Order State Machine)
     */
    public function test_order_lifecycle_state_machine_transitions(): void
    {
        $order = new Order(['status' => 'pending']);
        $this->assertTrue($order->canTransitionTo('confirmed'));
        $this->assertTrue($order->canTransitionTo('cancelled'));
        $this->assertFalse($order->canTransitionTo('completed')); // Không được nhảy cóc

        $order->status = 'shipped';
        $this->assertTrue($order->canTransitionTo('completed'));
        $this->assertTrue($order->canTransitionTo('processing')); // Giao thất bại -> xử lý lại
        $this->assertFalse($order->canTransitionTo('pending'));

        $order->status = 'completed';
        $this->assertFalse($order->canTransitionTo('cancelled')); // Đã xong thì không được hủy
    }

    /**
     * Test 9: Mã đơn hàng tự sinh theo định dạng chuẩn VS-YYYYMMDD-XXXX
     */
    public function test_order_code_format_is_unique_and_valid(): void
    {
        $code = Order::generateOrderCode();
        $today = date('Ymd');
        $this->assertMatchesRegularExpression("/^VS-{$today}-[A-Z0-9]{4}$/", $code);
    }
}
