document.addEventListener('DOMContentLoaded', function () {
    var content = document.getElementById('post-content');
    var tocList = document.getElementById('toc-list');
    var tocContainer = document.getElementById('toc-container');

    if (!content) {
        return;
    }

    // ─── Ẩn block Elementor "Read this article in English language" ───
    // (khối nền gradient xám + nút, nhúng cứng trong content; element-id khác nhau
    //  mỗi bài nên match theo CHỮ rồi ẩn cả container e-parent. Theme-side → áp cả prod.)
    (function hideEnglishCta() {
        var titles = content.querySelectorAll('.elementor-heading-title, h1, h2, h3, h4, span');
        for (var i = 0; i < titles.length; i++) {
            var t = (titles[i].textContent || '').trim().toLowerCase();
            if (t === 'read this article in english language' || t === 'read this article in english') {
                var box = titles[i].closest('.e-parent') || titles[i].closest('.e-con') || titles[i].closest('.elementor-element');
                if (box) { box.style.display = 'none'; }
                break;
            }
        }
    })();

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

    // Tự render marker: h2 = số La Mã (I, II, III), h3 = bullet → bỏ marker mặc định <ul>
    tocList.style.listStyleType = 'none';
    tocList.style.paddingLeft = '0';
    tocList.style.margin = '0';

    var tocToggle = document.getElementById('toc-toggle');
    // Dùng inline style (đè chắc) vì .sh-single__toc-list{display:flex} đứng SAU .hidden
    // trong bundle nên class .hidden không thắng được.
    if (window.innerWidth < 768) {
        tocList.style.display = 'none';
        if (tocToggle) {
            tocToggle.innerText = '[Mở rộng]';
        }
    }

    if (tocToggle) {
        tocToggle.addEventListener('click', function () {
            var isHidden = (tocList.style.display === 'none');
            tocList.style.display = isHidden ? 'flex' : 'none';
            this.innerText = isHidden ? '[Thu gọn]' : '[Mở rộng]';
        });
    }

    var tocLinks = [];
    var h2Count = 0;
    function toRoman(n) {
        var m = [[1000,'M'],[900,'CM'],[500,'D'],[400,'CD'],[100,'C'],[90,'XC'],[50,'L'],[40,'XL'],[10,'X'],[9,'IX'],[5,'V'],[4,'IV'],[1,'I']], r = '';
        for (var i = 0; i < m.length; i++) { while (n >= m[i][0]) { r += m[i][1]; n -= m[i][0]; } }
        return r;
    }

    headings.forEach(function (heading, index) {
        if (!heading.id) {
            heading.id = 'heading-' + index;
        }

        var li = document.createElement('li');
        li.style.display = 'flex';
        li.style.alignItems = 'baseline';
        li.style.gap = '.45rem';

        var marker = document.createElement('span');
        marker.style.flexShrink = '0';
        if (heading.tagName === 'H3') {
            li.style.marginLeft = '1.5rem';
            marker.innerText = '•';
            marker.style.opacity = '.55';
        } else {
            h2Count++;
            marker.innerText = toRoman(h2Count) + '.';
            marker.style.fontWeight = '700';
            marker.style.color = 'var(--gold)';
            marker.style.minWidth = '2rem';
        }

        var link = document.createElement('a');
        link.href = '#' + heading.id;
        link.innerText = heading.innerText;
        link.className = 'hover:text-secondary transition-colors py-1 cursor-pointer';

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

        li.appendChild(marker);
        li.appendChild(link);
        tocList.appendChild(li);
        tocLinks.push({ link: link, heading: heading });
    });

    // TOC highlight removed — TOC collapses by default so user doesn't see it while scrolling
});
