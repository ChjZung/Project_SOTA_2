@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Bài Viết')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Chỉnh Sửa Bài Viết: {{ $post->title }}</h4>
        <p class="text-muted small mb-0">Cập nhật nội dung bài viết chia sẻ</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('recipe.show', $post->slug) }}" target="_blank" class="btn btn-outline-success">
            <i class="bi bi-eye me-1"></i> Xem trên Web
        </a>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>
</div>

<form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card mb-4">
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $post->title) }}" required>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label fw-semibold">Đường dẫn tĩnh (Slug)</label>
                    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $post->slug) }}">
                </div>

                <div class="mb-3">
                    <label for="excerpt" class="form-label fw-semibold">Đoạn tóm tắt mở đầu</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="form-control">{{ old('excerpt', $post->excerpt) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-semibold">Nội dung chi tiết <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" rows="12" class="form-control" required>{{ old('content', $post->content) }}</textarea>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-card mb-4">
                <h5 class="fw-bold mb-3">Phân Loại</h5>

                <div class="mb-3">
                    <label for="category" class="form-label fw-semibold">Chuyên mục</label>
                    <select name="category" id="category" class="form-select">
                        <option value="Recipes" {{ old('category', $post->category) === 'Recipes' ? 'selected' : '' }}>Recipes (Công thức)</option>
                        <option value="News" {{ old('category', $post->category) === 'News' ? 'selected' : '' }}>News (Tin tức)</option>
                        <option value="Lifestyle" {{ old('category', $post->category) === 'Lifestyle' ? 'selected' : '' }}>Lifestyle (Lối sống)</option>
                    </select>
                </div>
            </div>

            <div class="admin-card mb-4">
                <h5 class="fw-bold mb-3">Ảnh Đại Diện</h5>

                <div class="mb-3">
                    <label for="thumbnail" class="form-label fw-semibold">Ảnh đại diện</label>
                    @if($post->thumbnail)
                        <div class="mb-2">
                            <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="rounded-2 border object-fit-cover" width="100%" height="140">
                        </div>
                    @endif
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/*">
                    <div class="form-text small">Chọn file mới nếu muốn thay ảnh.</div>
                </div>
            </div>

            <div class="admin-card mb-4">
                <h5 class="fw-bold mb-3">Xuất Bản</h5>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_published" id="is_published" value="1" {{ old('is_published', $post->is_published) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_published">Xuất bản bài viết</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="bi bi-check-circle me-1"></i> Cập Nhật Bài Viết
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
