<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Đặt Hàng - #{{ $order->order_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            color: #212529;
            padding: 24px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #dee2e6;
            padding: 30px;
            border-radius: 8px;
        }
        .brand-title {
            color: #2d5a27;
            font-weight: 800;
            font-size: 1.6rem;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .invoice-box {
                border: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <!-- Action Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom no-print">
        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
            &larr; Quay lại đơn hàng
        </a>
        <button onclick="window.print()" class="btn btn-sm btn-success">
            In Hóa Đơn Này (Print)
        </button>
    </div>

    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <div class="brand-title">AVADA VEGAN STORE</div>
            <p class="text-muted small mb-0">Cửa hàng thực phẩm & bánh ngọt hữu cơ cao cấp</p>
            <p class="text-muted small mb-0">Website: https://veganstore.vn</p>
        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            <h4 class="fw-bold mb-1">HÓA ĐƠN BÁN HÀNG</h4>
            <div class="text-success fw-bold font-monospace">#{{ $order->order_code }}</div>
            <div class="text-muted small">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <hr>

    <!-- Info Row -->
    <div class="row mb-4">
        <div class="col-sm-6">
            <h6 class="fw-bold text-dark mb-2">THÔNG TIN KHÁCH HÀNG:</h6>
            <div><strong>Họ và tên:</strong> {{ $order->customer_name }}</div>
            <div><strong>Số điện thoại:</strong> {{ $order->customer_phone ?? 'Chưa cập nhật' }}</div>
            <div><strong>Email:</strong> {{ $order->customer_email }}</div>
            <div><strong>Địa chỉ nhận:</strong> {{ $order->shipping_address }}</div>
        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            <h6 class="fw-bold text-dark mb-2">THANH TOÁN & GIAO HÀNG:</h6>
            <div><strong>Hình thức:</strong> {{ strtoupper($order->payment_method) }}</div>
            <div>
                <strong>Trạng thái TT:</strong> 
                {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                @if($order->paid_at)
                    ({{ $order->paid_at->format('d/m/Y H:i') }})
                @endif
            </div>
            <div><strong>Trạng thái đơn:</strong> {{ ucfirst($order->status) }}</div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="table table-bordered mb-4">
        <thead class="table-light">
            <tr>
                <th style="width: 40px;" class="text-center">STT</th>
                <th>Tên Mặt Hàng</th>
                <th class="text-end" style="width: 120px;">Đơn Giá</th>
                <th class="text-center" style="width: 80px;">Số Lượng</th>
                <th class="text-end" style="width: 140px;">Thành Tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <div class="fw-bold">{{ $item->product_name }}</div>
                        @if($item->options)<div class="small">{{ implode(' / ', $item->options) }}</div>@endif
                        @if($item->product && $item->product->product_code)
                            <small class="text-muted">SKU: {{ $item->product->product_code }}</small>
                        @endif
                    </td>
                    <td class="text-end">${{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="text-center fw-bold">{{ $item->quantity }}</td>
                    <td class="text-end fw-bold">${{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-end fw-semibold">Tạm tính:</td>
                <td class="text-end fw-bold">${{ number_format((float) $order->subtotal, 2) }}</td>
            </tr>
            @if($order->discount_amount > 0)
                <tr>
                    <td colspan="4" class="text-end text-danger fw-semibold">
                        Giảm giá khuyến mãi ({{ $order->coupon_code }}):
                    </td>
                    <td class="text-end text-danger fw-bold">-${{ number_format((float) $order->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="4" class="text-end fw-semibold">Phí vận chuyển:</td>
                <td class="text-end fw-bold">${{ number_format((float) $order->shipping_fee, 2) }}</td>
            </tr>
            <tr class="table-light">
                <td colspan="4" class="text-end fw-bold fs-6">TỔNG THANH TOÁN:</td>
                <td class="text-end fw-bold text-success fs-5">${{ number_format((float) $order->total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($order->customer_note)
        <div class="mb-3">
            <span class="fw-bold">Ghi chú của khách:</span>
            <span class="fst-italic text-secondary">{{ $order->customer_note }}</span>
        </div>
    @endif

    <div class="text-center text-muted small mt-5 pt-3 border-top">
        Cảm ơn quý khách đã tin tưởng và đồng hành cùng thực phẩm thuần chay Avada Vegan Store!<br>
        Mọi thắc mắc xin vui lòng liên hệ hotline hỗ trợ.
    </div>
</div>

</body>
</html>
