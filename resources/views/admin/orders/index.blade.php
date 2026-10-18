@extends('admin.layouts.app')

@section('title', 'Quản Lý Đơn Hàng')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Quản Lý Đơn Hàng</h4>
        <p class="text-muted small mb-0">Theo dõi, duyệt và xử lý giao vận các đơn hàng trực tuyến</p>
    </div>
</div>

<div class="admin-card">
    <!-- Filter and Search Form -->
    <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Tìm theo mã đơn (#VS...), tên, sđt, email..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận (Pending)</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận (Confirmed)</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang chuẩn bị (Processing)</option>
                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Đang giao hàng (Shipped)</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Đã hoàn thành (Completed)</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy (Cancelled)</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="payment_status" class="form-select">
                <option value="">-- Thanh toán --</option>
                <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-outline-success w-100">
                <i class="bi bi-filter me-1"></i> Lọc
            </button>
            @if(request()->hasAny(['search', 'status', 'payment_status']))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th>Mã Đơn</th>
                    <th>Khách Hàng</th>
                    <th>Số Mặt Hàng</th>
                    <th>Tổng Tiền</th>
                    <th>Thanh Toán</th>
                    <th>Trạng Thái Giao</th>
                    <th>Ngày Tạo</th>
                    <th class="text-end" style="width: 100px;">Chi Tiết</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="fw-bold text-success">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-success text-decoration-none">
                                #{{ $order->order_number }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $order->customer_name }}</div>
                            <div class="text-muted small">
                                @if($order->customer_phone)
                                    <i class="bi bi-telephone me-1"></i>{{ $order->customer_phone }}
                                @else
                                    <i class="bi bi-envelope me-1"></i>{{ $order->customer_email }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $order->items->sum('quantity') }} sản phẩm
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-dark fs-6">${{ number_format((float) $order->total_amount, 2) }}</span>
                        </td>
                        <td>
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success-subtle text-success fw-bold">Đã thanh toán</span>
                            @elseif($order->payment_status === 'refunded')
                                <span class="badge bg-danger-subtle text-danger fw-bold">Đã hoàn tiền</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning fw-bold">Chưa trả (COD)</span>
                            @endif
                        </td>
                        <td>
                            {!! $order->status_badge !!}
                        </td>
                        <td class="text-muted small">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-eye me-1"></i> Xem
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Chưa có đơn hàng nào khớp với tìm kiếm.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
