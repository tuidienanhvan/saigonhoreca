
class HeroCarousel {
    constructor(selector) {
        this.container = document.querySelector(selector);
        if (!this.container) return;
        this.slides = Array.from(this.container.querySelectorAll('.carousel-slide'));
        this.dots = Array.from(this.container.querySelectorAll('.carousel-dot'));
        this.prevBtn = document.getElementById('hero-prev');
        this.nextBtn = document.getElementById('hero-next');
        if (this.slides.length === 0) return;
        this.activeIndex = 0;
        this.isAnimating = false;
        this.interval = null;
        this.intervalTime = 6000;
        this.safetyTimer = null; 
        try {
            this.init();
        } catch (error) {
            this.container.classList.remove('is-hero-ready');
            this.isAnimating = false;
        }
    }
    init() {
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
        this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
        this.container.addEventListener('mouseleave', () => this.startAutoPlay());
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopAutoPlay();
            } else {
                this.isAnimating = false;
                this.startAutoPlay();
            }
        });
        this.startAutoPlay();
    }
    restartSlideBackgroundAnimation(slide) {
        if (!slide) return;
        const bg = slide.querySelector('.slide-bg');
        if (!bg) return;
        bg.style.animation = 'none';
        bg.style.transform = '';
        void bg.offsetWidth; 
        const dur = getComputedStyle(this.container).getPropertyValue('--sh-ken-burns-duration') || '6s';
        bg.style.animation = 'shKenBurnsZoom ' + dur + ' linear forwards';
    }
    goTo(index) {
        if (index === this.activeIndex) return;
        if (this.isAnimating) return; 
        if (this.slides.length <= 1) return;
        this.isAnimating = true;
        if (index >= this.slides.length) index = 0;
        if (index < 0) index = this.slides.length - 1;
        const currentSlide = this.slides[this.activeIndex];
        const nextSlide = this.slides[index];
        this.container.classList.add('is-hero-ready');
        nextSlide.style.display = 'block';
        nextSlide.style.zIndex = '30';
        nextSlide.style.opacity = '0';
        nextSlide.classList.add('incoming');
        this.restartSlideBackgroundAnimation(nextSlide);
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                nextSlide.style.transition = 'opacity 1.2s ease-in-out';
                nextSlide.style.opacity = '1';
            });
        });
        let isCleanedUp = false;
        const onTransitionEnd = (e) => {
            if (e && e.target !== nextSlide) return;
            if (e && e.propertyName !== 'opacity') return;
            cleanup();
        };
        nextSlide.addEventListener('transitionend', onTransitionEnd);
        const cleanup = () => {
            if (isCleanedUp) return;
            isCleanedUp = true;
            clearTimeout(this.safetyTimer);
            this.safetyTimer = null;
            nextSlide.removeEventListener('transitionend', onTransitionEnd);
            currentSlide.classList.remove('active');
            currentSlide.style.display = 'none';
            currentSlide.style.opacity = '0';
            currentSlide.style.zIndex = '0';
            nextSlide.classList.remove('incoming');
            nextSlide.classList.add('active');
            nextSlide.style.zIndex = '20';
            nextSlide.style.transition = ''; // Xóa inline transition
            nextSlide.querySelectorAll('.hero-content').forEach(el => {
                el.style.opacity = '';
                el.style.transform = '';
            });
            currentSlide.querySelectorAll('.hero-content').forEach(el => {
                el.style.opacity = '';
                el.style.transform = '';
            });
            const currentBg = currentSlide.querySelector('.slide-bg');
            if (currentBg) {
                currentBg.style.animation = '';
                currentBg.style.transform = '';
            }
            this.activeIndex = index;
            this.updateDots(index);
            this.isAnimating = false;
        };
        clearTimeout(this.safetyTimer);
        this.safetyTimer = setTimeout(() => {
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
            if (isActive) dot.setAttribute('aria-current', 'true');
            else dot.removeAttribute('aria-current');
            dot.setAttribute('tabindex', isActive ? '0' : '-1');
        });
    }
    startAutoPlay() {
        if (this.slides.length <= 1) return;
        if (this.interval) clearInterval(this.interval);
        if (this.firstSlideTimer) clearTimeout(this.firstSlideTimer);
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
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeroCarousel, { once: true });
} else {
    initHeroCarousel();
}
