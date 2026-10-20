# 🌿 TÀI LIỆU HƯỚNG DẪN VẬN HÀNH & BÀN GIAO HỆ THỐNG
# AVADA VEGAN STORE (`Project_SOTA_2`)

---

## 📌 1. THÔNG TIN TRUY CẬP VÀ TÀI KHOẢN MẶC ĐỊNH

### 1.1. Đường Dẫn Hệ Thống
- **Trang chủ Khách hàng (Storefront)**: `http://localhost/Project_SOTA_2/public` (hoặc `http://127.0.0.1:8018` nếu chạy `artisan serve`)
- **Trang Quản trị Admin**: `http://localhost/Project_SOTA_2/public/admin/login` (hoặc `http://127.0.0.1:8018/admin/login`)
- **Sitemap XML**: `http://localhost/Project_SOTA_2/public/sitemap.xml`
- **Robots.txt**: `http://localhost/Project_SOTA_2/public/robots.txt`

### 1.2. Danh Sách Tài Khoản Mẫu
| Vai trò | Email đăng nhập | Mật khẩu | Quyền hạn |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@veganstore.vn` | `Admin@123456` | Toàn quyền quản trị Dashboard, Sản phẩm, Đơn hàng, Banner, Coupon, Cài đặt |
| **Khách hàng mẫu (Customer)** | `lannguyen@gmail.com` | `Customer@123` | Mua hàng, Giỏ hàng, Áp mã giảm giá, Quản lý đơn hàng trong My Account |

> Khách vãng lai cũng có thể tự do bấm **Register** (`/register`) ngoài trang web để tạo tài khoản mới ngay lập tức.

---

## 🚀 2. HƯỚNG DẪN KHỞI CHẠY HỆ THỐNG TRÊN MÁY TÍNH

Khi khởi động máy hoặc sau khi mở lại Laragon:
1. Mở phần mềm **Laragon** và bấm **Start All** (để khởi chạy MySQL port 3306 và Apache).
2. Mở cửa sổ dòng lệnh (Terminal / PowerShell) tại thư mục `C:\laragon\www\Project_SOTA_2` và chạy:
   ```bash
   php artisan serve --host=127.0.0.1 --port=8018
   ```
3. Mở trình duyệt web truy cập: `http://127.0.0.1:8018`.

---

## 🛡️ 3. CÁC TÍNH NĂNG BẢO MẬT & TOÀN VẸN DỮ LIỆU ĐÃ TRIỂN KHAI

Hệ thống được lập trình tuân thủ nghiêm ngặt các quy tắc an toàn dữ liệu trong thương mại điện tử:

1. **Server-side Price Calculation (Không tin dữ liệu trình duyệt)**:
   - Dù khách hàng có can thiệp sửa giá hoặc tổng tiền trên DevTools thành `0đ`, khi gửi đơn về server, hệ thống sẽ bỏ qua toàn bộ số tiền từ client và **tự động truy vấn giá niêm yết từ CSDL để tính lại 100%**.
2. **Pessimistic Locking & DB Transactions**:
   - Quá trình đặt hàng chạy trong `DB::transaction()` với câu lệnh `lockForUpdate()`. Nếu 2 khách hàng cùng đặt mua sản phẩm cuối cùng cùng 1 giây, hệ thống xếp hàng xử lý tuần tự: khách đầu tiên đặt thành công, khách thứ hai nhận thông báo hết hàng mà không gây âm kho.
3. **Chống Gửi Đơn Trùng Lặp (Idempotency Token)**:
   - Mỗi phiên thanh toán sinh một mã `idempotency_token` duy nhất (UUID v4). Nếu khách hàng bấm nút "Place Order" nhiều lần liên tiếp hoặc reload trang, hệ thống chỉ tạo duy nhất 1 đơn hàng.
4. **Phân Quyền Dữ Liệu Chặt Chẽ (Policy-based Authorization)**:
   - Khách A tuyệt đối không xem được đơn hàng của khách B (HTTP 404/403).
   - Khách thường truy cập `/admin/*` sẽ lập tức bị chặn và chuyển hướng về trang đăng nhập.
5. **Vòng Đời Đơn Hàng Chuẩn (Order State Machine)**:
   - Thứ tự chuyển trạng thái: `Chờ xác nhận (pending) ➔ Đã xác nhận (confirmed) ➔ Đang chuẩn bị (processing) ➔ Đang giao (shipped) ➔ Hoàn thành (completed)`.
   - Khi Admin bấm **Hủy đơn (cancelled)**: Tồn kho của các sản phẩm trong đơn được **tự động hoàn trả**, và lượt sử dụng của Mã giảm giá được **tự động hoàn lại**.
