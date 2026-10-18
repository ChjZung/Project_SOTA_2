@extends('admin.layouts.app')

@section('title', 'Sửa Mã Giảm Giá: ' . $coupon->code)

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Chỉnh Sửa Mã Giảm Giá: {{ $coupon->code }}</h4>
        <p class="text-muted small mb-0">Cập nhật hạn sử dụng, mức giảm và điều kiện của voucher</p>
    </div>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="admin-card">
    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="code" class="form-label fw-semibold">Mã Giảm Giá (Code) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase font-monospace @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $coupon->code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label fw-semibold">Loại Chiết Khấu <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="percent" {{ old('type', $coupon->type) === 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                            <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Giảm số tiền cố định ($)</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="value" class="form-label fw-semibold">Giá Trị Giảm <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value', $coupon->value) }}" required>
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="min_order_amount" class="form-label fw-semibold">Giá Trị Đơn Hàng Tối Thiểu ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" placeholder="VD: 50.00 (Để trống nếu không yêu cầu)">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="starts_at" class="form-label fw-semibold">Ngày Bắt Đầu Áp Dụng</label>
                        <input type="date" class="form-control @error('starts_at') is-invalid @enderror" id="starts_at" name="starts_at" value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : '') }}">
                        @error('starts_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="expires_at" class="form-label fw-semibold">Ngày Hết Hạn</label>
                        <input type="date" class="form-control @error('expires_at') is-invalid @enderror" id="expires_at" name="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}">
                        @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="max_uses" class="form-label fw-semibold">Giới Hạn Số Lần Sử Dụng Tối Đa</label>
                    <input type="number" min="1" class="form-control" id="max_uses" name="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" placeholder="Để trống nếu không giới hạn">
                </div>
            </div>

            <div class="col-lg-4">
                <div class="p-3 bg-light rounded-3 border mb-4">
                    <h6 class="fw-bold text-dark mb-2">Thống Kê Sử Dụng</h6>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Đã dùng:</span>
                        <span class="fw-bold text-success">{{ $coupon->used_count }} lần</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Tối đa cho phép:</span>
                        <span class="fw-semibold text-dark">{{ $coupon->max_uses ?? 'Không giới hạn' }}</span>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Kích hoạt mã</label>
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
