@extends('admin.layouts.app')

@section('title', 'Quản Lý Banner Quảng Cáo')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Banner & Slider Quảng Cáo</h4>
        <p class="text-muted small mb-0">Quản lý các banner trang chủ, slider hero và các khối khuyến mãi</p>
    </div>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-admin-primary">
        <i class="bi bi-plus-lg me-1"></i> Thêm Banner Mới
    </a>
</div>

<div class="admin-card">
    <!-- Filter -->
    <form action="{{ route('admin.banners.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Tìm theo tiêu đề, phụ đề banner..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="position" class="form-select">
                <option value="">-- Tất cả vị trí --</option>
                <option value="hero" {{ request('position') === 'hero' ? 'selected' : '' }}>Hero Slider (Trang chủ trên cùng)</option>
                <option value="promo" {{ request('position') === 'promo' ? 'selected' : '' }}>Khối Promo (Giữa trang chủ)</option>
                <option value="sidebar" {{ request('position') === 'sidebar' ? 'selected' : '' }}>Sidebar (Cột bên)</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-success w-100">
                <i class="bi bi-filter me-1"></i> Lọc
            </button>
            @if(request()->hasAny(['search', 'position']))
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th style="width: 120px;">Ảnh Preview</th>
                    <th>Tiêu Đề & Phụ Đề</th>
                    <th class="text-center">Vị Trí</th>
                    <th class="text-center">Thứ Tự</th>
                    <th>Nút Bấm & Liên Kết</th>
                    <th class="text-center">Trạng Thái</th>
                    <th class="text-end" style="width: 120px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banners as $banner)
                    <tr>
                        <td>
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="rounded-2 object-fit-cover shadow-sm" width="110" height="55" style="border: 1px solid #e0e0e0;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $banner->title }}</div>
                            <small class="text-muted">{{ $banner->subtitle ?? 'Không có phụ đề' }}</small>
                        </td>
                        <td class="text-center">
                            @if($banner->position === 'hero')
                                <span class="badge bg-primary-subtle text-primary fw-bold">Hero Slider</span>
                            @elseif($banner->position === 'promo')
                                <span class="badge bg-warning-subtle text-warning fw-bold">Promo Box</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary fw-bold">Sidebar</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $banner->sort_order }}</td>
                        <td>
                            @if($banner->button_text)
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-link-45deg me-1"></i>{{ $banner->button_text }}
                                </span>
                                <small class="text-muted d-block text-truncate" style="max-width: 150px;">{{ $banner->button_link }}</small>
                            @else
                                <span class="text-muted small">Không có nút</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm border-0 bg-transparent p-0" title="Bấm để bật/tắt hiển thị">
                                    @if($banner->is_active)
                                        <i class="bi bi-toggle-on text-success fs-3"></i>
                                    @else
                                        <i class="bi bi-toggle-off text-muted fs-3"></i>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary" title="Sửa banner">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa banner này?');">
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
                            Chưa có banner nào trong hệ thống.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($banners->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $banners->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
