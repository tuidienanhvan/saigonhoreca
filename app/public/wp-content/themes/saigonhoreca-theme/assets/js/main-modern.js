
function shDebounce(fn, ms) { var t; return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), ms || 100); }; }
document.addEventListener('DOMContentLoaded', () => {
    const header = document.getElementById('main-header');
    if (header) {
        let headerTicking = false;
        const updateHeaderState = () => {
            if (window.scrollY > 50) {
                header.classList.add('sh-header--scrolled');
            } else {
                header.classList.remove('sh-header--scrolled');
            }
            headerTicking = false;
        };
        window.addEventListener('scroll', () => {
            if (headerTicking) return;
            headerTicking = true;
            window.requestAnimationFrame(updateHeaderState);
        }, { passive: true });
        updateHeaderState();
    }
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const mobileOverlay = document.getElementById('mobile-sidebar-overlay');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const mobileTpl = document.getElementById('sgh-mobile-menu-tpl');
    let mobileHydrated = false;
    function bindSubmenuAccordion(root) {
        const submenuToggles = root.querySelectorAll('.mobile-submenu-toggle');
        submenuToggles.forEach((toggle) => {
            const parentLi = toggle.closest('li');
            const submenu = parentLi ? parentLi.querySelector('.sub-menu') : null;
            if (submenu && !submenu.parentNode.classList.contains('sub-menu-wrapper')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'sub-menu-wrapper';
                submenu.parentNode.insertBefore(wrapper, submenu);
                wrapper.appendChild(submenu);
                submenu.classList.remove('hidden');
            }
            toggle.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                const wrapper = parentLi.querySelector('.sub-menu-wrapper');
                const icon = toggle.querySelector('svg');
                submenuToggles.forEach((otherToggle) => {
                    if (otherToggle === toggle) return;
                    const otherLi = otherToggle.closest('li');
                    const otherWrapper = otherLi.querySelector('.sub-menu-wrapper');
                    const otherIcon = otherToggle.querySelector('svg');
                    if (otherWrapper && otherWrapper.classList.contains('is-open')) {
                        otherWrapper.classList.remove('is-open');
                        if (otherIcon) otherIcon.style.transform = '';
                        otherToggle.classList.remove('is-active');
                    }
                });
                if (!wrapper) return;
                const isOpen = wrapper.classList.toggle('is-open');
                if (icon) icon.style.transform = isOpen ? 'rotate(180deg)' : '';
                toggle.classList.toggle('is-active', isOpen);
            });
        });
    }
    function hydrateMobileMenu() {
        if (mobileHydrated || !mobileTpl || !mobileSidebar) return;
        mobileSidebar.appendChild(mobileTpl.content.cloneNode(true));
        mobileHydrated = true;
        bindSubmenuAccordion(mobileSidebar);
        const closeBtn = mobileSidebar.querySelector('#mobile-menu-close');
        if (closeBtn) closeBtn.addEventListener('click', () => toggleSidebar(false));
    }
    function toggleSidebar(show) {
        if (!mobileSidebar || !mobileOverlay) return;
        if (show) {
            hydrateMobileMenu();
            mobileSidebar.classList.add('is-open');
            mobileOverlay.classList.add('is-visible');
            document.body.classList.add('overflow-hidden');
        } else {
            mobileSidebar.classList.remove('is-open');
            mobileOverlay.classList.remove('is-visible');
            document.body.classList.remove('overflow-hidden');
        }
    }
    if (mobileToggle) mobileToggle.addEventListener('click', () => toggleSidebar(true));
    if (mobileOverlay) mobileOverlay.addEventListener('click', () => toggleSidebar(false));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') toggleSidebar(false);
    });
    document.querySelectorAll('li.sh-nav-item[data-sgh-dropdown="lazy"]').forEach((li) => {
        const tpl = li.querySelector('template.sh-dropdown-tpl');
        if (!tpl) return;
        const hydrate = () => {
            if (li.dataset.sghDropdown !== 'lazy') return;
            li.appendChild(tpl.content.cloneNode(true));
            li.dataset.sghDropdown = 'mounted';
        };
        li.addEventListener('pointerenter', hydrate, { once: true, passive: true });
        li.addEventListener('focusin', hydrate, { once: true });
    });
    const backToTop = document.getElementById('back-to-top');
    const progressCircle = document.getElementById('scroll-progress-circle');
    if (backToTop) {
        const updateBackToTop = () => {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = docHeight > 0 ? Math.min(scrollTop / docHeight, 1) : 0;
            if (progressCircle) {
                progressCircle.style.strokeDashoffset = String(100 - (100 * scrollPercent));
            }
            if (scrollTop > 100) {
                backToTop.style.display = 'flex';
                backToTop.style.opacity = '1';
            } else {
                backToTop.style.display = 'none';
                backToTop.style.opacity = '0';
            }
        };
        window.addEventListener('scroll', shDebounce(updateBackToTop, 100), { passive: true });
        updateBackToTop();
        backToTop.addEventListener('click', (event) => {
            event.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    const tabContainers = document.querySelectorAll('#townhouse-tabs, #villa-tabs');
    tabContainers.forEach(container => {
        const buttons = container.querySelectorAll('.sh-tab-btn');
        const galleryGrid = container.parentElement.querySelector('#townhouse-gallery, #villa-gallery');
        if (!galleryGrid) return;
        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetCat = btn.dataset.tab;
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const items = galleryGrid.querySelectorAll('.sh-gallery-item');
                items.forEach((item, idx) => {
                    const itemCat = item.dataset.category;
                    if (itemCat === targetCat) {
                        item.classList.remove('hidden');
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(20px) scale(0.95)';
                        setTimeout(() => {
                            item.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                            item.style.opacity = '1';
                            item.style.transform = 'translateY(0) scale(1)';
                        }, idx * 50);
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });
        });
    });
    const scrollElements = document.querySelectorAll('.scroll-reveal');
    if (scrollElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target); 
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        scrollElements.forEach(el => observer.observe(el));
    }
    const lazyIframes = document.querySelectorAll('iframe[data-lazy-src]');
    if (lazyIframes.length > 0) {
        const loadIframe = (iframe) => {
            if (!iframe || !iframe.dataset.lazySrc || iframe.dataset.lazyLoaded === '1') return;
            iframe.src = iframe.dataset.lazySrc;
            iframe.dataset.lazyLoaded = '1';
            iframe.removeAttribute('data-lazy-src');
        };
        if ('IntersectionObserver' in window) {
            const iframeObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    loadIframe(entry.target);
                    observer.unobserve(entry.target);
                });
            }, {
                rootMargin: '350px 0px'
            });
            lazyIframes.forEach((iframe) => iframeObserver.observe(iframe));
        } else {
            lazyIframes.forEach(loadIframe);
        }
    }
    const tabButtons = document.querySelectorAll('[data-tab-target]');
    if (tabButtons.length > 0) {
        tabButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const target = btn.dataset.tabTarget;
                const container = btn.closest('[data-tab-container]');
                if (!container) return;
                const buttons = container.querySelectorAll('[data-tab-target]');
                buttons.forEach(b => {
                    if (b === btn) {
                        b.classList.add('active');
                    } else {
                        b.classList.remove('active');
                    }
                });
                const contentGroup = container.getAttribute('data-tab-group');
                const contents = document.querySelectorAll(`[data-tab-group="${contentGroup}"] .sh-tab-item`);
                contents.forEach(item => {
                    const itemCat = item.dataset.category;
                    if (target === 'all' || itemCat === target) {
                        item.classList.remove('hidden');
                        item.classList.remove('animate-in-fade');
                        void item.offsetWidth;
                        item.classList.add('animate-in-fade');
                        item.addEventListener('animationend', () => {
                            item.classList.remove('animate-in-fade');
                        }, { once: true });
                    } else {
                        item.classList.add('hidden');
                        item.classList.remove('animate-in-fade');
                    }
                });
            });
        });
    }
    document.querySelectorAll('.animate-in-fade').forEach(el => {
        el.addEventListener('animationend', () => {
            el.classList.remove('animate-in-fade');
        }, { once: true });
    });
    (function() {
        if (typeof dataLayer === 'undefined') return;
        const depthsMark = [25, 50, 75, 100];
        const depthsFired = new Set();
        const pageType = document.body.classList.contains('single-post') ? 'post'
            : document.body.classList.contains('page-template-page-bang-gia') ? 'pricing'
            : document.body.classList.contains('front-page') ? 'homepage' : 'other';
        function getScrollDepthPct() {
            const doc = document.documentElement;
            const scrollTop = window.scrollY || doc.scrollTop;
            const maxScroll = doc.scrollHeight - doc.clientHeight;
            return maxScroll > 0 ? Math.round((scrollTop / maxScroll) * 100) : 0;
        }
        function onScrollDepth() {
            const pct = getScrollDepthPct();
            depthsMark.forEach(function(depth) {
                if (!depthsFired.has(depth) && pct >= depth) {
                    depthsFired.add(depth);
                    dataLayer.push({ event: 'scroll_depth', depth: depth + '%', page_type: pageType });
                    if (depthsFired.size === depthsMark.length) {
                        window.removeEventListener('scroll', onScrollDepth, { passive: true });
                    }
                }
            });
        }
        window.addEventListener('scroll', onScrollDepth, { passive: true });
    })();
    const animatedEls = document.querySelectorAll('[class*="animate-spin"]');
    if (animatedEls.length > 0) {
        const animObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                entry.target.style.animationPlayState = entry.isIntersecting ? 'running' : 'paused';
            });
        }, { rootMargin: '100px' });
        animatedEls.forEach(el => {
            el.style.animationPlayState = 'paused';
            animObserver.observe(el);
        });
    }
    document.addEventListener('click', function(e) {
        var popup = e.target.closest('[data-share-action="popup"]');
        if (popup) {
            e.preventDefault();
            if (window.innerWidth > 640) {
                window.open(popup.href, 'share', 'width=600,height=500,scrollbars=yes');
            } else {
                window.open(popup.href, '_blank');
            }
            return;
        }
        var copy = e.target.closest('[data-share-action="copy"]');
        if (copy) {
            var url = copy.dataset.shareUrl;
            var label = copy.querySelector('span');
            if (url && navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    if (label) {
                        label.textContent = '�� copy!';
                        setTimeout(function() { label.textContent = 'Copy link'; }, 2000);
                    }
                });
            }
        }
    });
});
