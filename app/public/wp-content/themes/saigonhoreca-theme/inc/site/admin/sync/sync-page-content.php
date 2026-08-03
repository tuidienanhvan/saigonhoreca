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
        //
        // Hai nhóm cuối là KHỐI DÙNG CHUNG của mọi trang dự án:
        //   template-parts/project-pillar/_related.php -> "Dự án nổi bật"
        //   template-parts/project-pillar/_cta.php     -> "Thông tin liên hệ"
        // Cả hai bọc bởi <section class="pp__section"> chung chung, nhưng bên
        // trong có class riêng (pp-related__*, pp-cta__*) — bắt theo dấu hiệu đó.
        // Không loại thì post_content của cả 30 dự án đều dính cùng một đuôi:
        // số điện thoại, địa chỉ, và tên vài dự án khác — vừa trùng lặp vừa
        // khiến Google đọc nhầm chủ đề trang.
        $kill = $xp->query(
            '//script | //style | //noscript | //svg | //template | //form'
            . ' | //*[@aria-hidden="true"]'
            . ' | //*[@role="button"] | //*[@role="navigation"] | //*[@role="tablist"]'
            . ' | //button | //nav'
            . ' | //section[.//*[contains(@class,"pp-related")]]'
            . ' | //section[.//*[contains(@class,"pp-cta")]]'
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
    function sgh_sync_page_content($dry = false, $args = []) {
        $log = []; $updated = 0; $skipped = 0; $failed = 0;

        $args = wp_parse_args($args, [
            // 'page' | 'project' | cả hai
            'post_types' => ['page'],
            // Ghi đè cả nội dung KHÔNG có marker. Chỉ dùng cho việc di trú một
            // lần — mặc định false để không nuốt mất bài người ta tự viết.
            'force'      => false,
        ]);

        $targets = [];   // [WP_Post, ...]

        if (in_array('page', (array) $args['post_types'], true)) {
            $defs = function_exists('sgh_get_pages_definition') ? sgh_get_pages_definition() : [];
            if (!$defs) {
                $log[] = 'Không đọc được bản khai page (sgh_get_pages_definition).';
                $failed++;
            }
            foreach (array_keys($defs) as $slug) {
                $page = get_page_by_path($slug);
                if ($page) $targets[] = $page;
                else { $log[] = "Không thấy page: $slug"; $failed++; }
            }
        }

        if (in_array('project', (array) $args['post_types'], true)) {
            // Dự án: nội dung hiển thị nằm ở single-project/<slug>.php +
            // template-parts/project-pillar/<slug>/. post_content hiện tại là
            // bản CRAWL TỪ SITE CŨ (ảnh trỏ saigonhoreca.com/uploads/2020/...),
            // không khớp thiết kế đang chạy — nên cần 'force' để thay.
            $targets = array_merge($targets, get_posts([
                'post_type'   => 'project',
                'post_status' => 'publish',
                'numberposts' => -1,
                'orderby'     => 'title',
                'order'       => 'ASC',
            ]));
        }

        $marker = sgh_page_content_marker();

        foreach ($targets as $page) {
            $slug = $page->post_name;

            $existing = (string) $page->post_content;
            if ($existing !== '' && strpos($existing, $marker) === false && !$args['force']) {
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

            // ⚠️ post_excerpt cũng phải sinh lại — nó mới là nguồn của
            // og:description / twitter:description (kiểm bằng HTML thật).
            // Thay mỗi post_content là chưa đủ: Facebook/Zalo vẫn hiện mô tả cũ.
            // Trước lần chạy này: 6 dự án dùng CHUNG excerpt của Sheh Fung,
            // 3 dự án excerpt rỗng, the-brix còn văn bản crawl tiếng Anh 2020.
            $update = [
                'ID'           => $page->ID,
                'post_content' => $marker . "\n" . $content,
            ];

            $cur_excerpt = trim((string) $page->post_excerpt);
            $is_generated = $cur_excerpt === ''
                || strpos($existing, $marker) !== false   // vòng chạy trước là của mình
                || $args['force'];
            if ($is_generated) {
                // Lấy đoạn <p> đầu đủ dài — mở bài, không phải nhãn vụn.
                $excerpt = '';
                if (preg_match_all('#<p>(.*?)</p>#s', $content, $ps)) {
                    foreach ($ps[1] as $p) {
                        $t = trim(html_entity_decode(wp_strip_all_tags($p), ENT_QUOTES, 'UTF-8'));
                        if (mb_strlen($t) >= 60) { $excerpt = $t; break; }
                    }
                }
                if ($excerpt !== '') {
                    if (mb_strlen($excerpt) > 300) {
                        $excerpt = rtrim(mb_substr($excerpt, 0, 297), " ,.;:–-") . '…';
                    }
                    $update['post_excerpt'] = $excerpt;
                }
            }

            $ok = wp_update_post($update, true);

            if (is_wp_error($ok)) { $log[] = "Lỗi ghi $slug: " . $ok->get_error_message(); $failed++; continue; }

            $log[] = sprintf('✓ %s — %d ký tự, ~%d từ', $slug, mb_strlen($content), $words);
            $updated++;
        }

        if (!$dry && function_exists('sgh_static_cache_flush')) sgh_static_cache_flush();

        return compact('updated', 'skipped', 'failed', 'log');
    }
}
