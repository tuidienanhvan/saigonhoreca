/**
 * Floating Buttons — Saigon Horeca
 *
 * Toàn bộ hành vi JS của cụm #sh-floating-buttons (back-to-top + kéo-thả +
 * ghi nhớ vị trí) gộp về 1 file — trước đây bị tách làm 2 nơi không liên quan
 * (back-to-top nằm trong main-modern.js, kéo-thả nằm inline trong
 * template-parts/footer/floating-buttons.php). Enqueue có điều kiện, phụ
 * thuộc main-modern (dùng chung shDebounce toàn cục).
 *
 * @package SaigonHoreca
 */
(function () {
    var el = document.getElementById('sh-floating-buttons');
    if (!el) return;

    var debounce = typeof shDebounce === 'function'
        ? shDebounce
        : function (fn, ms) {
            var t;
            return function () {
                var args = arguments, ctx = this;
                clearTimeout(t);
                t = setTimeout(function () { fn.apply(ctx, args); }, ms || 100);
            };
        };

    /* ── Back to top: hiện/ẩn theo scroll, vòng progress, cuộn mượt ── */
    var backToTop = document.getElementById('back-to-top');
    var progressCircle = document.getElementById('scroll-progress-circle');
    if (backToTop) {
        var updateBackToTop = function () {
            var scrollTop = window.scrollY;
            var docHeight = document.documentElement.scrollHeight - window.innerHeight;
            var scrollPercent = docHeight > 0 ? Math.min(scrollTop / docHeight, 1) : 0;
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
        window.addEventListener('scroll', debounce(updateBackToTop, 100), { passive: true });
        updateBackToTop();
        backToTop.addEventListener('click', function (event) {
            event.preventDefault();
            var startY = window.scrollY;
            var duration = 800; // 800ms cuộn mượt kiểu luxury
            var startTime = performance.now();

            function scrollStep(currentTime) {
                var elapsed = currentTime - startTime;
                var progress = Math.min(elapsed / duration, 1);
                var ease = 1 - Math.pow(1 - progress, 3); // ease-out cubic
                window.scrollTo(0, startY * (1 - ease));
                if (progress < 1) {
                    requestAnimationFrame(scrollStep);
                }
            }
            requestAnimationFrame(scrollStep);
        });
    }

    /* ── Kéo-thả + ghi nhớ vị trí (localStorage), snap về mép màn hình ── */
    var STORE_KEY = 'sh_fab_pos';
    var DRAG_THRESHOLD = 6;
    var EDGE_MARGIN = 12;
    var SNAP_SPEED = 280;
    var MOBILE_BREAKPOINT = 768;
    var MOBILE_NAV_HEIGHT = 60;
    var FAB_TO_NAV_GAP = 5;
    var HEADER_RESERVE = 16;

    var isDragging = false;
    var wasDragged = false;
    var startX, startY, startLeft, startTop;
    var lastTap = 0;
    var lastViewportWasMobile = null;

    var mql = window.matchMedia('(max-width: ' + (MOBILE_BREAKPOINT - 1) + 'px)');
    function isMobile() {
        return mql.matches;
    }

    function getBounds() {
        var bottomReserve = isMobile()
            ? (MOBILE_NAV_HEIGHT + FAB_TO_NAV_GAP)
            : EDGE_MARGIN;

        return {
            minX: EDGE_MARGIN,
            minY: HEADER_RESERVE,
            maxX: Math.max(EDGE_MARGIN, window.innerWidth - el.offsetWidth - EDGE_MARGIN),
            maxY: Math.max(HEADER_RESERVE, window.innerHeight - el.offsetHeight - bottomReserve)
        };
    }

    function clampToBounds(x, y) {
        var bounds = getBounds();
        return {
            x: Math.min(Math.max(x, bounds.minX), bounds.maxX),
            y: Math.min(Math.max(y, bounds.minY), bounds.maxY)
        };
    }

    function resetToDefault() {
        el.style.transition = '';
        el.style.left = '';
        el.style.top = '';
        el.style.bottom = '';
        el.style.right = '';
        el.style.position = '';
    }

    function applyPosition(x, y, animate) {
        var clamped = clampToBounds(x, y);
        el.style.position = 'fixed';
        el.style.bottom = 'auto';
        el.style.right = 'auto';
        el.style.left = clamped.x + 'px';
        el.style.top = clamped.y + 'px';
        el.style.transition = animate
            ? 'left ' + SNAP_SPEED + 'ms cubic-bezier(0.25, 0.46, 0.45, 0.94), top ' + SNAP_SPEED + 'ms cubic-bezier(0.25, 0.46, 0.45, 0.94)'
            : 'none';
    }

    function savePosition() {
        try {
            localStorage.setItem(STORE_KEY, JSON.stringify({
                x: parseInt(el.style.left, 10),
                y: parseInt(el.style.top, 10)
            }));
        } catch (e) {}
    }

    function snapToEdge() {
        var rect = el.getBoundingClientRect();
        var centerX = rect.left + (rect.width / 2);
        var bounds = getBounds();
        var snapX = centerX < (window.innerWidth / 2) ? bounds.minX : bounds.maxX;
        var rawY = parseInt(el.style.top, 10) || rect.top;
        var snapY = Math.min(Math.max(rawY, bounds.minY), bounds.maxY);

        applyPosition(snapX, snapY, true);
        setTimeout(savePosition, SNAP_SPEED + 50);
    }

    function restorePosition() {
        try {
            if (lastViewportWasMobile === null) {
                lastViewportWasMobile = isMobile();
            }
            var saved = JSON.parse(localStorage.getItem(STORE_KEY));
            if (!saved) return;

            applyPosition(saved.x, saved.y, false);
            if (saved.x !== clampToBounds(saved.x, saved.y).x) {
                snapToEdge();
            }
        } catch (e) {}
    }

    el.addEventListener('mousedown', function (e) {
        var rect = el.getBoundingClientRect();
        isDragging = true;
        wasDragged = false;
        startX = e.clientX;
        startY = e.clientY;
        startLeft = rect.left;
        startTop = rect.top;
        el.style.transition = 'none';
        el.classList.add('sh-fab-dragging');
        e.preventDefault();
    });

    document.addEventListener('mousemove', function (e) {
        if (!isDragging) return;

        var dx = e.clientX - startX;
        var dy = e.clientY - startY;
        if (!wasDragged && Math.abs(dx) + Math.abs(dy) < DRAG_THRESHOLD) return;

        wasDragged = true;
        applyPosition(startLeft + dx, startTop + dy, false);
    });

    document.addEventListener('mouseup', function () {
        if (!isDragging) return;

        isDragging = false;
        el.classList.remove('sh-fab-dragging');
        if (wasDragged) {
            snapToEdge();
        }
    });

    el.addEventListener('touchstart', function (e) {
        var touch = e.touches[0];
        var rect = el.getBoundingClientRect();
        isDragging = true;
        wasDragged = false;
        startX = touch.clientX;
        startY = touch.clientY;
        startLeft = rect.left;
        startTop = rect.top;
        el.style.transition = 'none';
        el.classList.add('sh-fab-dragging');
    }, { passive: true });

    document.addEventListener('touchmove', function (e) {
        if (!isDragging) return;

        var touch = e.touches[0];
        var dx = touch.clientX - startX;
        var dy = touch.clientY - startY;
        if (!wasDragged && Math.abs(dx) + Math.abs(dy) < DRAG_THRESHOLD) return;

        wasDragged = true;
        e.preventDefault();
        applyPosition(startLeft + dx, startTop + dy, false);
    }, { passive: false });

    document.addEventListener('touchend', function () {
        if (!isDragging) return;

        isDragging = false;
        el.classList.remove('sh-fab-dragging');
        if (wasDragged) {
            snapToEdge();
        }
    });

    el.addEventListener('click', function (e) {
        if (wasDragged) {
            e.preventDefault();
            e.stopPropagation();
            wasDragged = false;
        }
    }, true);

    el.addEventListener('click', function (e) {
        // Chỉ tính double-tap-để-reset khi KHÔNG trúng 1 nút chức năng (a/button) —
        // double-click nhanh vào Zalo/Facebook/Chatbot không được vô tình reset vị trí.
        if (e.target.closest('a, button')) return;

        var now = Date.now();
        if (now - lastTap < 350) {
            localStorage.removeItem(STORE_KEY);
            el.style.transition = 'all ' + SNAP_SPEED + 'ms ease';
            resetToDefault();
            setTimeout(function () {
                el.style.transition = '';
            }, SNAP_SPEED);
        }
        lastTap = now;
    });

    var resizeTimeout;
    window.addEventListener('resize', function () {
        if (resizeTimeout) clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function () {
            var viewportIsMobile = isMobile();
            if (lastViewportWasMobile === null) {
                lastViewportWasMobile = viewportIsMobile;
            }

            if (!el.style.left) {
                lastViewportWasMobile = viewportIsMobile;
                return;
            }

            if (viewportIsMobile !== lastViewportWasMobile) {
                lastViewportWasMobile = viewportIsMobile;
                snapToEdge();
                return;
            }

            var x = parseInt(el.style.left, 10);
            var y = parseInt(el.style.top, 10);
            var clamped = clampToBounds(x, y);
            applyPosition(clamped.x, clamped.y, true);
            setTimeout(savePosition, SNAP_SPEED + 50);
        }, 100);
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(restorePosition, 150);
        });
    } else {
        setTimeout(restorePosition, 150);
    }
})();
