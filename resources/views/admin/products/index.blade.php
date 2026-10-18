@extends('admin.layouts.app')

@section('title', 'Quản Lý Sản Phẩm')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Quản Lý Sản Phẩm</h4>
        <p class="text-muted small mb-0">Quản lý kho hàng, giá bán và hình ảnh sản phẩm Vegan Store</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.create') }}" class="btn btn-admin-primary">
            <i class="bi bi-plus-lg me-1"></i> Thêm Sản Phẩm Mới
        </a>
    </div>
</div>

<div class="admin-card">
    <!-- Filter Tabs (Active vs Thùng rác) -->
    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
        <ul class="nav nav-pills gap-2">
            <li class="nav-item">
                <a class="nav-link {{ request('filter') !== 'trashed' ? 'active bg-success' : 'text-dark bg-light' }}" href="{{ route('admin.products.index') }}">
                    <i class="bi bi-box-seam me-1"></i> Tất Cả Sản Phẩm
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') === 'trashed' ? 'active bg-danger' : 'text-danger bg-danger-subtle' }}" href="{{ route('admin.products.index', ['filter' => 'trashed']) }}">
                    <i class="bi bi-trash3 me-1"></i> Thùng Rác
                    @if($trashedCount > 0)
                        <span class="badge bg-white text-danger ms-1">{{ $trashedCount }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    @if(request('filter') === 'trashed')
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
                <strong>Chế độ Thùng rác (Soft Delete):</strong> Các sản phẩm dưới đây đã bị xóa mềm, không hiển thị trên giao diện cửa hàng nhưng vẫn được lưu vết trong các đơn hàng cũ. Bạn có thể khôi phục lại bất kỳ lúc nào.
            </div>
        </div>
    @endif

    <!-- Filter and Search Form -->
    <form action="{{ route('admin.products.index') }}" method="GET" class="row g-2 mb-3">
        @if(request('filter') === 'trashed')
            <input type="hidden" name="filter" value="trashed">
        @endif

        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Tìm tên, mã sản phẩm, slug..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-select">
                <option value="">-- Tất cả danh mục --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @if(request('filter') !== 'trashed')
            <div class="col-md-3">
                <select name="filter" class="form-select">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="featured" {{ request('filter') === 'featured' ? 'selected' : '' }}>Sản phẩm nổi bật (Featured)</option>
                    <option value="deal" {{ request('filter') === 'deal' ? 'selected' : '' }}>Khuyến mãi (Hot Deals)</option>
                    <option value="out_of_stock" {{ request('filter') === 'out_of_stock' ? 'selected' : '' }}>Hết hàng (Tồn = 0)</option>
                    <option value="inactive" {{ request('filter') === 'inactive' ? 'selected' : '' }}>Đang ẩn</option>
                </select>
            </div>
        @endif
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-outline-success w-100">
                <i class="bi bi-filter me-1"></i> Lọc
            </button>
            @if(request()->hasAny(['search', 'category_id']) || (request('filter') && request('filter') !== 'trashed'))
                <a href="{{ request('filter') === 'trashed' ? route('admin.products.index', ['filter' => 'trashed']) : route('admin.products.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th style="width: 70px;">Ảnh</th>
                    <th>Sản Phẩm & Mã</th>
                    <th>Danh Mục</th>
                    <th>Giá Bán</th>
                    <th>Tồn Kho</th>
                    <th>Nhãn</th>
                    <th>Hiển Thị</th>
                    <th class="text-end" style="width: 160px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="{{ $product->trashed() ? 'table-light text-muted' : '' }}">
                        <td>
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-2 object-fit-cover" width="52" height="52">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">
                                @if(!$product->trashed())
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-dark text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                @else
                                    <span class="text-muted text-decoration-line-through">{{ $product->name }}</span>
                                @endif
                            </div>
                            <div class="text-muted small">
                                Mã: <span class="badge bg-light text-secondary border">{{ $product->product_code ?? 'VGN-'.$product->id }}</span>
                                <span class="ms-2">Slug: <code>{{ $product->slug }}</code></span>
                                @if($product->trashed())
                                    <span class="badge bg-danger ms-2">Đã xóa mềm: {{ $product->deleted_at->format('d/m/Y H:i') }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $product->category->name ?? 'Chưa phân loại' }}
                            </span>
                        </td>
                        <td>
                            @if($product->sale_price)
                                <div class="text-success fw-bold">{{ $product->formatted_sale_price }}</div>
                                <div class="text-decoration-line-through text-muted small">{{ $product->formatted_price }}</div>
                            @else
                                <div class="fw-bold text-dark">{{ $product->formatted_price }}</div>
                            @endif
                        </td>
                        <td>
                            @if($product->stock > 10)
                                <span class="badge bg-success-subtle text-success fw-bold">{{ $product->stock }} chiếc</span>
                            @elseif($product->stock > 0)
                                <span class="badge bg-warning-subtle text-warning fw-bold">{{ $product->stock }} chiếc</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fw-bold">Hết hàng</span>
                            @endif
                        </td>
                        <td>
                            @if($product->is_featured)
                                <span class="badge bg-primary-subtle text-primary small d-inline-block mb-1">Nổi bật</span>
                            @endif
                            @if($product->is_deal)
                                <span class="badge bg-danger-subtle text-danger small d-inline-block">Hot Deal</span>
                            @endif
                            @if(!$product->is_featured && !$product->is_deal)
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if(!$product->trashed())
                                <form action="{{ route('admin.products.toggle', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm border-0 bg-transparent p-0" title="Bấm để bật/tắt hiển thị">
                                        @if($product->is_active)
                                            <i class="bi bi-toggle-on text-success fs-3"></i>
                                        @else
                                            <i class="bi bi-toggle-off text-muted fs-3"></i>
                                        @endif
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-secondary">Vô hiệu</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($product->trashed())
                                <div class="btn-group">
                                    <form action="{{ route('admin.products.restore', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Khôi phục sản phẩm">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Khôi phục
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.products.force-delete', $product->id) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Xóa vĩnh viễn không thể khôi phục lại! Bạn có chắc không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger ms-1" title="Xóa vĩnh viễn">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="btn-group">
                                    <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Xem trên web">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này vào thùng rác?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa mềm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            {{ request('filter') === 'trashed' ? 'Thùng rác trống. Không có sản phẩm nào bị xóa.' : 'Không tìm thấy sản phẩm nào phù hợp.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
