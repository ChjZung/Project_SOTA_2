<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vegan Store Admin') - Quản Trị Hệ Thống</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --admin-primary: #2d5a27;
            --admin-primary-dark: #1e3f1a;
            --admin-primary-light: #44803f;
            --admin-sidebar-bg: #132213;
            --admin-sidebar-text: #b8ccb6;
            --admin-sidebar-active: #5cb85c;
            --admin-bg: #f8faf8;
            --admin-card-border: #e6ede5;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--admin-bg);
            color: #2c3e50;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #adminSidebar {
            width: 260px;
            min-height: 100vh;
            background-color: var(--admin-sidebar-bg);
            color: var(--admin-sidebar-text);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 4px 0 20px rgba(0,0,0,0.08);
        }

        .sidebar-brand {
            padding: 24px 20px;
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            text-decoration: none;
        }
        .sidebar-brand .brand-badge {
            background-color: #2d5a27;
            color: #8ce682;
            font-size: 0.72rem;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .sidebar-menu {
            padding: 16px 12px;
            list-style: none;
            margin: 0;
        }
        .sidebar-header {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #698267;
            padding: 12px 14px 6px;
        }
        .sidebar-item {
            margin-bottom: 4px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            color: var(--admin-sidebar-text);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }
        .sidebar-link i {
            font-size: 1.15rem;
            color: #7d9c7a;
            transition: color 0.2s;
        }
        .sidebar-link:hover {
            background-color: rgba(255,255,255,0.06);
            color: #ffffff;
        }
        .sidebar-link:hover i {
            color: #9fe897;
        }
        .sidebar-link.active {
            background-color: #2d5a27;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(45, 90, 39, 0.4);
        }
        .sidebar-link.active i {
            color: #bbf7b5;
        }

        /* Main Content Wrapper */
        #adminMain {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Top Header Navbar */
        .admin-navbar {
            height: 70px;
            background: #ffffff;
            border-bottom: 1px solid var(--admin-card-border);
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .user-dropdown .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e2f0e0;
            color: #2d5a27;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.05rem;
        }

        /* Content Area */
        .admin-content {
            padding: 30px;
            flex: 1;
        }

        /* Cards */
        .admin-card {
            background: #ffffff;
            border: 1px solid var(--admin-card-border);
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            padding: 24px;
            margin-bottom: 24px;
        }

        /* Buttons */
        .btn-admin-primary {
            background-color: var(--admin-primary);
            color: #ffffff;
            font-weight: 600;
            border: none;
            padding: 9px 18px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-admin-primary:hover {
            background-color: var(--admin-primary-dark);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(45, 90, 39, 0.3);
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        .table thead th {
            background-color: #f3f7f2;
            color: #496347;
            font-weight: 700;
            font-size: 0.84rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            padding: 14px 16px;
        }
        .table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-color: #f0f4ef;
            font-size: 0.92rem;
        }

        /* Mobile Responsive */
        @media (max-width: 991.98px) {
            #adminSidebar {
                transform: translateX(-100%);
            }
            #adminSidebar.show {
                transform: translateX(0);
            }
            #adminMain {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside id="adminSidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="bi bi-flower1 fs-4 text-success"></i>
            <span>Vegan Store</span>
            <span class="brand-badge ms-auto">ADMIN</span>
        </a>

        <ul class="sidebar-menu">
            <li class="sidebar-header">Tổng Quan</li>
            <li class="sidebar-item">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Bảng Điều Khiển</span>
                </a>
            </li>

            <li class="sidebar-header">Bán Hàng & Kho</li>
            <li class="sidebar-item">
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-bag-check-fill"></i>
                    <span>Đơn Hàng</span>
                    @php $pendingCount = \App\Models\Order::where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Sản Phẩm</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags-fill"></i>
                    <span>Danh Mục</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.coupons.index') }}" class="sidebar-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-perforated-fill"></i>
                    <span>Mã Giảm Giá</span>
                </a>
            </li>

            <li class="sidebar-header">Khách Hàng & Phản Hồi</li>
            <li class="sidebar-item">
                <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Khách Hàng</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="bi bi-star-fill"></i>
                    <span>Đánh Giá SP</span>
                    @php $pendingReviewCount = \App\Models\Review::where('is_approved', false)->count(); @endphp
                    @if($pendingReviewCount > 0)
                        <span class="badge bg-warning text-dark rounded-pill ms-auto">{{ $pendingReviewCount }}</span>
                    @endif
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.inquiries.index') }}" class="sidebar-link {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope-fill"></i>
                    <span>Hộp Thư Liên Hệ</span>
                    @php $newInquiryCount = \App\Models\Inquiry::where('status', 'new')->count(); @endphp
                    @if($newInquiryCount > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $newInquiryCount }}</span>
                    @endif
                </a>
            </li>

            <li class="sidebar-header">Nội Dung & Giao Diện</li>
            <li class="sidebar-item">
                <a href="{{ route('admin.banners.index') }}" class="sidebar-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <i class="bi bi-images"></i>
                    <span>Banner Slider</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('admin.posts.index') }}" class="sidebar-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                    <i class="bi bi-newspaper"></i>
                    <span>Bài Viết & Recipes</span>
                </a>
            </li>

            <li class="sidebar-header">Hệ Thống</li>
            <li class="sidebar-item">
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i>
                    <span>Cài Đặt Website</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('home') }}" target="_blank" class="sidebar-link">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Xem Website</span>
                </a>
            </li>
            <li class="sidebar-item mt-4">
                <form action="{{ route('admin.logout') }}" method="POST" id="logoutForm">
                    @csrf
                    <button type="submit" class="sidebar-link w-100 text-start border-0 bg-transparent text-danger">
                        <i class="bi bi-box-arrow-left text-danger"></i>
                        <span>Đăng Xuất</span>
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div id="adminMain">
        <!-- Topbar Navbar -->
        <header class="admin-navbar">
            <button class="btn btn-light d-lg-none" id="sidebarToggle" type="button">
                <i class="bi bi-list fs-5"></i>
            </button>

            <div class="d-none d-md-flex align-items-center gap-2">
                <span class="text-muted small">Cửa hàng trực tuyến:</span>
                <span class="badge bg-success-subtle text-success fw-bold px-3 py-2 rounded-pill">
                    <i class="bi bi-circle-fill me-1 small"></i> Đang Hoạt Động
                </span>
            </div>

            <!-- User Menu -->
            <div class="dropdown user-dropdown">
                <button class="btn d-flex align-items-center gap-3 p-1 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'Admin', 0, 1)) }}
                    </div>
                    <div class="text-start d-none d-sm-block">
                        <div class="fw-bold small lh-1">{{ Auth::user()->name ?? 'Administrator' }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">{{ Auth::user()->email ?? 'admin@veganstore.vn' }}</div>
                    </div>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2 mt-2">
                    <li><h6 class="dropdown-header">Tài khoản quản trị</h6></li>
                    <li><a class="dropdown-item py-2" href="{{ route('admin.profile.edit') }}"><i class="bi bi-person-gear me-2"></i> Hồ Sơ & Mật Khẩu</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button class="dropdown-item py-2 text-danger" onclick="document.getElementById('logoutForm').submit();">
                            <i class="bi bi-box-arrow-left me-2"></i> Đăng Xuất
                        </button>
                    </li>
                </ul>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="admin-content">
            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                        <strong>Thành công!</strong> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                        <strong>Thông báo:</strong> {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
                        <strong>Có lỗi xảy ra:</strong>
                    </div>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar on Mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const adminSidebar = document.getElementById('adminSidebar');
        if (sidebarToggle && adminSidebar) {
            sidebarToggle.addEventListener('click', () => {
                adminSidebar.classList.toggle('show');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
