<?php
if (!defined('ABSPATH')) exit;

/**
 * Chiều NHẬN request: đổi alias theo ngôn ngữ về bản ghi gốc TRƯỚC khi WP_Query chạy.
 *
 * Vì sao hook `request` chứ không phải chỗ khác:
 *   - `parse_request` chạy sau khi WP đã khớp rewrite rule; filter `request` là
 *     điểm cuối cùng còn sửa được query var trước khi WP_Query dựng câu SQL.
 *   - `pre_get_posts` là QUÁ MUỘN cho việc này: lúc đó `name`/`pagename` đã
 *     quyết định số phận, và `Pi_Multilang_Frontend::filter_query` đang chiếm
 *     prio 10 ở đó cho việc khác (lọc theo pi_language).
 *   - KHÔNG dùng `add_rewrite_rule`: sinh hàng chục rule cho mỗi ngôn ngữ, đòi
 *     `flush_rewrite_rules()` mỗi lần đổi slug, và phồng option `rewrite_rules`.
 *     Đổi query var là O(1) và không cần flush — cùng triết lý với cách
 *     class-core.php dùng filter `home_url` thay vì rewrite rule cho tiền tố.
 *
 * Thứ tự bắt buộc trong filter_request(): `pagename` TRƯỚC `name`. Với cấu trúc
 * permalink /%postname%/, WP đặt CẢ HAI cho một URL một đoạn; xử lý `name` trước
 * sẽ cướp mất trang.
 */
class Pi_Multilang_Router {

    /** Đích 301 chờ phát ở template_redirect. Không redirect ngay trong filter. */
    private static $pending_301 = null;

    public static function init() {
        // prio 5: sớm hơn mọi thứ của theme, nhưng vẫn sau khi WP dựng xong
        // query var từ rewrite rule.
        add_filter('request', [__CLASS__, 'filter_request'], 5);

        // prio -30: TRƯỚC sgh_pillar_route_serve (prio -10) và trước
        // Pi_Api_SeoRedirects::handle_request (prio 1). Slug cũ phải được trả
        // lời trước khi bất kỳ lớp nào khác kịp dựng trang 404.
        add_action('template_redirect', [__CLASS__, 'maybe_redirect_legacy_alias'], -30);
    }

    /* ================================================================== *
     * Chiều vào
     * ================================================================== */

    public static function filter_request($query_vars) {
        if (!class_exists('Pi_Multilang_Slugs')) return $query_vars;
        if (!Pi_Multilang_Slugs::flag('slug_aliases')) return $query_vars;
        if (!class_exists('Pi_Multilang_Core') || !Pi_Multilang_Core::is_routing()) return $query_vars;
        if (!defined('PI_CURRENT_LANGUAGE')) return $query_vars;

        $lang  = PI_CURRENT_LANGUAGE;
        $start = microtime(true);

        // Đoạn cấu trúc (du-an, danh-muc-san-pham...) phải xử lý TRƯỚC TIÊN:
        // rewrite rule của WP chỉ biết slug tiếng Việt, nên `/en/product-category/x/`
        // không khớp rule nào và rơi thành pagename nhiều đoạn. Dịch rồi khớp lại.
        if (Pi_Multilang_Slugs::flag('in_resolve_route')) {
            $rebuilt = self::rematch_route_alias($lang, $query_vars);
            if ($rebuilt !== null) $query_vars = $rebuilt;
        }

        // pagename TRƯỚC name — xem docblock lớp.
        if (!empty($query_vars['pagename'])) {
            $query_vars = self::resolve_pagename($query_vars, $lang);
        }
        if (!empty($query_vars['name'])) {
            $query_vars = self::resolve_name($query_vars, $lang);
        }

        // Chẩn đoán bật theo yêu cầu: ?pi_slug_debug=1. Không phụ thuộc WP_DEBUG
        // vì trên máy này WP_DEBUG tắt, mà lỗi tầng slug thì im lặng — trang vẫn
        // 200, chỉ là sai trang. Header chỉ lộ query var, không lộ dữ liệu.
        if (!headers_sent()
            && ((defined('WP_DEBUG') && WP_DEBUG) || isset($_GET['pi_slug_debug']))) {
            header('X-Pi-Slug-Ms: ' . round((microtime(true) - $start) * 1000, 2));
            header('X-Pi-Slug-Req: ' . (isset($GLOBALS['wp']->request) ? $GLOBALS['wp']->request : '-'));
            header('X-Pi-Slug-Vars: ' . substr(http_build_query($query_vars), 0, 200));
        }
        return $query_vars;
    }

