@extends('admin.layouts.app')

@section('title', 'Bảng Điều Khiển')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Bảng Điều Khiển Quản Trị</h4>
        <p class="text-muted small mb-0">Thống kê hoạt động kinh doanh và chỉ số vận hành của Avada Vegan Store</p>
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

<!-- 4 Thẻ Chỉ Số Cốt Lõi (Theo Quy Tắc Nghiệp Vụ) -->
<div class="row g-3 mb-4">
    <!-- 1. Doanh thu (Chỉ đơn completed + paid) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="admin-card h-100 p-3 p-xl-4 mb-0 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-success-subtle text-success fs-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Doanh Thu Đã Thu</span>
                <h4 class="fw-bold mb-0 text-success">${{ number_format($totalRevenue, 2) }}</h4>
                <span class="text-muted" style="font-size: 0.75rem;">Đơn Hoàn thành & Đã thanh toán</span>
            </div>
        </div>
    </div>

    <!-- 2. Đơn hàng mới (Pending) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="admin-card h-100 p-3 p-xl-4 mb-0 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-warning-subtle text-warning fs-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Đơn Hàng Mới</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $pendingOrders }}</h4>
                <span class="text-muted" style="font-size: 0.75rem;">Trên tổng số {{ $totalOrders }} đơn hàng</span>
            </div>
        </div>
    </div>

    <!-- 3. Tổng sản phẩm -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="admin-card h-100 p-3 p-xl-4 mb-0 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Sản Phẩm Trong Kho</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $totalProducts }}</h4>
                <span class="text-muted" style="font-size: 0.75rem;">{{ $totalCategories }} danh mục hoạt động</span>
            </div>
        </div>
    </div>

    <!-- 4. Tổng khách hàng -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="admin-card h-100 p-3 p-xl-4 mb-0 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 bg-info-subtle text-info fs-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Tổng Khách Hàng</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $totalCustomers }}</h4>
                <span class="text-muted" style="font-size: 0.75rem;">Tài khoản khách hàng</span>
            </div>
        </div>
    </div>
</div>

<!-- Biểu đồ & Thống kê trạng thái -->
<div class="row g-4 mb-4">
    <!-- Biểu đồ Doanh thu 7 ngày gần nhất -->
    <div class="col-12 col-xl-8">
        <div class="admin-card mb-0 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Xu Hướng Doanh Thu (7 Ngày Gần Nhất)</h5>
                    <p class="text-muted small mb-0">Chỉ tính từ các đơn hàng thực thu đã hoàn thành</p>
                </div>
                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-graph-up-arrow me-1"></i> Trực Quan Doanh Thu
                </span>
            </div>
            <div style="height: 260px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Trạng Thái Vòng Đời Đơn Hàng -->
    <div class="col-12 col-xl-4">
        <div class="admin-card mb-0 h-100">
            <h5 class="fw-bold mb-3">Trạng Thái Đơn Hàng</h5>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light">
                    <span class="d-flex align-items-center gap-2 small fw-semibold text-warning">
                        <i class="bi bi-hourglass-split fs-6"></i> Chờ Xác Nhận
                    </span>
                    <span class="badge bg-warning text-dark rounded-pill">{{ $orderStats['pending'] }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light">
                    <span class="d-flex align-items-center gap-2 small fw-semibold text-info">
                        <i class="bi bi-check-circle fs-6"></i> Đã Xác Nhận
                    </span>
                    <span class="badge bg-info text-dark rounded-pill">{{ $orderStats['confirmed'] }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light">
                    <span class="d-flex align-items-center gap-2 small fw-semibold text-primary">
                        <i class="bi bi-gear-wide-connected fs-6"></i> Đang Chuẩn Bị
                    </span>
                    <span class="badge bg-primary rounded-pill">{{ $orderStats['processing'] }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light">
                    <span class="d-flex align-items-center gap-2 small fw-semibold" style="color: #6f42c1;">
                        <i class="bi bi-truck fs-6"></i> Đang Giao Hàng
                    </span>
                    <span class="badge rounded-pill text-white" style="background:#6f42c1;">{{ $orderStats['shipped'] }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light">
                    <span class="d-flex align-items-center gap-2 small fw-semibold text-success">
                        <i class="bi bi-check2-all fs-6"></i> Hoàn Thành
                    </span>
                    <span class="badge bg-success rounded-pill">{{ $orderStats['completed'] }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light">
                    <span class="d-flex align-items-center gap-2 small fw-semibold text-danger">
                        <i class="bi bi-x-circle fs-6"></i> Đã Hủy
                    </span>
                    <span class="badge bg-danger rounded-pill">{{ $orderStats['cancelled'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- 5 Đơn Hàng Mới Nhất -->
    <div class="col-12 col-xl-7">
        <div class="admin-card mb-0">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold mb-0">5 Đơn Hàng Mới Nhất</h5>
                    <p class="text-muted small mb-0">Theo dõi dòng giao dịch tức thì</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light fw-semibold text-success">
                    Tất cả đơn &rarr;
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
                            <th class="text-end">Chi Tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="fw-bold text-success font-monospace">#{{ $order->order_code }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $order->customer_name }}</div>
                                    <div class="text-muted small">{{ $order->customer_phone ?? $order->customer_email }}</div>
                                </td>
                                <td class="fw-bold">${{ number_format((float) $order->total, 2) }}</td>
                                <td>
                                    @if($order->payment_status === 'paid')
                                        <span class="badge bg-success-subtle text-success">Đã TT</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">Chưa TT</span>
                                    @endif
                                </td>
                                <td>
                                    {!! $order->status_badge !!}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Chưa có đơn hàng nào được ghi nhận.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sản Phẩm Sắp Hết Hàng (stock < 10) -->
    <div class="col-12 col-xl-5">
        <div class="admin-card mb-0">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-diamond-fill"></i> Cảnh Báo Tồn Kho
                    </h5>
                    <p class="text-muted small mb-0">Sản phẩm có tồn kho dưới 10 đơn vị</p>
                </div>
                <a href="{{ route('admin.products.index', ['filter' => 'out_of_stock']) }}" class="btn btn-sm btn-light fw-semibold text-danger">
                    Kho hàng &rarr;
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Sản Phẩm</th>
                            <th>Danh Mục</th>
                            <th class="text-center">Tồn</th>
                            <th class="text-end">Sửa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockProducts as $lowProduct)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($lowProduct->image)
                                            <img src="{{ asset($lowProduct->image) }}" class="rounded" style="width: 36px; height: 36px; object-fit: cover;" alt="">
                                        @else
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted" style="width: 36px; height: 36px;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold text-truncate" style="max-width: 140px;" title="{{ $lowProduct->name }}">
                                                {{ $lowProduct->name }}
                                            </div>
                                            <small class="text-muted font-monospace">{{ $lowProduct->product_code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary">{{ $lowProduct->category->name ?? 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($lowProduct->stock <= 0)
                                        <span class="badge bg-danger">Hết hàng</span>
                                    @else
                                        <span class="badge bg-warning text-dark fw-bold">{{ $lowProduct->stock }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.products.edit', $lowProduct) }}" class="btn btn-sm btn-light text-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-check-circle text-success me-1"></i> Toàn bộ sản phẩm đều đảm bảo lượng tồn an toàn.
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        const labels = @json($chartLabels);
        const data = @json($chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu ($)',
                    data: data,
                    borderColor: '#2d5a27',
                    backgroundColor: 'rgba(45, 90, 39, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2d5a27',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Doanh thu: $' + Number(context.parsed.y).toLocaleString('en-US', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(val) {
                                return '$' + val;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
