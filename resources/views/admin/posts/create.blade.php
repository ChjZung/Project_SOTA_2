@extends('admin.layouts.app')

@section('title', 'Tạo Bài Viết Mới')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Tạo Bài Viết Mới</h4>
        <p class="text-muted small mb-0">Chia sẻ kiến thức, tin tức dinh dưỡng hoặc công thức món chay mới</p>
    </div>
    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Quay lại
    </a>
</div>

<form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card mb-4">
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="Ví dụ: Top 10 Delicious Vegan Breakfast Recipes" value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label fw-semibold">Đường dẫn tĩnh (Slug)</label>
                    <input type="text" name="slug" id="slug" class="form-control" placeholder="Tự động sinh từ tiêu đề nếu để trống" value="{{ old('slug') }}">
                </div>

                <div class="mb-3">
                    <label for="excerpt" class="form-label fw-semibold">Đoạn tóm tắt mở đầu</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="form-control" placeholder="Tóm tắt ngắn gọn 1-2 câu về nội dung bài viết...">{{ old('excerpt') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-semibold">Nội dung chi tiết <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" rows="12" class="form-control" placeholder="Nội dung bài viết, nguyên liệu và các bước hướng dẫn chế biến..." required>{{ old('content') }}</textarea>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-card mb-4">
                <h5 class="fw-bold mb-3">Phân Loại</h5>

                <div class="mb-3">
                    <label for="category" class="form-label fw-semibold">Chuyên mục</label>
                    <select name="category" id="category" class="form-select">
                        <option value="Recipes" {{ old('category') === 'Recipes' ? 'selected' : '' }}>Recipes (Công thức)</option>
                        <option value="News" {{ old('category') === 'News' ? 'selected' : '' }}>News (Tin tức)</option>
                        <option value="Lifestyle" {{ old('category') === 'Lifestyle' ? 'selected' : '' }}>Lifestyle (Lối sống)</option>
                    </select>
                </div>
            </div>

            <div class="admin-card mb-4">
                <h5 class="fw-bold mb-3">Ảnh Đại Diện</h5>

                <div class="mb-3">
                    <label for="thumbnail" class="form-label fw-semibold">Tải ảnh bài viết</label>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/*">
                    <div class="form-text small">Định dạng JPG, PNG, WEBP (Tối đa 3MB).</div>
                </div>
            </div>

            <div class="admin-card mb-4">
                <h5 class="fw-bold mb-3">Xuất Bản</h5>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_published" id="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_published">Xuất bản bài viết ngay</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Lưu & Xuất Bản
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