    /**
     * Đoạn đầu là alias của một đoạn cấu trúc -> dịch về slug đăng ký rồi khớp
     * lại rewrite rule trên path đã dịch.
     *
     * Vì sao phải tự khớp lại chứ không gọi lại WP::parse_request(): gọi lại là
     * đệ quy. Ở đây lặp đúng bộ rule mà WP đang dùng ($wp_rewrite), theo cùng
     * logic core dùng ở class-wp.php — API này ổn định 15 năm.
     *
     * Chỉ chạy khi đoạn đầu THỰC SỰ là alias route, tức dưới 1% request, nên
     * vòng lặp qua ~157 rule không nằm trên đường nóng.
     *
     * @return array|null query vars mới, hoặc null nếu không phải ca này.
     */
    private static function rematch_route_alias($lang, array $incoming) {
        global $wp, $wp_rewrite;
        if (!isset($wp->request) || $wp->request === '' || !$wp_rewrite) return null;

        $path = trim((string) $wp->request, '/');
        $segs = explode('/', $path);
        if (!$segs) return null;

        $canonical = Pi_Multilang_Slugs::resolve_in($segs[0], $lang, 'route');
        if (!is_string($canonical) || $canonical === '' || $canonical === $segs[0]) return null;

        $segs[0] = $canonical;
        $translated = implode('/', $segs);

        $rules = $wp_rewrite->wp_rewrite_rules();
        if (!is_array($rules)) return null;

        foreach ($rules as $match => $query) {
            if (!preg_match("#^{$match}#", $translated, $matches)) continue;

            // Thay $matches[n] trong chuỗi query của rule, y như core làm.
            //
            // KHÔNG đòi dấu '?' đứng trước: chuỗi query của rule có dạng
            // "index.php?pagename=$matches[1]" — dấu '?' nằm sau index.php chứ
            // không dính vào $matches. Bản đầu của hàm này khớp '\?\$matches'
            // nên không thay được gì, để nguyên chuỗi "$matches[1]" rồi parse_str
            // ra query var rác, và /en/contact/ render ra TRANG BLOG.
            $query = preg_replace_callback(
                '#\$matches\[(\d+)\]#',
                static function ($m) use ($matches) {
                    $i = (int) $m[1];
                    return isset($matches[$i]) ? urlencode($matches[$i]) : '';
                },
                $query
            );

            // Cắt phần "index.php?" ở đầu trước khi parse.
            $qpos = strpos($query, '?');
            $qs   = ($qpos !== false) ? substr($query, $qpos + 1) : $query;

            $vars = [];
            parse_str($qs, $vars);
            $vars = array_filter($vars, static fn($v) => $v !== '' && $v !== null);
            if (!$vars) continue;

            // Ánh xạ query var của CPT sang post_type + name.
            //
            // BẮT BUỘC, không phải tối ưu: WP làm việc này trong parse_request
            // TRƯỚC khi chạy filter `request` (class-wp.php, vòng lặp
            // $post_type_query_vars). Biến ta chèn vào SAU đó nên không ai dịch
            // hộ — WP_Query gặp `project=casa-maria` mà không có post_type thì
            // coi như query rỗng và trả TRANG BLOG. Đã đo đúng ca này.
            foreach ($GLOBALS['wp_post_types'] as $pt_name => $pt) {
                if (empty($pt->query_var) || !isset($vars[$pt->query_var])) continue;
                $vars['post_type'] = $pt_name;
                $vars['name']      = $vars[$pt->query_var];
                break;
            }

            // Giữ lại query var phụ (paged, s, feed...) NHƯNG phải vứt bỏ mọi
            // biến định danh đường dẫn cũ. Mang `pagename` cũ theo là WP ưu tiên
            // nó và trả 404 — đã dính đúng lỗi này khi đo /en/product-category/.
            $drop = ['pagename', 'name', 'attachment', 'error', 'page_id', 'p', 'post_type'];
            $keep = array_diff_key($incoming, array_flip($drop));
            return array_merge($keep, $vars);
        }
        return null;
    }

    /**
     * Trang, có phân cấp cha/con.
     *
     * Không dịch chuỗi `pagename` rồi trả lại cho WP, mà phân giải thẳng ra ID
     * và đặt `page_id`. Lý do: `page_id` là đích xác định — WP bỏ qua hoàn toàn
     * `get_page_by_path()`, nên không còn ca "hai trang cùng slug khác cha" và
     * không có đường nào sinh ra vòng lặp chuyển hướng.
     */
    private static function resolve_pagename($query_vars, $lang) {
        $segments = array_values(array_filter(explode('/', trim((string) $query_vars['pagename'], '/')), 'strlen'));
        if (!$segments) return $query_vars;

        $parent = 0;
        $id     = 0;
        foreach ($segments as $seg) {
            $id = self::resolve_segment_to_id($seg, $lang, 'page', $parent);
            if (!$id) return $query_vars;   // không dịch được -> để WP tự xử, có thể 404
            $parent = $id;
        }

        unset($query_vars['pagename'], $query_vars['name']);
        $query_vars['page_id'] = $id;
        return $query_vars;
    }

