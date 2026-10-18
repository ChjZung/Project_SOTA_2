<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị - Vegan Store</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #132213 0%, #1e3f1a 50%, #2d5a27 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            padding: 40px 36px;
        }
        .brand-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: #e2f0e0;
            color: #2d5a27;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 16px;
        }
        .btn-submit {
            background-color: #2d5a27;
            color: #ffffff;
            font-weight: 700;
            padding: 12px;
            border-radius: 8px;
            border: none;
            width: 100%;
            transition: all 0.25s;
        }
        .btn-submit:hover {
            background-color: #1e3f1a;
            box-shadow: 0 6px 18px rgba(45, 90, 39, 0.4);
            color: #ffffff;
        }
        .form-control:focus {
            border-color: #2d5a27;
            box-shadow: 0 0 0 0.25rem rgba(45, 90, 39, 0.15);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon">
                <i class="bi bi-flower1"></i>
            </div>
            <h4 class="fw-bold mb-1">Vegan Store Admin</h4>
            <p class="text-muted small">Hệ thống quản trị bán hàng & nội dung</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger py-2 small border-0 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success py-2 small border-0 mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger py-2 small border-0 mb-3" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold text-secondary">Địa chỉ Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control border-start-0 ps-0" placeholder="admin@veganstore.vn" value="{{ old('email', 'admin@veganstore.vn') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold text-secondary">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label small text-muted" for="remember">
                        Ghi nhớ đăng nhập
                    </label>
                </div>
                <a href="{{ route('home') }}" class="small text-decoration-none text-success fw-semibold">
                    Về Website &rarr;
                </a>
            </div>

            <button type="submit" class="btn btn-submit mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Đăng Nhập
            </button>

            <div class="text-center text-muted small" style="font-size: 0.8rem;">
                Tài khoản mặc định: <strong>admin@veganstore.vn</strong> / <strong>Admin@123456</strong>
            </div>
        </form>
    </div>

</body>
</html>
