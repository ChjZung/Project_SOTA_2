@extends('admin.layouts.app')

@section('title', 'Quản Lý Khách Hàng')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Quản Lý Khách Hàng</h4>
        <p class="text-muted small mb-0">Danh sách tài khoản khách hàng, tổng đơn mua và trạng thái hoạt động</p>
    </div>
</div>

<div class="admin-card">
    <!-- Filter and Search Form -->
    <form action="{{ route('admin.customers.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, email, số điện thoại khách hàng..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Đã bị khóa</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-success w-100">
                <i class="bi bi-search me-1"></i> Tìm Kiếm
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Xóa Lọc</a>
            @endif
        </div>
    </form>

    <!-- Customers Table -->
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th style="width: 50px;">Avatar</th>
                    <th>Khách Hàng</th>
                    <th>Liên Hệ</th>
                    <th class="text-center">Tổng Đơn</th>
                    <th>Tổng Chi Tiêu</th>
                    <th>Ngày Đăng Ký</th>
                    <th class="text-center">Trạng Thái</th>
                    <th class="text-end" style="width: 140px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>
                            @if($customer->avatar)
                                <img src="{{ asset(ltrim($customer->avatar, '/')) }}" alt="{{ $customer->name }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-dark text-decoration-none">
                                    {{ $customer->name }}
                                </a>
                            </div>
                            <small class="text-muted">{{ $customer->email }}</small>
                        </td>
                        <td>
                            <div><i class="bi bi-telephone text-muted me-1"></i> {{ $customer->phone ?? 'Chưa có SĐT' }}</div>
                            <small class="text-muted text-truncate d-inline-block" style="max-width: 180px;">{{ $customer->address ?? 'Chưa có địa chỉ' }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border fw-bold">{{ $customer->orders_count }} đơn</span>
                        </td>
                        <td>
                            <span class="fw-bold text-success">${{ number_format((float) ($customer->total_spent ?? 0), 2) }}</span>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $customer->created_at ? $customer->created_at->format('d/m/Y') : 'N/A' }}</span>
                        </td>
                        <td class="text-center">
                            @if($customer->is_active)
                                <span class="badge bg-success-subtle text-success fw-semibold">Hoạt động</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fw-semibold">Đã khóa</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết & lịch sử">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.customers.toggle', $customer->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn {{ $customer->is_active ? 'KHÓA' : 'MỞ KHÓA' }} tài khoản khách hàng này?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $customer->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $customer->is_active ? 'Khóa tài khoản' : 'Mở khóa' }}">
                                        <i class="bi {{ $customer->is_active ? 'bi-lock' : 'bi-unlock' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Chưa có khách hàng nào được tìm thấy.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $customers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
