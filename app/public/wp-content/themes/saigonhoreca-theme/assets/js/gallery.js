/**
 * Gallery — Filter + Lightbox + Load More
 * Saigon House Theme
 */
(function () {
    'use strict';

    // ── State ──────────────────────────────────────────────────────────
    var currentFilter = 'all';
    var visibleItems  = [];   // ordered list of currently visible items
    var lightboxIndex = 0;

    // ── DOM refs ────────────────────────────────────────────────────────
    var grid         = document.getElementById('sh-port-grid');
    var filterBtns   = document.querySelectorAll('.sh-port-filter-btn');
    var countNum     = document.getElementById('sh-port-count-num');
    var noResults    = document.getElementById('sh-port-no-results');
    var lightbox     = document.getElementById('sh-lightbox');
    var lbImg        = document.getElementById('sh-lightbox-img');
    var lbCaption    = document.getElementById('sh-lightbox-caption');
    var lbClose      = document.getElementById('sh-lightbox-close');
    var lbPrev       = document.getElementById('sh-lightbox-prev');
    var lbNext       = document.getElementById('sh-lightbox-next');
    var lbCounter    = document.getElementById('sh-lightbox-counter');

    if (!grid) return;

    // All items (NodeList → Array once)
    var allItems = Array.prototype.slice.call(grid.querySelectorAll('.sh-port-item'));

    // ── Reveal on load (IntersectionObserver) ───────────────────────────
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        allItems.forEach(function (item, i) {
            item.style.transitionDelay = (i % 6) * 0.06 + 's';
            io.observe(item);
        });
    } else {
        // Fallback: show all immediately
        allItems.forEach(function (el) { el.classList.add('is-visible'); });
    }

    // ── Filter ──────────────────────────────────────────────────────────
    function applyFilter(filter) {
        currentFilter = filter;
        grid.classList.add('is-filtering');

        // Small delay so the fade-out is visible
        setTimeout(function () {
            visibleItems = [];

            allItems.forEach(function (item) {
                var cats = (item.getAttribute('data-cat') || '').split(' ');
                var show = (filter === 'all') || cats.indexOf(filter) !== -1;

                if (show) {
                    item.classList.remove('is-hidden');
                    item.classList.add('is-visible');
                    visibleItems.push(item);
                } else {
                    item.classList.add('is-hidden');
                    item.classList.remove('is-visible');
                }
            });

            // Staggered re-reveal
            visibleItems.forEach(function (item, i) {
                item.style.transitionDelay = (i % 6) * 0.05 + 's';
            });

            // Update count
            if (countNum) countNum.textContent = visibleItems.length;

            // Toggle no-results state
            if (noResults) {
                noResults.hidden = visibleItems.length > 0;
            }

            grid.classList.remove('is-filtering');
        }, 200);
    }

    // Filter button click
    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            // Update active state
            filterBtns.forEach(function (b) {
                b.classList.remove('is-active');
                b.setAttribute('aria-selected', 'false');
            });
            btn.classList.add('is-active');
            btn.setAttribute('aria-selected', 'true');

            applyFilter(btn.getAttribute('data-filter') || 'all');
        });
    });

    // Initial visible items list
    visibleItems = allItems.slice();

    // ── Lightbox ────────────────────────────────────────────────────────
    var lastFocusedEl = null;

    function openLightbox(index) {
        if (!lightbox || visibleItems.length === 0) return;
        lastFocusedEl = document.activeElement;
        lightboxIndex = Math.max(0, Math.min(index, visibleItems.length - 1));
        updateLightbox();
        lightbox.removeAttribute('hidden');
        lightbox.setAttribute('role', 'dialog');
        lightbox.setAttribute('aria-modal', 'true');
        lightbox.setAttribute('aria-label', 'Xem ảnh phóng to');
        document.body.style.overflow = 'hidden';
        if (lbClose) lbClose.focus();
        // Focus trap
        lightbox.addEventListener('keydown', trapFocus);
    }

    function closeLightbox() {
        if (!lightbox) return;
        lightbox.setAttribute('hidden', '');
        document.body.style.overflow = '';
        lightbox.removeEventListener('keydown', trapFocus);
        if (lastFocusedEl) lastFocusedEl.focus();
    }

    function trapFocus(e) {
        if (e.key !== 'Tab') return;
        var focusable = lightbox.querySelectorAll('button:not([disabled]), [tabindex]:not([tabindex="-1"])');
        if (focusable.length === 0) return;
        var first = focusable[0], last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }

    function updateLightbox() {
        var item = visibleItems[lightboxIndex];
        if (!item) return;

        var src     = item.getAttribute('data-img') || item.querySelector('.sh-port-img').src;
        var title   = item.getAttribute('data-title') || item.querySelector('.sh-port-item-title')?.textContent || '';

        // Fade out old image, load new one
        lbImg.style.opacity = '0';
        lbImg.onload = function () {
            lbImg.style.opacity = '1';
        };
        lbImg.src = src;
        lbImg.alt = title;
        if (lbCaption) lbCaption.textContent = title;
        if (lbCounter) lbCounter.textContent = (lightboxIndex + 1) + ' / ' + visibleItems.length;

        // Prev/Next state
        if (lbPrev) lbPrev.disabled = lightboxIndex === 0;
        if (lbNext) lbNext.disabled = lightboxIndex === visibleItems.length - 1;
    }

    // Lightbox trigger — delegated to grid
    grid.addEventListener('click', function (e) {
        var btn = e.target.closest('.sh-port-lightbox-btn');
        if (!btn) return;
        e.preventDefault();

        var item = btn.closest('.sh-port-item');
        var idx  = visibleItems.indexOf(item);
        if (idx !== -1) openLightbox(idx);
    });

    // Also open on item click directly (not on link button)
    grid.addEventListener('click', function (e) {
        if (e.target.closest('.sh-port-link-btn') || e.target.closest('.sh-port-lightbox-btn')) return;
        var item = e.target.closest('.sh-port-item');
        if (!item) return;
        var idx = visibleItems.indexOf(item);
        if (idx !== -1) openLightbox(idx);
    });

    if (lbClose) lbClose.addEventListener('click', closeLightbox);
    if (lbPrev)  lbPrev.addEventListener('click', function () { if (lightboxIndex > 0) openLightbox(lightboxIndex - 1); });
    if (lbNext)  lbNext.addEventListener('click', function () { if (lightboxIndex < visibleItems.length - 1) openLightbox(lightboxIndex + 1); });

    // Click backdrop to close
    if (lightbox) {
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) closeLightbox();
        });
    }

    // Keyboard nav
    document.addEventListener('keydown', function (e) {
        if (!lightbox || lightbox.hasAttribute('hidden')) return;
        if (e.key === 'Escape' || e.key === 'Esc') closeLightbox();
        if (e.key === 'ArrowLeft'  && lightboxIndex > 0) openLightbox(lightboxIndex - 1);
        if (e.key === 'ArrowRight' && lightboxIndex < visibleItems.length - 1) openLightbox(lightboxIndex + 1);
    });

    // ── Load More (simple show-hidden pages via AJAX) ────────────────────
    var loadMoreBtn = document.getElementById('sh-port-loadmore');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () {
            var btn     = this;
            var page    = parseInt(btn.getAttribute('data-page') || '1', 10) + 1;
            var maxPage = parseInt(btn.getAttribute('data-max') || '1', 10);

            btn.disabled = true;
            btn.textContent = 'Đang tải...';

            var data = new FormData();
            data.append('action', 'sh_portfolio_load_more');
            data.append('page', page);
            data.append('nonce', (window.SH_DATA && window.SH_DATA.nonce) || '');

            fetch((window.SH_DATA && window.SH_DATA.ajax_url) || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: data,
            })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.success && res.data && res.data.html) {
                    var tmp = document.createElement('div');
                    tmp.innerHTML = res.data.html;
                    var newItems = Array.prototype.slice.call(tmp.querySelectorAll('.sh-port-item'));

                    newItems.forEach(function (el) {
                        grid.appendChild(el);
                        allItems.push(el);

                        // Observe for reveal
                        if ('IntersectionObserver' in window && typeof io !== 'undefined') {
                            io.observe(el);
                        } else {
                            el.classList.add('is-visible');
                        }
                    });

                    // Re-apply current filter
                    applyFilter(currentFilter);

                    btn.setAttribute('data-page', page);

                    if (page >= maxPage) {
                        var wrap = document.getElementById('sh-port-loadmore-wrap');
                        if (wrap) wrap.remove();
                    } else {
                        btn.disabled = false;
                        btn.textContent = 'Xem Thêm Công Trình';
                    }
                } else {
                    btn.textContent = 'Đã tải hết';
                    btn.disabled = true;
                }
            })
            .catch(function () {
                btn.disabled = false;
                btn.textContent = 'Xem Thêm Công Trình';
            });
        });
    }

})();
