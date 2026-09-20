
// Auto-hide Pure Floating Overlay Scrollbar: 60/120fps Zero-Jank Compositor-Driven & Precision Draggable Engine
(function initAutoHideScrollbar() {
    // 1. Kiểm tra thiết bị thuần cảm ứng (Mobile / Tablet): Không khởi tạo overlay scrollbar tự chế
    if (window.matchMedia && window.matchMedia('(pointer: coarse) and (hover: none)').matches) {
        return;
    }

    const root = document.documentElement;
    const HIDE_DELAY = 1200; // ms
    const TRACK_INSET = 6;   // Khoảng cách đệm mép trên/dưới của track (px), khớp với top: 6px / bottom: 6px
    const MIN_THUMB = 36;    // Chiều cao tối thiểu của thumb (px)
    const MAX_THUMB = 120;   // Chiều cao tối đa của thumb (px)

    let track = null;
    let thumb = null;
    let scrollTimer = null;
    let isTicking = false;

    // Kiểm tra xem body có đang bị khóa cuộn (Modal / Mobile Drawer / Lightbox) hay không
    function isBodyScrollLocked() {
        const body = document.body;
        if (!body) return false;
        return body.classList.contains('overflow-hidden') || 
               body.style.overflow === 'hidden' || 
               root.classList.contains('overflow-hidden') ||
               root.style.overflow === 'hidden';
    }

    // Trạng thái tương tác kéo thả & hover (Precision Dragging & Tactile Feedback)
    let isDragging = false;
    let isHovering = false;
    let dragPointerId = null;
    let dragGrabOffsetY = 0; // Khoảng cách từ con trỏ chuột đến đỉnh của thumb khi bắt đầu bấm
    let pendingScrollTop = null;
    let dragRafId = null;
    let dragBound = false;

    // Bộ nhớ đệm thông số hình học (Metrics Cache) — Triệt tiêu hoàn toàn Layout Thrashing
    const metrics = {
        viewportHeight: 0,
        scrollHeight: 0,
        maxScroll: 0,
        thumbHeight: 48,
        maxTravel: 0,
        canScroll: false
    };

    function ensureElements() {
        if (!track) {
            track = document.getElementById('sh-overlay-scrollbar-track');
            if (!track && document.body) {
                track = document.createElement('div');
                track.id = 'sh-overlay-scrollbar-track';
                track.setAttribute('aria-hidden', 'true');
                track.setAttribute('role', 'presentation');
                document.body.appendChild(track);
            }
        } else if (!track.hasAttribute('aria-hidden')) {
            track.setAttribute('aria-hidden', 'true');
            track.setAttribute('role', 'presentation');
        }

        if (!thumb) {
            thumb = document.getElementById('sh-overlay-scrollbar-thumb');
            if (!thumb && document.body) {
                thumb = document.createElement('div');
                thumb.id = 'sh-overlay-scrollbar-thumb';
                thumb.setAttribute('aria-hidden', 'true');
                thumb.setAttribute('role', 'presentation');
                document.body.appendChild(thumb);
            }
        } else if (!thumb.hasAttribute('aria-hidden')) {
            thumb.setAttribute('aria-hidden', 'true');
            thumb.setAttribute('role', 'presentation');
        }

        if (track && thumb && !dragBound) {
            setupDragInteraction();
            dragBound = true;
        }

        return track && thumb;
    }

    /**
     * Phase 1: Metrics Recalculation (Layout Read / Write)
     * Chỉ thực thi khi khởi tạo, resize window, hoặc ResizeObserver phát hiện DOM thay đổi.
     * TUYỆT ĐỐI KHÔNG CHẠY TRONG SỰ KIỆN SCROLL ĐỂ TRÁNH FORCED SYNCHRONOUS LAYOUT THRASHING.
     */
    function updateMetrics() {
        if (!ensureElements()) return;

        const vpHeight = window.innerHeight;
        const totalHeight = root.scrollHeight || (document.body ? document.body.scrollHeight : 0);
        const maxSc = Math.max(0, totalHeight - vpHeight);

        metrics.viewportHeight = vpHeight;
        metrics.scrollHeight = totalHeight;
        metrics.maxScroll = maxSc;
        metrics.canScroll = maxSc > 2; // Biên an toàn 2px tránh sai số subpixel

        if (!metrics.canScroll) {
            track.style.display = 'none';
            thumb.style.display = 'none';
            root.classList.remove('is-scrolling', 'is-dragging', 'is-scrollbar-hovered');
            return;
        }

        track.style.display = '';
        thumb.style.display = '';

        // Chiều cao hữu dụng của ray trượt (viewport trừ 2 đầu đệm 6px)
        const trackTravelHeight = Math.max(0, vpHeight - (TRACK_INSET * 2));
        // Tính chiều cao thumb chuẩn theo tỷ lệ viewport / scrollHeight
        const computedThumbHeight = Math.max(MIN_THUMB, Math.min(MAX_THUMB, (vpHeight / totalHeight) * trackTravelHeight));
        metrics.thumbHeight = computedThumbHeight;
        metrics.maxTravel = Math.max(0, trackTravelHeight - computedThumbHeight);

        // Cập nhật thuộc tính hình học style.height DUY NHẤT một lần tại phase này
        thumb.style.height = `${computedThumbHeight}px`;

        // Đồng bộ ngay vị trí hiển thị hiện tại của thumb
        renderThumbPosition();
    }

    /**
     * Phase 2: Compositor-Only Thumb Position Render (GPU Acceleration)
     * Đọc window.pageYOffset và cập nhật qua GPU translate3d.
     * Không chạm vào scrollHeight/clientHeight, không đổi style.height -> 0 Layout, 0 Paint.
     * Chi phí thực thi < 0.2ms (hoàn toàn nằm trong Frame Budget 8.3ms của 120fps).
     */
    function renderThumbPosition(overrideTop) {
        if (!thumb || !metrics.canScroll) return;

        let currentTop;
        if (typeof overrideTop === 'number') {
            currentTop = overrideTop;
        } else {
            const scrollTop = window.pageYOffset || root.scrollTop || (document.body ? document.body.scrollTop : 0) || 0;
            const pct = metrics.maxScroll > 0 ? Math.max(0, Math.min(1, scrollTop / metrics.maxScroll)) : 0;
            currentTop = TRACK_INSET + (pct * metrics.maxTravel);
        }

        thumb.style.transform = `translate3d(0, ${currentTop.toFixed(2)}px, 0)`;
    }

    /**
     * rAF Ticking Callback cho nhịp cuộn
     */
    function onScrollTick() {
        if (!isDragging) {
            renderThumbPosition();
        }
        isTicking = false;
    }

    /**
     * Kích hoạt hiển thị thanh cuộn khi người dùng cuộn hoặc tương tác
     */
    function activateScrollbar() {
        if (!metrics.canScroll || isBodyScrollLocked()) return;

        root.classList.add('is-scrolling');

        // rAF Ticking Throttling: Khóa nhịp đồng bộ VSync (16.6ms @ 60Hz / 8.3ms @ 120Hz)
        if (!isTicking) {
            isTicking = true;
            window.requestAnimationFrame(onScrollTick);
        }

        // Auto-hide Timer: Tự động ẩn sau HIDE_DELAY ms (trừ khi đang kéo thả hoặc đang hover)
        if (scrollTimer) clearTimeout(scrollTimer);
        if (!isDragging && !isHovering) {
            scrollTimer = setTimeout(() => {
                if (!isDragging && !isHovering) {
                    root.classList.remove('is-scrolling');
                }
            }, HIDE_DELAY);
        }
    }

    /**
     * Tương tác chuột & cảm ứng chính xác: Kéo thumb (Draggable) và Nhấp track (Smooth Jump)
     */
    function setupDragInteraction() {
        if (!thumb || !track) return;

        // ── 1. Click vào Track: Nhảy mượt (smooth scroll) đến vị trí tương ứng ──
        track.addEventListener('pointerdown', (e) => {
            if (e.button !== 0) return; // Chỉ nhận click chuột trái
            if (e.target === thumb) return; // Bỏ qua nếu click trúng thumb

            e.preventDefault();
            if (!metrics.canScroll || metrics.maxTravel <= 0) return;

            // Tính vị trí click trên track sao cho tâm thumb khớp với điểm click
            const clickY = e.clientY;
            const desiredTop = clickY - (metrics.thumbHeight / 2);
            const clampedTop = Math.max(TRACK_INSET, Math.min(TRACK_INSET + metrics.maxTravel, desiredTop));
            const pct = (clampedTop - TRACK_INSET) / metrics.maxTravel;
            const targetScrollTop = pct * metrics.maxScroll;

            window.scrollTo({
                top: targetScrollTop,
                behavior: 'smooth'
            });

            activateScrollbar();
        });

        // ── 2. Kéo thả Thumb (Draggable Thumb): setPointerCapture + VSync rAF ──
        function onThumbPointerDown(e) {
            if (e.button !== 0) return;
            e.preventDefault();
            e.stopPropagation();

            if (!metrics.canScroll || metrics.maxTravel <= 0) return;

            isDragging = true;
            dragPointerId = e.pointerId;

            // Bắt sự kiện pointer trên thumb ngay cả khi chuột văng ra ngoài khung hình / mép màn hình
            if (typeof thumb.setPointerCapture === 'function') {
                try {
                    thumb.setPointerCapture(e.pointerId);
                } catch (_) {}
            }

            // Đo vị trí bấm tương đối bên trong thumb (grab offset) để tránh giật hình khi bắt đầu kéo
            const thumbRect = thumb.getBoundingClientRect();
            dragGrabOffsetY = e.clientY - thumbRect.top;

            // Kích hoạt trạng thái kéo: Giữ thanh cuộn luôn hiện, đổi cursor sang grabbing, tactile expansion
            root.classList.add('is-dragging', 'is-scrolling');
            thumb.classList.add('is-dragging');
            track.classList.add('is-dragging');

            if (scrollTimer) {
                clearTimeout(scrollTimer);
                scrollTimer = null;
            }

            // Gán fallback listeners trên window phòng ngừa trường hợp pointer capture bị ngắt
            window.addEventListener('pointermove', onPointerMove, { passive: false });
            window.addEventListener('pointerup', onPointerUp, { passive: false });
            window.addEventListener('pointercancel', onPointerUp, { passive: false });
        }

        function onPointerMove(e) {
            if (!isDragging || metrics.maxTravel <= 0) return;
            e.preventDefault();

            // Tính vị trí mới của thumb theo tọa độ chuột hiện tại trừ grab offset
            const desiredTop = e.clientY - dragGrabOffsetY;
            const clampedTop = Math.max(TRACK_INSET, Math.min(TRACK_INSET + metrics.maxTravel, desiredTop));
            const pct = (clampedTop - TRACK_INSET) / metrics.maxTravel;
            const targetScrollTop = pct * metrics.maxScroll;

            // Cập nhật vị trí hiển thị trực tiếp của thumb ngay lập tức (0ms visual latency)
            renderThumbPosition(clampedTop);

            // Đồng bộ cuộn trang với nhịp VSync qua requestAnimationFrame (instant cuộn 1:1, không lag)
            pendingScrollTop = targetScrollTop;
            if (!dragRafId) {
                dragRafId = requestAnimationFrame(() => {
                    if (pendingScrollTop !== null) {
                        window.scrollTo({
                            top: pendingScrollTop,
                            behavior: 'instant'
                        });
                        pendingScrollTop = null;
                    }
                    dragRafId = null;
                });
            }
        }

        function onPointerUp(e) {
            if (!isDragging) return;
            if (dragPointerId !== null && e.pointerId !== undefined && e.pointerId !== dragPointerId) {
                return;
            }

            isDragging = false;
            if (dragPointerId !== null && typeof thumb.releasePointerCapture === 'function') {
                try {
                    thumb.releasePointerCapture(dragPointerId);
                } catch (_) {}
            }
            dragPointerId = null;

            if (dragRafId) {
                cancelAnimationFrame(dragRafId);
                dragRafId = null;
                if (pendingScrollTop !== null) {
                    window.scrollTo({
                        top: pendingScrollTop,
                        behavior: 'instant'
                    });
                    pendingScrollTop = null;
                }
            }

            window.removeEventListener('pointermove', onPointerMove);
            window.removeEventListener('pointerup', onPointerUp);
            window.removeEventListener('pointercancel', onPointerUp);

            root.classList.remove('is-dragging');
            thumb.classList.remove('is-dragging');
            track.classList.remove('is-dragging');

            // Đồng bộ lại chính xác vị trí thumb từ scrollTop thực tế của trang
            renderThumbPosition();

            // Khởi động lại bộ đếm tự động ẩn
            if (scrollTimer) clearTimeout(scrollTimer);
            scrollTimer = setTimeout(() => {
                if (!isDragging && !isHovering) {
                    root.classList.remove('is-scrolling');
                }
            }, HIDE_DELAY);
        }

        thumb.addEventListener('pointerdown', onThumbPointerDown);

        // ── 3. Hover Feedback: Giữ scrollbar không bị ẩn khi đang rê chuột vào thanh cuộn ──
        const onEnter = () => {
            isHovering = true;
            root.classList.add('is-scrolling', 'is-scrollbar-hovered');
            if (scrollTimer) {
                clearTimeout(scrollTimer);
                scrollTimer = null;
            }
        };

        const onLeave = () => {
            isHovering = false;
            root.classList.remove('is-scrollbar-hovered');
            if (!isDragging) {
                if (scrollTimer) clearTimeout(scrollTimer);
                scrollTimer = setTimeout(() => {
                    if (!isDragging && !isHovering) {
                        root.classList.remove('is-scrolling');
                    }
                }, HIDE_DELAY);
            }
        };

        track.addEventListener('pointerenter', onEnter);
        track.addEventListener('pointerleave', onLeave);
        thumb.addEventListener('pointerenter', onEnter);
        thumb.addEventListener('pointerleave', onLeave);
    }

    // ── Gán các trình lắng nghe sự kiện tối ưu ──

    // 1. Scroll listener với passive: true
    window.addEventListener('scroll', activateScrollbar, { passive: true });

    // 2. Window Resize: gom cụm bằng requestAnimationFrame
    let resizeRaf = null;
    window.addEventListener('resize', () => {
        if (resizeRaf) cancelAnimationFrame(resizeRaf);
        resizeRaf = requestAnimationFrame(updateMetrics);
    }, { passive: true });

    // 3. ResizeObserver: Theo dõi nội dung động (Lazy images, Accordion FAQs, Ajax catalog)
    function initResizeObserver() {
        if (typeof ResizeObserver !== 'undefined' && document.body) {
            let roRaf = null;
            const ro = new ResizeObserver(() => {
                if (roRaf) cancelAnimationFrame(roRaf);
                roRaf = requestAnimationFrame(updateMetrics);
            });
            ro.observe(document.body);
        }
    }

    // 4. MutationObserver: Theo dõi khóa cuộn trang (Modal / Mobile Drawer / Lightbox)
    function initMutationObserver() {
        if (typeof MutationObserver !== 'undefined' && document.body) {
            const lockObserver = new MutationObserver(() => {
                if (isBodyScrollLocked()) {
                    root.classList.remove('is-scrolling', 'is-dragging', 'is-scrollbar-hovered');
                    if (scrollTimer) clearTimeout(scrollTimer);
                    if (thumb) thumb.style.display = 'none';
                    if (track) track.style.display = 'none';
                } else if (metrics.canScroll) {
                    if (thumb) thumb.style.display = '';
                    if (track) track.style.display = '';
                    updateMetrics();
                }
            });
            lockObserver.observe(document.body, {
                attributes: true,
                attributeFilter: ['class', 'style']
            });
        }
    }

    // 5. Di chuột gần mép phải màn hình (chỉ áp dụng cho fine pointer/mouse, ngưỡng 14px tránh che nút)
    if (window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        window.addEventListener('mousemove', (e) => {
            if (window.innerWidth - e.clientX <= 14) {
                activateScrollbar();
            }
        }, { passive: true });
    }

    // 6. Khởi động và đồng bộ khi load / fonts ready
    if (document.readyState !== 'loading') {
        updateMetrics();
        initResizeObserver();
        initMutationObserver();
    } else {
        document.addEventListener('DOMContentLoaded', () => {
            updateMetrics();
            initResizeObserver();
            initMutationObserver();
        });
    }

    window.addEventListener('load', updateMetrics, { passive: true });
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(updateMetrics);
    }
})();

