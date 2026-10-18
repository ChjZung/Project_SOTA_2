@extends('admin.layouts.app')

@section('title', 'Thêm Banner Quảng Cáo Mới')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Thêm Banner Quảng Cáo Mới</h4>
        <p class="text-muted small mb-0">Thiết lập hình ảnh, tiêu đề và nút kêu gọi hành động cho banner</p>
    </div>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="admin-card">
    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Cột Trái: Thông tin chính -->
            <div class="col-lg-8">
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">Tiêu Đề Banner <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="VD: Sống Xanh Mỗi Ngày Cùng Thực Phẩm Thuần Chay..." required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="subtitle" class="form-label fw-semibold">Phụ Đề / Mô Tả Ngắn</label>
                    <input type="text" class="form-control @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" value="{{ old('subtitle') }}" placeholder="VD: Giảm ngay 10% cho tất cả đơn hàng dinh dưỡng">
                    @error('subtitle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="button_text" class="form-label fw-semibold">Chữ Trên Nút Bấm (CTA)</label>
                        <input type="text" class="form-control" id="button_text" name="button_text" value="{{ old('button_text', 'Khám Phá Ngay') }}" placeholder="VD: Mua Ngay, Xem Thêm...">
                    </div>
                    <div class="col-md-6">
                        <label for="button_link" class="form-label fw-semibold">Đường Dẫn Khi Bấm Nút</label>
                        <input type="text" class="form-control" id="button_link" name="button_link" value="{{ old('button_link', '/shop') }}" placeholder="VD: /shop hoặc link khuyến mãi">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="position" class="form-label fw-semibold">Vị Trí Hiển Thị <span class="text-danger">*</span></label>
                        <select name="position" id="position" class="form-select @error('position') is-invalid @enderror" required>
                            <option value="hero" {{ old('position') === 'hero' ? 'selected' : '' }}>Hero Slider (Trên cùng trang chủ)</option>
                            <option value="promo" {{ old('position') === 'promo' ? 'selected' : '' }}>Khối Promo (Giữa trang chủ)</option>
                            <option value="sidebar" {{ old('position') === 'sidebar' ? 'selected' : '' }}>Sidebar (Cột bên)</option>
                        </select>
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="sort_order" class="form-label fw-semibold">Thứ Tự Sắp Xếp</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                        <small class="text-muted" style="font-size: 0.75rem;">Số nhỏ hơn hiển thị trước.</small>
                    </div>
                </div>
            </div>

            <!-- Cột Phải: Hình ảnh & Kích hoạt -->
            <div class="col-lg-4">
                <div class="mb-3">
                    <label for="image" class="form-label fw-semibold">Tải Tệp Ảnh Lên</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                    <small class="text-muted" style="font-size: 0.75rem;">Định dạng: JPG, PNG, WEBP. Tối đa 3MB.</small>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="image_url" class="form-label fw-semibold">Hoặc Đường Dẫn URL Ảnh Sẵn Có</label>
                    <input type="text" class="form-control" id="image_url" name="image_url" value="{{ old('image_url') }}" placeholder="VD: /wp-content/uploads/sites/186/2024/02/vegan-shop-hero-bg.jpg">
                    <small class="text-muted" style="font-size: 0.75rem;">Ưu tiên tệp tải lên nếu có.</small>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Kích hoạt hiển thị ngay</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="bi bi-plus-circle me-1"></i> Thêm Banner
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