6. **Bảo Vệ Lịch Sử Đơn Hàng (Soft Delete)**:
   - Khi Admin xóa một sản phẩm, sản phẩm sẽ vào Thùng rác (ẩn khỏi website) nhưng thông tin sản phẩm và ảnh chụp snapshot trong các đơn hàng cũ vẫn được bảo tồn vẹn nguyên.

---

## 🛒 4. HƯỚNG DẪN QUẢN TRỊ DÀNH CHO ADMIN

Đăng nhập tại `/admin/login` với tài khoản `admin@veganstore.vn`:

### 4.1. Quản lý Sản phẩm (`/admin/products`)
- **Thêm sản phẩm mới**: Bấm "Thêm sản phẩm", nhập Tên, Mã SKU, Danh mục, Giá gốc, Giá khuyến mãi (nếu có), Số lượng tồn kho, chọn Ảnh đại diện và tải lên nhiều ảnh cho Bộ sưu tập (Gallery).
- **Bật/Tắt hiển thị nhanh**: Bấm vào công tắc trạng thái ở bảng danh sách để ẩn/hiện sản phẩm ngoài trang chủ ngay lập tức.
- **Thùng rác & Phục hồi**: Bấm xóa sản phẩm sẽ chuyển vào thùng rác; Admin có thể bấm "Khôi phục" (Restore) bất cứ lúc nào.

### 4.2. Quản lý Đơn hàng (`/admin/orders`)
- **Xem chi tiết đơn**: Bấm vào mã đơn hàng để xem danh sách sản phẩm, địa chỉ giao hàng, phương thức thanh toán (COD hoặc Chuyển khoản).
- **Cập nhật trạng thái**: Chuyển đổi trạng thái đơn hàng theo đúng tiến độ giao hàng.
- **Xác nhận thanh toán**: Với đơn chuyển khoản, khi tiền đã vào tài khoản ngân hàng, Admin bấm "Xác nhận đã nhận tiền" để hệ thống ghi nhận thời điểm thanh toán (`paid_at`).
- **In hóa đơn**: Bấm nút "In đơn hàng" (Print Invoice) để xuất phiếu giao hàng sạch đẹp chuẩn A4.

### 4.3. Quản lý Mã giảm giá (`/admin/coupons`)
- Tạo mã giảm giá mới: Nhập mã (VD: `SUMMER15`), chọn loại giảm (`%` hoặc số tiền cố định `$`), nhập giá trị, hạn mức đơn hàng tối thiểu và số lượt dùng tối đa.

### 4.4. Quản lý Đánh giá (`/admin/reviews`)
- Khách hàng sau khi đăng nhập có thể gửi đánh giá sao và bình luận dưới sản phẩm. Đánh giá mới sẽ ở trạng thái "Chờ duyệt". Admin vào mục Đánh giá bấm "Duyệt" thì bình luận mới xuất hiện ra ngoài web và điểm sao trung bình của sản phẩm tự động cập nhật.

### 4.5. Quản lý Banner & Slider (`/admin/banners`)
- Thay đổi hình ảnh Banner Hero đầu trang chủ, Banner Flash Deals hoặc Banner cột bên; gắn nút bấm kêu gọi hành động (CTA button) và link liên kết.

### 4.6. Cài đặt Website (`/admin/settings`)
- Thay đổi Tên shop, Hotline liên hệ, Địa chỉ hiển thị ở Footer, và Thông tin tài khoản ngân hàng (Tên ngân hàng, Số tài khoản, Chủ tài khoản) hiển thị trên trang Thanh toán khi khách chọn chuyển khoản.

---

## 🧪 5. DANH SÁCH LỆNH KIỂM THỬ TỰ ĐỘNG (AUTOMATED TESTS)

Hệ thống đi kèm bộ kiểm thử tự động toàn diện đạt chuẩn 100% xanh lá cây. Để chạy kiểm tra toàn bộ hệ thống:

```bash
# 1. Chạy toàn bộ 28 bài test tự động (87 assertions)
php vendor/bin/phpunit tests/Feature/VeganStoreDayOneTest.php tests/Feature/Phase1DatabaseTest.php tests/Feature/Phase8SecurityAndStorefrontTest.php tests/Feature/StorefrontInteractionTest.php

# 2. Kiểm tra sitemap XML và robots.txt
curl http://127.0.0.1:8018/sitemap.xml
curl http://127.0.0.1:8018/robots.txt
```

---

*Hệ thống được phát triển và hoàn thiện bởi kỹ sư ChjZung (hochidung15062005@gmail.com).*
