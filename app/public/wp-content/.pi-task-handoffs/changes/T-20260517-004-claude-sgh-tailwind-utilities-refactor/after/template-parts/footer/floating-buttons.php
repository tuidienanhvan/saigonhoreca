<?php
/**
 * Template Part: Footer Floating Buttons (Back to Top, Phone, Zalo)
 *
 * Component hiển thị các nút thao tác nổi.
 * Draggable — user có thể kéo nhóm nút đến bất kỳ đâu trên màn hình.
 * @var array $args Có thể truyền 'contact' (mảng)
 * 
 * @package SaigonHouse
 */

$contact = $args['contact'] ?? (function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : []);
$hotline_raw = $contact['hotline_raw'] ?? str_replace([' ', '.', ','], '', $contact['hotline'] ?? '');
?>
<div id="sh-floating-buttons">

    <!-- 1. Back to Top -->
    <a href="#" id="back-to-top" aria-label="Cuộn lên đầu trang" role="button">
        <svg class="sh-scroll-ring" viewBox="0 0 36 36">
            <path class="sh-scroll-track" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
            <path id="scroll-progress-circle" class="sh-scroll-progress" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
        </svg>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary" style="position:relative; z-index:1;"><path d="m18 15-6-6-6 6"/></svg>
    </a>

    <!-- 2. AI Chatbot Toggle — rendered by Pi Chatbot plugin via wp_footer -->

    <!-- 3. Phone -->
    <?php if (!empty($contact['hotline'])) : ?>
    <a href="tel:<?php echo esc_attr($hotline_raw); ?>" class="sh-phone-float" aria-label="Gọi hotline <?php echo esc_attr($contact['hotline']); ?>" data-gtm-click="click_phone" data-gtm-location="floating_button">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
    </a>
    <?php endif; ?>

    <!-- 4. Zalo -->
    <?php if (!empty($contact['zalo'])) : ?>
    <a href="<?php echo esc_url($contact['zalo']); ?>" target="_blank" rel="noopener noreferrer" class="sh-zalo-btn" aria-label="Chat qua Zalo" data-gtm-click="click_zalo" data-gtm-location="floating_button">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/zalo-icon.webp" alt="Chat Zalo" class="sh-zalo-btn__img" loading="lazy" decoding="async" width="56" height="56">
    </a>
    <?php endif; ?>
</div>

