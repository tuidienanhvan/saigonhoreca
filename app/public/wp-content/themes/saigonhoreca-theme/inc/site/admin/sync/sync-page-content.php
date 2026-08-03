<?php
/**
 * Sync post_content cho các trang do TEMPLATE dựng.
 *
 * VẤN ĐỀ: 3 page (Trang Chủ, Về Saigon Horeca, Liên hệ) có giao diện do file
 * PHP dựng (front-page.php, page-templates/*.php), `post_content` RỖNG. Hệ quả
 * đo được:
 *   - Dashboard hiện "0 Từ"
 *   - Bộ dựng meta description không có gì để lấy -> rơi vào đoạn quảng cáo chung
 *   - Tìm kiếm nội bộ WordPress không thấy 3 trang này
 *   - pi-multilang bỏ qua khi dịch (is_translatable đòi post_content khác rỗng)
 *
 * 30 dự án đã theo đúng khuôn này từ trước: có `post_content` (~11KB) làm NỘI
 * DUNG NGẦM — template không gọi the_content() nên nó không hiển thị, chỉ phục
 * vụ SEO / tìm kiếm / trích dẫn. Script này áp cùng khuôn cho 3 page.
 *
 * ⚠️ AN TOÀN — đã kiểm trước khi viết: KHÔNG template nào trong số
 * front-page.php, page-gioi-thieu.php, page-lien-he.php, single-project/*.php
 * gọi the_content(). Nên điền post_content KHÔNG gây render nhân đôi.
 * Thêm template mới có gọi the_content() thì phải loại nó khỏi danh sách này.
 *
 * CHẠY: chỉ admin / CLI. Không hook vào front-end.
 *   POST /pi/v1/system/deploy {"action":"sync-page-content"}
 *   hoặc wp eval-file .../sync-page-content.php
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('sgh_page_content_marker')) {
    /** Dấu nhận biết nội dung do script sinh — để lần sau ghi đè đúng chỗ. */
    function sgh_page_content_marker() {
        return '<!-- sgh:auto-content -->';
    }
}

if (!function_exists('sgh_extract_main_content')) {
    /**
     * Bóc phần nội dung có nghĩa từ HTML đầy đủ của một trang.
     *
     * Vì sao lọc kỹ: production .vn đang có meta description là
     * "Previous slide Next slide Những dự án tiêu biểu..." — chữ điều khiển
     * slider lọt vào vì bộ dựng cũ lấy thẳng post_content của Elementor.
     * Ở đây phải chặn đúng loại rác đó.
     *
     * @param string $html HTML đầy đủ của trang.
     * @return string HTML rút gọn (h1..h3, p, li) hoặc '' nếu không bóc được.
     */
    function sgh_extract_main_content($html) {
        if (!is_string($html) || $html === '') return '';
        if (!class_exists('DOMDocument')) return '';

        // Cắt lấy <main> trước cho nhẹ; không có thì bỏ qua, đừng đoán.
        if (!preg_match('#<main\b[^>]*>(.*?)</main>#is', $html, $m)) return '';
        $inner = $m[1];

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div>' . $inner . '</div>', LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        $xp = new DOMXPath($doc);

        // Gỡ thẳng những nhánh không bao giờ là nội dung.
        $kill = $xp->query(
            '//script | //style | //noscript | //svg | //template | //form'
            . ' | //*[@aria-hidden="true"]'
            . ' | //*[@role="button"] | //*[@role="navigation"] | //*[@role="tablist"]'
            . ' | //button | //nav'
        );
        foreach (iterator_to_array($kill) as $node) {
            if ($node->parentNode) $node->parentNode->removeChild($node);
        }

        $out   = [];
        $seen  = [];
        $nodes = $xp->query('//h1 | //h2 | //h3 | //p | //li');
        foreach ($nodes as $node) {
            $text = trim(preg_replace('/\s+/u', ' ', $node->textContent));
            if ($text === '' || mb_strlen($text) < 12) continue;   // bỏ nhãn vụn
            $key = mb_strtolower($text);
            if (isset($seen[$key])) continue;                       // bỏ lặp
            $seen[$key] = true;

            $tag = $node->nodeName;
            if (!in_array($tag, ['h1', 'h2', 'h3', 'p', 'li'], true)) $tag = 'p';
            $out[] = '<' . $tag . '>' . esc_html($text) . '</' . $tag . '>';
        }

        return $out ? implode("\n", $out) : '';
    }
}

if (!function_exists('sgh_sync_page_content')) {
    /**
     * Điền post_content cho các page do template dựng.
     *
     * KHÔNG ghi đè nội dung người dùng tự viết: chỉ ghi khi post_content rỗng,
     * hoặc khi nó vốn do script này sinh (có marker).
     *
     * @param bool $dry Xem trước, không ghi.
     * @return array{updated:int,skipped:int,failed:int,log:string[]}
     */
    function sgh_sync_page_content($dry = false) {
        $log = []; $updated = 0; $skipped = 0; $failed = 0;

        $defs = function_exists('sgh_get_pages_definition') ? sgh_get_pages_definition() : [];
        if (!$defs) {
            return ['updated' => 0, 'skipped' => 0, 'failed' => 1,
                    'log' => ['Không đọc được bản khai page (sgh_get_pages_definition).']];
        }

        $marker = sgh_page_content_marker();

        foreach (array_keys($defs) as $slug) {
            $page = get_page_by_path($slug);
            if (!$page) { $log[] = "Không thấy page: $slug"; $failed++; continue; }

            $existing = (string) $page->post_content;
            if ($existing !== '' && strpos($existing, $marker) === false) {
                $log[] = "Bỏ qua $slug — có nội dung tự viết, không ghi đè.";
                $skipped++;
                continue;
            }

            $url = get_permalink($page->ID);
            // Query string để đi vòng cache tĩnh (khoá theo locale + uri).
            $res = wp_remote_get(add_query_arg('sgh_nocache', time(), $url), [
                'timeout'   => 20,
                'sslverify' => false,   // local dev dùng cert tự ký
            ]);

            if (is_wp_error($res)) {
                $log[] = "Lỗi tải $slug: " . $res->get_error_message();
                $failed++;
                continue;
            }
            $code = wp_remote_retrieve_response_code($res);
            if ($code !== 200) { $log[] = "Lỗi HTTP $code khi tải $slug"; $failed++; continue; }

            $content = sgh_extract_main_content(wp_remote_retrieve_body($res));
            if ($content === '') {
                $log[] = "Không bóc được nội dung từ $slug (thiếu <main> hoặc rỗng).";
                $failed++;
                continue;
            }

            $words = str_word_count(wp_strip_all_tags($content), 0, 'àáâãèéêìíòóôõùúýăđĩũơưạảấầẩẫậắằẳẵặẹẻẽếềểễệỉịọỏốồổỗộớờởỡợụủứừửữựỳỵỷỹ');
            if ($dry) {
                $log[] = sprintf('sẽ ghi %s — %d ký tự, ~%d từ', $slug, mb_strlen($content), $words);
                $updated++;
                continue;
            }

            $ok = wp_update_post([
                'ID'           => $page->ID,
                'post_content' => $marker . "\n" . $content,
            ], true);

            if (is_wp_error($ok)) { $log[] = "Lỗi ghi $slug: " . $ok->get_error_message(); $failed++; continue; }

            $log[] = sprintf('✓ %s — %d ký tự, ~%d từ', $slug, mb_strlen($content), $words);
            $updated++;
        }

        if (!$dry && function_exists('sgh_static_cache_flush')) sgh_static_cache_flush();

        return compact('updated', 'skipped', 'failed', 'log');
    }
}
