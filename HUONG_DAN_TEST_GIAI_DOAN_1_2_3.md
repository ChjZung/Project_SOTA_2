# 📋 Hướng Dẫn Kiểm Thử Toàn Diện (Giai Đoạn 1, 2 & 3)

**Dự án**: Avada Vegan Store (`Project_SOTA_2`)  
**Mục đích**: Tài liệu kịch bản kiểm thử nhanh từng bước cho Quản trị viên & Lập trình viên trước khi bước sang Giai đoạn 4 & 5.

---

## 🚀 1. Chuẩn Bị & Khởi Chạy Môi Trường

### 1.1. Khởi động MySQL & Server
Mở terminal tại thư mục dự án:
```bash
# 1. Khởi chạy server Laravel
php artisan serve
```
* Trang chủ Website: [http://127.0.0.1:8000](http://127.0.0.1:8000)
* Trang Đăng Nhập Quản Trị: [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login)

### 1.2. Chạy Kiểm Thử Tự Động (1-Click Test)
Hệ thống đã tích hợp sẵn 28 test cases kiểm thử tự động, bạn có thể chạy bất kỳ lúc nào:
```bash
# Chạy toàn bộ test suite của dự án (28 tests)
php artisan test

# Hoặc chỉ chạy riêng bộ test Giai đoạn 2 & 3 (11 tests)
php artisan test --filter=Phase2And3AdminTest
```

### 1.3. Thông Tin Tài Khoản Mẫu

| Tài khoản | Email | Mật khẩu | Mục đích kiểm thử |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@veganstore.vn` | `Admin@123456` | Toàn quyền kiểm thử Dashboard & CRUD |
| **Khách hàng 1** | `lannguyen@gmail.com` | `Customer@123` | Tài khoản khách hàng hoạt động bình thường |
| **Khách hàng 2** | `duchoang@gmail.com` | `Customer@123` | Tài khoản khách hàng hoạt động bình thường |
| **Khách hàng 3 (Khóa)** | `chaule@gmail.com` | `Customer@123` | Tài khoản bị khóa (dùng để test chặn đăng nhập) |

---

## 🔍 2. Kịch Bản Test Giai Đoạn 1: CSDL & Models

| STT | Tên Kịch Bản | Thao Tác Thực Hiện | Kết Quả Mong Đợi | Trạng Thái |
|:---:|:---|:---|:---|:---:|
| 1.1 | **Kiểm tra 12 bảng CSDL** | Mở HeidiSQL / phpMyAdmin hoặc chạy: `php artisan migrate:status` | Đủ 12 bảng nghiệp vụ: `users`, `categories`, `products`, `orders`, `order_items`, `posts`, `inquiries`, `banners`, `coupons`, `reviews`, `wishlists`, `settings`. | [ ] |
| 1.2 | **Ràng buộc Unique Wishlist & Review** | Chạy `php artisan test --filter=test_unique_constraint` | DB từ chối nếu 1 user lưu trùng wishlist hoặc đánh giá 2 lần trên cùng 1 sản phẩm. | [ ] |
| 1.3 | **Cột bổ sung quan trọng** | Kiểm tra bảng `orders`, `products`, `users` | Có đủ các cột: `orders.idempotency_token`, `orders.coupon_id`, `orders.paid_at`, `products.deleted_at` (soft delete), `products.avg_rating`. | [ ] |

---

## 📊 3. Kịch Bản Test Giai Đoạn 2: Admin Auth, Dashboard & Layout

### Kịch bản 2.1: Đăng Nhập & Bảo Mật Admin
1. Truy cập [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login).
2. Thử nhập sai mật khẩu $\rightarrow$ Hệ thống báo lỗi `"Mật khẩu quản trị viên không chính xác"`.
3. Nhập `admin@veganstore.vn` / `Admin@123456` $\rightarrow$ Chuyển hướng vào trang **Bảng Điều Khiển** (`/admin/dashboard`).

### Kịch bản 2.2: Kiểm Tra 4 Thẻ Số Liệu Dashboard
* **Thẻ Doanh Thu**: 
  * Chỉ tính tổng tiền của các đơn có `status = 'completed'` **VÀ** `payment_status = 'paid'`.
  * Các đơn `pending`, `processing` hoặc `cancelled` **tuyệt đối không** được cộng dồn vào thẻ này.
* **Thẻ Đơn Hàng Mới**: Hiển thị chính xác số đơn hàng có trạng thái `pending`.
* **Thẻ Sản Phẩm**: Tổng số sản phẩm đang có trong kho.
* **Thẻ Tổng Khách Hàng**: Tổng số tài khoản khách hàng (`role = 'customer'`).

### Kịch bản 2.3: Biểu Đồ & Cảnh Báo Tồn Kho
* **Biểu đồ Chart.js**: Hiển thị đường xu hướng doanh thu 7 ngày gần nhất, rê chuột hiển thị tooltip số tiền USD.
* **Bảng Cảnh Báo Tồn Kho**: Hiển thị các sản phẩm có `stock < 10` với số lượng cảnh báo màu vàng/đỏ.

### Kịch bản 2.4: Sidebar Menu Đầy Đủ & Badge Đếm
* Kiểm tra thanh Sidebar bên trái có đầy đủ các phân hệ:
  * **Bán Hàng & Kho**: Đơn Hàng (kèm badge đỏ số đơn chờ), Sản Phẩm, Danh Mục, Mã Giảm Giá.
  * **Khách Hàng & Phản Hồi**: Khách Hàng, Đánh Giá SP (kèm badge vàng số review chờ duyệt), Hộp Thư Liên Hệ (kèm badge đỏ số thư mới).
  * **Nội Dung**: Banner Slider, Bài Viết & Recipes.
  * **Hệ Thống**: Cài Đặt Website, Xem Website, Đăng Xuất.

### Kịch bản 2.5: Trang Hồ Sơ Admin & Đổi Mật Khẩu
1. Bấm vào Avatar góc trên cùng bên phải $\rightarrow$ Chọn **"Hồ Sơ & Mật Khẩu"** (`/admin/profile`).
2. Sửa Tên, Số điện thoại $\rightarrow$ Bấm **"Lưu Cập Nhật"** $\rightarrow$ Thông báo thành công và hiển thị thông tin mới.
3. Thử đổi mật khẩu: Nhập sai mật khẩu cũ $\rightarrow$ Báo lỗi. Nhập đúng mật khẩu cũ + mật khẩu mới $\rightarrow$ Thông báo đổi mật khẩu thành công.

---

## 🛠️ 4. Kịch Bản Test Giai Đoạn 3: CRUD Admin Toàn Bộ Module

### 📦 3b. Quản Lý Sản Phẩm (+ Soft Delete & Thùng Rác)
* **URL**: `/admin/products`
1. **Xóa mềm sản phẩm**:
   * Bấm nút **Thùng rác** trên 1 sản phẩm $\rightarrow$ Xác nhận xóa.
   * Sản phẩm biến mất khỏi tab *"Tất Cả Sản Phẩm"*.
2. **Khôi phục từ Thùng rác**:
   * Bấm vào tab **"Thùng Rác"** (có badge số lượng màu đỏ).
   * Thấy sản phẩm vừa xóa tại đây.
   * Bấm nút **"Khôi phục"** $\rightarrow$ Sản phẩm quay trở lại tab *"Tất Cả Sản Phẩm"*.
3. **Bảo toàn dữ liệu**: Đơn hàng cũ có chứa sản phẩm đã xóa mềm vẫn hiển thị đầy đủ tên, ảnh và giá lịch sử.

---

### 🛒 3d. Quản Lý Đơn Hàng (+ Vòng Đời, Hoàn Kho & In Hóa Đơn)
* **URL**: `/admin/orders`
1. **Chuyển trạng thái hợp lệ**:
   * Mở chi tiết 1 đơn hàng `pending` $\rightarrow$ Dropdown trạng thái chỉ hiển thị: `Confirmed` và `Cancelled`.
   * Chọn `Confirmed` $\rightarrow$ Bấm **Lưu Cập Nhật** $\rightarrow$ Đơn chuyển thành công.
2. **Chặn trạng thái kết thúc**:
   * Khi đơn đã chuyển sang `Completed` hoặc `Cancelled` $\rightarrow$ Dropdown trạng thái tự động bị khóa, thông báo đây là trạng thái kết thúc.
3. **Quy tắc Hoàn Kho & Hoàn Coupon khi HỦY ĐƠN**:
   * Mở 1 đơn hàng đang có mã giảm giá và sản phẩm.
   * Chuyển trạng thái sang `Cancelled`.
   * Kiểm tra: Số lượng tồn kho của sản phẩm được **tự động cộng trả lại**, lượt sử dụng `used_count` của coupon được **trừ trả lại**.
4. **Xác nhận thanh toán chuyển khoản**:
   * Đối với đơn chuyển khoản ngân hàng chưa thanh toán $\rightarrow$ Bấm nút xanh **"Xác Nhận Đã Nhận Tiền"** $\rightarrow$ Đơn chuyển sang `paid`, tự động ghi nhận thời gian `paid_at`.
5. **In hóa đơn bán hàng**:
   * Bấm nút **"In Hóa Đơn"** $\rightarrow$ Mở tab hóa đơn chuẩn in ấn `/admin/orders/{id}/print`.

---

### 👥 3e. Quản Lý Khách Hàng (Customer Management)
* **URL**: `/admin/customers`
1. **Xem danh sách & Tổng chi tiêu**: Hiển thị số đơn hàng đã mua và tổng tiền đã thanh toán của từng khách.
2. **Xem chi tiết**: Bấm biểu tượng con mắt $\rightarrow$ Xem lịch sử toàn bộ các đơn hàng của riêng khách đó.
3. **Khóa tài khoản**:
   * Bấm nút biểu tượng **Ổ khóa** tại một khách hàng $\rightarrow$ Trạng thái chuyển thành *"Đã khóa"*.
   * Dùng tài khoản đó đăng nhập $\rightarrow$ Hệ thống từ chối đăng nhập với thông báo rõ ràng.
   * Bấm nút mở khóa $\rightarrow$ Khách có thể đăng nhập lại bình thường.

---

### ✉️ 3f. Quản Lý Hộp Thư Liên Hệ (Inquiries)
* **URL**: `/admin/inquiries`
1. **Thống kê 3 thẻ**: Thư mới, Đang liên hệ, Đã giải quyết.
2. **Chi tiết & Phản hồi**:
   * Bấm vào xem thư từ khách $\rightarrow$ Đọc nội dung.
   * Chọn trạng thái `Đã giải quyết`, nhập ghi chú phản hồi vào ô `admin_reply` $\rightarrow$ Bấm **"Lưu Xử Lý"**.
3. Badge đếm thư mới trên Sidebar tự động giảm đi khi thư được xử lý.

---

### 🖼️ 3g. Quản Lý Banner Quảng Cáo (Banners)
* **URL**: `/admin/banners`
1. **Thêm Banner mới**:
   * Bấm **"Thêm Banner Mới"** $\rightarrow$ Nhập Tiêu đề, chọn Vị trí (`Hero Slider` / `Promo Box` / `Sidebar`), chọn ảnh tải lên, nhập chữ nút CTA và link.
   * Bấm Lưu $\rightarrow$ Banner hiển thị trong danh sách kèm ảnh thumbnail preview.
2. **Bật / Tắt nhanh**: Bấm vào nút gạt toggle $\rightarrow$ Chuyển đổi trạng thái Ẩn / Hiển thị tức thì.

---

### 🎟️ 3h. Quản Lý Mã Giảm Giá (Coupons)
* **URL**: `/admin/coupons`
1. **Tạo mã mới**:
   * Bấm **"Tạo Mã Giảm Giá Mới"**.
   * Nhập mã: `SUMMER20` (hệ thống tự viết hoa).
   * Chọn loại: Giảm % (VD: 20%) hoặc Số tiền cố định (VD: $10).
   * Đặt đơn hàng tối thiểu, giới hạn số lượt dùng, ngày bắt đầu và kết thúc.
2. **Bật / Tắt mã**: Bấm toggle để kích hoạt hoặc tạm dừng mã.

---

### ⭐ 3i. Quản Lý Đánh Giá (Reviews & Tự Tính Điểm SP)
* **URL**: `/admin/reviews`
1. **Duyệt đánh giá**:
   * Tìm đánh giá đang ở trạng thái `Chờ duyệt` (màu vàng).
   * Bấm nút xanh **"Duyệt"** $\rightarrow$ Đánh giá chuyển sang `Đã duyệt`.
   * **Kiểm tra**: Điểm đánh giá trung bình `avg_rating` của sản phẩm đó trong kho được **tự động tính toán lại**.
2. **Ẩn / Xóa đánh giá**: Bấm **Ẩn** hoặc **Xóa** $\rightarrow$ `avg_rating` của sản phẩm cũng được tự động cập nhật lại ngay lập tức.

---

### ⚙️ 3j. Cài Đặt Website (Settings)
* **URL**: `/admin/settings`
1. **Tab 1: Cài đặt chung & Hotline**:
   * Đổi Tên cửa hàng, Slogan, Hotline, Email, Địa chỉ, tải Logo mới.
   * Bấm **Lưu** $\rightarrow$ Dữ liệu được ghi đè và xóa cache 24h tự động.
2. **Tab 2: Tài khoản chuyển khoản & Phí Ship**:
   * Cập nhật Tên ngân hàng, Số tài khoản, Chủ tài khoản, Phí ship mặc định, Đơn tối thiểu để freeship.
   * Thông tin này sẽ được dùng để hiển thị tự động tại bước Checkout mua hàng.
3. **Tab 3: Mạng xã hội**: Cập nhật link Facebook, Instagram, YouTube, X.

---

## 📝 5. Checklist Tự Đánh Giá Nghiệm Thu

Bạn có thể tick chọn vào các mục dưới đây trong quá trình tự kiểm tra:

- [ ] **Giai đoạn 1**: `php artisan test` chạy xanh 100% (28/28 tests passed).
- [ ] **Giai đoạn 2**: Đăng nhập Admin thành công vào Dashboard.
- [ ] **Giai đoạn 2**: Thẻ Doanh thu chỉ tính đơn `completed` + `paid`.
- [ ] **Giai đoạn 2**: Biểu đồ Chart.js hiển thị mượt mà.
- [ ] **Giai đoạn 2**: Sidebar có đủ 10 phân hệ và có badge số lượng.
- [ ] **Giai đoạn 2**: Đổi mật khẩu admin thành công tại `/admin/profile`.
- [ ] **Giai đoạn 3**: Xóa sản phẩm chuyển vào Thùng rác và khôi phục thành công.
- [ ] **Giai đoạn 3**: Đơn hàng tuân thủ vòng đời; hủy đơn hoàn lại kho và coupon.
- [ ] **Giai đoạn 3**: Nút in hóa đơn hiển thị hóa đơn đẹp, chuẩn in.
- [ ] **Giai đoạn 3**: Khóa khách hàng $\rightarrow$ chặn đăng nhập; mở khóa $\rightarrow$ đăng nhập lại được.
- [ ] **Giai đoạn 3**: Duyệt review $\rightarrow$ `avg_rating` của sản phẩm tự động cập nhật.
- [ ] **Giai đoạn 3**: Thêm banner và coupon hoạt động trơn tru.
- [ ] **Giai đoạn 3**: Cập nhật thông tin ngân hàng trong Cài đặt thành công.

---
*Tài liệu được khởi tạo cho dự án **Avada Vegan Store** — Tác giả: **ChjZung**.*
