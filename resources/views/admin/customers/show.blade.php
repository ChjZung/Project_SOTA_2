@extends('admin.layouts.app')

@section('title', 'Khách Hàng: ' . $customer->name)

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0">{{ $customer->name }}</h4>
            @if($customer->is_active)
                <span class="badge bg-success-subtle text-success">Đang hoạt động</span>
            @else
                <span class="badge bg-danger-subtle text-danger">Tài khoản bị khóa</span>
            @endif
        </div>
        <p class="text-muted small mb-0 mt-1">Đăng ký ngày {{ $customer->created_at ? $customer->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('admin.customers.toggle', $customer->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn {{ $customer->is_active ? 'btn-outline-danger' : 'btn-success' }}" onclick="return confirm('Bạn có chắc chắn muốn {{ $customer->is_active ? 'KHÓA' : 'MỞ KHÓA' }} tài khoản này?');">
                <i class="bi {{ $customer->is_active ? 'bi-lock' : 'bi-unlock' }} me-1"></i>
                {{ $customer->is_active ? 'Khóa Tài Khoản' : 'Mở Khóa Tài Khoản' }}
            </button>
        </form>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Cột Thông tin khách -->
    <div class="col-12 col-lg-4">
        <div class="admin-card text-center p-4">
            @if($customer->avatar)
                <img src="{{ asset(ltrim($customer->avatar, '/')) }}" alt="{{ $customer->name }}" class="rounded-circle mb-3 shadow-sm" width="100" height="100" style="object-fit: cover;">
            @else
                <div class="rounded-circle mx-auto mb-3 bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px; font-size: 2.5rem;">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
            @endif

            <h5 class="fw-bold mb-1">{{ $customer->name }}</h5>
            <p class="text-muted small mb-3">{{ $customer->email }}</p>

            <div class="row g-2 text-center border-top border-bottom py-3 mb-3">
                <div class="col-6 border-end">
                    <span class="text-muted small d-block">Tổng Đơn</span>
                    <span class="fw-bold text-dark fs-5">{{ $customer->orders_count }}</span>
                </div>
                <div class="col-6">
                    <span class="text-muted small d-block">Tổng Chi Tiêu</span>
                    <span class="fw-bold text-success fs-5">${{ number_format((float) ($customer->total_spent ?? 0), 2) }}</span>
                </div>
            </div>

            <div class="text-start small">
                <div class="py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-telephone me-2"></i>Điện thoại:</span>
                    <span class="fw-semibold text-dark">{{ $customer->phone ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-geo-alt me-2"></i>Địa chỉ:</span>
                    <span class="fw-semibold text-dark">{{ $customer->address ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="py-2">
                    <span class="text-muted"><i class="bi bi-shield-check me-2"></i>Trạng thái:</span>
                    <span class="fw-bold {{ $customer->is_active ? 'text-success' : 'text-danger' }}">
                        {{ $customer->is_active ? 'Được phép đăng nhập' : 'Bị vô hiệu hóa đăng nhập' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột Lịch sử đơn hàng của khách -->
    <div class="col-12 col-lg-8">
        <div class="admin-card mb-0">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-bag-check-fill text-success"></i> Lịch Sử Đơn Hàng Của Khách ({{ $customer->orders->count() }})
            </h5>

            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Ngày Đặt</th>
                            <th class="text-center">Số SP</th>
                            <th>Tổng Tiền</th>
                            <th>Thanh Toán</th>
                            <th>Trạng Thái</th>
                            <th class="text-end">Chi Tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->orders as $order)
                            <tr>
                                <td class="fw-bold text-success font-monospace">#{{ $order->order_code }}</td>
                                <td class="text-muted small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $order->items->count() }} món</span>
                                </td>
                                <td class="fw-bold">${{ number_format((float) $order->total, 2) }}</td>
                                <td>
                                    @if($order->payment_status === 'paid')
                                        <span class="badge bg-success-subtle text-success">Đã trả</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">Chờ trả</span>
                                    @endif
                                </td>
                                <td>{!! $order->status_badge !!}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Khách hàng này chưa có đơn hàng nào trong hệ thống.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
