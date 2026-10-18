@extends('admin.layouts.app')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->order_number)

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0">Đơn Hàng #{{ $order->order_number }}</h4>
            @if($order->status === 'completed')
                <span class="badge bg-success">Hoàn thành</span>
            @elseif($order->status === 'processing')
                <span class="badge bg-primary">Đang giao</span>
            @elseif($order->status === 'cancelled')
                <span class="badge bg-danger">Đã hủy</span>
            @else
                <span class="badge bg-warning text-dark">Chờ xử lý</span>
            @endif
        </div>
        <p class="text-muted small mb-0 mt-1">Đặt lúc {{ $order->created_at->format('d/m/Y \l\ú\c H:i:s') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Order Items & Customer details -->
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="admin-card mb-4">
            <h5 class="fw-bold mb-3">Danh Sách Sản Phẩm Đặt Mua</h5>

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
                                    @if($item->product)
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="rounded-2 object-fit-cover" width="48" height="48">
                                    @else
                                        <div class="rounded-2 bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $item->product_name }}</div>
                                    @if($item->product)
                                        <div class="text-muted small">Mã: {{ $item->product->product_code ?? 'VGN-'.$item->product->id }}</div>
                                    @endif
                                </td>
                                <td>${{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                <td class="text-end fw-bold text-success">${{ number_format((float) $item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-semibold">Tạm tính hàng:</td>
                            <td class="text-end fw-bold">${{ number_format((float) $order->items->sum('subtotal'), 2) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="4" class="text-end text-danger fw-semibold">Giảm giá khuyến mãi:</td>
                                <td class="text-end text-danger fw-bold">-${{ number_format((float) $order->discount_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="text-end fw-semibold">Phí vận chuyển:</td>
                            <td class="text-end fw-bold">${{ number_format((float) $order->shipping_fee, 2) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="4" class="text-end fw-bold fs-6">TỔNG CỘNG:</td>
                            <td class="text-end fw-bold text-success fs-5">${{ number_format((float) $order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Customer & Shipping Information -->
        <div class="admin-card">
            <h5 class="fw-bold mb-3">Thông Tin Người Nhận & Giao Hàng</h5>

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
                    <div class="fw-semibold text-dark text-uppercase">{{ $order->payment_method ?? 'COD (Thanh toán khi nhận hàng)' }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Địa chỉ nhận hàng:</div>
                    <div class="fw-semibold text-dark p-3 bg-light rounded-2 border">
                        <i class="bi bi-geo-alt text-danger me-1"></i> {{ $order->shipping_address ?? 'Địa chỉ mặc định của khách hàng' }}
                    </div>
                </div>
                @if($order->notes)
                    <div class="col-12">
                        <div class="text-muted small">Ghi chú của khách hàng:</div>
                        <div class="fst-italic text-secondary p-3 bg-light rounded-2 border">
                            "{{ $order->notes }}"
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Status Update Form -->
    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <h5 class="fw-bold mb-3">Cập Nhật Trạng Thái Đơn</h5>

            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Trạng thái vận chuyển</label>
                    <select name="status" id="status" class="form-select">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xác nhận (Pending)</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Đang xử lý / Giao hàng (Processing)</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Đã giao thành công (Completed)</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Hủy đơn hàng (Cancelled)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="payment_status" class="form-label fw-semibold">Trạng thái thanh toán</label>
                    <select name="payment_status" id="payment_status" class="form-select">
                        <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán đủ</option>
                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền khách</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label fw-semibold">Ghi chú nội bộ</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Ghi chú về đơn hàng này...">{{ old('notes', $order->notes) }}</textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="bi bi-save me-1"></i> Lưu Cập Nhật
                    </button>
                </div>
            </form>
        </div>

        <div class="admin-card">
            <h6 class="fw-bold text-danger mb-2">Vùng Nguy Hiểm</h6>
            <p class="text-muted small mb-3">Nếu đơn hàng này là đơn rác hoặc khách yêu cầu xóa hoàn toàn khỏi cơ sở dữ liệu:</p>
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
