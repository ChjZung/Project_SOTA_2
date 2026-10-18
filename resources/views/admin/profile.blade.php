@extends('admin.layouts.app')

@section('title', 'Thông Tin Quản Trị & Bảo Mật')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Hồ Sơ Quản Trị Viên</h4>
        <p class="text-muted small mb-0">Quản lý thông tin định danh và bảo mật tài khoản quản trị hệ thống</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Quay Lại Bảng Điều Khiển
    </a>
</div>

<div class="row g-4">
    <!-- Cột thông tin nhanh & Avatar -->
    <div class="col-12 col-lg-4">
        <div class="admin-card text-center p-4">
            <div class="position-relative d-inline-block mb-3">
                @if($user->avatar)
                    <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center text-success fw-bold shadow-sm" style="width: 120px; height: 120px; background-color: #e2f0e0; font-size: 3rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <span class="position-absolute bottom-0 end-0 badge bg-success rounded-pill p-2" title="Trạng thái hoạt động">
                    <i class="bi bi-check-lg"></i>
                </span>
            </div>

            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <p class="text-muted small mb-2">{{ $user->email }}</p>
            <div>
                <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-bold">
                    {{ strtoupper($user->role) }}
                </span>
            </div>

            <hr class="my-4">

            <div class="text-start small">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-telephone me-2"></i>Điện thoại:</span>
                    <span class="fw-semibold text-dark">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-geo-alt me-2"></i>Địa chỉ:</span>
                    <span class="fw-semibold text-dark">{{ $user->address ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted"><i class="bi bi-calendar3 me-2"></i>Tham gia:</span>
                    <span class="fw-semibold text-dark">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột Biểu mẫu cập nhật & Đổi mật khẩu -->
    <div class="col-12 col-lg-8">
        <!-- Form cập nhật thông tin cá nhân -->
        <div class="admin-card mb-4">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-person-gear text-success"></i> Thông Tin Cá Nhân
            </h5>
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label fw-semibold small">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label fw-semibold small">Email Đăng Nhập</label>
                        <input type="email" class="form-control bg-light" id="email" value="{{ $user->email }}" readonly disabled>
                        <small class="text-muted" style="font-size: 0.75rem;">Email là tài khoản quản trị chính thức, không thể thay đổi tại đây.</small>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="phone" class="form-label fw-semibold small">Số Điện Thoại</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="avatar" class="form-label fw-semibold small">Ảnh Đại Diện Mới</label>
                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label fw-semibold small">Địa Chỉ</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address) }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-admin-primary px-4">
                        <i class="bi bi-save me-1"></i> Lưu Cập Nhật
                    </button>
                </div>
            </form>
        </div>

        <!-- Form đổi mật khẩu bảo mật -->
        <div class="admin-card mb-0">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
                <i class="bi bi-shield-lock text-danger"></i> Đổi Mật Khẩu Quản Trị
            </h5>
            <form action="{{ route('admin.profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12">
                        <label for="current_password" class="form-label fw-semibold small">Mật Khẩu Hiện Tại <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required autocomplete="current-password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label fw-semibold small">Mật Khẩu Mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" minlength="8" required autocomplete="new-password">
                        <small class="text-muted" style="font-size: 0.75rem;">Ít nhất 8 ký tự an toàn.</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="password_confirmation" class="form-label fw-semibold small">Xác Nhận Mật Khẩu Mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="8" required autocomplete="new-password">
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-dark px-4">
                        <i class="bi bi-key-fill me-1"></i> Cập Nhật Mật Khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
