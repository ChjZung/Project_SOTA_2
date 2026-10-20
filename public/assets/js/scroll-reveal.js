/* Reveal on downward entry; fade upward when scrolling back past the element. */
(() => {
    const home = document.querySelector('.storefront-home');
    if (!home || !('IntersectionObserver' in window)) return;

    const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const targets = new Set();

    home.querySelectorAll('.post-content > .fusion-fullwidth').forEach(section => {
        // Keep the hero immediately readable when opening the page.
        if (section.querySelector('.awb-background-slider')) return;

        section.querySelectorAll(':scope > .fusion-builder-row > .fusion-layout-column').forEach(column => {
            if (!column.querySelector('.fusion-post-cards')) {
                targets.add(column);
                return;
            }

            column.querySelectorAll('.fusion-title').forEach(title => {
                if (!title.closest('.fusion-post-cards')) targets.add(title);
            });
            column.querySelectorAll('.fusion-post-cards').forEach(cards => {
                // Animate the carousel container rather than its moving slides.
                if (cards.classList.contains('awb-swiper-carousel')) targets.add(cards);
                else cards.querySelectorAll('.post-card').forEach(card => targets.add(card));
            });
        });
    });

    document.querySelectorAll('.fusion-tb-footer .fusion-fullwidth > .fusion-builder-row > .fusion-layout-column')
        .forEach(column => targets.add(column));

    let observer;
    let resizeTimer;

    const configure = () => {
        observer?.disconnect();
        const boundary = window.innerHeight * .9;

        if (motion.matches) {
            targets.forEach(element => element.classList.remove('store-scroll-reveal', 'is-revealed', 'is-exiting-up'));
            return;
        }

        observer = new IntersectionObserver(entries => {
            entries.forEach(({ target, isIntersecting, boundingClientRect }) => {
                if (isIntersecting || boundingClientRect.top < boundary) {
                    target.classList.remove('is-exiting-up');
                    target.classList.add('is-revealed');
                } else {
                    // This edge is crossed when the page is scrolled upward.
                    target.classList.add('is-exiting-up');
                    target.classList.remove('is-revealed');
                }
            });
        }, { rootMargin: `0px 0px -${Math.round(window.innerHeight * .1)}px 0px`, threshold: 0 });

        [...targets].forEach((element, index) => {
            const initialized = element.classList.contains('store-scroll-reveal');
            element.style.setProperty('--reveal-delay', `${(index % 3) * 70}ms`);
            if (!initialized) {
                element.classList.toggle('is-revealed', element.getBoundingClientRect().top < boundary);
                element.classList.add('store-scroll-reveal');
            }
            observer.observe(element);
        });
    };

    motion.addEventListener('change', configure);
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(configure, 150);
    }, { passive: true });
    configure();
})();
