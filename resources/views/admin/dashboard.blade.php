@extends('admin.layouts.app')

@section('title', 'Bảng Điều Khiển')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Bảng Điều Khiển Quản Trị</h4>
        <p class="text-muted small mb-0">Thống kê hoạt động kinh doanh và trạng thái hệ thống của Avada Vegan Store</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.create') }}" class="btn btn-admin-primary">
            <i class="bi bi-plus-lg me-1"></i> Thêm Sản Phẩm Mới
        </a>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-outline-success">
            <i class="bi bi-folder-plus me-1"></i> Thêm Danh Mục
        </a>
    </div>
</div>

<!-- Stats Metric Cards -->
<div class="row g-3 mb-4">
    <!-- Doanh thu -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="admin-card h-100 p-3 p-xl-4 mb-0 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-success-subtle text-success fs-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Doanh Thu Đã Thu</span>
                <h4 class="fw-bold mb-0 text-dark">${{ number_format($totalRevenue, 2) }}</h4>
            </div>
        </div>
    </div>

    <!-- Tổng đơn hàng -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="admin-card h-100 p-3 p-xl-4 mb-0 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-bag-check"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Tổng Đơn Hàng</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $totalOrders }}</h4>
                @if($pendingOrders > 0)
                    <span class="badge bg-warning-subtle text-warning fw-bold small">{{ $pendingOrders }} chờ xử lý</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Sản phẩm -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="admin-card h-100 p-3 p-xl-4 mb-0 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-info-subtle text-info fs-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Sản Phẩm Trong Kho</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $totalProducts }}</h4>
                <span class="text-muted small">{{ $totalCategories }} danh mục</span>
            </div>
        </div>
    </div>

    <!-- Bài viết -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="admin-card h-100 p-3 p-xl-4 mb-0 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-warning-subtle text-warning fs-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-newspaper"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Bài Viết & Recipes</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $totalPosts }}</h4>
                <span class="text-muted small">Cập nhật tin tức</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders Table -->
    <div class="col-12 col-xl-8">
        <div class="admin-card mb-0">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Đơn Hàng Gần Đây</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light fw-semibold text-success">
                    Xem tất cả &rarr;
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>Tổng Tiền</th>
                            <th>Thanh Toán</th>
                            <th>Trạng Thái</th>
                            <th>Thời Gian</th>
                            <th class="text-end">Chi Tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="fw-bold text-success">#{{ $order->order_number }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $order->customer_name }}</div>
                                    <div class="text-muted small">{{ $order->customer_phone ?? $order->customer_email }}</div>
                                </td>
                                <td class="fw-bold">${{ number_format((float) $order->total_amount, 2) }}</td>
                                <td>
                                    @if($order->payment_status === 'paid')
                                        <span class="badge bg-success-subtle text-success">Đã trả</span>
                                    @elseif($order->payment_status === 'refunded')
                                        <span class="badge bg-danger-subtle text-danger">Hoàn tiền</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">Chờ trả</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->status === 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @elseif($order->status === 'processing')
                                        <span class="badge bg-primary">Đang giao</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="badge bg-danger">Đã hủy</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Chưa có đơn hàng nào được ghi nhận.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Latest Products & Quick Shortcuts -->
    <div class="col-12 col-xl-4">
        <!-- Latest Products -->
        <div class="admin-card mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Sản Phẩm Mới Thêm</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-light fw-semibold text-success">
                    Xem kho &rarr;
                </a>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($latestProducts as $prod)
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="rounded-2 object-fit-cover" width="48" height="48">
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="mb-0 text-truncate fw-semibold" style="max-width: 190px;">
                                <a href="{{ route('admin.products.edit', $prod) }}" class="text-dark text-decoration-none">
                                    {{ $prod->name }}
                                </a>
                            </h6>
                            <span class="text-muted small">{{ $prod->category->name ?? 'Chưa phân loại' }}</span>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-success">{{ $prod->formatted_price }}</div>
                            <span class="badge bg-light text-secondary border">Kho: {{ $prod->stock }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small text-center my-3">Chưa có sản phẩm.</p>
                @endforelse
            </div>
        </div>

        <!-- Quick Info Box -->
        <div class="admin-card bg-light border-0">
            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle text-success me-1"></i> Thông Tin Vận Hành</h6>
            <p class="text-muted small mb-3">Hệ thống đồng bộ trực tiếp với CSDL MySQL `vegan_store_db`. Khi bạn thêm/sửa sản phẩm hoặc danh mục tại đây, giao diện người dùng trên web sẽ cập nhật ngay lập tức.</p>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-admin-primary">
                    <i class="bi bi-plus-circle me-1"></i> Đăng Sản Phẩm Mới
                </a>
                <a href="{{ route('admin.posts.create') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil-square me-1"></i> Viết Bài Tin Tức / Công Thức
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
