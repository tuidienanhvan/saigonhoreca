<?php
if (!defined('ABSPATH')) exit;

class Pi_Multilang_Frontend {
    public static function init() {
        add_action('pre_get_posts', [__CLASS__, 'filter_query']);
        add_shortcode('pi_lang_switcher', [__CLASS__, 'render_switcher']);
        add_filter('post_link', [__CLASS__, 'rewrite_url_domain'], 10, 2);
        add_filter('page_link', [__CLASS__, 'rewrite_url_domain'], 10, 2);
        add_filter('post_type_link', [__CLASS__, 'rewrite_url_domain'], 10, 2);

        // Liên kết KHÔNG gắn với một bài cụ thể: archive của CPT và trang
        // taxonomy. Chúng đi qua home_url() nên đã có tiền tố đúng, nhưng đoạn
        // cấu trúc (du-an, danh-muc-san-pham) vẫn là tiếng Việt — phải dịch
        // riêng vì không có $post để suy ngôn ngữ.
        add_filter('post_type_archive_link', [__CLASS__, 'rewrite_url_current_lang'], 10);
        add_filter('term_link', [__CLASS__, 'rewrite_url_current_lang'], 10);
    }

    /**
     * Lọc truy vấn chính theo ngôn ngữ hiện tại.
     *
     * Quy tắc: nhận bài gắn ĐÚNG ngôn ngữ hiện tại, HOẶC bài chưa gắn thẻ nào.
     * Chỉ loại bài đã gắn thẻ một ngôn ngữ KHÁC.
     *
     * Bản cũ hardcode 'vi' là ngôn ngữ duy nhất được thấy bài chưa gắn thẻ, còn
     * mọi ngôn ngữ khác đòi thẻ khớp tuyệt đối. Hệ quả đo được: /en/lien-he/
     * trả 404 dù trang tồn tại, vì trang đó chưa gắn pi_language. Với site dịch
     * giao diện bằng gettext thì phần lớn trang KHÔNG có bản sao riêng cho từng
     * ngôn ngữ — chúng dùng chung một bài, chỉ đổi chuỗi giao diện. Bắt buộc
     * gắn thẻ nghĩa là mọi trang 404 cho tới khi được nhân bản, tức là không
     * ngôn ngữ nào ngoài mặc định dùng được.
     *
     * Cách này cũng bỏ luôn 'vi' hardcode: ngôn ngữ nào cũng thấy nội dung
     * chung, còn cặp bài đã gắn thẻ vẫn tách nhau đúng như trước.
     */
    public static function filter_query($query) {
        if (is_admin() || !$query->is_main_query()) return;
        if (!defined('PI_CURRENT_LANGUAGE')) return;

        $meta_query = $query->get('meta_query');
        if (!is_array($meta_query)) $meta_query = [];

        $mq = [
            'relation' => 'OR',
            ['key' => 'pi_language', 'value' => PI_CURRENT_LANGUAGE, 'compare' => '='],
            ['key' => 'pi_language', 'compare' => 'NOT EXISTS'],
        ];

        if (empty($meta_query)) {
            $meta_query = $mq;
        } else {
            $meta_query = [
                'relation' => 'AND',
                $meta_query,
                $mq
            ];
        }

        $query->set('meta_query', $meta_query);
    }

    /**
     * Rewrites a permalink to the correct host/path prefix for the post's
     * OWN language. Delegates entirely to Pi_Multilang_Core — previously
     * this method had its own hardcoded en.saigonhoreca.* string-replace
     * logic, a SEPARATE copy of the domain scheme from class-core.php's,
     * which is exactly how it drifted out of sync across 2 rework rounds
     * (class-core.php got fixed to saigonhoreca.com/.vn, this method didn't,
     * because nothing forced them to share one source of truth). Now there
     * is only one place that knows about hosts/path prefixes.
     */
    public static function rewrite_url_domain($url, $post) {
        if (is_int($post)) $post = get_post($post);
        if (!$post) return $url;

        // Bài ĐÃ gắn thẻ ngôn ngữ thì permalink của nó thuộc về ngôn ngữ đó —
        // một bài tiếng Việt luôn trỏ về URL tiếng Việt, kể cả khi đang xem
        // trang tiếng Anh (đó mới là canonical đúng của nó).
        //
        // Bài CHƯA gắn thẻ là nội dung dùng chung cho mọi ngôn ngữ (xem
        // filter_query), nên link của nó phải đi theo ngôn ngữ người đang xem.
        // Bản cũ mặc định 'vi' cứng, làm mọi link nội dung chung tụt về tiếng
        // Việt ngay giữa trang /en/ — đo được: link tới trang tư vấn quầy bar
        // là link duy nhất mất tiền tố trên /en/lien-he/.
        $lang = get_post_meta($post->ID, 'pi_language', true);
        if ($lang === '' || $lang === false || $lang === null) {
            $lang = defined('PI_CURRENT_LANGUAGE') ? PI_CURRENT_LANGUAGE : $lang;
        }
        if (!$lang) return $url;

        // Bật dịch slug ĐÚNG ở chỗ này: WP đã dựng permalink từ post_name gốc,
        // ta chỉ đổi đoạn slug sang alias của ngôn ngữ. Đây là điểm DUY NHẤT
        // trong toàn hệ bật cờ đó — xem docblock rewrite_url_for_language().
        return Pi_Multilang_Core::rewrite_url_for_language($url, $lang, true, true);
    }

