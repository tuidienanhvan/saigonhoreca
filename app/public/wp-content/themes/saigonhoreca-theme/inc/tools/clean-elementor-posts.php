<?php
/**
 * One-shot DB cleanup: strip Elementor markup khỏi posts.
 *
 * KHÔNG phải filter runtime — viết clean HTML thẳng vào post_content. Sau đó
 * native template render bằng entry-content.css thường.
 *
 * USAGE:
 *   /?sgh_clean_elementor=1                       → DRY RUN (preview only)
 *   /?sgh_clean_elementor=1&run=1                 → áp dụng
 *   /?sgh_clean_elementor=1&run=1&id=757          → 1 post cụ thể
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    if (!isset($_GET['sgh_clean_elementor'])) return;
    $token_ok = isset($_GET['token']) && defined('SGH_REIMPORT_TOKEN')
        && hash_equals(SGH_REIMPORT_TOKEN, (string) $_GET['token']);
    if (!$token_ok && !current_user_can('manage_options')) {
        wp_die('Admin only.', '', ['response' => 403]);
    }

    @set_time_limit(0);
    @ini_set('memory_limit', '512M');
    header('Content-Type: text/plain; charset=UTF-8');
    while (ob_get_level()) ob_end_flush();

    $is_run = !empty($_GET['run']);
    $only_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    echo "Elementor cleanup — " . ($is_run ? 'RUN' : 'DRY') . "\n\n";

    $args = [
        'post_type'      => ['post', 'page', 'project'],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ];
    if ($only_id) $args['p'] = $only_id;
    $ids = get_posts($args);

    $hits = []; $ok = 0; $skip = 0;
    foreach ($ids as $pid) {
        $content = (string) get_post_field('post_content', $pid);
        if (strpos($content, 'elementor-') === false
            && strpos($content, 'data-elementor-') === false
            && strpos($content, 'data-element_type') === false) {
            continue;
        }
        $hits[] = $pid;
    }

    echo "Found " . count($hits) . " posts với Elementor markup\n\n";

    foreach ($hits as $idx => $pid) {
        $title = get_the_title($pid);
        $content = (string) get_post_field('post_content', $pid);
        $clean = sgh_clean_elementor_html($content);
        $before = mb_strlen(strip_tags($content));
        $after  = mb_strlen(strip_tags($clean));

        printf("[%d/%d] #%d %s\n", $idx+1, count($hits), $pid, $title);
        printf("        before: %d chars HTML / %d chars text\n", strlen($content), $before);
        printf("        after : %d chars HTML / %d chars text\n", strlen($clean), $after);

        if ($after < 100 && $before >= 100) {
            echo "        ⚠ SKIP — cleaned content quá ngắn, có thể parser strip nhầm\n\n";
            $skip++;
            continue;
        }

        if ($is_run) {
            wp_update_post(['ID' => $pid, 'post_content' => wp_slash($clean)]);
            echo "        ✓ Updated\n";
            $ok++;
        }
        echo "\n";
    }

    echo "DONE — ok: $ok, skip: $skip\n";
    if (!$is_run) echo "(re-run với &run=1)\n";
    exit;
});

/**
 * Strip Elementor wrappers, preserve content nodes.
 *
 * Unwrap section/container/column/widget divs; keep img, headings, p, ul, table, a, strong.
 * Promote data-src → src cho lazyload images.
 */
