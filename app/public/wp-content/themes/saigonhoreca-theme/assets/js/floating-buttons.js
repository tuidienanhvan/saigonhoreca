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
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ── Kéo-thả + ghi nhớ vị trí (localStorage), snap về mép màn hình ── */
    var STORE_KEY = 'sh_fab_pos';
    var DRAG_THRESHOLD = 6;
    var EDGE_MARGIN = 12;
    // prefers-reduced-motion: animation snap-to-edge/scale-khi-kéo chạy bằng JS
    // (style.transition đặt trực tiếp qua inline style), CSS @media không override
    // được inline style của JS — kiểm tra 1 lần ở đây, dùng SNAP_SPEED=0 khi user
    // bật "giảm chuyển động" (audit flag: trước đây animation luôn chạy vô điều
    // kiện, bỏ qua tuỳ chọn hệ điều hành).
    var prefersReducedMotion = !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
    var SNAP_SPEED = prefersReducedMotion ? 0 : 280;
    var MOBILE_BREAKPOINT = 768;
    var MOBILE_NAV_HEIGHT = 60;
    var FAB_TO_NAV_GAP = 5;
    var HEADER_RESERVE = 16;

    var isDragging = false;
    var wasDragged = false;
    var startLeft, startTop;
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

    /* KHÔNG tự set el.style.transition = '' ở đây nữa — bug thật đã gặp: nơi gọi
       duy nhất (double-tap reset bên dưới) set transition NGAY TRƯỚC khi gọi hàm
       này để animate về vị trí mặc định, nhưng resetToDefault() lại xoá transition
       đó trong CÙNG lệnh, nên animation không bao giờ chạy — vị trí nhảy cứng thay
       vì mượt. Quản lý transition là việc của nơi gọi, hàm này chỉ xoá style vị trí. */
    function resetToDefault() {
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

    /*
     * Kéo-thả dùng Pointer Events (chuẩn hợp nhất chuột+chạm+bút, thay cho 2 bộ
     * listener mouse/touch riêng biệt trước đây — gộp lại để tối ưu, giảm trùng
     * lặp và giảm bề mặt bug).
     *
     * 2 bug thật đã gặp trên bản mouse/touch cũ, cả 2 đều sửa dứt điểm ở đây:
     *
     * 1) "Bấm lần đầu như kéo, phải bấm lại" — code cũ vào "chế độ kéo" NGAY khi
     *    mousedown, nên chỉ cần rung tay vài px lúc click (rất thường gặp, nhất
     *    là trackpad) là bị tính nhầm thành đã kéo → cụm nút tự snap vị trí VÀ
     *    click bị nuốt (capture-listener bên dưới stopPropagation khi wasDragged).
     *    → Sửa: chỉ THẬT SỰ commit "đang kéo" (set isDragging/class) KHI đã xác
     *    nhận di chuyển vượt DRAG_THRESHOLD trong pointermove — click thường
     *    (không di chuyển) không bao giờ chạm nhánh code kéo-thả.
     *
     * 2) "Nhấn 2 lần toàn kéo ra ảnh Zalo/số điện thoại/ảnh FB" — hệ quả phụ của
     *    fix (1): bỏ preventDefault() khỏi mousedown vô tình để lộ hành vi KÉO
     *    ẢNH/LINK GỐC của trình duyệt (native HTML5 drag-and-drop) trên <img>
     *    Zalo/Facebook và <a href="tel:...">, không có gì chặn nó nữa.
     *    → Sửa: chặn hẳn dragstart gốc (bên dưới) — logic kéo-thả cụm nút của
     *    theme hoàn toàn tách biệt, tự quản bằng pointer events, không cần và
     *    không nên để trình duyệt tự khởi tạo native drag trên bất kỳ phần tử
     *    con nào (ảnh, link) trong cụm.
     */
    el.addEventListener('dragstart', function (e) { e.preventDefault(); });

    var pointerDownAt = null; // {x, y, left, top} — ghi lúc pointerdown, chưa chắc là kéo
    var activePointerId = null;
    var captureTarget = null;

    el.addEventListener('pointerdown', function (e) {
        var rect = el.getBoundingClientRect();
        pointerDownAt = { x: e.clientX, y: e.clientY, left: rect.left, top: rect.top };
        activePointerId = e.pointerId;
        wasDragged = false;
        // Capture NGAY từ pointerdown trên CHÍNH e.target thay vì el.
        // Bắt trên el sẽ làm mất sự kiện click của thẻ con (trình duyệt gán click cho el).
        captureTarget = e.target;
        if (captureTarget.setPointerCapture) {
            try { captureTarget.setPointerCapture(activePointerId); } catch (err) {}
        }
    });

    el.addEventListener('pointermove', function (e) {
        if (!pointerDownAt || e.pointerId !== activePointerId) return;

        var dx = e.clientX - pointerDownAt.x;
        var dy = e.clientY - pointerDownAt.y;
        if (!isDragging) {
            if (Math.abs(dx) + Math.abs(dy) < DRAG_THRESHOLD) return;
            // Vượt ngưỡng lần đầu — bây giờ mới thật sự bắt đầu kéo.
            isDragging = true;
            wasDragged = true;
            startLeft = pointerDownAt.left;
            startTop = pointerDownAt.top;
            el.style.transition = 'none';
            el.classList.add('sh-fab-dragging');
        }
        applyPosition(startLeft + dx, startTop + dy, false);
    });

    function endPointerInteraction(e) {
        if (e && e.pointerId !== activePointerId) return;
        if (captureTarget && captureTarget.releasePointerCapture && captureTarget.hasPointerCapture && captureTarget.hasPointerCapture(activePointerId)) {
            try { captureTarget.releasePointerCapture(activePointerId); } catch (err) {}
        }
        pointerDownAt = null;
        activePointerId = null;
        captureTarget = null;
        // wasDragged KHÔNG xoá ngay ở đây — click theo sau 1 cú kéo bằng chuột thật
        // dispatch NGAY SAU pointerup trong CÙNG tác vụ đồng bộ, capture-handler bên
        // dưới cần đọc được wasDragged=true lúc đó để nuốt đúng click đó. Dọn dẹp
        // bằng setTimeout(0) — chạy SAU khi click (nếu có) đã xử lý xong trong cùng
        // tick, đảm bảo wasDragged không "sống sót" quá lâu để nuốt nhầm 1 click
        // không liên quan sau này (vd Enter bàn phím kích hoạt link, không có
        // pointerdown trước đó — bug thật đã gặp khi audit).
        setTimeout(function () { wasDragged = false; }, 0);
        if (!isDragging) return;

        isDragging = false;
        el.classList.remove('sh-fab-dragging');
        if (wasDragged) {
            snapToEdge();
        }
    }
    el.addEventListener('pointerup', endPointerInteraction);
    el.addEventListener('pointercancel', endPointerInteraction);

    el.addEventListener('click', function (e) {
        if (wasDragged) {
            e.preventDefault();
            // stopImmediatePropagation (không chỉ stopPropagation) — bug thật đã gặp:
            // stopPropagation không chặn listener KHÁC trên CÙNG target, nên listener
            // double-tap bên dưới vẫn chạy cho chính click bị kéo-suppress này, có
            // thể vô tình ghi nhận thành 1 lần "tap" và gộp với tap kế tiếp thành
            // double-tap giả, reset nhầm vị trí cụm nút.
            e.stopImmediatePropagation();
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
