<?php
/**
 * Re-import product descriptions from production saigonhoreca.vn.
 *
 * Lý do: import gốc chỉ lấy title + thumbnail + excerpt ngắn (≤200 chars),
 * thiếu body (Specifications Sheet, Default equipment, Optional equipment,
 * dimensions, weight, …). Tool này duyệt mọi product có post_content < 500
 * ký tự, fetch HTML production tương ứng, parse WooCommerce description tabs
 * và lưu lại vào DB.
 *
 * USAGE (chỉ admin):
 *   /?sgh_reimport_products=1                 → dry run, in danh sách + dự kiến
 *   /?sgh_reimport_products=1&run=1           → thực sự cập nhật DB
 *   /?sgh_reimport_products=1&run=1&limit=10  → chỉ chạy 10 sản phẩm đầu
 *   /?sgh_reimport_products=1&run=1&id=1588   → chỉ chạy 1 product ID cụ thể
 *
 * Stream progress as text/plain (browser hiển thị real-time).
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

// One-time secret token cho phép chạy không cần login admin (CLI / curl).
// Sau khi import xong nên đổi token hoặc xoá file này.
const SGH_REIMPORT_TOKEN = 'reimp_8d2f4a9c_2026';

// Cleanup endpoint: balance unbalanced </div> tags in all product post_content.
add_action('init', function () {
    if (!isset($_GET['sgh_fix_div_balance'])) return;
    $token_ok = isset($_GET['token']) && hash_equals(SGH_REIMPORT_TOKEN, (string) $_GET['token']);
    if (!$token_ok && !current_user_can('manage_options')) {
        wp_die('Admin only.', '', ['response' => 403]);
    }
    header('Content-Type: text/plain; charset=UTF-8');
    while (ob_get_level()) ob_end_flush();

    $ids = get_posts(['post_type'=>'product','post_status'=>'publish','posts_per_page'=>-1,'fields'=>'ids']);
    $fixed = 0;
    foreach ($ids as $pid) {
        $c = (string) get_post_field('post_content', $pid);
        $opens  = preg_match_all('#<div\b#i', $c);
        $closes = substr_count(strtolower($c), '</div>');
        if ($closes > $opens) {
            // Strip trailing whitespace + </div> until balanced
            $excess = $closes - $opens;
            $new = $c;
            for ($k = 0; $k < $excess; $k++) {
                $new = preg_replace('#</div>\s*$#i', '', $new, 1);
            }
            wp_update_post(['ID'=>$pid, 'post_content'=>wp_slash($new)]);
            $fixed++;
            echo "#$pid  $opens<div  $closes</div  →  trimmed $excess\n";
        }
    }
    echo "\nDONE — fixed $fixed products\n";
    exit;
});

add_action('init', function () {
    if (!isset($_GET['sgh_reimport_products'])) return;
    $token_ok = isset($_GET['token']) && hash_equals(SGH_REIMPORT_TOKEN, (string) $_GET['token']);
    if (!$token_ok && !current_user_can('manage_options')) {
        wp_die('Admin only (or pass &token=...).', '', ['response' => 403]);
    }

    @set_time_limit(0);
    @ini_set('max_execution_time', 0);
    @ini_set('memory_limit', '512M');
    nocache_headers();
    header('Content-Type: text/plain; charset=UTF-8');
    while (ob_get_level()) ob_end_flush();

    $is_run    = !empty($_GET['run']);
    $limit     = isset($_GET['limit']) ? max(0, (int) $_GET['limit']) : 0;
    $single_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    echo "════════════════════════════════════════════════════\n";
    echo "Saigon Horeca — Product Content Re-Import\n";
    echo "Mode: " . ($is_run ? 'RUN (will modify DB)' : 'DRY RUN (preview only)') . "\n";
    if ($limit) echo "Limit: $limit\n";
    if ($single_id) echo "Only ID: $single_id\n";
    echo "════════════════════════════════════════════════════\n\n";
    @flush();

    // Build query: products with short content (chưa import)
    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'orderby'        => 'ID',
        'order'          => 'ASC',
    ];
    if ($single_id) $args['p'] = $single_id;
    $ids = get_posts($args);

    // Filter to short content only (unless single_id forced)
    $targets = [];
    foreach ($ids as $id) {
        $content = (string) get_post_field('post_content', $id);
        // Re-import điều kiện: forced single ID, hoặc placeholder __RESET__,
        // hoặc content rỗng / quá ngắn (< 100 chars text).
        if ($single_id
            || trim($content) === '__RESET__'
            || mb_strlen(strip_tags($content)) < 100) {
            $targets[] = $id;
        }
    }
    if ($limit) $targets = array_slice($targets, 0, $limit);

    echo "Tổng products cần xử lý: " . count($targets) . "\n\n";
    @flush();

    $ok = 0; $fail = 0; $skip = 0;
    foreach ($targets as $idx => $pid) {
        $n = $idx + 1;
        $slug = get_post_field('post_name', $pid);
        $title = get_the_title($pid);
        $url = 'https://saigonhoreca.vn/san-pham/' . $slug . '/';

        printf("[%d/%d] #%d %s\n", $n, count($targets), $pid, $title);
        printf("        → %s\n", $url);
        @flush();

        // Fetch production HTML
        $resp = wp_remote_get($url, [
            'timeout'     => 25,
            'redirection' => 3,
            'user-agent'  => 'Mozilla/5.0 (compatible; SGHReimporter/1.0)',
        ]);
        if (is_wp_error($resp)) {
            echo "        ✗ FETCH ERROR: " . $resp->get_error_message() . "\n\n";
            $fail++; continue;
        }
        $code = wp_remote_retrieve_response_code($resp);
        if ($code !== 200) {
            echo "        ✗ HTTP $code\n\n";
            $fail++; continue;
        }
        $html = (string) wp_remote_retrieve_body($resp);
        if (strlen($html) < 1000) {
            echo "        ✗ Body too small (" . strlen($html) . " bytes)\n\n";
            $fail++; continue;
        }

        // Parse description tab (Woo standard markup)
        $new_content = sgh_extract_woo_description($html);
        if ($new_content === '' || mb_strlen(strip_tags($new_content)) < 100) {
            echo "        ⚠ Không tìm thấy description trên trang production (skip)\n\n";
            $skip++;
            usleep(400000);
            continue;
        }

        $clean_len = mb_strlen(strip_tags($new_content));
        printf("        ✓ Parsed %d chars HTML (%d chars text)\n", strlen($new_content), $clean_len);

        if ($is_run) {
            $result = wp_update_post([
                'ID'           => $pid,
                'post_content' => wp_slash($new_content),
            ], true);
            if (is_wp_error($result)) {
                echo "        ✗ DB ERROR: " . $result->get_error_message() . "\n\n";
                $fail++; continue;
            }
            echo "        ✓ Updated DB\n";
            $ok++;
        } else {
            echo "        (dry run — không update)\n";
            $ok++;
        }

        echo "\n";
        @flush();

        // Throttle: 400ms between requests (server-friendly)
        usleep(400000);
    }

    echo "════════════════════════════════════════════════════\n";
    printf("DONE — ok: %d, skip: %d, fail: %d\n", $ok, $skip, $fail);
    if (!$is_run) echo "(re-run với &run=1 để áp dụng)\n";
    echo "════════════════════════════════════════════════════\n";
    exit;
});

/**
 * Parse WooCommerce single-product HTML, extract product description.
 *
 * Production saigonhoreca.vn dùng Astra + Woo, mỗi trang sản phẩm có:
 *   <div id="tab-description"> ... </div>
 * chứa: <h2>Mô tả</h2> + description body (lists, tables, paragraphs).
 *
 * Strip toàn bộ inline style + Elementor remnants, normalize whitespace.
 */
