// ============================================
// NHI BINH PLASTIC - MAIN JAVASCRIPT
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    // --- Mobile Menu Toggle ---
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            const icon = navToggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        });
    }

    // --- Hero Banner Slider (Tự động lia nhiều ảnh & bấm nút chuyển) ---
    const heroSlider = document.getElementById('heroSlider');
    if (heroSlider) {
        const slides = heroSlider.querySelectorAll('.hero-slide-item');
        const dotsContainer = document.getElementById('sliderDots');
        const prevBtn = document.getElementById('sliderPrev');
        const nextBtn = document.getElementById('sliderNext');
        let currentSlide = 0;
        let slideInterval = null;
        const totalSlides = slides.length;

        // Tạo chấm chỉ số (dots) tự động
        if (dotsContainer && totalSlides > 1) {
            dotsContainer.innerHTML = '';
            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('span');
                dot.classList.add('slider-dot');
                if (i === 0) dot.classList.add('active');
                dot.addEventListener('click', () => {
                    goToSlide(i);
                    restartAutoPlay();
                });
                dotsContainer.appendChild(dot);
            }
        }

        const updateDots = () => {
            const dots = dotsContainer ? dotsContainer.querySelectorAll('.slider-dot') : [];
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        };

        const goToSlide = (n) => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (n + totalSlides) % totalSlides;
            slides[currentSlide].classList.add('active');
            updateDots();
        };

        const nextSlide = () => goToSlide(currentSlide + 1);
        const prevSlide = () => goToSlide(currentSlide - 1);

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                nextSlide();
                restartAutoPlay();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                prevSlide();
                restartAutoPlay();
            });
        }

        const startAutoPlay = () => {
            if (totalSlides > 1) {
                slideInterval = setInterval(nextSlide, 4500); // Lia ảnh mỗi 4.5 giây
            }
        };

        const stopAutoPlay = () => {
            if (slideInterval) clearInterval(slideInterval);
        };

        const restartAutoPlay = () => {
            stopAutoPlay();
            startAutoPlay();
        };

        heroSlider.addEventListener('mouseenter', stopAutoPlay);
        heroSlider.addEventListener('mouseleave', startAutoPlay);

        // Hỗ trợ vuốt chuyển ảnh trên màn hình cảm ứng điện thoại
        let touchStartX = 0;
        let touchEndX = 0;
        heroSlider.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        heroSlider.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) {
                nextSlide();
                restartAutoPlay();
            } else if (touchEndX - touchStartX > 50) {
                prevSlide();
                restartAutoPlay();
            }
        }, { passive: true });

        startAutoPlay();
    }

    // --- Scroll Reveal (Fade In khi lướt tới) ---
    const revealElements = document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right, .fade-in-zoom, .reveal-on-scroll');
    
    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target); // Kích hoạt 1 lần khi cuộn tới
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        });

        revealElements.forEach(el => revealObserver.observe(el));
    } else {
        // Fallback nếu trình duyệt cũ không hỗ trợ
        revealElements.forEach(el => el.classList.add('revealed'));
    }

    // --- Back to Top ---
    const btnTop = document.getElementById('btn-back-top');
    if (btnTop) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 350) {
                btnTop.style.display = 'flex';
            } else {
                btnTop.style.display = 'none';
            }
        });
        btnTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // --- Auto-hide Alert ---
    const alert = document.getElementById('alert-success');
    if (alert) {
        setTimeout(() => alert.remove(), 5000);
    }
});