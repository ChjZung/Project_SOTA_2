<!DOCTYPE html>
<html class="avada-html-layout-wide avada-html-header-position-top" lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <base href="{{ url('/') }}/">

    <title>@yield('title', 'Avada Vegan Store - 100% Plant-Based & Organic Store')</title>
    <meta name="description" content="@yield('description', 'A Customizable Pre-built Vegan Store Styled Website. 100% Organic, Vegan & Plant-Based Products with Fast Delivery.')">

    <!-- Favicon -->
    <link rel="shortcut icon" href="/wp-content/uploads/sites/186/2024/02/favicon-2.png" type="image/x-icon" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Happy+Monkey&family=Sen:wght@400;700;800&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Core Stylesheets -->
    <link rel="stylesheet" href="/assets/css/wc-blocks.css" media="all" />
    <link rel="stylesheet" href="/assets/css/fss-section-copy.css" media="all" />
    <link rel="stylesheet" href="/assets/css/avada-style.css" media="all" />
    <link rel="stylesheet" href="/assets/css/custom-inline.css" media="all" />

    <style>
        /* Smooth scrolling and base styling */
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Sen', sans-serif;
            color: #4a5568;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        img { max-width: 100%; height: auto; }
        /* Font Awesome / Icomoon fallback */
        .awb-vegan-storeuser::before { content: "\e900"; }
        .awb-vegan-storecart::before { content: "\e901"; }
        .awb-vegan-storemenu::before { content: "\e902"; }
        .awb-vegan-storeclose::before { content: "\e903"; }
        .awb-vegan-storeicon-vegan::before { content: "\e904"; }
    </style>
</head>
<body class="home page-template-default page page-id-5 wp-custom-logo theme-Avada woocommerce-no-js fusion-image-hovers fusion-body fusion-builder-auto-safari fusion-flex-container layout-wide-mode side-header-after-wrapper">
    <div id="boxed-wrapper">
        <div class="fusion-sides-frame"></div>
        <div id="wrapper" class="fusion-wrapper">
            <div id="home" style="position:relative;top:-1px;"></div>
            
            {{-- Header Component --}}
            @include('components.header')

            {{-- Main Page Content --}}
            @yield('content')

            {{-- Footer Component --}}
            @include('components.footer')
        </div>
    </div>

    <!-- Scripts -->
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/add-to-cart.min.js"></script>
    <script src="/assets/js/woocommerce.min.js"></script>
    <script src="/assets/js/avada.min.js"></script>
    
    @stack('scripts')
</body>
</html>