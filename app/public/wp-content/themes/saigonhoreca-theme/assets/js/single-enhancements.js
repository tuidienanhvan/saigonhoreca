document.addEventListener('DOMContentLoaded', function () {
    var content = document.getElementById('post-content');
    var tocList = document.getElementById('toc-list');
    var tocContainer = document.getElementById('toc-container');

    if (!content) {
        return;
    }

    // ─── Reading Progress Bar ───
    (function setupReadingProgress() {
        var bar = document.createElement('div');
        bar.id = 'sh-reading-progress';
        document.body.insertBefore(bar, document.body.firstChild);

        function updateProgress() {
            var article = content;
            var articleTop = article.getBoundingClientRect().top + window.pageYOffset;
            var articleHeight = article.offsetHeight;
            var scrolled = window.pageYOffset - articleTop;
            var pct = Math.min(100, Math.max(0, (scrolled / (articleHeight - window.innerHeight)) * 100));
            bar.style.width = pct + '%';
        }

        window.addEventListener('scroll', updateProgress, { passive: true });
        updateProgress();
    })();

    // ─── Lightbox — Premium Image Viewer ───
    (function setupLightbox() {
        var lightbox = document.getElementById('sh-lightbox');
        var lightboxImage = document.getElementById('sh-lightbox-img');
        var lightboxCaption = document.getElementById('sh-lightbox-caption');
        var closeBtn = document.getElementById('sh-lightbox-close');
        var figure = lightbox && lightbox.querySelector('.sh-single__lightbox-figure');

        if (!lightbox || !lightboxImage || !closeBtn) {
            return;
        }

        var lastFocused = null;  // For focus restoration on close

        function openLightbox(src, alt) {
            lastFocused = document.activeElement;
            lightboxImage.alt = alt || '';
            if (lightboxCaption) lightboxCaption.textContent = alt || '';

            // Show shell first (with loading state) → image fades in when loaded
            lightbox.hidden = false;
            lightbox.classList.add('is-loading');
            document.body.style.overflow = 'hidden';

            // Trigger reflow before adding 'is-open' so transition runs
            void lightbox.offsetWidth;
            lightbox.classList.add('is-open');

            // Preload image — fade-in only when ready
            var preload = new Image();
            preload.onload = function () {
                lightboxImage.src = src;
                lightbox.classList.remove('is-loading');
            };
            preload.onerror = function () {
                lightbox.classList.remove('is-loading');
                if (lightboxCaption) lightboxCaption.textContent = 'Không thể tải ảnh.';
            };
            preload.src = src;

            // Move focus into the dialog (a11y)
            requestAnimationFrame(function () { closeBtn.focus(); });
        }

        function closeLightbox() {
            lightbox.classList.remove('is-open', 'is-loading');
            // Wait for fade-out animation before unmount + cleanup
            setTimeout(function () {
                lightbox.hidden = true;
                lightboxImage.src = '';
                lightboxImage.alt = '';
                if (lightboxCaption) lightboxCaption.textContent = '';
                document.body.style.overflow = '';
                if (lastFocused && typeof lastFocused.focus === 'function') {
                    lastFocused.focus();
                }
            }, 250);
        }

        // Wire up all images in post content
        content.querySelectorAll('img').forEach(function (img) {
            img.style.cursor = 'zoom-in';
            img.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var src = img.getAttribute('data-src') || img.src;
                if (img.parentElement && img.parentElement.tagName === 'A') {
                    var href = img.parentElement.getAttribute('href');
                    if (href && /\.(jpg|jpeg|png|webp|gif|svg)/i.test(href)) {
                        src = href;
                    }
                }
                openLightbox(src, img.alt);
            });
        });

        // Backdrop click closes (image click does NOT close — stops propagation)
        lightbox.addEventListener('click', closeLightbox);
        if (figure) {
            figure.addEventListener('click', function (e) { e.stopPropagation(); });
        }
        closeBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            closeLightbox();
        });

        // Esc to close — only when open
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && lightbox.classList.contains('is-open')) {
                closeLightbox();
            }
        });
    })();

    // ─── Table Responsive Wrapper ───
    content.querySelectorAll('table').forEach(function (table) {
        var parent = table.parentElement;
        if (!parent || parent.classList.contains('table-responsive-wrapper')) {
            return;
        }

        var wrapper = document.createElement('div');
        wrapper.className = 'table-responsive-wrapper';
        parent.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    });

    if (!tocList || !tocContainer) {
        return;
    }

    // ─── Table of Contents ───
    var headings = Array.from(content.querySelectorAll('h2, h3')).filter(function (heading) {
        var text = heading.innerText.trim();
        if (text === '' || heading.closest('table, .sh-pricing-table, blockquote, figure')) {
            return false;
        }
        if (/^[\d.,\-\s+*%]+$/i.test(text)) {
            return false;
        }
        if (text.endsWith(';') || (text.length > 80 && /^\d+[\.)]\s/.test(text))) {
            return false;
        }
        return true;
    });

    if (headings.length === 0) {
        return;
    }

    tocContainer.classList.remove('hidden');

    var tocToggle = document.getElementById('toc-toggle');
    if (window.innerWidth < 768) {
        tocList.classList.add('hidden');
        if (tocToggle) {
            tocToggle.innerText = '[Mở rộng]';
        }
    }

    if (tocToggle) {
        tocToggle.addEventListener('click', function () {
            var isHidden = tocList.classList.toggle('hidden');
            this.innerText = isHidden ? '[Mở rộng]' : '[Thu gọn]';
        });
    }

    var tocLinks = [];

    headings.forEach(function (heading, index) {
        if (!heading.id) {
            heading.id = 'heading-' + index;
        }

        var li = document.createElement('li');
        var link = document.createElement('a');
        link.href = '#' + heading.id;
        link.innerText = heading.innerText;
        link.className = 'hover:text-secondary transition-colors block py-1 cursor-pointer';

        if (heading.tagName === 'H3') {
            li.className = 'ml-4 list-square';
        }

        link.addEventListener('click', function (event) {
            event.preventDefault();

            var target = document.getElementById(this.getAttribute('href').substring(1));
            if (!target) {
                return;
            }

            var headerOffset = 120;
            var offsetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerOffset;
            window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
            history.pushState(null, null, '#' + target.id);
        });

        li.appendChild(link);
        tocList.appendChild(li);
        tocLinks.push({ link: link, heading: heading });
    });

    // TOC highlight removed — TOC collapses by default so user doesn't see it while scrolling
});
