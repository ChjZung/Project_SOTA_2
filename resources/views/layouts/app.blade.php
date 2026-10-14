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
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css" media="all" />

    <style>
        /* Base Reset & Smooth Scroll */
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Sen', sans-serif;
            color: #4a5568;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        img { max-width: 100%; height: auto; display: block; }

        /* Font Awesome / Avada Custom Icons */
        .awb-vegan-storeuser::before { content: "\e900"; font-family: "Avada-Vegan-Store", "awb-icons" !important; }
        .awb-vegan-storecart::before { content: "\e901"; font-family: "Avada-Vegan-Store", "awb-icons" !important; }
        .awb-vegan-storemenu::before { content: "\e902"; font-family: "Avada-Vegan-Store", "awb-icons" !important; }
        .awb-vegan-storeclose::before { content: "\e903"; font-family: "Avada-Vegan-Store", "awb-icons" !important; }
        .awb-vegan-storeicon-vegan::before { content: "\e904"; font-family: "Avada-Vegan-Store", "awb-icons" !important; }

        /* 1. Continuous Marquee Ticker (Top Bar) */
        .fusion-title-marquee {
            overflow: hidden !important;
            position: relative !important;
            width: 100% !important;
        }
        .fusion-title-marquee .fusion-title-heading {
            display: flex !important;
            width: max-content !important;
            white-space: nowrap !important;
            animation: marqueeContinuous 32s linear infinite !important;
        }
        .fusion-title-marquee:hover .fusion-title-heading {
            animation-play-state: paused !important;
        }
        .fusion-title-marquee .awb-marquee-content {
            display: inline-block !important;
            padding-right: 80px !important;
            flex-shrink: 0 !important;
        }
        @keyframes marqueeContinuous {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* 2. Hero Background Zoom & Cross-Fade Slider */
        .awb-background-slider {
            position: absolute !important;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
            overflow: hidden !important;
            z-index: 1 !important;
            pointer-events: none !important;
        }
        .awb-background-slider .swiper-wrapper {
            position: relative !important;
            width: 100% !important;
            height: 100% !important;
            display: block !important;
        }
        .awb-background-slider .swiper-slide {
            position: absolute !important;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
            opacity: 0;
            transform: scale(1.0);
            transition: opacity 1.8s ease-in-out, transform 7s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
        }
        .awb-background-slider .swiper-slide.is-active,
        .awb-background-slider .swiper-slide.swiper-slide-active {
            opacity: 1 !important;
            transform: scale(1.08) !important;
        }
        .awb-background-slider img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
        }

        /* 3. Scroll-Triggered Fade-In / Fade-Up Animations */
        .reveal-fade-in,
        .fusion-animated {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1) !important;
            will-change: opacity, transform;
            visibility: visible !important;
        }
        .reveal-fade-in.is-visible,
        .fusion-animated.is-visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        /* 4. Swiper Product Carousels Styling */
        .awb-swiper-carousel {
            overflow: hidden !important;
            position: relative !important;
            padding-bottom: 30px !important;
        }
        .awb-swiper-carousel > .swiper-wrapper {
            display: flex !important;
            flex-wrap: nowrap !important;
            transition-property: transform !important;
            box-sizing: content-box !important;
        }
        .awb-swiper-carousel .swiper-slide {
            flex-shrink: 0 !important;
            height: auto !important;
            box-sizing: border-box !important;
        }
        .awb-swiper-button-prev,
        .awb-swiper-button-next {
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 44px !important;
            height: 44px !important;
            background: #ffffff !important;
            color: #2d5a27 !important;
            border-radius: 50% !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12) !important;
            cursor: pointer !important;
            z-index: 25 !important;
            transition: all 0.3s ease !important;
            position: absolute !important;
            top: 40% !important;
            transform: translateY(-50%) !important;
        }
        .awb-swiper-button-prev { left: 10px !important; }
        .awb-swiper-button-next { right: 10px !important; }
        .awb-swiper-button-prev:hover,
        .awb-swiper-button-next:hover {
            background: #2d5a27 !important;
            color: #ffffff !important;
            transform: translateY(-50%) scale(1.1) !important;
        }
        .awb-swiper-button-prev::before { content: "‹"; font-size: 28px; line-height: 1; font-weight: 300; }
        .awb-swiper-button-next::before { content: "›"; font-size: 28px; line-height: 1; font-weight: 300; }
        
        .swiper-pagination {
            position: relative !important;
            margin-top: 25px !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .swiper-pagination-bullet {
            width: 10px !important;
            height: 10px !important;
            background: #cbd5e0 !important;
            opacity: 0.6 !important;
            border-radius: 50% !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }
        .swiper-pagination-bullet-active {
            background: #2d5a27 !important;
            opacity: 1 !important;
            transform: scale(1.35) !important;
        }

        /* 5. Product Cards Hover Zoom & Lift */
        .post-card,
        .product-grid-view,
        .fusion-post-cards-grid-column {
            transition: transform 0.35s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.35s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
            border-radius: 8px;
        }
        .post-card:hover,
        .product-grid-view:hover {
            transform: translateY(-6px) !important;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.08) !important;
        }
        .fusion-image-wrapper,
        .fusion-imageframe {
            overflow: hidden !important;
            border-radius: 8px !important;
        }
        .fusion-image-wrapper img,
        .fusion-imageframe img {
            transition: transform 0.65s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
            will-change: transform;
        }
        .post-card:hover .fusion-image-wrapper img,
        .post-card:hover .fusion-imageframe img,
        .product-grid-view:hover img,
        .fusion-image-element:hover img,
        .hover-type-zoomin:hover img {
            transform: scale(1.07) !important;
        }

        /* 6. Buttons Avada Slide Fill Animation */
        .fusion-button {
            position: relative !important;
            overflow: hidden !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.35s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
            border-radius: 6px !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px !important;
            z-index: 1 !important;
        }
        .fusion-button::before {
            content: "" !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: var(--button_gradient_top_color_hover, #1f421b) !important;
            transform: scaleX(0) !important;
            transform-origin: left center !important;
            transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
            z-index: -1 !important;
            border-radius: inherit !important;
        }
        .fusion-button:hover::before {
            transform: scaleX(1) !important;
        }
        .fusion-button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(45, 90, 39, 0.28) !important;
        }
        .fusion-button .awb-button__icon {
            display: inline-block !important;
            transition: transform 0.3s ease !important;
            margin-left: 8px !important;
        }
        .fusion-button:hover .awb-button__icon {
            transform: translateX(5px) !important;
        }

        /* 7. Dropdown Menu & Mega Menu Transitions */
        .awb-menu__main-li {
            position: relative;
        }
        .awb-menu__sub-ul,
        .awb-menu__mega-wrap {
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
            transform: translateY(12px) !important;
            transition: opacity 0.25s cubic-bezier(0.2, 0.8, 0.2, 1), transform 0.25s cubic-bezier(0.2, 0.8, 0.2, 1), visibility 0.25s !important;
            display: block !important;
            z-index: 99999 !important;
        }
        .awb-menu__main-li:hover > .awb-menu__sub-ul,
        .awb-menu__main-li:hover > .awb-menu__mega-wrap,
        .awb-menu__main-li.hovered > .awb-menu__sub-ul,
        .awb-menu__main-li.hovered > .awb-menu__mega-wrap {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
            transform: translateY(0) !important;
        }

        /* 8. Off-Canvas Cart Popup Modal (#awb-oc-1369) */
        #awb-oc-1369 {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.35s ease, visibility 0.35s ease;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(4px);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #awb-oc-1369.is-open {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
        }
        #awb-oc-1369 .awb-off-canvas {
            transform: translateY(35px) scale(0.96);
            opacity: 0;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s ease;
            max-width: 550px;
            width: 90%;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
            padding: 24px;
            position: relative;
        }
        #awb-oc-1369.is-open .awb-off-canvas {
            transform: translateY(0) scale(1) !important;
            opacity: 1 !important;
        }
        .off-canvas-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
            z-index: 10;
        }

        /* 9. Off-Canvas Sliding Side Drawer (#awb-oc-2892) */
        #awb-oc-2892 {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(4px);
            z-index: 9999999;
            opacity: 0;
            transition: opacity 0.35s ease;
        }
        #awb-oc-2892.is-open {
            display: block !important;
            opacity: 1 !important;
        }
        #awb-oc-2892 .awb-off-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 380px;
            max-width: 85vw;
            height: 100vh;
            background: #ffffff;
            box-shadow: 10px 0 50px rgba(0,0,0,0.25);
            transform: translateX(-100%);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            overflow-y: auto;
            padding: 32px 24px;
        }
        #awb-oc-2892.is-open .awb-off-canvas {
            transform: translateX(0) !important;
        }

        /* 10. Sticky Header on Scroll */
        .fusion-tb-header {
            transition: all 0.3s ease;
        }
        .fusion-tb-header.header-sticky {
            position: sticky;
            top: -46px; /* Hides top marquee, keeps main nav fixed */
            z-index: 99999;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
            background: #ffffff;
        }

        /* 11. Floating Back to Top Button */
        #toTop {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 46px;
            height: 46px;
            background: #2d5a27;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 20px rgba(0,0,0,0.18);
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: opacity 0.35s ease, transform 0.35s ease, visibility 0.35s, background-color 0.25s;
            z-index: 9999;
            cursor: pointer;
            text-decoration: none;
        }
        #toTop.is-visible {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        #toTop:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25) !important;
            background: #44803f !important;
        }
        #toTop::before {
            content: "▲";
            font-size: 13px;
            line-height: 1;
        }

        /* 12. Category Navigation Hover */
        .fusion-builder-nested-column-0,
        .fusion-builder-nested-column-1,
        .fusion-builder-nested-column-2,
        .fusion-builder-nested-column-3,
        .fusion-builder-nested-column-4 {
            transition: transform 0.25s ease, background-color 0.25s ease;
            border-radius: 6px;
            padding: 4px 8px;
        }
        .fusion-builder-nested-column-0:hover,
        .fusion-builder-nested-column-1:hover,
        .fusion-builder-nested-column-2:hover,
        .fusion-builder-nested-column-3:hover,
        .fusion-builder-nested-column-4:hover {
            transform: translateX(8px);
            background-color: #f7fafc;
        }
    </style>
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
            @include('components.side-menu')
        </div>
    </div>

    <!-- Floating Back to Top Button -->
    <a href="#" id="toTop" aria-label="Go to Top"></a>

    <!-- Scripts -->
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/swiper-bundle.min.js"></script>
    <script src="/assets/js/add-to-cart.min.js"></script>
    <script src="/assets/js/woocommerce.min.js"></script>
    <script src="/assets/js/avada.min.js"></script>

    <script>
        // Global Avada Config
        window.off_canvas_1369 = {"type":"popup","width":"550px","height":"fit"};
        window.off_canvas_2892 = {"type":"sliding-bar","height":"full","position":"left"};
        var fusionNavIsCollapsed = function(e) {
            if (!e) return;
            var bp = parseInt(e.getAttribute("data-breakpoint")) || 800;
            if (window.innerWidth <= bp) {
                e.classList.add("collapse-enabled");
                e.classList.remove("awb-menu_desktop");
            } else {
                e.classList.remove("collapse-enabled");
                e.classList.add("awb-menu_desktop");
            }
        };

        $(document).ready(function() {
            // 1. Hero Background Slider Auto-Rotate & Zoom
            const bgSlides = document.querySelectorAll('.awb-background-slider .swiper-slide');
            if (bgSlides.length > 0) {
                let currentSlide = 0;
                bgSlides[0].classList.add('is-active');
                
                if (bgSlides.length > 1) {
                    setInterval(() => {
                        bgSlides[currentSlide].classList.remove('is-active');
                        currentSlide = (currentSlide + 1) % bgSlides.length;
                        bgSlides[currentSlide].classList.add('is-active');
                    }, 5000);
                }
            }

            // 2. Swiper Product Carousels Initialization
            if (typeof Swiper !== 'undefined') {
                document.querySelectorAll('.fusion-post-cards.awb-swiper-carousel').forEach(function(carousel) {
                    const prevBtn = carousel.querySelector('.awb-swiper-button-prev');
                    const nextBtn = carousel.querySelector('.awb-swiper-button-next');
                    const paginationEl = carousel.querySelector('.swiper-pagination');
                    const cols = parseInt(carousel.getAttribute('data-columns')) || 3;
                    const margin = parseInt(carousel.getAttribute('data-itemmargin')) || 24;

                    new Swiper(carousel, {
                        slidesPerView: 1,
                        spaceBetween: 20,
                        loop: true,
                        speed: 600,
                        grabCursor: true,
                        navigation: {
                            prevEl: prevBtn,
                            nextEl: nextBtn
                        },
                        pagination: {
                            el: paginationEl,
                            clickable: true
                        },
                        breakpoints: {
                            600: {
                                slidesPerView: 2,
                                spaceBetween: margin
                            },
                            1024: {
                                slidesPerView: cols,
                                spaceBetween: margin
                            }
                        }
                    });
                });
            }

            // 3. Scroll-triggered Fade-In animations (Observer)
            const observerOptions = {
                root: null,
                rootMargin: '0px 0px -30px 0px',
                threshold: 0.08
            };

            const animateElements = document.querySelectorAll('.fusion-animated, .post-card, .fusion-column-wrapper, .fusion-title:not(.fusion-title-marquee)');
            
            animateElements.forEach(el => {
                el.classList.add('reveal-fade-in');
            });

            const scrollObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        entry.target.classList.add('do-animate');
                        const animType = entry.target.getAttribute('data-animationtype') || 'fadeInUp';
                        entry.target.classList.add(animType);
                    }
                });
            }, observerOptions);

            animateElements.forEach(el => scrollObserver.observe(el));

            // Immediate check for elements currently in viewport
            setTimeout(() => {
                animateElements.forEach(el => {
                    const rect = el.getBoundingClientRect();
                    if (rect.top < window.innerHeight) {
                        el.classList.add('is-visible');
                        el.classList.add('do-animate');
                    }
                });
            }, 80);

            // 4. Sticky Header & Back to Top visibility on scroll
            const header = document.querySelector('.fusion-tb-header');
            const toTop = document.getElementById('toTop');

            window.addEventListener('scroll', () => {
                const currentScrollY = window.scrollY;
                if (header) {
                    if (currentScrollY > 120) {
                        header.classList.add('header-sticky');
                    } else {
                        header.classList.remove('header-sticky');
                    }
                }
                if (toTop) {
                    if (currentScrollY > 350) {
                        toTop.classList.add('is-visible');
                    } else {
                        toTop.classList.remove('is-visible');
                    }
                }
            }, { passive: true });

            // Back to top click
            $('#toTop').on('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // 5. Off-canvas Cart Popup toggle
            $('.avada-main-menu-cart, .fusion-widget-cart a, a[href="#awb-open-oc__1369"]').on('click', function(e) {
                e.preventDefault();
                $('#awb-oc-1369').addClass('is-open');
            });

            // 6. Off-canvas Side Drawer toggle (hamburger icon)
            $('a[href="#awb-open-oc__2892"], .awb-vegan-storemenu').on('click', function(e) {
                e.preventDefault();
                $('#awb-oc-2892').addClass('is-open');
            });

            // Close buttons for modals
            $('.off-canvas-close, #awb-oc-1369, #awb-oc-2892').on('click', function(e) {
                if (e.target === this || $(e.target).hasClass('off-canvas-close') || $(e.target).hasClass('awb-vegan-storeclose')) {
                    $('#awb-oc-1369, #awb-oc-2892').removeClass('is-open');
                }
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('#awb-oc-1369, #awb-oc-2892').removeClass('is-open');
                }
            });

            // 7. Mobile menu button toggle
            $('.awb-menu__m-toggle').on('click', function(e) {
                e.preventDefault();
                const menu = $(this).closest('.awb-menu').find('.awb-menu__main-ul');
                menu.toggleClass('mobile-active');
                if (menu.hasClass('mobile-active')) {
                    menu.css({
                        'display': 'block',
                        'background': '#ffffff',
                        'position': 'absolute',
                        'top': '100%',
                        'left': '0',
                        'width': '100%',
                        'box-shadow': '0 10px 30px rgba(0,0,0,0.1)',
                        'z-index': '99999'
                    });
                } else {
                    menu.css('display', '');
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>