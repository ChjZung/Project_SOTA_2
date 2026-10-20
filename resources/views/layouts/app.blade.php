<!DOCTYPE html>
<html class="avada-html-layout-wide avada-html-header-position-top avada-is-100-percent-template" lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <base href="{{ url('/') }}/">

    <title>@yield('title', 'Avada Vegan Store - 100% Plant-Based & Organic Store')</title>
    <meta name="description" content="@yield('description', 'A Customizable Pre-built Vegan Store Styled Website. 100% Organic, Vegan & Plant-Based Products with Fast Delivery.')">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('wp-content/uploads/sites/186/2024/02/favicon-2.png') }}" type="image/x-icon" />

    <!-- Local styles and fonts -->
    <link rel="stylesheet" href="{{ asset('assets/css/wc-blocks.css') }}" media="all" />
    <link rel="stylesheet" href="{{ asset('assets/css/fss-section-copy.css') }}" media="all" />
    <link rel="stylesheet" href="{{ asset('assets/css/avada-style.css') }}" media="all" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom-inline.css') }}" media="all" />
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}" media="all" />

    <link rel="stylesheet" href="{{ asset('assets/css/storefront.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/scroll-reveal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/commerce.css') }}">
    @stack('styles')
</head>
<body class="home wp-singular page-template page-template-100-width page-template-100-width-php page page-id-5 wp-theme-Avada theme-Avada woocommerce-js awb-no-sidebars fusion-image-hovers fusion-pagination-sizing fusion-button_type-flat fusion-button_span-no fusion-button_gradient-linear avada-image-rollover-circle-yes avada-image-rollover-yes avada-image-rollover-direction-left fusion-body ltr fusion-sticky-header no-tablet-sticky-header no-mobile-sticky-header no-mobile-slidingbar no-mobile-totop fusion-disable-outline fusion-sub-menu-fade mobile-logo-pos-left layout-wide-mode avada-has-boxed-modal-shadow-none layout-scroll-offset-full avada-has-zero-margin-offset-top fusion-top-header menu-text-align-center fusion-woo-product-design-classic fusion-woo-shop-page-columns-4 fusion-woo-related-columns-4 fusion-woo-archive-page-columns-3 avada-has-woo-gallery-disabled woo-outofstock-badge-top_bar mobile-menu-design-classic fusion-show-pagination-text fusion-header-layout-v3 avada-responsive avada-footer-fx-none avada-menu-highlight-style-bar fusion-search-form-clean fusion-main-menu-search-overlay fusion-avatar-circle avada-dropdown-styles avada-blog-layout-large avada-blog-archive-layout-large avada-header-shadow-no avada-menu-icon-position-left avada-has-megamenu-shadow avada-has-mobile-menu-search avada-has-main-nav-search-icon avada-has-breadcrumb-mobile-hidden avada-has-titlebar-hide avada-header-border-color-full-transparent avada-has-pagination-width_height avada-flyout-menu-direction-fade avada-ec-views-v1 awb-link-decoration" data-awb-post-id="5">
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

            {{-- Side Menu Drawer Component --}}
            @include('components.storefront-drawers')
        </div>
    </div>

    <!-- Floating Back to Top Button -->
    <a href="#" id="toTop" aria-label="Go to Top"></a>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/swiper-bundle.min.js') }}" defer></script>
    <script src="{{ asset('assets/js/storefront.js') }}" defer></script>
    <script src="{{ asset('assets/js/scroll-reveal.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