    /** Bài và CPT một đoạn. */
    private static function resolve_name($query_vars, $lang) {
        $alias = (string) $query_vars['name'];

        // post_type có thể đã được rewrite rule đặt (vd /du-an/<slug>/).
        $post_type = isset($query_vars['post_type']) ? $query_vars['post_type'] : '';

        // Tầng đuôi dài: product KHÔNG nằm trong chỉ mục (586 bài, xem
        // Pi_Multilang_Slugs::get_index). Chỉ tra CSDL khi ngữ cảnh đã cho biết.
        if ($post_type === 'product') {
            $id = Pi_Multilang_Slugs::resolve_alias_db($alias, $lang, 'product');
            if ($id) {
                unset($query_vars['name']);
                self::drop_cpt_query_vars($query_vars);
                $query_vars['p']         = $id;
                $query_vars['post_type'] = 'product';
            }
            return $query_vars;
        }

        $viaRoute = Pi_Multilang_Slugs::resolve_in($alias, $lang, 'route');
        if (is_string($viaRoute) && $viaRoute !== '') $alias = $viaRoute;

        $target = Pi_Multilang_Slugs::resolve_in($alias, $lang, 'post');
        $post   = is_int($target) ? get_post($target) : get_page_by_path($alias, OBJECT, 'page');
        if (!$post) return $query_vars;

        unset($query_vars['name'], $query_vars['pagename']);
        self::drop_cpt_query_vars($query_vars);
        if ($post->post_type === 'page') {
            $query_vars['page_id'] = $post->ID;
        } else {
            $query_vars['p'] = $post->ID;
            // Thiếu post_type thì `p` với CPT sẽ ra 404.
            $query_vars['post_type'] = $post->post_type;
        }
        return $query_vars;
    }

    /**
     * Vứt query var RIÊNG của CPT (`project=`, `product=`...) sau khi đã phân
     * giải alias thành `p` + `post_type`.
     *
     * Bắt buộc, và im lặng nếu quên: WP_Query ưu tiên `project=<slug>` hơn
     * `p=<id>`, nên bỏ sót nó thì `/en/projects/casa-maria-bistro/` trả 404
     * dù query var đã có đúng `p=714`. Đo được bằng header X-Pi-Slug-Vars.
     *
     * Duyệt $wp_post_types chứ không liệt kê tên cứng — thêm CPT mới là tự
     * chạy đúng, không phải nhớ quay lại sửa chỗ này.
     */
    private static function drop_cpt_query_vars(array &$query_vars) {
        if (empty($GLOBALS['wp_post_types'])) return;
        foreach ($GLOBALS['wp_post_types'] as $pt) {
            if (empty($pt->query_var)) continue;
            unset($query_vars[$pt->query_var]);
        }
    }

    /**
     * Một đoạn -> ID, thử alias trước rồi mới tới slug gốc.
     *
     * Chấp nhận CẢ HAI là có chủ đích: /en/lien-he/ (slug gốc) và /en/contact/
     * (alias) đều trả 200. Nhờ vậy không bao giờ tồn tại cặp URL cùng redirect
     * về nhau — điều kiện đủ để không có vòng lặp 301.
     */
    private static function resolve_segment_to_id($seg, $lang, $post_type, $parent) {
        // Alias có thể là hàng `route` (đoạn cấu trúc) trỏ tới một slug gốc —
        // vd 'contact' -> 'lien-he'. Quy về slug gốc trước rồi mới phân giải
        // như bình thường, nhờ vậy alias route và alias bài dùng chung một
        // đường, không phải hai nhánh song song dễ lệch nhau.
        $viaRoute = Pi_Multilang_Slugs::resolve_in($seg, $lang, 'route');
        if (is_string($viaRoute) && $viaRoute !== '') $seg = $viaRoute;

        $target = Pi_Multilang_Slugs::resolve_in($seg, $lang, 'post');
        if (is_int($target)) {
            $post = get_post($target);
            if ($post && (int) $post->post_parent === (int) $parent) return (int) $post->ID;
        }

        $page = get_page_by_path($seg, OBJECT, $post_type);
        if ($page && (int) $page->post_parent === (int) $parent) return (int) $page->ID;

        return 0;
    }

