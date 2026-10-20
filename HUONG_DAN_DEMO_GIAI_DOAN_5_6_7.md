# Bản demo giai đoạn 5–7

Đã cập nhật trực tiếp tại `C:\laragon\www\Project_SOTA_2`.

## Mở website

- Website: http://127.0.0.1:8018
- Admin: http://127.0.0.1:8018/admin/login
- Admin: `admin@veganstore.vn` / `Admin@123456`
- Khách mẫu: `lannguyen@gmail.com` / `Customer@123`
- Có thể tự đăng ký tài khoản mới tại `/register`.

Nếu server đã tắt, bật MySQL trong Laragon, mở terminal tại thư mục project và chạy:

```powershell
php artisan serve --host=127.0.0.1 --port=8018
```

## Các phần đã hoàn thành

**Giai đoạn 5:** giỏ hàng AJAX và trang giỏ hàng; cập nhật/xóa số lượng; áp dụng/gỡ mã giảm giá; checkout cho khách vãng lai và khách đăng nhập; COD/chuyển khoản; tính phí giao hàng; trang xác nhận đơn; lưu liên hệ vào admin.

Giá, lựa chọn và tồn kho đều kiểm tra từ database. Tạo đơn dùng transaction, khóa dữ liệu, lưu thông tin sản phẩm tại thời điểm mua và trừ kho. Gửi lại cùng mã đặt hàng chỉ tạo một đơn. Admin hủy đơn sẽ hoàn kho và lượt dùng coupon; chỉ đơn đã hủy mới được xóa.

**Giai đoạn 6:** trang chủ, danh mục, sản phẩm, giá, tồn kho, ảnh, bộ sưu tập ảnh, banner, bài viết, thông tin cửa hàng và footer dùng dữ liệu database. Shop có phân trang, lọc danh mục/giá và sắp xếp. Sản phẩm ngừng hiển thị và bài viết nháp bị ẩn ngoài website. Card dùng chung và dẫn đến chi tiết đúng sản phẩm.

**Giai đoạn 7:** tìm kiếm tên/mô tả; lọc danh mục trong tìm kiếm; trạng thái không có kết quả; đánh giá của khách đăng nhập; admin duyệt đánh giá; tính điểm từ đánh giá đã duyệt. Mỗi khách có một đánh giá cho mỗi sản phẩm; sửa đánh giá sẽ chuyển về chờ duyệt.

Đã bổ sung đăng ký, đăng nhập, đăng xuất, hồ sơ, đổi mật khẩu và lịch sử đơn trong My Account để phục vụ các luồng trên. Hiệu ứng cuộn xuống hiện dần/cuộn lên mờ lại được giữ nguyên.

Theo phạm vi bạn yêu cầu: không cấu hình Gmail/SMTP, không gửi email tự động; không triển khai gói bảo mật nâng cao của giai đoạn 8. Wishlist hiện lưu trên trình duyệt.

## Test nhanh một lượt

1. Đăng nhập khách mẫu hoặc tạo tài khoản. Mở một sản phẩm, chọn lựa chọn nếu có rồi thêm giỏ.
2. Thử 3 **Fresh Organic Cake**: tạm tính **$21.00**. Áp `VEGAN10`: giảm **$2.10**, giao hàng **$5.00**, tổng **$23.90** theo cấu hình hiện tại.
3. Đặt COD hoặc chuyển khoản. Kiểm tra mã đơn, lựa chọn sản phẩm, số tiền và thông tin nhận hàng trên trang xác nhận.
4. Mở admin → Đơn hàng: đơn mới xuất hiện, tồn kho đã giảm. Hủy đơn để kiểm tra hoàn kho; hoặc chuyển lần lượt Chờ xác nhận → Đã xác nhận → Đang chuẩn bị → Đang giao → Hoàn thành. COD hoàn thành tự chuyển thành đã thanh toán.
5. Mở My Account: kiểm tra đơn vừa đặt. Gửi đánh giá một sản phẩm, vào admin → Đánh giá để duyệt, tải lại trang sản phẩm.
6. Sửa tên/giá/ảnh sản phẩm, banner hoặc bài viết trong admin, tải lại website để thấy dữ liệu mới. Bỏ tick hiển thị/xuất bản để kiểm tra ẩn nội dung.
7. Thử tìm kiếm, lọc giá, phân trang và gửi biểu mẫu Liên hệ. Liên hệ xuất hiện trong admin → Liên hệ.

Mã giảm giá đang có:

| Mã | Giảm | Tạm tính tối thiểu |
|---|---:|---:|
| VEGAN10 | 10% | $20 |
| WELCOME5 | $5 | $15 |
| SAVE20 | 20% | $50 |

Phí giao mặc định $5; miễn phí khi giá trị **sau giảm giá** đạt $50. Đổi thông tin ngân hàng, phí giao và liên hệ tại admin → Cài đặt. Chuyển khoản trong bản demo được admin xác nhận thủ công.

## Kết quả kiểm tra

- **61/61 kiểm tra HTTP và trình duyệt thành công**, chạy trên MySQL thử nghiệm riêng, không tạo đơn thử vào database demo chính.
- Bao gồm giá do server tính, tồn kho, coupon, chống gửi trùng, COD/chuyển khoản, quyền xem đơn, hoàn kho, tài khoản, đánh giá và dữ liệu admin cập nhật ra website.
- Giao diện desktop/mobile không tràn ngang trong các trang đã kiểm tra; hiệu ứng cuộn hai chiều hoạt động; không ghi nhận lỗi JavaScript trong các luồng đã chạy.
- PHP lint các file thay đổi, biên dịch Blade và `npm run build` thành công.
- PHPUnit chưa khởi động được trong môi trường công cụ hiện tại: báo `Cannot open bootstrap script ... vendor/autoload.php`. Vì vậy kết quả 61 kiểm tra ở trên là kiểm tra HTTP/trình duyệt, không phải tuyên bố toàn bộ PHPUnit suite đã xanh.

Chi tiết: [phase567-test-results.json](phase567-test-results.json).

Database hiện có 48 sản phẩm đang hiển thị và 3 bài viết đã xuất bản. Catalogue mẫu được nhập vào database; frontend đọc database trực tiếp, vì vậy sửa admin sẽ có hiệu lực khi tải lại trang. Không cần chạy seed lại để demo. Migration bổ sung `products.variations` và `order_items.options` đã được chạy.
