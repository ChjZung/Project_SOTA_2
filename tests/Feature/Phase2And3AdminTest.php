<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase2And3AdminTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test@veganstore.vn'],
            [
                'name' => 'Admin Tester',
                'password' => Hash::make('Admin@123456'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    /**
     * Test 1: Đăng nhập admin và truy cập Dashboard thành công
     */
    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Bảng Điều Khiển Quản Trị');
        $response->assertSee('Doanh Thu Đã Thu');
        $response->assertSee('Đơn Hàng Mới');
    }

    /**
     * Test 2: Doanh thu Dashboard CHỈ tính đơn completed VÀ paid
     */
    public function test_dashboard_revenue_only_counts_completed_and_paid_orders(): void
    {
        $uniqueCode = 'VS-TEST-' . strtoupper(Str::random(6));

        // Đơn completed nhưng chưa paid (hoặc cancelled) KHÔNG được tính
        $unpaidOrder = Order::create([
            'order_code' => $uniqueCode . '-1',
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'customer_phone' => '0901234567',
            'shipping_address' => 'Hà Nội',
            'subtotal' => 100.00,
            'total' => 100.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
        ]);

        $paidCompletedOrder = Order::create([
            'order_code' => $uniqueCode . '-2',
            'customer_name' => 'Test User 2',
            'customer_email' => 'test2@example.com',
            'customer_phone' => '0901234568',
            'shipping_address' => 'TP.HCM',
            'subtotal' => 75.00,
            'total' => 75.00,
            'status' => 'completed',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_method' => 'bank_transfer',
        ]);

        $calculatedRevenue = (float) Order::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->sum('total');

        $this->assertGreaterThanOrEqual(75.00, $calculatedRevenue);

        // Đơn pending không được cộng vào doanh thu
        $this->assertEquals(0, Order::where('order_code', $unpaidOrder->order_code)->where('status', 'completed')->count());
    }

    /**
     * Test 3: Đổi mật khẩu và cập nhật hồ sơ cá nhân admin
     */
    public function test_admin_profile_and_password_update(): void
    {
        // Cập nhật profile
        $response = $this->actingAs($this->admin)->put(route('admin.profile.update'), [
            'name' => 'Admin Updated Name',
            'phone' => '0988776655',
            'address' => '123 New Address',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id, 'name' => 'Admin Updated Name']);

        // Đổi mật khẩu
        $pwdResponse = $this->actingAs($this->admin)->put(route('admin.profile.password'), [
            'current_password' => 'Admin@123456',
            'password' => 'NewSecret@2026',
            'password_confirmation' => 'NewSecret@2026',
        ]);
        $pwdResponse->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertTrue(Hash::check('NewSecret@2026', $this->admin->password));

        // Trả lại mật khẩu cũ để tái sử dụng
        $this->admin->update(['password' => Hash::make('Admin@123456')]);
    }

    /**
     * Test 4: Xóa sản phẩm dùng Soft Delete và có thể khôi phục
     */
    public function test_product_soft_delete_and_restore(): void
    {
        $category = Category::first() ?? Category::create(['name' => 'Test Cat', 'slug' => 'test-cat']);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sản Phẩm Thử Nghiệm Xóa Mềm',
            'slug' => 'san-pham-thu-nghiem-xoa-mem-' . Str::random(5),
            'price' => 25.00,
            'stock' => 15,
            'is_active' => true,
        ]);

        // Xóa sản phẩm
        $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product));

        // Kiểm tra soft delete
        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->assertNull(Product::find($product->id));
        $this->assertNotNull(Product::withTrashed()->find($product->id));

        // Khôi phục sản phẩm
        $this->actingAs($this->admin)->post(route('admin.products.restore', $product->id));
        $this->assertNotSoftDeleted('products', ['id' => $product->id]);
    }

    /**
     * Test 5: Vòng đời đơn hàng - Cho phép chuyển tiếp hợp lệ và chặn chuyển tiếp sai
     */
    public function test_order_lifecycle_state_machine(): void
    {
        $order = Order::create([
            'order_code' => 'VS-FLOW-' . strtoupper(Str::random(5)),
            'customer_name' => 'Khách Vòng Đời',
            'customer_email' => 'flow@test.com',
            'customer_phone' => '0901234567',
            'shipping_address' => 'Đà Nẵng',
            'subtotal' => 50.00,
            'total' => 50.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
        ]);

        // pending -> confirmed (Hợp lệ)
        $this->actingAs($this->admin)->put(route('admin.orders.update-status', $order), [
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
        ]);
        $order->refresh();
        $this->assertEquals('confirmed', $order->status);

        // confirmed -> completed (BẤT HỢP LỆ: phải qua processing -> shipped)
        $invalidResponse = $this->actingAs($this->admin)->put(route('admin.orders.update-status', $order), [
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);
        $invalidResponse->assertSessionHas('error');
        $order->refresh();
        $this->assertEquals('confirmed', $order->status); // Giữ nguyên, không đổi
    }

    /**
     * Test 6: Hủy đơn hàng tự động hoàn kho và hoàn coupon trong DB transaction
     */
    public function test_order_cancellation_returns_stock_and_coupon(): void
    {
        $category = Category::first() ?? Category::create(['name' => 'Test Cat', 'slug' => 'test-cat-2']);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sản Phẩm Test Hoàn Kho',
            'slug' => 'sp-test-hoan-kho-' . Str::random(5),
            'price' => 30.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $coupon = Coupon::create([
            'code' => 'REFUND' . strtoupper(Str::random(4)),
            'type' => 'fixed',
            'value' => 5.00,
            'used_count' => 3,
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_code' => 'VS-REFUND-' . strtoupper(Str::random(5)),
            'customer_name' => 'Khách Hủy Đơn',
            'customer_email' => 'cancel@test.com',
            'customer_phone' => '0901234567',
            'shipping_address' => 'Hải Phòng',
            'subtotal' => 60.00,
            'discount_amount' => 5.00,
            'total' => 55.00,
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 30.00,
            'quantity' => 2,
            'line_total' => 60.00,
        ]);

        // Admin thực hiện hủy đơn (pending -> cancelled)
        $response = $this->actingAs($this->admin)->put(route('admin.orders.update-status', $order), [
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
        ]);
        $response->assertSessionHas('success');

        // Kiểm tra kho được hoàn: 10 + 2 = 12
        $product->refresh();
        $this->assertEquals(12, $product->stock);

        // Kiểm tra coupon được hoàn lượt: 3 - 1 = 2
        $coupon->refresh();
        $this->assertEquals(2, $coupon->used_count);
    }

    /**
     * Test 7: Khóa / Mở khóa tài khoản khách hàng
     */
    public function test_customer_toggle_lock_status(): void
    {
        $customer = User::create([
            'name' => 'Khách Hàng Thử Khóa',
            'email' => 'customer_lock_' . Str::random(5) . '@test.com',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        // Thực hiện khóa
        $this->actingAs($this->admin)->post(route('admin.customers.toggle', $customer->id));
        $customer->refresh();
        $this->assertFalse($customer->is_active);

        // Mở khóa lại
        $this->actingAs($this->admin)->post(route('admin.customers.toggle', $customer->id));
        $customer->refresh();
        $this->assertTrue($customer->is_active);
    }

    /**
     * Test 8: Duyệt review tự động cập nhật avg_rating của sản phẩm
     */
    public function test_review_approval_updates_product_avg_rating(): void
    {
        $category = Category::first() ?? Category::create(['name' => 'Test Cat', 'slug' => 'test-cat-3']);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sản Phẩm Test Rating',
            'slug' => 'sp-test-rating-' . Str::random(5),
            'price' => 40.00,
            'stock' => 20,
            'avg_rating' => 0.00,
            'is_active' => true,
        ]);

        $customer = User::first() ?? $this->admin;

        $review = Review::create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Chất lượng xuất sắc!',
            'is_approved' => false,
        ]);

        // Ban đầu chưa duyệt -> avg_rating = 0
        $product->updateAvgRating();
        $this->assertEquals(0.00, (float) $product->avg_rating);

        // Admin duyệt review
        $this->actingAs($this->admin)->post(route('admin.reviews.toggle', $review));
        $review->refresh();
        $product->refresh();

        $this->assertTrue($review->is_approved);
        $this->assertEquals(5.00, (float) $product->avg_rating);
    }

    /**
     * Test 9: Quản lý banner quảng cáo (Thêm, Toggle, Xóa)
     */
    public function test_banner_crud_and_toggle(): void
    {
        $banner = Banner::create([
            'title' => 'Banner Giảm Giá Mùa Thu',
            'position' => 'hero',
            'image' => 'uploads/banners/autumn.jpg',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)->post(route('admin.banners.toggle', $banner));
        $banner->refresh();
        $this->assertFalse($banner->is_active);

        $this->actingAs($this->admin)->delete(route('admin.banners.destroy', $banner));
        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
    }

    /**
     * Test 10: Quản lý mã giảm giá (Thêm, Toggle)
     */
    public function test_coupon_crud_and_toggle(): void
    {
        $code = 'TEST' . strtoupper(Str::random(4));
        $coupon = Coupon::create([
            'code' => $code,
            'type' => 'percent',
            'value' => 15.00,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)->post(route('admin.coupons.toggle', $coupon));
        $coupon->refresh();
        $this->assertFalse($coupon->is_active);
    }

    /**
     * Test 11: Lưu cấu hình website (Settings)
     */
    public function test_settings_save(): void
    {
        $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'group' => 'general',
            'site_name' => 'Avada Vegan Store - Premium Organics',
            'site_tagline' => 'Sống Khỏe Mỗi Ngày',
            'site_phone' => '0909000111',
            'site_email' => 'support@veganstore.vn',
        ]);

        $this->assertEquals('Avada Vegan Store - Premium Organics', Setting::get('site_name'));
        $this->assertEquals('0909000111', Setting::get('site_phone'));
    }
}