    /* ================================================================== *
     * 301 slug cũ
     * ================================================================== */

    /**
     * Slug đã về hưu -> URL hiện hành, ĐÚNG MỘT bước.
     *
     * Đích lấy từ chỉ mục `old` vốn đã giải chuỗi sẵn lúc dựng (a→b→c thì
     * old[a] lưu thẳng c), nên không bao giờ 301 nối 301 — theo cấu trúc, không
     * phải theo kỷ luật.
     *
     * URL đích dựng bằng get_language_base_url() chứ KHÔNG qua home_url():
     * home_url() đã bị filter gắn tiền tố ngôn ngữ hiện tại, đi qua nó là tự
     * chuốc thêm một vòng dịch nữa.
     */
    public static function maybe_redirect_legacy_alias() {
        if (!class_exists('Pi_Multilang_Slugs')) return;
        if (!Pi_Multilang_Slugs::flag('legacy_301')) return;
        if (!Pi_Multilang_Core::is_routing() || !defined('PI_CURRENT_LANGUAGE')) return;
        if (is_admin() || wp_doing_ajax()) return;
        if (!is_404()) return;   // chỉ cứu URL đã chết, không đụng trang đang sống

        $uri  = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        $path = (string) strtok($uri, '?');
        $qs   = (strpos($uri, '?') !== false) ? substr($uri, strpos($uri, '?')) : '';

        $lang   = PI_CURRENT_LANGUAGE;
        $prefix = Pi_Multilang_Core::prefix_for($lang);
        $rel    = trim($path, '/');
        if ($prefix !== '' && strpos($rel, $prefix . '/') === 0) $rel = substr($rel, strlen($prefix) + 1);
        if ($prefix !== '' && $rel === $prefix) $rel = '';
        if ($rel === '') return;

        $segments = explode('/', $rel);
        $changed  = false;
        foreach ($segments as $i => $seg) {
            foreach (['post', 'route'] as $type) {
                $old = Pi_Multilang_Slugs::resolve_old($seg, $lang, $type);
                if ($old && !empty($old['to'])) { $segments[$i] = $old['to']; $changed = true; break; }
            }
        }
        if (!$changed) {
            self::maybe_redirect_prefixed_root_alias($lang, $prefix, $rel, $qs);
            return;
        }

        $target = rtrim(Pi_Multilang_Core::get_language_base_url($lang), '/')
                . '/' . implode('/', $segments)
                . (substr($path, -1) === '/' ? '/' : '') . $qs;

        if (!headers_sent()) header('X-Pi-Redirect-Source: alias');
        wp_redirect($target, 301);
        exit;
    }

    /**
     * Cầu nối cho `pillar-routes.php` (theme, prio -10) — nó 301 alias gốc
     * `/casa-maria/` sang `/du-an/casa-maria/`, nhưng regex của nó đọc thẳng
     * REQUEST_URI nên `/en/casa-maria/` có HAI đoạn, không khớp, và chết 404.
     * Đo được: `/casa-maria/` 301 một hop, `/en/casa-maria/` 404.
     *
     * Không sửa engine, cũng KHÔNG giả REQUEST_URI rồi gọi lại hàm của theme:
     * hàm đó dựng canonical bằng `home_url()` (đã mang tiền tố) rồi so với URI
     * đã bị cắt tiền tố -> lệch nhau -> 301 vòng vô tận. Ở đây tự dựng đích từ
     * permalink, vốn đã qua filter ngôn ngữ của chính module này.
     */
    private static function maybe_redirect_prefixed_root_alias($lang, $prefix, $rel, $qs) {
        if ($prefix === '') return;               // ngôn ngữ mặc định: để theme lo
        if (!Pi_Multilang_Slugs::flag('root_alias_301')) return;
        if (strpos($rel, '/') !== false) return;  // chỉ alias MỘT đoạn
        if (!preg_match('#^[a-z0-9\-]+$#i', $rel)) return;

        $found = get_posts([
            'name'             => strtolower($rel),
            'post_type'        => 'project',
            'post_status'      => 'publish',
            'numberposts'      => 1,
            'no_found_rows'    => true,
            'suppress_filters' => true,
        ]);
        if (empty($found)) return;

        $target = get_permalink($found[0]);
        if (!$target) return;
        $target_path = (string) parse_url($target, PHP_URL_PATH);
        $here        = (string) strtok((string) $_SERVER['REQUEST_URI'], '?');
        if (untrailingslashit($target_path) === untrailingslashit($here)) return;  // chặn vòng lặp

        if (!headers_sent()) header('X-Pi-Redirect-Source: root-alias');
        wp_redirect($target . $qs, 301);
        exit;
    }
}
