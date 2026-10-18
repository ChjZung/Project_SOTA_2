@extends('admin.layouts.app')

@section('title', 'Cài Đặt Hệ Thống Website')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Cài Đặt Hệ Thống</h4>
        <p class="text-muted small mb-0">Thiết lập thông tin thương hiệu, hotline liên hệ, mạng xã hội và tài khoản thanh toán</p>
    </div>
</div>

<div class="admin-card">
    <!-- Tabs Header -->
    <ul class="nav nav-tabs border-bottom mb-4" id="settingTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold text-dark" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-selected="true">
                <i class="bi bi-sliders me-1 text-success"></i> Cài Đặt Chung & Liên Hệ
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold text-dark" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab" aria-selected="false">
                <i class="bi bi-bank me-1 text-primary"></i> Chuyển Khoản & Vận Chuyển
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold text-dark" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab" aria-selected="false">
                <i class="bi bi-share me-1 text-danger"></i> Liên Kết Mạng Xã Hội
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="settingTabsContent">
        <!-- 1. Cài Đặt Chung & Liên Hệ -->
        <div class="tab-pane fade show active" id="general" role="tabpanel">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="group" value="general">

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="site_name" class="form-label fw-semibold">Tên Website / Cửa Hàng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="site_name" name="site_name" value="{{ old('site_name', $allSettings['site_name'] ?? 'Avada Vegan Store') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="site_tagline" class="form-label fw-semibold">Khẩu Hiệu (Tagline / Slogan)</label>
                                <input type="text" class="form-control" id="site_tagline" name="site_tagline" value="{{ old('site_tagline', $allSettings['site_tagline'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="site_phone" class="form-label fw-semibold">Hotline Điện Thoại</label>
                                <input type="text" class="form-control" id="site_phone" name="site_phone" value="{{ old('site_phone', $allSettings['site_phone'] ?? '+1 (800) 234-5678') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="site_email" class="form-label fw-semibold">Email Liên Hệ Chính</label>
                                <input type="email" class="form-control" id="site_email" name="site_email" value="{{ old('site_email', $allSettings['site_email'] ?? 'contact@veganstore.vn') }}">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label for="site_address" class="form-label fw-semibold">Địa Chỉ Showroom / Cửa Hàng</label>
                                <input type="text" class="form-control" id="site_address" name="site_address" value="{{ old('site_address', $allSettings['site_address'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="site_working_hours" class="form-label fw-semibold">Giờ Hoạt Động</label>
                                <input type="text" class="form-control" id="site_working_hours" name="site_working_hours" value="{{ old('site_working_hours', $allSettings['site_working_hours'] ?? '8:00 - 21:00') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="site_footer_about" class="form-label fw-semibold">Đoạn Giới Thiệu Chân Trang (Footer About)</label>
                            <textarea class="form-control" id="site_footer_about" name="site_footer_about" rows="3">{{ old('site_footer_about', $allSettings['site_footer_about'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <label class="form-label fw-semibold mb-2">Logo Cửa Hàng</label>
                            <div class="mb-3 p-3 bg-white text-center rounded border">
                                @if(!empty($allSettings['site_logo']))
                                    <img src="{{ asset(ltrim($allSettings['site_logo'], '/')) }}" alt="Logo" class="img-fluid" style="max-height: 50px;">
                                @else
                                    <span class="text-muted small">Chưa có logo</span>
                                @endif
                            </div>
                            <label for="site_logo" class="form-label small fw-semibold">Tải Tệp Logo Mới</label>
                            <input type="file" class="form-control form-control-sm" id="site_logo" name="site_logo" accept="image/*">
                            <small class="text-muted" style="font-size: 0.72rem;">Hỗ trợ SVG, PNG, WebP.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-admin-primary">
                                <i class="bi bi-save me-1"></i> Lưu Cài Đặt Chung
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- 2. Chuyển Khoản & Vận Chuyển -->
        <div class="tab-pane fade" id="payment" role="tabpanel">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <input type="hidden" name="group" value="payment">

                <div class="row g-4">
                    <div class="col-lg-7">
                        <h5 class="fw-bold mb-3 text-dark">
                            <i class="bi bi-credit-card-2-front text-primary me-2"></i>Tài Khoản Ngân Hàng (Hiển thị tại Checkout)
                        </h5>

                        <div class="mb-3">
                            <label for="bank_name" class="form-label fw-semibold">Tên Ngân Hàng & Chi Nhánh <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $allSettings['bank_name'] ?? 'Vietcombank - Chi nhánh Bến Thành') }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="bank_account_number" class="form-label fw-semibold">Số Tài Khoản <span class="text-danger">*</span></label>
                                <input type="text" class="form-control font-monospace fw-bold" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $allSettings['bank_account_number'] ?? '0123456789999') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="bank_account_name" class="form-label fw-semibold">Chủ Tài Khoản (Không dấu) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name', $allSettings['bank_account_name'] ?? 'CONG TY TNHH VEGAN STORE VIET NAM') }}" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3 text-dark">
                            <i class="bi bi-truck text-success me-2"></i>Chính Sách Phí Giao Hàng
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="shipping_fee_default" class="form-label fw-semibold">Phí Giao Hàng Tiêu Chuẩn ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control" id="shipping_fee_default" name="shipping_fee_default" value="{{ old('shipping_fee_default', $allSettings['shipping_fee_default'] ?? '5.00') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="free_shipping_min_amount" class="form-label fw-semibold">Miễn Phí Giao Hàng Cho Đơn Từ ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control" id="free_shipping_min_amount" name="free_shipping_min_amount" value="{{ old('free_shipping_min_amount', $allSettings['free_shipping_min_amount'] ?? '50.00') }}" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-admin-primary">
                            <i class="bi bi-save me-1"></i> Lưu Cấu Hình Thanh Toán & Ship
                        </button>
                    </div>

                    <div class="col-lg-5">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-check text-success me-1"></i> Ghi Chú Thanh Toán</h6>
                            <p class="text-muted small mb-3">
                                Khi khách hàng chọn hình thức <strong>Chuyển khoản ngân hàng</strong> ở trang Checkout, toàn bộ số tài khoản và ngân hàng này sẽ được hiển thị kèm mã QR/cú pháp chuyển tiền.
                            </p>
                            <div class="p-3 bg-white rounded border small">
                                <strong>Cú pháp mẫu gợi ý:</strong><br>
                                <code>VEGAN [MÃ_ĐƠN_HÀNG] [SỐ_ĐIỆN_THOẠI]</code>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- 3. Liên Kết Mạng Xã Hội -->
        <div class="tab-pane fade" id="social" role="tabpanel">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <input type="hidden" name="group" value="social">

                <div class="row g-3 mb-4" style="max-width: 700px;">
                    <div class="col-12">
                        <label for="social_facebook" class="form-label fw-semibold">
                            <i class="bi bi-facebook text-primary me-2"></i>Trang Facebook (URL)
                        </label>
                        <input type="url" class="form-control" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $allSettings['social_facebook'] ?? '') }}" placeholder="https://facebook.com/your-page">
                    </div>
                    <div class="col-12">
                        <label for="social_instagram" class="form-label fw-semibold">
                            <i class="bi bi-instagram text-danger me-2"></i>Trang Instagram (URL)
                        </label>
                        <input type="url" class="form-control" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $allSettings['social_instagram'] ?? '') }}" placeholder="https://instagram.com/your-handle">
                    </div>
                    <div class="col-12">
                        <label for="social_youtube" class="form-label fw-semibold">
                            <i class="bi bi-youtube text-danger me-2"></i>Kênh YouTube (URL)
                        </label>
                        <input type="url" class="form-control" id="social_youtube" name="social_youtube" value="{{ old('social_youtube', $allSettings['social_youtube'] ?? '') }}" placeholder="https://youtube.com/@channel">
                    </div>
                    <div class="col-12">
                        <label for="social_twitter" class="form-label fw-semibold">
                            <i class="bi bi-twitter-x text-dark me-2"></i>Tài Khoản X / Twitter (URL)
                        </label>
                        <input type="url" class="form-control" id="social_twitter" name="social_twitter" value="{{ old('social_twitter', $allSettings['social_twitter'] ?? '') }}" placeholder="https://x.com/your-handle">
                    </div>
                </div>

                <button type="submit" class="btn btn-admin-primary">
                    <i class="bi bi-save me-1"></i> Lưu Liên Kết Mạng Xã Hội
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
