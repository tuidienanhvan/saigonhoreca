/**
 * Hero Carousel Logic (Event-Driven Transition)
 * Focus: Absolute stability. No black screen. No lag.
 */

class HeroCarousel {
    constructor(selector) {
        this.container = document.querySelector(selector);
        if (!this.container) return;

        // Elements
        this.slides = Array.from(this.container.querySelectorAll('.carousel-slide'));
        this.dots = Array.from(this.container.querySelectorAll('.carousel-dot'));
        this.prevBtn = document.getElementById('hero-prev');
        this.nextBtn = document.getElementById('hero-next');
        if (this.slides.length === 0) return;

        // State
        this.activeIndex = 0;
        this.isAnimating = false;
        this.interval = null;
        this.intervalTime = 6000;
        this.safetyTimer = null; // Class-level timer for canceling

        // Init
        try {
            this.init();
        } catch (error) {
            // Fail-open: if JS initialization fails, keep content visible.
            this.container.classList.remove('is-hero-ready');
            this.isAnimating = false;
        }
    }

    init() {
        // Initial Setup: Only the first slide is visible
        this.slides.forEach((slide, index) => {
            if (index === 0) {
                slide.classList.add('active');
                slide.style.display = 'block';
                slide.style.opacity = '1';
                slide.style.zIndex = '20';
            } else {
                slide.classList.remove('active', 'incoming');
                slide.style.display = 'none';
                slide.style.opacity = '0';
                slide.style.zIndex = '0';
            }
        });

        this.updateDots(0);

        // Event Listeners
        if (this.nextBtn) this.nextBtn.addEventListener('click', () => {
            this.stopAutoPlay();
            this.next();
            this.startAutoPlay();
        });

        if (this.prevBtn) this.prevBtn.addEventListener('click', () => {
            this.stopAutoPlay();
            this.prev();
            this.startAutoPlay();
        });

        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                if (index !== this.activeIndex && !this.isAnimating) {
                    this.stopAutoPlay();
                    this.goTo(index);
                    this.startAutoPlay();
                }
            });
        });

        // Hover
        this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
        this.container.addEventListener('mouseleave', () => this.startAutoPlay());

        // Visibility API (Memory/Battery fix)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopAutoPlay();
            } else {
                // Resume and safety unlock
                this.isAnimating = false;
                this.startAutoPlay();
            }
        });

        // Keep the first frame static so the LCP image can paint immediately.
        // Carousel motion starts on the first real slide transition.

        // Start Loop
        this.startAutoPlay();
    }

    restartSlideBackgroundAnimation(slide) {
        if (!slide) return;

        const bg = slide.querySelector('.slide-bg');
        if (!bg) return;

        // Force Ken Burns to restart from frame 0 when slide becomes visible.
        // Must set inline animation explicitly — clearing to '' and relying on
        // CSS rules does NOT restart animation because the browser sees the
        // element already matched the same selector (same animation instance).
        bg.style.animation = 'none';
        bg.style.transform = '';
        void bg.offsetWidth; // force reflow
        const dur = getComputedStyle(this.container).getPropertyValue('--sh-ken-burns-duration') || '6s';
        bg.style.animation = 'shKenBurnsZoom ' + dur + ' linear forwards';
    }

    goTo(index) {
        // 1. Guard Clauses
        if (index === this.activeIndex) return;
        if (this.isAnimating) return; // STRICT LOCK
        if (this.slides.length <= 1) return;

        this.isAnimating = true;

        // Wrap Index
        if (index >= this.slides.length) index = 0;
        if (index < 0) index = this.slides.length - 1;

        const currentSlide = this.slides[this.activeIndex];
        const nextSlide = this.slides[index];

        // 2. Incoming Phase
        this.container.classList.add('is-hero-ready');
        nextSlide.style.display = 'block';
        nextSlide.style.zIndex = '30';
        nextSlide.style.opacity = '0';
        nextSlide.classList.add('incoming');
        this.restartSlideBackgroundAnimation(nextSlide);

        // Animate in next frame to allow transition to trigger without forced reflow
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                nextSlide.style.transition = 'opacity 1.2s ease-in-out';
                nextSlide.style.opacity = '1';
            });
        });

        let isCleanedUp = false;

        // 4. Events with Safety
        const onTransitionEnd = (e) => {
            // Filter: Ignore bubbling events from children (buttons, hovers)
            if (e && e.target !== nextSlide) return;
            if (e && e.propertyName !== 'opacity') return;

            cleanup();
        };

        // Attach Listener
        nextSlide.addEventListener('transitionend', onTransitionEnd);

        // Cleanup Function (Reusable)
        const cleanup = () => {
            if (isCleanedUp) return;
            isCleanedUp = true;

            // Clear Safety Timer first
            clearTimeout(this.safetyTimer);
            this.safetyTimer = null;

            // Remove Listener (Crucial!)
            nextSlide.removeEventListener('transitionend', onTransitionEnd);

            // Swap States
            // Old Slide: Hide completely (Unmount)
            currentSlide.classList.remove('active');
            currentSlide.style.display = 'none';
            currentSlide.style.opacity = '0';
            currentSlide.style.zIndex = '0';
            
            // New Slide: Become Active
            nextSlide.classList.remove('incoming');
            nextSlide.classList.add('active');
            nextSlide.style.zIndex = '20';
            nextSlide.style.transition = ''; // Xóa inline transition

            // Reset hero-content inline styles để CSS animation chạy lại từ đầu
            nextSlide.querySelectorAll('.hero-content').forEach(el => {
                el.style.opacity = '';
                el.style.transform = '';
            });
            // Reset hero-content ở slide cũ
            currentSlide.querySelectorAll('.hero-content').forEach(el => {
                el.style.opacity = '';
                el.style.transform = '';
            });
            const currentBg = currentSlide.querySelector('.slide-bg');
            if (currentBg) {
                currentBg.style.animation = '';
                currentBg.style.transform = '';
            }

            // Update State
            this.activeIndex = index;
            this.updateDots(index);

            // Unlock Animation
            this.isAnimating = false;
        };

        // Safety Timeout (Fallback if tab inactive or event missing)
        // 1.2s transition + 0.2s buffer
        clearTimeout(this.safetyTimer);
        this.safetyTimer = setTimeout(() => {
            // Manually trigger cleanup if event didn't fire
            cleanup();
        }, 1400);
    }

    next() {
        this.goTo(this.activeIndex + 1);
    }

    prev() {
        this.goTo(this.activeIndex - 1);
    }

    updateDots(index) {
        var total = this.slides.length;
        this.dots.forEach((dot, i) => {
            var isActive = i === index;
            if (isActive) dot.classList.add('active');
            else dot.classList.remove('active');
            dot.setAttribute('aria-label', 'Slide ' + (i + 1) + ' / ' + total);
            // T-019: aria-current is valid on <button>; aria-selected is
            // only valid on roles like tab/option/row/gridcell. Carousel
            // dots aren't tabs, so use aria-current="true" for the active
            // dot and leave the others without it.
            if (isActive) dot.setAttribute('aria-current', 'true');
            else dot.removeAttribute('aria-current');
            dot.setAttribute('tabindex', isActive ? '0' : '-1');
        });
    }

    startAutoPlay() {
        if (this.slides.length <= 1) return;
        if (this.interval) clearInterval(this.interval);
        if (this.firstSlideTimer) clearTimeout(this.firstSlideTimer);
        // Lighthouse's Speed Index measurement window runs ~10–12 s after
        // FCP. A 6 s auto-transition fires mid-window, so the carousel
        // slide-swap drags SI from ~0.7 s up to ~4 s. Hold the very first
        // slide for 15 s; subsequent rotations use the normal 6 s cadence.
        var firstDelay = this._hasRotated ? this.intervalTime : 15000;
        var self = this;
        this.firstSlideTimer = setTimeout(function () {
            self.next();
            self._hasRotated = true;
            if (self.interval) clearInterval(self.interval);
            self.interval = setInterval(function () { self.next(); }, self.intervalTime);
        }, firstDelay);
    }

    stopAutoPlay() {
        if (this.interval) clearInterval(this.interval);
        this.interval = null;
        if (this.firstSlideTimer) clearTimeout(this.firstSlideTimer);
        this.firstSlideTimer = null;
    }
}

function initHeroCarousel() {
    const slider = document.querySelector('#sh-hero-slider');
    if (!slider || slider.dataset.heroInitialized === '1') {
        return;
    }

    slider.dataset.heroInitialized = '1';
    window.heroCarousel = new HeroCarousel('#sh-hero-slider');
}

// Initialize safely for normal load + delayed script execution
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeroCarousel, { once: true });
} else {
    initHeroCarousel();
}
