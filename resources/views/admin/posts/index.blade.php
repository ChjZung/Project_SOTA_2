@extends('admin.layouts.app')

@section('title', 'Quản Lý Bài Viết & Công Thức')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Quản Lý Bài Viết & Công Thức</h4>
        <p class="text-muted small mb-0">Các bài viết chia sẻ mẹo sống khỏe, công thức món chay thuần thực vật</p>
    </div>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-admin-primary">
        <i class="bi bi-plus-lg me-1"></i> Viết Bài Mới
    </a>
</div>

<div class="admin-card">
    <!-- Filter Bar -->
    <form action="{{ route('admin.posts.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Tìm theo tiêu đề, tóm tắt..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">-- Tất cả chủ đề --</option>
                <option value="Recipes" {{ request('category') === 'Recipes' ? 'selected' : '' }}>Recipes (Công thức nấu)</option>
                <option value="News" {{ request('category') === 'News' ? 'selected' : '' }}>News (Tin tức)</option>
                <option value="Lifestyle" {{ request('category') === 'Lifestyle' ? 'selected' : '' }}>Lifestyle (Lối sống)</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-outline-success w-100">
                <i class="bi bi-search me-1"></i> Lọc
            </button>
            @if(request()->hasAny(['search', 'category']))
                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th style="width: 70px;">Ảnh</th>
                    <th>Tiêu Đề Bài Viết</th>
                    <th>Chuyên Mục</th>
                    <th>Tác Giả</th>
                    <th>Trạng Thái</th>
                    <th>Ngày Đăng</th>
                    <th class="text-end" style="width: 120px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                    <tr>
                        <td>
                            <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="rounded-2 object-fit-cover" width="54" height="42">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-dark text-decoration-none">
                                    {{ $post->title }}
                                </a>
                            </div>
                            <div class="text-muted small">Slug: <code>{{ $post->slug }}</code></div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $post->category ?? 'Recipes' }}</span>
                        </td>
                        <td>
                            <span class="small fw-semibold">{{ $post->author->name ?? 'Admin' }}</span>
                        </td>
                        <td>
                            @if($post->is_published)
                                <span class="badge bg-success-subtle text-success">Đã xuất bản</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">Bản nháp</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $post->created_at->format('d/m/Y') }}
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('recipe.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Xem trên web">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?');">
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
                            Chưa có bài viết nào được đăng tải.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $posts->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