    /**
     * Dịch một URL sang ngôn ngữ ĐANG XEM. Dùng cho liên kết không gắn bài
     * (archive, taxonomy) — nơi không có post meta để suy ngôn ngữ.
     */
    public static function rewrite_url_current_lang($url) {
        if (!defined('PI_CURRENT_LANGUAGE')) return $url;
        if (is_admin() || (defined('DOING_CRON') && DOING_CRON) || (defined('WP_CLI') && WP_CLI)) return $url;
        return Pi_Multilang_Core::rewrite_url_for_language($url, PI_CURRENT_LANGUAGE, true, true);
    }

    /**
     * URL của TRANG ĐANG XEM trong ngôn ngữ $lang. Dùng chung cho bộ chuyển
     * ngôn ngữ và thẻ hreflang — một bản cài đặt để hai nơi không thể lệch nhau
     * (đúng kiểu bug mà class-core.php đã trả giá một lần).
     *
     * @return string|null null = ngôn ngữ đó không có phiên bản của trang này.
     */
    public static function url_in_language($lang) {
        $config = Pi_Multilang_Core::get_language_config();
        if (empty($config[$lang])) return null;

        if (is_singular()) {
            $post = get_queried_object();
            if ($post instanceof WP_Post) {
                $own = get_post_meta($post->ID, 'pi_language', true);

                // Bài đã gắn thẻ: chỉ có URL riêng nếu tồn tại bản dịch cùng nhóm.
                if ($own !== '' && $own !== false && $own !== null) {
                    if ($own === $lang) return get_permalink($post->ID);

                    $group = get_post_meta($post->ID, 'pi_translation_group', true);
                    if ($group) {
                        $siblings = get_posts([
                            'post_type'      => get_post_type($post),
                            'post_status'    => 'publish',
                            'posts_per_page' => 1,
                            'meta_query'     => [
                                ['key' => 'pi_translation_group', 'value' => $group],
                                ['key' => 'pi_language',          'value' => $lang],
                            ],
                        ]);
                        if (!empty($siblings)) return get_permalink($siblings[0]->ID);
                    }
                    return null;   // không có bản dịch -> không bịa ra URL
                }
                // Bài chưa gắn thẻ = nội dung dùng chung, có ở mọi ngôn ngữ.
            }
        }

        // Mọi trang khác (trang chủ, lưu trữ, taxonomy, tìm kiếm): cùng đường
        // dẫn, đổi tiền tố. rewrite_url_for_language() idempotent nên URI đang
        // có sẵn tiền tố cũng không bị chồng thêm.
        $scheme  = is_ssl() ? 'https' : 'http';
        $host    = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $uri     = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        if ($host === '') return Pi_Multilang_Core::get_language_base_url($lang);

        // translate = true: URI hiện tại đang mang alias của ngôn ngữ ĐANG XEM
        // (vd /en/contact/). Không dịch thì thẻ hreflang cho vi/ja sẽ trỏ tới
        // /contact/ và /ja/contact/ — cả hai đều 404, mà hreflang trỏ vào 404 là
        // tín hiệu hỏng, công cụ tìm kiếm bỏ qua cả cụm. Đã đo đúng ca này.
        return Pi_Multilang_Core::rewrite_url_for_language($scheme . '://' . $host . $uri, $lang, true, true);
    }

    /**
     * Bộ chuyển ngôn ngữ, dựng từ cấu hình.
     *
     * Bản cũ chỉ hiện trên bài singular ĐÃ có nhóm dịch, nên trang chủ, lưu trữ
     * và mọi trang chưa nhân bản đều không có cách đổi ngôn ngữ — tức là gần
     * như không dùng được. Bản này chạy trên mọi loại trang và liệt kê đúng
     * những ngôn ngữ có phiên bản của trang đang xem.
     */
    public static function render_switcher($atts = []) {
        $config = Pi_Multilang_Core::get_language_config();
        if (count($config) < 2) return '';

        $current = defined('PI_CURRENT_LANGUAGE') ? PI_CURRENT_LANGUAGE : null;
        $host    = isset($_SERVER['HTTP_HOST']) ? strtolower((string) $_SERVER['HTTP_HOST']) : '';

        $items = '';
        foreach ($config as $code => $c) {
            // Chỉ hiện ngôn ngữ nằm trên host đang xem — trên local thì cả 3
            // cùng host, trên production thì không chào mời sang domain khác
            // giữa chừng.
            if ($host !== '' && isset($c['host']) && $c['host'] !== $host) continue;

            $url = self::url_in_language($code);
            if ($url === null) continue;

            $is_active = ($code === $current);
            $items .= sprintf(
                '<a href="%s" class="pi-lang-link pi-lang-%s%s" hreflang="%s"%s>%s</a>',
                esc_url($url),
                esc_attr($code),
                $is_active ? ' is-active' : '',
                esc_attr($c['hreflang']),
                $is_active ? ' aria-current="true"' : '',
                esc_html($c['label'])
            );
        }
        if ($items === '') return '';

        return '<nav class="pi-lang-switcher" aria-label="Ngôn ngữ">' . $items . '</nav>';
    }
}

/**
 * Template tag để theme gọi trực tiếp: <?php pi_multilang_switcher(); ?>
 * Shortcode [pi_lang_switcher] vẫn dùng chung một renderer.
 */
if (!function_exists('pi_multilang_switcher')) {
    function pi_multilang_switcher($echo = true) {
        $html = Pi_Multilang_Frontend::render_switcher();
        if ($echo) echo $html;
        return $html;
    }
}
