@extends('admin.layouts.app')

@section('title', 'Thêm Sản Phẩm Mới')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Thêm Sản Phẩm Mới</h4>
        <p class="text-muted small mb-0">Nhập thông tin sản phẩm thuần chay mới vào cơ sở dữ liệu</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Quay lại
    </a>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-lg-8">
            <div class="admin-card mb-4">
                <h5 class="fw-bold mb-3">Thông Tin Chung</h5>

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Ví dụ: Fresh Organic Cake" value="{{ old('name') }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-semibold">Đường dẫn tĩnh (Slug)</label>
                        <input type="text" name="slug" id="slug" class="form-control" placeholder="Tự động tạo nếu để trống" value="{{ old('slug') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="product_code" class="form-label fw-semibold">Mã sản phẩm (SKU)</label>
                        <input type="text" name="product_code" id="product_code" class="form-control" placeholder="Ví dụ: VGN-CAKE-01" value="{{ old('product_code') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="summary" class="form-label fw-semibold">Tóm tắt ngắn (Hiện ở thẻ sản phẩm)</label>
                    <input type="text" name="summary" id="summary" class="form-control" placeholder="Ví dụ: Made with real whole grains and organic fruits" value="{{ old('summary') }}">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Mô tả chi tiết sản phẩm</label>
                    <textarea name="description" id="description" rows="6" class="form-control" placeholder="Chi tiết thành phần, hướng dẫn sử dụng, bảo quản...">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="admin-card">
                <h5 class="fw-bold mb-3">Giá & Quản Lý Kho</h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="price" class="form-label fw-semibold">Giá niêm yết ($ USD) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" placeholder="9.00" value="{{ old('price') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="sale_price" class="form-label fw-semibold">Giá khuyến mãi ($ USD)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="sale_price" id="sale_price" class="form-control" placeholder="7.00" value="{{ old('sale_price') }}">
                        </div>
                        <div class="form-text small">Để trống nếu không có giảm giá.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="stock" class="form-label fw-semibold">Số lượng tồn kho <span class="text-danger">*</span></label>
                        <input type="number" name="stock" id="stock" class="form-control" placeholder="100" value="{{ old('stock', 50) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Media & Settings -->
        <div class="col-lg-4">
            <div class="admin-card mb-4">
                <h5 class="fw-bold mb-3">Phân Loại</h5>

                <div class="mb-3">
                    <label for="category_id" class="form-label fw-semibold">Danh mục sản phẩm <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="admin-card mb-4">
                <h5 class="fw-bold mb-3">Hình Ảnh Sản Phẩm</h5>

                <div class="mb-3">
                    <label for="image" class="form-label fw-semibold">Ảnh đại diện chính</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    <div class="form-text small">Định dạng JPG, PNG, WEBP (Tối đa 3MB).</div>
                </div>

                <div class="mb-3">
                    <label for="gallery" class="form-label fw-semibold">Thư viện ảnh phụ (Gallery)</label>
                    <input type="file" name="gallery[]" id="gallery" class="form-control" accept="image/*" multiple>
                    <div class="form-text small">Có thể chọn nhiều hình cùng lúc.</div>
                </div>
            </div>

            <div class="admin-card mb-4">
                <h5 class="fw-bold mb-3">Trạng Thái & Hiển Thị</h5>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Hiển thị trên website</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Sản phẩm nổi bật (Featured)</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_deal" id="is_deal" value="1" {{ old('is_deal') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_deal">Chương trình Hot Deal</label>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-admin-primary btn-lg">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Đăng Bán Sản Phẩm
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
