<?php
/**
 * Template Part: Footer Floating Buttons (Back to Top, Phone, Zalo)
 *
 * Draggable floating action buttons with edge snap and persistent position.
 *
 * @var array $args Optional. May include a `contact` array.
 *
 * @package SaigonHoreca
 */

if (! defined('ABSPATH')) {
    exit;
}

$contact = $args['contact'] ?? (function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : []);
$hotline_raw = $contact['hotline_raw'] ?? str_replace([' ', '.', ','], '', $contact['hotline'] ?? '');
?>
<div id="sh-floating-buttons">
    <a href="#" id="back-to-top" aria-label="<?php esc_attr_e('Cuộn lên đầu trang', 'saigonhoreca'); ?>" role="button">
        <svg class="sh-scroll-ring" viewBox="0 0 36 36">
            <path class="sh-scroll-track" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
            <path id="scroll-progress-circle" class="sh-scroll-progress" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
        </svg>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary" style="position:relative; z-index:1;"><path d="m18 15-6-6-6 6"/></svg>
    </a>

    <?php if (! empty($contact['hotline'])) : ?>
    <a href="tel:<?php echo esc_attr($hotline_raw); ?>" class="sh-phone-float" aria-label="<?php echo esc_attr(sprintf(__('Gọi hotline %s', 'saigonhoreca'), $contact['hotline'])); ?>" data-gtm-click="click_phone" data-gtm-location="floating_button">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
    </a>
    <?php endif; ?>

    <?php if (! empty($contact['zalo'])) : ?>
    <a href="<?php echo esc_url($contact['zalo']); ?>" target="_blank" rel="noopener noreferrer" class="sh-zalo-btn" aria-label="<?php esc_attr_e('Chat qua Zalo', 'saigonhoreca'); ?>" data-gtm-click="click_zalo" data-gtm-location="floating_button">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/zalo-icon.webp" alt="<?php esc_attr_e('Chat Zalo', 'saigonhoreca'); ?>" class="sh-zalo-btn__img" loading="lazy" decoding="async" width="56" height="56">
    </a>
    <?php endif; ?>

    <?php if (! empty($contact['facebook'])) : ?>
    <a href="<?php echo esc_url($contact['facebook']); ?>" target="_blank" rel="noopener noreferrer" class="sh-fb-btn" aria-label="<?php esc_attr_e('Theo dõi Facebook', 'saigonhoreca'); ?>" data-gtm-click="click_facebook" data-gtm-location="floating_button">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
    </a>
    <?php endif; ?>
</div>

<script>
(function () {
    var el = document.getElementById('sh-floating-buttons');
    if (!el) return;

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
    var startX;
    var startY;
    var startLeft;
    var startTop;
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
        if (e.target.closest('a, button')) {
            startX = e.clientX;
            startY = e.clientY;
        }

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

    el.addEventListener('click', function () {
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
</script>
