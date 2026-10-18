<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Phase2And3Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo Khách Hàng Demo
        $customer1 = User::updateOrCreate(
            ['email' => 'lannguyen@gmail.com'],
            [
                'name' => 'Nguyễn Thị Lan',
                'password' => Hash::make('Customer@123'),
                'role' => 'customer',
                'phone' => '0987654321',
                'address' => '12 Nguyễn Thị Minh Khai, Phường Đa Kao, Quận 1, TP. Hồ Chí Minh',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $customer2 = User::updateOrCreate(
            ['email' => 'duchoang@gmail.com'],
            [
                'name' => 'Hoàng Minh Đức',
                'password' => Hash::make('Customer@123'),
                'role' => 'customer',
                'phone' => '0933112233',
                'address' => '45 Xuân Thủy, Phường Dịch Vọng Hậu, Cầu Giấy, Hà Nội',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $customer3 = User::updateOrCreate(
            ['email' => 'chaule@gmail.com'],
            [
                'name' => 'Lê Bảo Châu (Tài Khoản Đang Khóa)',
                'password' => Hash::make('Customer@123'),
                'role' => 'customer',
                'phone' => '0909555666',
                'address' => '78 Trần Phú, Quận Hải Châu, TP. Đà Nẵng',
                'is_active' => false, // Demo tài khoản bị khóa
                'email_verified_at' => now(),
            ]
        );

        // 2. Tạo Hộp Thư Liên Hệ Mẫu (Inquiries)
        Inquiry::updateOrCreate(
            ['email' => 'tuanmai@gmail.com', 'subject' => 'Hỏi về việc đặt hàng bánh kem thuần chay số lượng lớn cho sự kiện công ty'],
            [
                'name' => 'Mai Anh Tuấn',
                'phone' => '0918889999',
                'company' => 'Công ty Eco Green Solution',
                'subject' => 'Hỏi về việc đặt hàng bánh kem thuần chay số lượng lớn cho sự kiện công ty',
                'message' => "Chào Vegan Store, bên mình sắp tổ chức workshop về lối sống xanh vào cuối tuần tới và cần đặt khoảng 50 phần bánh kem hạt dinh dưỡng. Nhờ shop liên hệ báo giá và chính sách chiết khấu giúp mình nhé.",
                'status' => 'new',
            ]
        );

        Inquiry::updateOrCreate(
            ['email' => 'hapham@gmail.com', 'subject' => 'Tư vấn chế độ dinh dưỡng hạt chia và sữa hạnh nhân'],
            [
                'name' => 'Phạm Thu Hà',
                'phone' => '0977223344',
                'company' => null,
                'subject' => 'Tư vấn chế độ dinh dưỡng hạt chia và sữa hạnh nhân',
                'message' => "Mình mới bắt đầu chuyển sang chế độ ăn thuần chay và muốn được tư vấn loại sữa hạt phù hợp cho bữa sáng.",
                'status' => 'contacted',
                'admin_reply' => "Đã gọi điện tư vấn cho bạn Hà về dòng sữa Organic Almond Milk không đường và cách kết hợp cùng hạt chia Úc vào 9h sáng nay.",
            ]
        );

        Inquiry::updateOrCreate(
            ['email' => 'namdang@gmail.com', 'subject' => 'Hợp tác phân phối nguyên liệu bánh ngọt thuần chay'],
            [
                'name' => 'Đặng Văn Nam',
                'phone' => '0903445566',
                'company' => 'Nhà hàng Mầm Xanh',
                'subject' => 'Hợp tác phân phối nguyên liệu bánh ngọt thuần chay',
                'message' => "Chúng tôi muốn nhập sỉ các dòng nguyên liệu hữu cơ của quý cửa hàng định kỳ hàng tuần. Xin vui lòng gửi catalogue và bảng giá sỉ.",
                'status' => 'resolved',
                'admin_reply' => "Đã gửi trọn bộ catalogue sản phẩm qua email và liên hệ phòng thu mua để ký hợp đồng cung ứng.",
            ]
        );

        // 3. Tạo Đánh Giá Sản Phẩm Mẫu (Reviews)
        $cake = Product::where('slug', 'fresh-organic-cake')->first();
        $shake = Product::where('slug', 'vegan-chia-shake')->first();
        $milk = Product::where('slug', 'organic-almond-milk')->first();

        if ($cake) {
            Review::updateOrCreate(
                ['user_id' => $customer1->id, 'product_id' => $cake->id],
                [
                    'rating' => 5,
                    'comment' => "Bánh rất thơm ngon, vị ngọt thanh tự nhiên từ mật chuối và chà là, không hề bị ngấy. Cả nhà mình đều rất thích!",
                    'is_approved' => true,
                ]
            );
            $cake->updateAvgRating();
        }

        if ($shake) {
            Review::updateOrCreate(
                ['user_id' => $customer2->id, 'product_id' => $shake->id],
                [
                    'rating' => 5,
                    'comment' => "Thức uống dinh dưỡng tuyệt vời sau mỗi buổi tập yoga. Chai thủy tinh bảo vệ môi trường, giao lạnh tận nơi.",
                    'is_approved' => true,
                ]
            );
            $shake->updateAvgRating();
        }

        if ($milk) {
            Review::updateOrCreate(
                ['user_id' => $customer1->id, 'product_id' => $milk->id],
                [
                    'rating' => 4,
                    'comment' => "Sữa hạnh nhân ngậy béo tự nhiên, vị hạt rang nhẹ rất cuốn hút. Rất mong shop sớm có thêm size chai 1 lít tiết kiệm hơn.",
                    'is_approved' => false, // Chờ duyệt
                ]
            );
            $milk->updateAvgRating();
        }
    }
}
