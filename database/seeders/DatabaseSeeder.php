<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tạo tài khoản Quản trị viên mặc định
        $admin = User::updateOrCreate(
            ['email' => 'admin@veganstore.vn'],
            [
                'name' => 'Quản trị viên Vegan Store',
                'password' => Hash::make('Admin@123456'),
                'role' => 'admin',
                'phone' => '0901234567',
                'address' => 'Hồ Chí Minh, Việt Nam',
                'email_verified_at' => now(),
            ]
        );

        // 2. Chạy Seeder Danh mục & Sản phẩm
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        // 3. Tạo Bài viết tin tức mẫu (Vegan News & Articles)
        $posts = [
            [
                'author_id' => $admin->id,
                'title' => 'Discover The Best Vegan Treats For A Healthier Lifestyle',
                'slug' => 'best-vegan-treats',
                'thumbnail' => '/wp-content/uploads/sites/186/2024/02/blog-digestive-health-01.jpg',
                'excerpt' => 'Exploring the incredible health benefits of swapping processed snacks for natural plant-based whole foods.',
                'content' => '<p>Eating a vegan diet can be one of the best things you do for your digestive health and overall vitality. By focusing on whole, unprocessed ingredients like legumes, colorful leafy greens, and nuts, your gut microbiome thrives.</p><p>In this guide, we share our favorite everyday plant-based snacks that taste delicious while keeping you energized all day long.</p>',
                'category' => 'Health & Wellness',
                'is_published' => true,
            ],
            [
                'author_id' => $admin->id,
                'title' => 'Nourish Your Body With Our New Seasonal Vegan Recipes',
                'slug' => 'new-seasonal-vegan-recipes',
                'thumbnail' => '/wp-content/uploads/sites/186/2024/01/blog-vegan-lifestyle-04.jpg',
                'excerpt' => 'From warming grain bowls to vibrant zesty salads, discover easy gourmet dinners you can whip up in 20 minutes.',
                'content' => '<p>Eating in harmony with the seasons is the hallmark of mindful culinary living. Our kitchen team has curated three exclusive plant-powered recipes that highlight crisp vegetables, savory tahini marinades, and toasted seeds.</p><p>Grab your apron and let us show you how simple and hearty vegan home cooking can be!</p>',
                'category' => 'Recipes',
                'is_published' => true,
            ],
            [
                'author_id' => $admin->id,
                'title' => 'Vegan Food In 2026: The 50 Best Plant-Based Kitchen Plates',
                'slug' => 'best-plant-based-plates',
                'thumbnail' => '/wp-content/uploads/sites/186/2024/01/blog-kids-plates.jpg',
                'excerpt' => 'An insider look at sustainable kitchen culinary trends, plant milk innovations, and eco-friendly packaging.',
                'content' => '<p>The plant-based movement has matured into an exciting gastronomy revolution. From fermenting artisanal cashew cheeses to pioneering zero-waste pantry storage, here are the top trends defining sustainable kitchens this year.</p>',
                'category' => 'Lifestyle',
                'is_published' => true,
            ],
        ];

        foreach ($posts as $post) {
            Post::updateOrCreate(
                ['slug' => $post['slug']],
                $post
            );
        }

        // 4. Tạo Đơn hàng mẫu để Dashboard hiển thị số liệu thực tế
        $product1 = Product::where('slug', 'fresh-organic-cake')->first();
        $product2 = Product::where('slug', 'vegan-chia-shake')->first();
        $product3 = Product::where('slug', 'organic-almond-milk')->first();

        if ($product1 && $product2) {
            $order1 = Order::updateOrCreate(
                ['order_code' => 'VS-20261018-001'],
                [
                    'user_id' => null,
                    'customer_name' => 'Nguyễn Văn An',
                    'customer_email' => 'nguyenvanan@gmail.com',
                    'customer_phone' => '0908123456',
                    'shipping_address' => 'Số 123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
                    'customer_note' => 'Giao hàng giờ hành chính giúp mình nhé.',
                    'subtotal' => 20.00,
                    'shipping_fee' => 0.00,
                    'total' => 20.00,
                    'payment_method' => 'cod',
                    'payment_status' => 'unpaid',
                    'status' => 'processing',
                ]
            );

            OrderItem::firstOrCreate([
                'order_id' => $order1->id,
                'product_id' => $product1->id,
            ], [
                'product_name' => $product1->name,
                'price' => $product1->final_price,
                'quantity' => 2,
                'total' => $product1->final_price * 2,
            ]);

            OrderItem::firstOrCreate([
                'order_id' => $order1->id,
                'product_id' => $product2->id,
            ], [
                'product_name' => $product2->name,
                'price' => $product2->final_price,
                'quantity' => 1,
                'total' => $product2->final_price,
            ]);
        }

        if ($product3) {
            $order2 = Order::updateOrCreate(
                ['order_code' => 'VS-20261017-002'],
                [
                    'user_id' => null,
                    'customer_name' => 'Trần Thị Mai',
                    'customer_email' => 'maitran@gmail.com',
                    'customer_phone' => '0912345678',
                    'shipping_address' => '45 Lê Lợi, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh',
                    'customer_note' => null,
                    'subtotal' => 23.00,
                    'shipping_fee' => 0.00,
                    'total' => 23.00,
                    'payment_method' => 'bank_transfer',
                    'payment_status' => 'paid',
                    'status' => 'completed',
                ]
            );

            OrderItem::firstOrCreate([
                'order_id' => $order2->id,
                'product_id' => $product3->id,
            ], [
                'product_name' => $product3->name,
                'price' => $product3->final_price,
                'quantity' => 2,
                'total' => $product3->final_price * 2,
            ]);
        }
    }
}
