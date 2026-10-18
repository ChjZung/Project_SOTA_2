@extends('admin.layouts.app')

@section('title', 'Quản Lý Danh Mục')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Quản Lý Danh Mục Sản Phẩm</h4>
        <p class="text-muted small mb-0">Danh sách các danh mục hàng hóa trên hệ thống Vegan Store</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-admin-primary">
        <i class="bi bi-plus-lg me-1"></i> Thêm Danh Mục Mới
    </a>
</div>

<div class="admin-card">
    <!-- Search Bar -->
    <div class="row mb-3">
        <div class="col-md-5">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên danh mục, slug..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-success">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th style="width: 70px;">Ảnh</th>
                    <th>Tên Danh Mục</th>
                    <th>Đường Dẫn (Slug)</th>
                    <th>Số Sản Phẩm</th>
                    <th>Thứ Tự</th>
                    <th class="text-end" style="width: 140px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="rounded-circle object-fit-cover" width="48" height="48">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $category->name }}</div>
                            @if($category->description)
                                <div class="text-muted small text-truncate" style="max-width: 250px;">{{ $category->description }}</div>
                            @endif
                        </td>
                        <td>
                            <code>/product-category/{{ $category->slug }}</code>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success fw-bold px-3 py-2 rounded-pill">
                                {{ $category->products_count }} sản phẩm
                            </span>
                        </td>
                        <td>{{ $category->sort_order }}</td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này không? Các sản phẩm thuộc danh mục sẽ không bị mất mà được chuyển về trạng thái Chưa phân loại.');">
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
                        <td colspan="6" class="text-center py-4 text-muted">
                            Chưa tìm thấy danh mục nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
