@extends('admin.layouts.app')

@section('title', 'Sửa Banner: ' . $banner->title)

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Chỉnh Sửa Banner Quảng Cáo</h4>
        <p class="text-muted small mb-0">Cập nhật thông tin và vị trí xuất hiện của banner trên website</p>
    </div>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="admin-card">
    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Cột Trái: Thông tin chính -->
            <div class="col-lg-8">
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">Tiêu Đề Banner <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $banner->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="subtitle" class="form-label fw-semibold">Phụ Đề / Mô Tả Ngắn</label>
                    <input type="text" class="form-control @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}">
                    @error('subtitle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="button_text" class="form-label fw-semibold">Chữ Trên Nút Bấm (CTA)</label>
                        <input type="text" class="form-control" id="button_text" name="button_text" value="{{ old('button_text', $banner->button_text) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="button_link" class="form-label fw-semibold">Đường Dẫn Khi Bấm Nút</label>
                        <input type="text" class="form-control" id="button_link" name="button_link" value="{{ old('button_link', $banner->button_link) }}">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="position" class="form-label fw-semibold">Vị Trí Hiển Thị <span class="text-danger">*</span></label>
                        <select name="position" id="position" class="form-select @error('position') is-invalid @enderror" required>
                            <option value="hero" {{ old('position', $banner->position) === 'hero' ? 'selected' : '' }}>Hero Slider (Trên cùng trang chủ)</option>
                            <option value="promo" {{ old('position', $banner->position) === 'promo' ? 'selected' : '' }}>Khối Promo (Giữa trang chủ)</option>
                            <option value="sidebar" {{ old('position', $banner->position) === 'sidebar' ? 'selected' : '' }}>Sidebar (Cột bên)</option>
                        </select>
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="sort_order" class="form-label fw-semibold">Thứ Tự Sắp Xếp</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0">
                        <small class="text-muted" style="font-size: 0.75rem;">Số nhỏ hơn hiển thị trước.</small>
                    </div>
                </div>
            </div>

            <!-- Cột Phải: Hình ảnh & Kích hoạt -->
            <div class="col-lg-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ảnh Hiện Tại</label>
                    <div>
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="rounded-3 shadow-sm w-100 object-fit-cover" style="max-height: 140px; border: 1px solid #e0e0e0;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label fw-semibold">Thay Đổi Tệp Ảnh</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                    <small class="text-muted" style="font-size: 0.75rem;">Để trống nếu giữ ảnh hiện tại.</small>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="image_url" class="form-label fw-semibold">Hoặc Đường Dẫn URL Ảnh Sẵn Có</label>
                    <input type="text" class="form-control" id="image_url" name="image_url" value="{{ old('image_url', $banner->image) }}">
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Kích hoạt hiển thị</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="bi bi-save me-1"></i> Lưu Cập Nhật
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
