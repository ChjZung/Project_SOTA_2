@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Danh Mục')

@push('styles')<link rel="stylesheet" href="{{ asset('assets/css/admin-category-images.css') }}">@endpush
@push('scripts')<script src="{{ asset('assets/js/admin-category-images.js') }}" defer></script>@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Chỉnh Sửa Danh Mục: {{ $category->name }}</h4>
        <p class="text-muted small mb-0">Cập nhật thông tin phân loại sản phẩm</p>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="admin-card">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label fw-semibold">Đường dẫn tĩnh (Slug)</label>
                    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $category->slug) }}">
                    <div class="form-text small">Đường dẫn: <code>/product-category/{{ $category->slug }}</code></div>
                </div>

                <div class="mb-3">
                    <label for="parent_id" class="form-label fw-semibold">Danh mục cha</label>
                    <select name="parent_id" id="parent_id" class="form-select">
                        <option value="">-- Là danh mục gốc (không có cha) --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @include('admin.categories.image-field')

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Mô tả ngắn</label>
                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="sort_order" class="form-label fw-semibold">Thứ tự hiển thị</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order) }}" style="max-width: 150px;">
                </div>

                <button type="submit" class="btn btn-admin-primary">
                    <i class="bi bi-check-lg me-1"></i> Cập Nhật Danh Mục
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
