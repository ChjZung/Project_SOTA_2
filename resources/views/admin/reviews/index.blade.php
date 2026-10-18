@extends('admin.layouts.app')

@section('title', 'Quản Lý Đánh Giá Sản Phẩm')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Đánh Giá Sản Phẩm</h4>
        <p class="text-muted small mb-0">Kiểm duyệt nhận xét và số sao đánh giá của khách hàng trước khi hiển thị</p>
    </div>
</div>

<!-- Thống kê trạng thái duyệt -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6">
        <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="admin-card mb-0 d-flex align-items-center justify-content-between p-3 {{ request('status') === 'pending' ? 'border-warning bg-warning-subtle' : '' }}">
                <div>
                    <span class="text-muted small d-block">Đánh Giá Chờ Duyệt</span>
                    <h4 class="fw-bold mb-0 text-warning">{{ $stats['pending'] }}</h4>
                </div>
                <div class="fs-2 text-warning"><i class="bi bi-clock-history"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6">
        <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="text-decoration-none">
            <div class="admin-card mb-0 d-flex align-items-center justify-content-between p-3 {{ request('status') === 'approved' ? 'border-success bg-success-subtle' : '' }}">
                <div>
                    <span class="text-muted small d-block">Đánh Giá Đã Duyệt Hiển Thị</span>
                    <h4 class="fw-bold mb-0 text-success">{{ $stats['approved'] }}</h4>
                </div>
                <div class="fs-2 text-success"><i class="bi bi-patch-check-fill"></i></div>
            </div>
        </a>
    </div>
</div>

<div class="admin-card">
    <!-- Filter -->
    <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Tìm theo nội dung, tên khách, sản phẩm..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="product_id" class="form-select">
                <option value="">-- Tất cả sản phẩm --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="rating" class="form-select">
                <option value="">-- Số sao --</option>
                @for($r = 5; $r >= 1; $r--)
                    <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }} Sao ★</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-success w-100">
                <i class="bi bi-filter me-1"></i> Lọc
            </button>
            @if(request()->hasAny(['search', 'product_id', 'rating', 'status']))
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th>Khách Hàng</th>
                    <th>Sản Phẩm</th>
                    <th>Đánh Giá</th>
                    <th>Nhận Xét / Bình Luận</th>
                    <th class="text-center">Trạng Thái</th>
                    <th>Thời Gian</th>
                    <th class="text-end" style="width: 140px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr class="{{ !$review->is_approved ? 'table-warning' : '' }}">
                        <td>
                            <div class="fw-bold text-dark">{{ $review->user->name ?? 'Khách Ẩn Danh' }}</div>
                            <small class="text-muted">{{ $review->user->email ?? '' }}</small>
                        </td>
                        <td>
                            @if($review->product)
                                <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;" title="{{ $review->product->name }}">
                                    <a href="{{ route('product.show', $review->product->slug) }}" target="_blank" class="text-decoration-none text-dark">
                                        {{ $review->product->name }}
                                    </a>
                                </div>
                                <small class="text-muted">Avg: {{ $review->product->avg_rating }} ★</small>
                            @else
                                <span class="badge bg-secondary">Sản phẩm đã xóa</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star text-muted opacity-50"></i>
                                    @endif
                                @endfor
                                <span class="fw-bold text-dark ms-1">({{ $review->rating }}/5)</span>
                            </div>
                        </td>
                        <td>
                            <div class="text-dark" style="max-width: 300px; white-space: normal;">
                                "{{ $review->comment }}"
                            </div>
                        </td>
                        <td class="text-center">
                            @if($review->is_approved)
                                <span class="badge bg-success-subtle text-success fw-semibold">Đã duyệt</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning fw-semibold">Chờ duyệt</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted small">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <form action="{{ route('admin.reviews.toggle', $review) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $review->is_approved ? 'btn-outline-warning' : 'btn-success' }}" title="{{ $review->is_approved ? 'Ẩn đánh giá' : 'Duyệt hiển thị' }}">
                                        @if($review->is_approved)
                                            <i class="bi bi-eye-slash me-1"></i> Ẩn
                                        @else
                                            <i class="bi bi-check-lg me-1"></i> Duyệt
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-1" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Chưa có đánh giá nào được tìm thấy.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reviews->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $reviews->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
