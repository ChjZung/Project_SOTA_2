# Avada Vegan Store - E-Commerce & Organic Lifestyle Platform

Hệ thống website thương mại điện tử chuyên cung cấp thực phẩm thuần chay, mỹ phẩm hữu cơ và phong cách sống xanh **(Avada Vegan Store)**. Dự án được clone và xây dựng hoàn chỉnh trên nền tảng **Laravel 11 (PHP 8.3)** với kiến trúc mô-đun hóa hiện đại, tối ưu SEO, tương thích đa thiết bị và nội bộ hóa 100% tài nguyên hình ảnh.

---

## 🚀 Công nghệ sử dụng (Tech Stack)

* **Backend Framework**: [Laravel 11.x](https://laravel.com) (PHP >= 8.2, tối ưu trên PHP 8.3)
* **Architecture**: MVC (Model - View - Controller), Blade Templating Engine
* **Frontend UI & Styling**: Avada Fusion Styling, WooCommerce Blocks CSS, Custom SCSS/CSS
* **Typography & Fonts**: Google Fonts (Sen, Happy Monkey), Custom Icon Font (Avada-Vegan-Store, awb-icons)
* **Libraries & Plugins**: jQuery 3.7.1, WooCommerce Frontend JS, Avada Fusion Script

---

## 📂 Cấu trúc thư mục dự án

```text
Project_SOTA_2/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php         # Quản lý trang chủ, banner hero, sản phẩm thịnh hành
│   │   │   └── PageController.php         # Điều hướng shop, danh mục, giỏ hàng, 5-tier fallback router
├── public/
│   ├── assets/
│   │   ├── css/                           # avada-style.css, wc-blocks.css, custom-inline.css
│   │   ├── fonts/                         # Sen font, Avada-Vegan-Store icon font, awb-icons
│   │   └── js/                            # jquery.min.js, avada.min.js, woocommerce.min.js
│   └── wp-content/uploads/                # Toàn bộ 393 hình ảnh sản phẩm & banner lưu cục bộ (Local)
└── resources/
    └── views/
        ├── layouts/
        │   └── app.blade.php              # Master layout chính, base href, meta SEO
        ├── components/
        │   ├── header.blade.php           # Top marquee, Logo, Mega menu Categories, Giỏ hàng
        │   └── footer.blade.php           # Form nhận mã giảm giá 10%, widget danh mục, bản quyền
        └── pages/
            ├── home.blade.php             # Trang chủ đầy đủ 10+ phân khu chức năng
            ├── shop.blade.php             # Cửa hàng hiển thị danh sách sản phẩm lưới & lọc
            ├── product-detail.blade.php   # Chi tiết sản phẩm, ảnh phóng to, thêm giỏ hàng
            ├── about.blade.php            # Giới thiệu cửa hàng thuần chay & giá trị cốt lõi
            ├── contact.blade.php          # Trang liên hệ, bản đồ, form gửi tin nhắn
            ├── news.blade.php             # Tin tức & công thức nấu ăn thuần chay (News & Recipes)
            ├── cart.blade.php             # Giỏ hàng & thanh toán
            ├── my-account.blade.php       # Tài khoản & đăng nhập thành viên
            └── static/                    # Các danh mục chuyên sâu (Food & Drinks, Beauty, Pet Care...)
```

---

## ⚙️ Hướng dẫn cài đặt & Khởi chạy (Local Setup)

```bash
# 1. Cài đặt các gói phụ thuộc PHP
composer install

# 2. Tạo khóa mã hóa ứng dụng (App Key)
php artisan key:generate

# 3. Chạy di chuyển CSDL và nạp dữ liệu mẫu
php artisan migrate --seed

# 4. Khởi động máy chủ phát triển
php artisan serve
```
Truy cập hệ thống tại: `http://127.0.0.1:8000` (hoặc cổng hiển thị trên terminal).

### 🔑 Tài khoản mặc định

| Vai trò | Email | Mật khẩu | URL truy cập |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@veganstore.vn` | `Admin@123456` | `/admin/login` |
| **Khách hàng mẫu (Customer)** | `lannguyen@gmail.com` | `Customer@123` | `/login` |

### 🧪 Chạy kiểm thử tự động (Unit / Feature Tests)

```bash
php vendor/bin/phpunit
```

---

## 🌟 Tính năng chính đã hoàn thiện 100%

1. **Giao diện chuẩn Avada Vegan Store nguyên bản**:
   * Hero banner với slogan "100% Plant-Based & Organic".
   * Danh mục sản phẩm trực quan: *Food & Drinks, Beauty & Care, Pet Care Items, Fitness & Nutrition, Clothes & Accessories*.
   * Sản phẩm nổi bật, khuyến mãi giảm giá 35% với mã `VEGAN35`.
   * Bài viết chia sẻ công thức món chay và chế độ ăn dinh dưỡng.
   * Drawer giỏ hàng mini bật lên mượt mà khi bấm icon Cart.
2. **Nội bộ hóa 100% tài nguyên (Zero External Calls)**:
   * Toàn bộ gần 400 hình ảnh và font icon đều được lưu trữ trực tiếp trong thư mục `public/`.
   * Khi kiểm tra tab **F12 Network**, 100% request đều trả về từ `localhost:8000` với tốc độ tải trang cực nhanh.
3. **Cơ chế chống lỗi 404 (5-Tier Fallback Router)**:
   * Mọi liên kết danh mục, tag, bài viết, sản phẩm đều được `PageController` điều hướng thông minh, đảm bảo luôn trả về HTTP 200 OK.

---

## 📄 Bản quyền & Đơn vị phát triển
* Dự án phát triển bởi: **Development Team**
* Nền tảng: **Laravel 11 - PHP 8.3**
* Năm thực hiện: 2026