function shReady(fn) {
    if (document.readyState !== 'loading') {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

shReady(() => {
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
        document.dispatchEvent(new CustomEvent('sgh:mobile-menu-hydrated', { detail: { root: mobileSidebar } }));
    }
    function toggleSidebar(show) {
        if (!mobileSidebar || !mobileOverlay) return;
        if (show) {
            hydrateMobileMenu();
            mobileSidebar.classList.add('is-open');
            mobileOverlay.classList.add('is-visible');
            document.body.classList.add('overflow-hidden', 'sh-mobile-menu-active');
        } else {
            mobileSidebar.classList.remove('is-open');
            mobileOverlay.classList.remove('is-visible');
            document.body.classList.remove('overflow-hidden', 'sh-mobile-menu-active');
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
    // Back-to-top: gộp về assets/js/floating-buttons.js (cùng file với
    // logic kéo-thả của #sh-floating-buttons — trước đây bị tách 2 nơi).
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
    const lazyIframes = document.querySelectorAll('iframe[data-lazy-src]');
    if (lazyIframes.length > 0) {
        const loadIframe = (iframe) => {
            if (!iframe || !iframe.dataset.lazySrc || iframe.dataset.lazyLoaded === '1') return;
            iframe.src = iframe.dataset.lazySrc;
            iframe.dataset.lazyLoaded = '1';
            iframe.removeAttribute('data-lazy-src');
        };
        // Hero background-video iframes (autoplay, no controls) sit ABOVE the
        // fold, so IntersectionObserver fires immediately on page load — that
        // alone doesn't protect LCP. Gate on window `load` too: intersecting
        // iframes queue until the page has finished loading its critical
        // resources, so the YouTube player never competes with the LCP paint.
        let pageLoaded = document.readyState === 'complete';
        const pendingLoad = new Set();
        if (!pageLoaded) {
            window.addEventListener('load', () => {
                pageLoaded = true;
                pendingLoad.forEach(loadIframe);
                pendingLoad.clear();
            }, { once: true });
        }
        if ('IntersectionObserver' in window) {
            const iframeObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    observer.unobserve(entry.target);
                    if (pageLoaded) loadIframe(entry.target);
                    else pendingLoad.add(entry.target);
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
            : document.body.classList.contains('page-template-page-pricing') ? 'pricing'
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
    // Pause ANY infinite CSS animation while its element is off-screen — GPU/CPU
    // don't need to composite decorative motion (glow, float, shine, ken-burns...)
    // that nobody can see. Detected generically via computed style (not class
    // naming — the ~150 pillar keyframes don't share a common class prefix),
    // scanned off the critical path via requestIdleCallback so it never
    // competes with LCP/initial render.
    const pauseOffscreenAnimations = () => {
        // Quét toàn bộ DOM 1 lần (idle, không mỗi frame) — tên keyframe không có
        // quy ước class chung (150+ tên khác nhau trên các trang pillar) nên
        // selector-based matching sẽ bỏ sót; computed-style là cách chắc chắn duy nhất.
        const all = document.querySelectorAll('*');
        const animated = [];
        all.forEach((el) => {
            const cs = getComputedStyle(el);
            if (cs.animationName !== 'none' && cs.animationIterationCount === 'infinite') {
                animated.push(el);
            }
        });
        if (!animated.length) return;
        const animObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                entry.target.style.animationPlayState = entry.isIntersecting ? 'running' : 'paused';
            });
        }, { rootMargin: '100px' });
        animated.forEach((el) => {
            el.style.animationPlayState = 'paused';
            animObserver.observe(el);
        });
    };
    // Chạy SAU window.load (LCP chắc chắn đã paint xong), không dùng timeout
    // ép buộc — full-DOM scan + getComputedStyle mỗi phần tử đủ nặng để tự nó
    // chặn main-thread nếu chạy sớm, làm LCP TỆ HƠN thay vì tốt hơn (đã đo được
    // trên trang bambino: requestIdleCallback({timeout:2000}) ép chạy đồng bộ
    // ngay giữa cửa sổ LCP dưới CPU throttling).
    const scheduleAnimPause = () => {
        if ('requestIdleCallback' in window) requestIdleCallback(pauseOffscreenAnimations);
        else setTimeout(pauseOffscreenAnimations, 200);
    };
    if (document.readyState === 'complete') {
        scheduleAnimPause();
    } else {
        window.addEventListener('load', scheduleAnimPause, { once: true });
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
                        label.textContent = 'Đã copy!';
                        setTimeout(function() { label.textContent = 'Copy link'; }, 2000);
                    }
                });
            }
            return;
        }
        var langLink = e.target.closest('.sh-header__lang');
        if (langLink) {
            var lang = langLink.getAttribute('hreflang');
            if (lang) {
                document.cookie = 'sgh_lang_pref=' + encodeURIComponent(lang) + ';path=/;max-age=2592000;SameSite=Lax';
            }
        }
    });
});
