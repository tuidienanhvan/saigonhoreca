<?php
/**
 * Re-crawl post content từ production saigonhoreca.vn, extract chỉ body
 * (data-elementor-type="wp-post" ∩ widget-theme-post-content), strip:
 *   - eael-* (Essential Addons: related posts, breadcrumbs)
 *   - ekit-* (ElementsKit)
 *   - Font Awesome <i> stubs
 *   - Contact / Social widgets
 *   - Elementor decoration + structural wrappers
 *
 * Khác với clean-elementor-posts (chạy trên DB content), tool này LẤY LẠI
 * từ production để đảm bảo lấy đủ ảnh + content gốc.
 *
 * USAGE:
 *   /?sgh_recrawl_posts=1                  → DRY RUN
 *   /?sgh_recrawl_posts=1&run=1            → apply
 *   /?sgh_recrawl_posts=1&run=1&id=745     → single post
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    if (!isset($_GET['sgh_recrawl_posts'])) return;
    $token_ok = isset($_GET['token']) && defined('SGH_REIMPORT_TOKEN')
        && hash_equals(SGH_REIMPORT_TOKEN, (string) $_GET['token']);
    if (!$token_ok && !current_user_can('manage_options')) {
        wp_die('Admin only.', '', ['response' => 403]);
    }

    @set_time_limit(0);
    @ini_set('memory_limit', '512M');
    header('Content-Type: text/plain; charset=UTF-8');
    while (ob_get_level()) ob_end_flush();

    $is_run  = !empty($_GET['run']);
    $only_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    echo "Post re-crawl — " . ($is_run ? 'RUN' : 'DRY') . "\n\n";

    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ];
    if ($only_id) $args['p'] = $only_id;
    $ids = get_posts($args);

    // Filter: skip posts already clean (no eael, no Related Posts text). Còn lại re-crawl.
    if (!$only_id) {
        $ids = array_values(array_filter($ids, function ($id) {
            $c = (string) get_post_field('post_content', $id);
            return strpos($c, 'eael') !== false
                || strpos($c, 'Related Posts') !== false
                || strpos($c, 'elementor-element') !== false
                || strpos($c, 'fas fa') !== false;
        }));
    }

    // Limit support
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 0;
    if ($limit > 0) $ids = array_slice($ids, 0, $limit);

    $ok = 0; $skip = 0; $fail = 0;
    foreach ($ids as $idx => $pid) {
        $slug  = get_post_field('post_name', $pid);
        $title = get_the_title($pid);
        $url   = 'https://saigonhoreca.vn/' . $slug . '/';

        printf("[%d/%d] #%d %s\n        → %s\n", $idx+1, count($ids), $pid, $title, $url);

        $resp = wp_remote_get($url, ['timeout' => 25, 'user-agent' => 'Mozilla/5.0 (compatible; SGH/1.0)']);
        if (is_wp_error($resp) || wp_remote_retrieve_response_code($resp) !== 200) {
            echo "        ✗ fetch fail\n\n";
            $fail++; continue;
        }
        $html = (string) wp_remote_retrieve_body($resp);
        $clean = sgh_extract_post_body($html);

        if (mb_strlen(strip_tags($clean)) < 100) {
            echo "        ⚠ skip (cleaned < 100 chars)\n\n";
            $skip++;
            usleep(300000);
            continue;
        }

        printf("        ✓ %d chars HTML / %d chars text\n", strlen($clean), mb_strlen(strip_tags($clean)));
        if ($is_run) {
            wp_update_post(['ID' => $pid, 'post_content' => wp_slash($clean)]);
            echo "        ✓ Updated DB\n";
            $ok++;
        }
        echo "\n";
        usleep(300000);
    }
    echo "DONE — ok: $ok, skip: $skip, fail: $fail\n";
    exit;
});

/**
 * Extract post body từ production HTML, strip mọi widget/decoration.
 */