<script>
// ── Draggable Floating Buttons ──
// Kéo thả nhóm nút, edge-snap, lưu vị trí localStorage
(function(){
    var el = document.getElementById('sh-floating-buttons');
    if (!el) return;

    var STORE_KEY = 'sh_fab_pos';
    var DRAG_THRESHOLD = 6; // px di chuyển tối thiểu để bắt đầu coi là drag
    var EDGE_MARGIN = 12;   // khoảng cách tối thiểu đến cạnh trái/phải/trên
    var SNAP_SPEED = 280;   // ms animation snap vào cạnh
    var MOBILE_BREAKPOINT = 768; // < 768px = mobile (mobile-nav reserves bottom)
    var MOBILE_NAV_HEIGHT = 60;  // .sh-mnav physical height (excl. safe-area)
    var FAB_TO_NAV_GAP = 5;      // khoảng cách giữa stack và mobile-nav
    var HEADER_RESERVE = 16;     // top margin so FAB doesn't hug under header

    var isDragging = false, wasDragged = false;
    var startX, startY, startLeft, startTop;
    var dragStartTime = 0;

    function isMobile() { return window.innerWidth < MOBILE_BREAKPOINT; }

    /**
     * Compute valid drag area, accounting for the bottom mobile-nav so the
     * FAB can never be dragged into a region that's permanently covered.
     * This is THE "băng keo" mechanism — every position write goes through
     * `clampToBounds()` which uses these limits.
     */
    function getBounds() {
        // Mobile: stack đáy phải cách mobile-nav đúng FAB_TO_NAV_GAP (5px),
        // không hơn không kém — đồng bộ với CSS first-paint.
        var bottomReserve = isMobile()
            ? (MOBILE_NAV_HEIGHT + FAB_TO_NAV_GAP)
            : EDGE_MARGIN;
        return {
            minX: EDGE_MARGIN,
            minY: HEADER_RESERVE,
            maxX: Math.max(EDGE_MARGIN, window.innerWidth - el.offsetWidth - EDGE_MARGIN),
            maxY: Math.max(HEADER_RESERVE, window.innerHeight - el.offsetHeight - bottomReserve),
        };
    }

    function clampToBounds(x, y) {
        var b = getBounds();
        return {
            x: Math.min(Math.max(x, b.minX), b.maxX),
            y: Math.min(Math.max(y, b.minY), b.maxY),
        };
    }

    /** Reset inline positioning so the CSS-anchored default takes over. */
    function resetToDefault() {
        el.style.transition = '';
        el.style.left = '';
        el.style.top = '';
        el.style.bottom = '';
        el.style.right = '';
        el.style.position = '';
    }

    // Khôi phục vị trí đã lưu, clamp vào bounds hiện tại (kể cả khi user
    // đã save 1 vị trí desktop rồi mở lại trên mobile — sẽ được kéo lên).
    function restorePosition() {
        try {
            var saved = JSON.parse(localStorage.getItem(STORE_KEY));
            if (!saved) return;
            var clamped = clampToBounds(saved.x, saved.y);
            applyPosition(clamped.x, clamped.y, false);
        } catch(e) {}
    }

    function applyPosition(x, y, animate) {
        // Băng keo — every write goes through bounds clamp so the user can
        // never drag the stack into the mobile-nav zone or off-screen.
        var c = clampToBounds(x, y);
        // Chuyển từ bottom/right positioning sang top/left
        el.style.position = 'fixed';
        el.style.bottom = 'auto';
        el.style.right = 'auto';
        el.style.left = c.x + 'px';
        el.style.top = c.y + 'px';
        if (animate) {
            el.style.transition = 'left ' + SNAP_SPEED + 'ms cubic-bezier(0.25, 0.46, 0.45, 0.94), top ' + SNAP_SPEED + 'ms cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        } else {
            el.style.transition = 'none';
        }
    }

    function savePosition() {
        try {
            localStorage.setItem(STORE_KEY, JSON.stringify({
                x: parseInt(el.style.left),
                y: parseInt(el.style.top)
            }));
        } catch(e) {}
    }

    function snapToEdge() {
        var rect = el.getBoundingClientRect();
        var centerX = rect.left + rect.width / 2;
        var b = getBounds();

        // Snap X vào cạnh gần nhất (trái hoặc phải)
        var snapX = (centerX < window.innerWidth / 2) ? b.minX : b.maxX;
        // Snap Y vào bounds (mobile-nav-aware)
        var rawY = parseInt(el.style.top) || rect.top;
        var snapY = Math.min(Math.max(rawY, b.minY), b.maxY);

        applyPosition(snapX, snapY, true);
        setTimeout(savePosition, SNAP_SPEED + 50);
    }

    // ── Mouse Events ──
    el.addEventListener('mousedown', function(e) {
        // Không drag nếu click vào link/button bên trong
        if (e.target.closest('a, button')) {
            startX = e.clientX;
            startY = e.clientY;
        }

        var rect = el.getBoundingClientRect();
        isDragging = true;
        wasDragged = false;
        dragStartTime = Date.now();
        startX = e.clientX;
        startY = e.clientY;
        startLeft = rect.left;
        startTop = rect.top;
        el.style.transition = 'none';
        el.classList.add('sh-fab-dragging');
        e.preventDefault();
    });

    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        var dx = e.clientX - startX;
        var dy = e.clientY - startY;
        if (!wasDragged && Math.abs(dx) + Math.abs(dy) < DRAG_THRESHOLD) return;
        wasDragged = true;
        applyPosition(startLeft + dx, startTop + dy, false);
    });

    document.addEventListener('mouseup', function() {
        if (!isDragging) return;
        isDragging = false;
        el.classList.remove('sh-fab-dragging');
        if (wasDragged) {
            snapToEdge();
        }
    });

    // ── Touch Events (Mobile) ──
    el.addEventListener('touchstart', function(e) {
        var touch = e.touches[0];
        var rect = el.getBoundingClientRect();
        isDragging = true;
        wasDragged = false;
        dragStartTime = Date.now();
        startX = touch.clientX;
        startY = touch.clientY;
        startLeft = rect.left;
        startTop = rect.top;
        el.style.transition = 'none';
        el.classList.add('sh-fab-dragging');
    }, { passive: true });

    document.addEventListener('touchmove', function(e) {
        if (!isDragging) return;
        var touch = e.touches[0];
        var dx = touch.clientX - startX;
        var dy = touch.clientY - startY;
        if (!wasDragged && Math.abs(dx) + Math.abs(dy) < DRAG_THRESHOLD) return;
        wasDragged = true;
        e.preventDefault(); // Ngăn scroll khi đang drag
        applyPosition(startLeft + dx, startTop + dy, false);
    }, { passive: false });

    document.addEventListener('touchend', function() {
        if (!isDragging) return;
        isDragging = false;
        el.classList.remove('sh-fab-dragging');
        if (wasDragged) {
            snapToEdge();
        }
    });

    // Chặn click khi vừa drag xong (tránh mở link/chatbot khi thả)
    el.addEventListener('click', function(e) {
        if (wasDragged) {
            e.preventDefault();
            e.stopPropagation();
            wasDragged = false;
        }
    }, true);

    // Double-click/tap → reset về vị trí mặc định (góc phải dưới)
    var lastTap = 0;
    el.addEventListener('click', function() {
        var now = Date.now();
        if (now - lastTap < 350) {
            localStorage.removeItem(STORE_KEY);
            el.style.transition = 'all ' + SNAP_SPEED + 'ms ease';
            resetToDefault();
            setTimeout(function(){ el.style.transition = ''; }, SNAP_SPEED);
        }
        lastTap = now;
    });

    // Resize handler — re-clamp inline position vào bounds mới (mobile-nav-aware).
    // Khi cross breakpoint desktop → mobile, mobile-nav xuất hiện và bottom-reserve
    // tăng từ 12px → 92px, FAB nếu đang ở quá gần đáy sẽ tự động kéo lên.
    window.addEventListener('resize', function() {
        if (!el.style.left) return; // chưa từng drag → CSS anchor handle
        var x = parseInt(el.style.left);
        var y = parseInt(el.style.top);
        var c = clampToBounds(x, y);
        applyPosition(c.x, c.y, true);
        setTimeout(savePosition, SNAP_SPEED + 50);
    });

    // Khôi phục vị trí sau khi DOM sẵn sàng
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', restorePosition);
    } else {
        restorePosition();
    }
})();
</script>