function sgh_extract_woo_description(string $html): string {
    if ($html === '') return '';

    // Dùng DOMDocument để parse chính xác — regex không xử lý nested div.
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    // Cast UTF-8 hint
    $doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_clear_errors();
    $xpath = new DOMXPath($doc);

    // Selector chain: prefer tab-description → woocommerce-Tabs-panel--description → short-description
    $candidates = [
        '//div[@id="tab-description"]',
        '//div[contains(@class, "woocommerce-Tabs-panel--description")]',
        '//div[contains(@class, "ast-accordion-wrap")]', // Astra accordion (production saigonhoreca dùng cái này)
        '//div[contains(@class, "woocommerce-product-details__short-description")]',
    ];

    $node = null;
    foreach ($candidates as $xp) {
        $nodes = $xpath->query($xp);
        if ($nodes && $nodes->length > 0) { $node = $nodes->item(0); break; }
    }
    if (!$node) return '';

    // Trong node tìm được, loại bỏ các block không thuộc description:
    //   - .related (related products)
    //   - .upsells
    //   - #reviews / .reviews
    //   - #respond / .comment-respond
    //   - .clear (Astra spacer)
    $strip_xpaths = [
        './/*[contains(concat(" ", normalize-space(@class), " "), " related ")]',
        './/*[contains(concat(" ", normalize-space(@class), " "), " upsells ")]',
        './/*[@id="reviews"]',
        './/*[@id="comments"]',
        './/*[@id="respond"]',
        './/*[contains(concat(" ", normalize-space(@class), " "), " comment-respond ")]',
        './/*[contains(concat(" ", normalize-space(@class), " "), " comments-area ")]',
        './/script',
        './/style',
        './/noscript',
    ];
    foreach ($strip_xpaths as $sx) {
        $found = $xpath->query($sx, $node);
        if ($found) {
            // Iterate in reverse to safely remove
            for ($i = $found->length - 1; $i >= 0; $i--) {
                $el = $found->item($i);
                if ($el && $el->parentNode) $el->parentNode->removeChild($el);
            }
        }
    }

    // Promote lazyload images: src="image/svg+xml;..." (Astra placeholder) is
    // junk — actual image URL ở data-src. Swap trước khi strip data-* attrs.
    $imgs = $xpath->query('.//img', $node);
    if ($imgs) {
        foreach ($imgs as $img) {
            /** @var DOMElement $img */
            $real_src = $img->getAttribute('data-src');
            if ($real_src === '') $real_src = $img->getAttribute('data-lazy-src');
            if ($real_src !== '' && strpos($real_src, 'data:') !== 0) {
                $img->setAttribute('src', $real_src);
            }
            $real_srcset = $img->getAttribute('data-srcset');
            if ($real_srcset === '') $real_srcset = $img->getAttribute('data-lazy-srcset');
            if ($real_srcset !== '') {
                $img->setAttribute('srcset', $real_srcset);
            }
            // Strip lazyload class
            $cls = $img->getAttribute('class');
            if ($cls !== '') {
                $cls = trim(preg_replace('/\b(?:lazyload|lazy-load|lazyloaded)\b/i', '', $cls));
                $cls = preg_replace('/\s+/', ' ', $cls);
                if ($cls === '') $img->removeAttribute('class');
                else $img->setAttribute('class', $cls);
            }
        }
    }

    // Serialize children only (skip outer wrapper div)
    $body = '';
    foreach ($node->childNodes as $child) {
        $body .= $doc->saveHTML($child);
    }
    if ($body === '') return '';

    // Strip <h2>Mô tả</h2> opener (we don't need duplicate heading)
    $body = preg_replace('#<h2[^>]*>\s*M(?:ô|o)\s*t(?:ả|a)\s*</h2>\s*#iu', '', $body, 1);

    // Strip all id="..." (Woo adds anchors, không cần)
    $body = preg_replace('/\s+id="[^"]*"/i', '', $body);

    // Strip data-* attributes
    $body = preg_replace('/\s+data-[a-z0-9_-]+="[^"]*"/i', '', $body);

    // Strip inline styles
    $body = preg_replace('/\s+style="[^"]*"/i', '', $body);

    // Strip Elementor classes (defensive — if production page has any)
    $body = preg_replace_callback('/\sclass="([^"]+)"/i', function ($m) {
        $kept = array_filter(preg_split('/\s+/', $m[1]), function ($c) {
            return $c !== '' && stripos($c, 'elementor') !== 0 && stripos($c, 'wp-block-') !== 0;
        });
        return $kept ? ' class="' . implode(' ', $kept) . '"' : '';
    }, $body);

    // Decode HTML entities for any URL/text artifacts
    // ... actually keep entities as-is, browser handles them.

    // Trim and normalize whitespace
    $body = preg_replace('/\s+/', ' ', $body);
    $body = preg_replace('/>\s+</', '><', $body);
    $body = trim($body);

    // Reformat for readability — newline after block-level closing tags
    $body = preg_replace('#(</(?:p|h[1-6]|ul|ol|li|table|tr|div|section)>)#i', "$1\n", $body);

    return $body;
}
