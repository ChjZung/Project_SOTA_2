@extends('admin.layouts.app')

@section('title', 'Quản Lý Mã Giảm Giá')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Mã Giảm Giá & Khuyến Mãi</h4>
        <p class="text-muted small mb-0">Quản lý các mã voucher giảm giá %, số tiền cố định và điều kiện áp dụng</p>
    </div>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-admin-primary">
        <i class="bi bi-plus-lg me-1"></i> Tạo Mã Giảm Giá Mới
    </a>
</div>

<div class="admin-card">
    <!-- Filter -->
    <form action="{{ route('admin.coupons.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Tìm theo mã code (VD: VEGAN10, WELCOME50K)..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">-- Tất cả loại giảm giá --</option>
                <option value="percent" {{ request('type') === 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Giảm số tiền cố định ($)</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-success w-100">
                <i class="bi bi-search me-1"></i> Tìm Kiếm
            </button>
            @if(request()->hasAny(['search', 'type']))
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th>Mã Code</th>
                    <th>Loại & Mức Giảm</th>
                    <th>Đơn Tối Thiểu</th>
                    <th class="text-center">Lượt Đã Dùng / Tối Đa</th>
                    <th>Thời Gian Hiệu Lực</th>
                    <th class="text-center">Trạng Thái</th>
                    <th class="text-end" style="width: 120px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td>
                            <div class="fw-bold font-monospace text-success fs-6">
                                <i class="bi bi-ticket-perforated me-1"></i>{{ $coupon->code }}
                            </div>
                        </td>
                        <td>
                            @if($coupon->type === 'percent')
                                <span class="badge bg-primary-subtle text-primary fw-bold fs-6">
                                    Giảm {{ (float) $coupon->value }}%
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success fw-bold fs-6">
                                    Giảm ${{ number_format((float) $coupon->value, 2) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($coupon->min_order_amount)
                                <span class="fw-semibold text-dark">${{ number_format((float) $coupon->min_order_amount, 2) }}</span>
                            @else
                                <span class="text-muted small">Không giới hạn</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">
                                {{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted d-block">
                                Từ: {{ $coupon->starts_at ? $coupon->starts_at->format('d/m/Y') : 'Ngay bây giờ' }}
                            </small>
                            <small class="text-muted d-block">
                                Đến: {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y') : 'Vô thời hạn' }}
                            </small>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.coupons.toggle', $coupon) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm border-0 bg-transparent p-0" title="Bấm để bật/tắt kích hoạt">
                                    @if($coupon->is_active)
                                        <i class="bi bi-toggle-on text-success fs-3"></i>
                                    @else
                                        <i class="bi bi-toggle-off text-muted fs-3"></i>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary" title="Sửa mã">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Chưa có mã giảm giá nào trong hệ thống.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($coupons->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $coupons->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