function sgh_extract_post_body(string $html): string {
    if ($html === '') return '';
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_clear_errors();
    $xpath = new DOMXPath($doc);

    // Tìm container Elementor của wp-post
    $wp_post_nodes = $xpath->query('//div[@data-elementor-type="wp-post"]');
    if (!$wp_post_nodes || $wp_post_nodes->length === 0) return '';
    $wp_post = $wp_post_nodes->item(0);

    // EXTRACT-ONLY strategy: chỉ lấy widgets nội dung. Bỏ qua mọi widget khác
    // (post-title, post-info, social, icon-box, button, spacer, TOC, eael-*).
    // Whitelist widget types được nhặt theo thứ tự xuất hiện trong DOM.
    $keep_widget_types = [
        'elementor-widget-text-editor',
        'elementor-widget-heading',
        'elementor-widget-image',
        'elementor-widget-image-carousel',
        'elementor-widget-image-gallery',
        'elementor-widget-video',
    ];
    $pred = [];
    foreach ($keep_widget_types as $wt) {
        $pred[] = 'contains(concat(" ", @class, " "), " ' . $wt . ' ")';
    }
    $xp_keep = './/div[' . implode(' or ', $pred) . ']';
    $widgets = $xpath->query($xp_keep, $wp_post);
    if (!$widgets || $widgets->length === 0) return '';

    // Tạo container mới chứa CHỈ children của các widget được giữ.
    $new_doc = new DOMDocument();
    $new_doc->loadHTML('<?xml encoding="UTF-8"><div id="__sgh_body"></div>', LIBXML_NOERROR | LIBXML_NOWARNING);
    $body_node = $new_doc->getElementById('__sgh_body');

    foreach ($widgets as $widget) {
        /** @var DOMElement $widget */
        // Không lấy nested widgets (image-carousel có nhiều text-editor con,
        // nhưng tránh duplicate bằng cách bỏ qua widget nằm trong widget khác đã match).
        $parent = $widget->parentNode;
        $is_nested = false;
        while ($parent && $parent !== $wp_post) {
            if ($parent->nodeType === XML_ELEMENT_NODE) {
                $pcls = ' ' . $parent->getAttribute('class') . ' ';
                foreach ($keep_widget_types as $wt) {
                    if (strpos($pcls, ' ' . $wt . ' ') !== false) { $is_nested = true; break 2; }
                }
            }
            $parent = $parent->parentNode;
        }
        if ($is_nested) continue;

        // Import deep-copy của widget vào doc mới
        $imported = $new_doc->importNode($widget, true);
        $body_node->appendChild($imported);
    }

    // Reassign $node + $xpath sang doc mới để các bước sau làm việc trên scope này
    $doc   = $new_doc;
    $xpath = new DOMXPath($doc);
    $node  = $body_node;

    // Promote lazyload imgs trên doc mới
    $imgs = $xpath->query('.//img', $node);
    if ($imgs) foreach ($imgs as $img) {
        /** @var DOMElement $img */
        $real = $img->getAttribute('data-src');
        if ($real === '') $real = $img->getAttribute('data-lazy-src');
        if ($real !== '' && strpos($real, 'data:') !== 0) $img->setAttribute('src', $real);
        $rss = $img->getAttribute('data-srcset');
        if ($rss === '') $rss = $img->getAttribute('data-lazy-srcset');
        if ($rss !== '') $img->setAttribute('srcset', $rss);
    }

    // 2. Remove widgets / chrome.
    $remove_xp = [
        // Essential Addons widgets (related posts grid, breadcrumbs, meta)
        './/*[starts-with(@class, "eael-")]',
        './/*[contains(concat(" ", @class, " "), " eael-")]',
        // ElementsKit
        './/*[contains(@class, "ekit-")]',
        // FontAwesome icons (broken without FA CSS)
        './/i[contains(@class, "fa-") or starts-with(@class, "fa ") or starts-with(@class, "fas ") or starts-with(@class, "fab ") or starts-with(@class, "far ")]',
        // Astra/WP sidebar widgets — chỉ match WP widget naming chuẩn (widget_categories, widget_recent_entries…)
        // KHÔNG match elementor-widget__width-inherit (double underscore).
        './/*[contains(concat(" ", @class, " "), " widget_categories ")]',
        './/*[contains(concat(" ", @class, " "), " widget_recent_entries ")]',
        './/*[contains(concat(" ", @class, " "), " widget_archive ")]',
        './/*[contains(concat(" ", @class, " "), " widget_tag_cloud ")]',
        './/*[contains(concat(" ", @class, " "), " widget_recent_comments ")]',
        './/*[contains(concat(" ", @class, " "), " widget_meta ")]',
        './/*[contains(concat(" ", @class, " "), " widget_search ")]',
        './/*[contains(concat(" ", @class, " "), " widget_pages ")]',
        // Sharing / social
        './/*[contains(@class, "sharedaddy")]',
        './/*[contains(@class, "sd-block")]',
        './/*[contains(@class, "jp-relatedposts")]',
        './/*[contains(@class, "yarpp-related")]',
        // Generic related / footer
        './/*[contains(concat(" ", @class, " "), " related ")]',
        './/*[contains(concat(" ", @class, " "), " related-posts ")]',
        // Contact / hotline widgets (production renders contact card at bottom)
        './/*[contains(@class, "contact-info")]',
        './/*[contains(@class, "social-icons")]',
        // Elementor decoration
        './/*[contains(@class, "elementor-background-overlay")]',
        './/*[contains(@class, "elementor-shape")]',
        // Comments / reviews
        './/*[@id="reviews" or @id="comments" or @id="respond"]',
        // Code
        './/script', './/style', './/noscript',
    ];
    foreach ($remove_xp as $xp) {
        $found = $xpath->query($xp, $node);
        if ($found) for ($i = $found->length - 1; $i >= 0; $i--) {
            $el = $found->item($i);
            if ($el && $el->parentNode) $el->parentNode->removeChild($el);
        }
    }

    // 3. Unwrap Elementor structural divs.
    $structural = [
        'elementor-section','elementor-container','elementor-row','elementor-column',
        'elementor-column-wrap','elementor-widget-wrap','elementor-widget-container',
        'elementor-widget','elementor-element','elementor-top-section','elementor-top-column',
        'elementor-inner-section','elementor-inner-column',
    ];
    $pred = [];
    foreach ($structural as $c) $pred[] = 'contains(concat(" ", normalize-space(@class), " "), " ' . $c . ' ")';
    $xp_struct = './/*[' . implode(' or ', $pred) . ']';
    for ($pass = 0; $pass < 8; $pass++) {
        $els = $xpath->query($xp_struct, $node);
        if (!$els || $els->length === 0) break;
        for ($i = $els->length - 1; $i >= 0; $i--) {
            $el = $els->item($i);
            if (!$el || !$el->parentNode) continue;
            while ($el->firstChild) $el->parentNode->insertBefore($el->firstChild, $el);
            $el->parentNode->removeChild($el);
        }
    }

    // 4. Strip data-*, style, lingering Elementor classes from REMAINING elements.
    $all = $xpath->query('.//*', $node);
    if ($all) foreach ($all as $el) {
        /** @var DOMElement $el */
        $to_remove = [];
        foreach ($el->attributes as $attr) {
            $n = $attr->nodeName;
            if (strpos($n, 'data-') === 0 || $n === 'style') $to_remove[] = $n;
        }
        foreach ($to_remove as $n) $el->removeAttribute($n);
        $cls = $el->getAttribute('class');
        if ($cls !== '') {
            $kept = array_filter(preg_split('/\s+/', $cls), function ($c) {
                return $c !== ''
                    && stripos($c, 'elementor') !== 0
                    && stripos($c, 'eael-') !== 0
                    && stripos($c, 'ekit-') !== 0
                    && stripos($c, 'e-con') !== 0
                    && stripos($c, 'fa-') !== 0
                    && stripos($c, 'lazyload') !== 0
                    && stripos($c, 'lazy-load') !== 0;
            });
            if ($kept) $el->setAttribute('class', implode(' ', $kept));
            else $el->removeAttribute('class');
        }
    }

    // Serialize children
    $out = '';
    foreach ($node->childNodes as $child) $out .= $doc->saveHTML($child);

    // Strip trailing standalone "Related Posts" / "Bài viết liên quan" headings
    // (Elementor heading widget tiêu đề cho section related-posts đã bị xoá).
    $out = preg_replace('#<(?:span|h[1-6]|p)[^>]*>\s*(?:Related\s*Posts|Bài\s*viết\s*li[eê]n\s*quan)\s*</(?:span|h[1-6]|p)>#iu', '', $out);

    // Strip duplicate breadcrumb listing (often <ul><li><span>category, ...</span></li></ul>
    // immediately followed by h1 of post title — duplicate of WP).
    // Pattern: leading <ul>...</ul><h1>...</h1><ul>...time...</ul>
    $out = preg_replace('#^\s*<ul>\s*<li>.*?</li>\s*</ul>\s*<h1>.*?</h1>\s*<ul>.*?</ul>\s*#is', '', $out, 1);
    // Also strip leading <h1> if title alone (since theme renders h1 separately)
    $out = preg_replace('#^\s*<h1[^>]*>.*?</h1>\s*#is', '', $out, 1);

    // Cleanup whitespace
    $out = preg_replace('/\s+/', ' ', $out);
    $out = preg_replace('/>\s+</', '><', $out);
    $out = preg_replace('#(</(?:p|h[1-6]|ul|ol|li|table|tr|div|section|article|figure)>)#i', "$1\n", $out);
    return trim($out);
}