function sgh_clean_elementor_html(string $html): string {
    if ($html === '') return $html;
    if (strpos($html, 'elementor-') === false
        && strpos($html, 'data-elementor-') === false
        && strpos($html, 'data-element_type') === false) {
        return $html;
    }

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="UTF-8"><div id="__sgh_root">' . $html . '</div>', LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_clear_errors();
    $xpath = new DOMXPath($doc);
    $root = $doc->getElementById('__sgh_root');
    if (!$root) return $html;

    // 1. Promote lazyload images: data-src → src.
    // Production placeholders xuất hiện 2 dạng:
    //   src="data:image/svg+xml;base64,..."   (đúng data URI)
    //   src="image/svg+xml;base64,..."         (LẠI thiếu data: prefix — scrape sai)
    // Cả 2 đều invalid → promote từ data-src.
    $imgs = $xpath->query('.//img', $root);
    if ($imgs) {
        foreach ($imgs as $img) {
            /** @var DOMElement $img */
            $cur_src = $img->getAttribute('src');
            $real = $img->getAttribute('data-src');
            if ($real === '') $real = $img->getAttribute('data-lazy-src');
            $needs_promote = $cur_src === ''
                || strpos($cur_src, 'data:') === 0
                || strpos($cur_src, 'image/svg+xml') !== false;
            if ($needs_promote && $real !== '' && strpos($real, 'data:') !== 0) {
                $img->setAttribute('src', $real);
            }
            $cur_rss = $img->getAttribute('srcset');
            $rss = $img->getAttribute('data-srcset');
            if ($rss === '') $rss = $img->getAttribute('data-lazy-srcset');
            if ($rss !== '' && ($cur_rss === '' || strpos($cur_rss, 'data:') === 0)) {
                $img->setAttribute('srcset', $rss);
            }
        }
    }

    // 2. Remove elements that are pure decoration / widgets / related posts.
    $remove_xpaths = [
        // Elementor decoration
        './/*[contains(concat(" ", normalize-space(@class), " "), " elementor-background-overlay ")]',
        './/*[contains(concat(" ", normalize-space(@class), " "), " elementor-background-slideshow ")]',
        './/*[contains(concat(" ", normalize-space(@class), " "), " elementor-shape ")]',
        // Essential Addons widgets — related posts grid, breadcrumbs, post meta
        './/*[starts-with(@class, "eael-")]',
        './/*[contains(concat(" ", @class, " "), " eael-")]',
        // ElementsKit
        './/*[contains(concat(" ", @class, " "), " ekit-")]',
        // FontAwesome icon stubs (no JS to render → broken)
        './/i[contains(@class, "fa-") or starts-with(@class, "fa ") or starts-with(@class, "fas ") or starts-with(@class, "fab ") or starts-with(@class, "far ")]',
        // Astra widgets / footer / sharing
        './/*[contains(concat(" ", @class, " "), " widget_categories ")]',
        './/*[contains(concat(" ", @class, " "), " widget_recent_entries ")]',
        './/*[contains(concat(" ", @class, " "), " widget_archive ")]',
        './/*[contains(concat(" ", @class, " "), " widget_tag_cloud ")]',
        // Sharing / social widgets
        './/*[contains(concat(" ", @class, " "), " sharedaddy ")]',
        './/*[contains(concat(" ", @class, " "), " sd-block ")]',
        './/*[contains(concat(" ", @class, " "), " jp-relatedposts ")]',
        // Related posts (generic)
        './/*[contains(concat(" ", @class, " "), " related ")]',
        './/*[contains(concat(" ", @class, " "), " related-posts ")]',
        // Comments
        './/*[@id="reviews" or @id="comments" or @id="respond"]',
        // Code blocks
        './/script', './/style', './/noscript',
    ];
    foreach ($remove_xpaths as $xp) {
        $found = $xpath->query($xp, $root);
        if ($found) for ($i = $found->length - 1; $i >= 0; $i--) {
            $el = $found->item($i);
            if ($el && $el->parentNode) $el->parentNode->removeChild($el);
        }
    }

    // 3. Unwrap Elementor structural elements — replace với children inline.
    //    Lặp nhiều pass cho nested wrappers.
    $structural = [
        'elementor-section', 'elementor-container', 'elementor-row',
        'elementor-column', 'elementor-column-wrap', 'elementor-widget-wrap',
        'elementor-widget-container', 'elementor-widget', 'elementor-element',
        'elementor-top-section', 'elementor-top-column',
        'elementor-inner-section', 'elementor-inner-column',
    ];
    $cls_predicate = [];
    foreach ($structural as $c) {
        $cls_predicate[] = 'contains(concat(" ", normalize-space(@class), " "), " ' . $c . ' ")';
    }
    $xp_struct = './/*[' . implode(' or ', $cls_predicate) . ']';

    for ($pass = 0; $pass < 8; $pass++) {
        $els = $xpath->query($xp_struct, $root);
        if (!$els || $els->length === 0) break;
        // Process leaf-first (deepest unwrap first) — iterate in reverse
        for ($i = $els->length - 1; $i >= 0; $i--) {
            $el = $els->item($i);
            if (!$el || !$el->parentNode) continue;
            // Move children before this node, then remove it
            while ($el->firstChild) {
                $el->parentNode->insertBefore($el->firstChild, $el);
            }
            $el->parentNode->removeChild($el);
        }
    }

    // 4. Strip data-* + style + Elementor classes on REMAINING elements.
    $all = $xpath->query('.//*', $root);
    if ($all) {
        foreach ($all as $el) {
            /** @var DOMElement $el */
            // Collect data-* attribute names first
            $to_remove = [];
            foreach ($el->attributes as $attr) {
                $name = $attr->nodeName;
                if (strpos($name, 'data-') === 0 || $name === 'style') {
                    $to_remove[] = $name;
                }
            }
            foreach ($to_remove as $n) $el->removeAttribute($n);

            $cls = $el->getAttribute('class');
            if ($cls !== '') {
                $kept = array_filter(preg_split('/\s+/', $cls), function ($c) {
                    return $c !== ''
                        && stripos($c, 'elementor') !== 0
                        && stripos($c, 'e-con') !== 0
                        && stripos($c, 'lazyload') !== 0
                        && stripos($c, 'lazy-load') !== 0;
                });
                if ($kept) $el->setAttribute('class', implode(' ', $kept));
                else $el->removeAttribute('class');
            }
        }
    }

    // Serialize root's children
    $out = '';
    foreach ($root->childNodes as $child) {
        $out .= $doc->saveHTML($child);
    }
    // Cleanup whitespace
    $out = preg_replace('/\s+/', ' ', $out);
    $out = preg_replace('/>\s+</', '><', $out);
    // Reformat newlines after block closing
    $out = preg_replace('#(</(?:p|h[1-6]|ul|ol|li|table|tr|div|section|article|figure)>)#i', "$1\n", $out);
    return trim($out);
}
