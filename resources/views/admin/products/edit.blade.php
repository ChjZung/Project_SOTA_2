@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Sản Phẩm')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Chỉnh Sửa: {{ $product->name }}</h4>
        <p class="text-muted small mb-0">Cập nhật thông tin chi tiết và giá sản phẩm</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="btn btn-outline-success">
            <i class="bi bi-eye me-1"></i> Xem trên Web
        </a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-lg-8">
            @if($product->variations)
            <div class="admin-card mb-4"><h5 class="fw-bold mb-3">Giá theo lựa chọn</h5><p class="text-muted small">Với sản phẩm có lựa chọn, giá dưới đây được dùng trong giỏ hàng và thanh toán.</p>
                @foreach(old('variations',$product->variations) as $i=>$variation)<div class="row g-2 mb-3 align-items-center"><div class="col-7">{{ implode(' / ',$variation['attributes']) }}@foreach($variation['attributes'] as $key=>$value)<input type="hidden" name="variations[{{ $i }}][attributes][{{ $key }}]" value="{{ $value }}">@endforeach</div><div class="col-5"><label class="small">Giá USD<input class="form-control" type="number" name="variations[{{ $i }}][price]" value="{{ $variation['price'] }}" min="0" step="0.01" required></label></div></div>@endforeach
            </div>
            @endif
            <div class="admin-card mb-4">
                <h5 class="fw-bold mb-3">Thông Tin Chung</h5>

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-semibold">Đường dẫn tĩnh (Slug)</label>
                        <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $product->slug) }}">
                        <div class="form-text small">URL: <code>/product/{{ $product->slug }}</code></div>
                    </div>
                    <div class="col-md-6">
                        <label for="product_code" class="form-label fw-semibold">Mã sản phẩm (SKU)</label>
                        <input type="text" name="product_code" id="product_code" class="form-control" value="{{ old('product_code', $product->product_code) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="summary" class="form-label fw-semibold">Tóm tắt ngắn</label>
                    <input type="text" name="summary" id="summary" class="form-control" value="{{ old('summary', $product->summary) }}">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Mô tả chi tiết sản phẩm</label>
                    <textarea name="description" id="description" rows="6" class="form-control">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <div class="admin-card">
                <h5 class="fw-bold mb-3">Giá & Quản Lý Kho</h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="price" class="form-label fw-semibold">Giá niêm yết ($ USD) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="sale_price" class="form-label fw-semibold">Giá khuyến mãi ($ USD)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="sale_price" id="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}">
                        </div>
                        <div class="form-text small">Để trống nếu không có giảm giá.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="stock" class="form-label fw-semibold">Số lượng tồn kho <span class="text-danger">*</span></label>
                        <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
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
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
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
                    @if($product->image)
                        <div class="mb-2">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-3 border object-fit-contain" width="100" height="100">
                        </div>
                    @endif
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    <div class="form-text small">Chọn ảnh mới để thay đổi ảnh hiện tại.</div>
                </div>

                <div class="mb-3">
                    <label for="gallery" class="form-label fw-semibold">Thư viện ảnh phụ (Gallery)</label>
                    @if(!empty($product->gallery) && is_array($product->gallery))
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach($product->gallery as $galImg)
                                <img src="{{ asset(ltrim($galImg, '/')) }}" alt="Gallery image" class="rounded border object-fit-contain" width="55" height="55">
                            @endforeach
                        </div>
                    @endif
                    <input type="file" name="gallery[]" id="gallery" class="form-control" accept="image/*" multiple>
                    <div class="form-text small">Tải thêm ảnh vào bộ sưu tập.</div>
                </div>
            </div>

            <div class="admin-card mb-4">
                <h5 class="fw-bold mb-3">Trạng Thái & Hiển Thị</h5>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Hiển thị trên website</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Sản phẩm nổi bật (Featured)</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_deal" id="is_deal" value="1" {{ old('is_deal', $product->is_deal) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_deal">Chương trình Hot Deal</label>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-admin-primary btn-lg">
                    <i class="bi bi-check-circle me-1"></i> Cập Nhật Sản Phẩm
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
