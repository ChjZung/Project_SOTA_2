@extends('admin.layouts.app')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->order_code)

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0">Đơn Hàng #{{ $order->order_code }}</h4>
            {!! $order->status_badge !!}
            @if($order->payment_status === 'paid')
                <span class="badge bg-success-subtle text-success">
                    <i class="bi bi-check-circle-fill me-1"></i>Đã thanh toán ({{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : 'Đã xác nhận' }})
                </span>
            @else
                <span class="badge bg-warning-subtle text-warning">
                    <i class="bi bi-hourglass-split me-1"></i>Chờ thanh toán
                </span>
            @endif
        </div>
        <p class="text-muted small mb-0 mt-1">
            Đặt lúc {{ $order->created_at->format('d/m/Y \l\ú\c H:i:s') }}
            @if($order->idempotency_token)
                <span class="ms-2 font-monospace" style="font-size: 0.72rem;">Token: {{ substr($order->idempotency_token, 0, 8) }}...</span>
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        @if($order->payment_status !== 'paid' && $order->status !== 'cancelled')
            <form action="{{ route('admin.orders.confirm-payment', $order) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Xác nhận đã nhận đủ tiền thanh toán cho đơn hàng này?');">
                    <i class="bi bi-cash-stack me-1"></i> Xác Nhận Đã Nhận Tiền
                </button>
            </form>
        @endif
        <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="btn btn-outline-dark">
            <i class="bi bi-printer me-1"></i> In Hóa Đơn
        </a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay Lại
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Cột Trái: Danh sách sản phẩm & Thông tin giao hàng -->
    <div class="col-lg-8">
        <!-- Danh sách mặt hàng -->
        <div class="admin-card mb-4">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-basket-fill text-success"></i> Danh Sách Sản Phẩm Đặt Mua
            </h5>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Ảnh</th>
                            <th>Tên Sản Phẩm</th>
                            <th>Đơn Giá</th>
                            <th class="text-center">Số Lượng</th>
                            <th class="text-end">Thành Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    @if($item->product_image)
                                        <img src="{{ asset(ltrim($item->product_image, '/')) }}" alt="{{ $item->product_name }}" class="rounded-2 object-fit-cover" width="48" height="48">
                                    @elseif($item->product && $item->product->image_url)
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="rounded-2 object-fit-cover" width="48" height="48">
                                    @else
                                        <div class="rounded-2 bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                    @if($item->product)
                                        <div class="text-muted small">
                                            Mã: <span class="badge bg-light text-secondary border">{{ $item->product->product_code ?? 'VGN-'.$item->product->id }}</span>
                                            <span class="ms-1">Kho hiện tại: {{ $item->product->stock }} chiếc</span>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary small">Sản phẩm gốc đã ngừng kinh doanh</span>
                                    @endif
                                </td>
                                <td>${{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                <td class="text-end fw-bold text-success">${{ number_format((float) $item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-semibold">Tạm tính hàng:</td>
                            <td class="text-end fw-bold">${{ number_format((float) $order->subtotal, 2) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="4" class="text-end text-danger fw-semibold">
                                    Mã giảm giá áp dụng ({{ $order->coupon_code ?? 'Voucher' }}):
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
            </div>
        </div>

        <!-- Thông tin Người Nhận & Giao Hàng -->
        <div class="admin-card">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-truck text-success"></i> Thông Tin Giao Nhận & Khách Hàng
            </h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small">Họ và tên người nhận:</div>
                    <div class="fw-bold text-dark fs-6">{{ $order->customer_name }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Số điện thoại liên hệ:</div>
                    <div class="fw-bold text-dark fs-6">{{ $order->customer_phone ?? 'Chưa cập nhật' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Email:</div>
                    <div class="fw-semibold text-dark">{{ $order->customer_email }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Hình thức thanh toán:</div>
                    <div class="fw-semibold text-dark text-uppercase">
                        {{ $order->payment_method === 'cod' ? 'COD (Thanh toán khi nhận hàng)' : 'Chuyển khoản ngân hàng' }}
                    </div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Địa chỉ nhận hàng:</div>
                    <div class="fw-semibold text-dark p-3 bg-light rounded-2 border">
                        <i class="bi bi-geo-alt text-danger me-1"></i> {{ $order->shipping_address }}
                    </div>
                </div>
                @if($order->customer_note)
                    <div class="col-12">
                        <div class="text-muted small">Ghi chú của khách hàng:</div>
                        <div class="fst-italic text-secondary p-3 bg-light rounded-2 border">
                            "{{ $order->customer_note }}"
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Cột Phải: Cập nhật Trạng Thái Tuân Thủ Vòng Đời -->
    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-arrow-repeat text-success"></i> Quản Lý Trạng Thái
            </h5>

            @if(in_array($order->status, ['completed', 'cancelled'], true))
                <div class="alert {{ $order->status === 'completed' ? 'alert-success' : 'alert-danger' }} small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Đơn hàng đã ở trạng thái <strong>{{ strtoupper($order->status) }}</strong> (Trạng thái kết thúc). Không thể chuyển tiếp trạng thái theo quy định nghiệp vụ.
                </div>
            @endif

            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Trạng thái đơn hàng</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" {{ in_array($order->status, ['completed', 'cancelled'], true) ? 'disabled' : '' }}>
                        <option value="{{ $order->status }}" selected>
                            {{ ucfirst($order->status) }} (Hiện tại)
                        </option>
                        @php
                            $allowedNext = \App\Models\Order::VALID_TRANSITIONS[$order->status] ?? [];
                        @endphp
                        @foreach($allowedNext as $next)
                            <option value="{{ $next }}">
                                &rarr; Chuyển sang: {{ ucfirst($next) }}
                            </option>
                        @endforeach
                    </select>
                    @if(in_array($order->status, ['completed', 'cancelled'], true))
                        <input type="hidden" name="status" value="{{ $order->status }}">
                    @endif
                    <small class="text-muted" style="font-size: 0.75rem;">
                        Chỉ hiển thị các bước chuyển tiếp hợp lệ theo sơ đồ vòng đời.
                    </small>
                </div>

                <div class="mb-3">
                    <label for="payment_status" class="form-label fw-semibold">Trạng thái thanh toán</label>
                    <select name="payment_status" id="payment_status" class="form-select">
                        <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán (Paid)</option>
                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền (Refunded)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="admin_note" class="form-label fw-semibold">Ghi chú nội bộ Admin</label>
                    <textarea name="admin_note" id="admin_note" rows="3" class="form-control" placeholder="Ghi chú riêng của nhân viên quản trị (khách hàng không thấy)...">{{ old('admin_note', $order->admin_note) }}</textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="bi bi-save me-1"></i> Cập Nhật Đơn Hàng
                    </button>
                </div>
            </form>
        </div>

        <div class="admin-card">
            <h6 class="fw-bold text-danger mb-2">Xóa Đơn Hàng</h6>
            <p class="text-muted small mb-3">Chỉ dùng cho đơn rác thử nghiệm hoặc đơn đã hủy hoàn toàn:</p>
            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn đơn hàng này cùng toàn bộ chi tiết mặt hàng?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                    <i class="bi bi-trash me-1"></i> Xóa Đơn Hàng Này
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
