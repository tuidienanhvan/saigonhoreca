<?php
if (!defined('ABSPATH')) exit;

/**
 * Trang cấu hình ngôn ngữ & URL.
 *
 * Ngôn ngữ mặc định KHÔNG lưu bằng cờ riêng — nó là ngôn ngữ có `path_prefix`
 * rỗng trên host đó. Lý do: production có HAI mặc định cùng lúc (vi trên .vn,
 * en trên .com), nên mọi biến "mặc định" toàn cục đều sai với một trong hai; và
 * một cờ nằm song song với path_prefix chính là kiểu bug hai-bản-sao-lệch-nhau
 * mà class-core.php đã trả giá một lần. Giao diện vẫn cho chọn bằng radio cho
 * dễ hiểu — khi lưu thì quy đổi thành path_prefix.
 */
class Pi_Multilang_Languages_Admin {

    const SLUG      = 'pi-multilang-languages';
    const CAP       = 'manage_options';
    const ACTION    = 'pi_multilang_save_languages';
    const ACTION_RS = 'pi_multilang_reset_languages';

    /** Đoạn URL đầu không được dùng làm tiền tố ngôn ngữ. */
    private static function reserved_prefixes() {
        return [
            'wp-admin', 'wp-content', 'wp-includes', 'wp-json', 'wp-login', 'wp-cron',
            'xmlrpc', 'feed', 'page', 'comments', 'embed', 'author', 'category', 'tag',
            'search', 'sitemap', 'robots', 'favicon', 'index',
            // slug rewrite của CPT/taxonomy trong theme
            'du-an', 'san-pham', 'danh-muc-du-an', 'danh-muc-san-pham', 'thuong-hieu',
        ];
    }

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu'], 20);
        add_action('admin_post_' . self::ACTION,    [__CLASS__, 'handle_save']);
        add_action('admin_post_' . self::ACTION_RS, [__CLASS__, 'handle_reset']);
    }

    public static function add_menu() {
        add_submenu_page(
            Pi_Multilang_Admin::MENU_SLUG,
            'Ngôn ngữ & URL',
            'Ngôn ngữ & URL',
            self::CAP,
            self::SLUG,
            [__CLASS__, 'render']
        );
    }

    private static function redirect_back($args) {
        wp_safe_redirect(add_query_arg($args, admin_url('admin.php?page=' . self::SLUG)));
        exit;
    }

    /* ------------------------------------------------------------------ *
     * Lưu
     * ------------------------------------------------------------------ */

    public static function handle_save() {
        if (!current_user_can(self::CAP)) wp_die('Không đủ quyền.');
        check_admin_referer(self::ACTION);

        $rows    = isset($_POST['lang']) && is_array($_POST['lang']) ? wp_unslash($_POST['lang']) : [];
        $default = isset($_POST['default_lang']) ? sanitize_text_field(wp_unslash($_POST['default_lang'])) : '';

        $raw = [];
        foreach ($rows as $code => $r) {
            if (!empty($r['_delete'])) continue;
            $code = strtolower(trim((string) $code));
            if ($code === '') continue;
            $raw[$code] = [
                'label'       => isset($r['label'])       ? (string) $r['label']       : '',
                'host'        => isset($r['host'])        ? (string) $r['host']        : '',
                'path_prefix' => isset($r['path_prefix']) ? (string) $r['path_prefix'] : '',
                'wp_locale'   => isset($r['wp_locale'])   ? (string) $r['wp_locale']   : '',
                'hreflang'    => isset($r['hreflang'])    ? (string) $r['hreflang']    : '',
            ];
        }

        // Chuẩn hoá bằng ĐÚNG hàm mà lúc đọc config dùng — không có bộ luật thứ hai.
        $config = Pi_Multilang_Core::normalize_config($raw);
        if (empty($config)) {
            self::redirect_back(['pi_err' => 'Cần ít nhất một ngôn ngữ hợp lệ.']);
        }

        $config = self::apply_default_language($config, $default);

        $err = self::validate($config);
        if ($err !== null) self::redirect_back(['pi_err' => $err]);

        update_option(Pi_Multilang_Core::OPTION_KEY, $config);
        self::after_write();
        self::redirect_back(['pi_saved' => 1]);
    }

    public static function handle_reset() {
        if (!current_user_can(self::CAP)) wp_die('Không đủ quyền.');
        check_admin_referer(self::ACTION_RS);
        delete_option(Pi_Multilang_Core::OPTION_KEY);
        self::after_write();
        self::redirect_back(['pi_reset' => 1]);
    }

    /**
     * Quy đổi lựa chọn "ngôn ngữ mặc định" thành path_prefix, theo từng host.
     *
     * Public vì endpoint REST (Pi_Api_Translations) phải đi qua ĐÚNG phép quy
     * đổi này. Chép lại nó bên phía REST là tạo bộ luật thứ hai — đúng kiểu
     * lỗi hai-bản-sao-lệch-nhau mà class-core.php đã trả giá một lần.
     */
    public static function apply_default_language(array $config, $default) {
        if ($default === '' || !isset($config[$default])) return $config;

        $host = $config[$default]['host'];
        foreach ($config as $code => $c) {
            if ($c['host'] !== $host) continue;
            if ($code === $default) {
                $config[$code]['path_prefix'] = '';
            } elseif (trim($c['path_prefix'], '/') === '') {
                // Ngôn ngữ vừa mất ngôi mặc định phải nhận lại một tiền tố,
                // nếu không host sẽ có hai ngôn ngữ cùng ở gốc.
                $config[$code]['path_prefix'] = $code;
            }
        }
        return $config;
    }

    /** @return string|null thông báo lỗi, hoặc null nếu hợp lệ */
    public static function validate(array $config) {
        $host_now = strtolower((string) wp_parse_url(home_url(), PHP_URL_HOST));
        $host_now = preg_replace('/:\d+$/', '', $host_now);

        $reserved = self::reserved_prefixes();
        $seen     = [];   // host => [prefix => code]
        $roots    = [];   // host => code có tiền tố rỗng

        foreach ($config as $code => $c) {
            $host   = $c['host'];
            $prefix = trim($c['path_prefix'], '/');

            if ($prefix !== '') {
                if (in_array($prefix, $reserved, true)) {
                    return "Tiền tố \"{$prefix}\" trùng đoạn URL dành riêng của WordPress hoặc slug có sẵn của theme.";
                }
                if (get_page_by_path($prefix) instanceof WP_Post) {
                    return "Tiền tố \"{$prefix}\" trùng slug một trang đã có — URL sẽ tranh nhau.";
                }
                if (isset($seen[$host][$prefix])) {
                    return "Tiền tố \"{$prefix}\" bị dùng cho hai ngôn ngữ trên cùng host {$host}.";
                }
                $seen[$host][$prefix] = $code;
            } else {
                if (isset($roots[$host])) {
                    return "Host {$host} có hai ngôn ngữ cùng ở gốc ({$roots[$host]} và {$code}). Mỗi host chỉ được một ngôn ngữ mặc định.";
                }
                $roots[$host] = $code;
            }
        }

        foreach (array_keys($seen) as $host) {
            if (!isset($roots[$host])) {
                return "Host {$host} không có ngôn ngữ mặc định nào (mọi ngôn ngữ đều có tiền tố). Hãy chọn một ngôn ngữ mặc định.";
            }
        }

        // Nếu không có entry nào nêu host hiện tại thì Core sẽ bỏ qua toàn bộ
        // config này (bảo vệ môi trường) — tức là lưu xong không có tác dụng gì.
        // Chặn thẳng còn hơn để người dùng tưởng đã lưu được.
        if (!Pi_Multilang_Core::config_has_host($config, $host_now)) {
            return "Không ngôn ngữ nào trỏ về host hiện tại ({$host_now}). Cấu hình này sẽ bị bỏ qua, nên chưa lưu.";
        }
        return null;
    }

    /** Public: endpoint REST cũng phải xoá đúng bộ cache này sau khi ghi. */
    public static function after_write() {
        Pi_Multilang_Core::flush_config_cache();
        if (function_exists('sgh_static_cache_flush')) sgh_static_cache_flush();
        do_action('sgh_after_flush_cache');
        foreach (['sh_sitemap_xml', 'sh_sitemap_xml_gz'] as $t) {
            delete_transient($t);
        }
    }

    /* ------------------------------------------------------------------ *
     * Hiển thị
     * ------------------------------------------------------------------ */

    public static function render() {
        if (!current_user_can(self::CAP)) wp_die('Không đủ quyền.');

        $config   = Pi_Multilang_Core::get_language_config();
        $host_now = strtolower((string) wp_parse_url(home_url(), PHP_URL_HOST));
        $host_now = preg_replace('/:\d+$/', '', $host_now);
        $stored   = get_option(Pi_Multilang_Core::OPTION_KEY, null);

        echo '<div class="wrap"><h1>Ngôn ngữ &amp; URL</h1>';

        if (isset($_GET['pi_err']))   echo '<div class="notice notice-error"><p>' . esc_html(wp_unslash($_GET['pi_err'])) . '</p></div>';
        if (isset($_GET['pi_saved'])) echo '<div class="notice notice-success"><p>Đã lưu cấu hình ngôn ngữ và xoá cache.</p></div>';
        if (isset($_GET['pi_reset'])) echo '<div class="notice notice-success"><p>Đã xoá cấu hình tuỳ chỉnh, quay về mặc định theo môi trường.</p></div>';

        printf(
            '<p class="description" style="max-width:900px;">Host hiện tại: <code>%s</code>. '
          . 'Ngôn ngữ <strong>mặc định</strong> là ngôn ngữ không có tiền tố — truy cập thẳng <code>%s/</code> sẽ ra ngôn ngữ đó. '
          . 'Các ngôn ngữ còn lại nằm dưới tiền tố, ví dụ <code>%s/en/</code>.</p>',
            esc_html($host_now), esc_html($host_now), esc_html($host_now)
        );

        if (!is_array($stored)) {
            echo '<div class="notice notice-info inline"><p>Đang dùng <strong>cấu hình mặc định theo môi trường</strong> (chưa lưu tuỳ chỉnh nào). '
               . 'Host kết thúc bằng <code>.local</code> thì dùng tiền tố đường dẫn; các host khác giữ sơ đồ hai tên miền của production.</p></div>';
        } elseif (!Pi_Multilang_Core::config_has_host($stored, $host_now)) {
            echo '<div class="notice notice-warning inline"><p>Cấu hình đã lưu <strong>không nêu host hiện tại</strong> nên đang bị bỏ qua — '
               . 'nhiều khả năng nó được tạo ở môi trường khác rồi theo database sang đây. Site đang chạy theo mặc định của môi trường này.</p></div>';
        }

        $locales = array_values(array_unique(array_merge(
            ['vi', 'en_US', 'ja'],
            (array) get_available_languages(),
            class_exists('Pi_Multilang_UI_Strings') ? (array) Pi_Multilang_UI_Strings::available_locales() : []
        )));
        sort($locales);

        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        wp_nonce_field(self::ACTION);
        echo '<input type="hidden" name="action" value="' . esc_attr(self::ACTION) . '">';

        echo '<table class="wp-list-table widefat striped" style="max-width:1100px;"><thead><tr>'
           . '<th style="width:70px;">Mặc định</th><th style="width:80px;">Mã</th><th>Tên hiển thị</th>'
           . '<th>Host</th><th style="width:110px;">Tiền tố URL</th><th style="width:120px;">Locale WP</th>'
           . '<th style="width:90px;">hreflang</th><th style="width:60px;">Xoá</th></tr></thead><tbody>';

        foreach ($config as $code => $c) {
            $is_default = (trim($c['path_prefix'], '/') === '');
            $n = 'lang[' . esc_attr($code) . ']';
            echo '<tr>';
            printf('<td style="text-align:center;"><input type="radio" name="default_lang" value="%s"%s></td>',
                esc_attr($code), checked($is_default, true, false));
            printf('<td><code>%s</code></td>', esc_html($code));
            printf('<td><input type="text" name="%s[label]" value="%s" class="regular-text" style="width:100%%;"></td>',
                $n, esc_attr($c['label']));
            printf('<td><input type="text" name="%s[host]" value="%s" style="width:100%%;"></td>',
                $n, esc_attr($c['host']));
            printf('<td><input type="text" name="%s[path_prefix]" value="%s" style="width:100%%;" placeholder="(mặc định)"></td>',
                $n, esc_attr($c['path_prefix']));

            echo '<td><select name="' . $n . '[wp_locale]" style="width:100%;">';
            foreach ($locales as $loc) {
                printf('<option value="%s"%s>%s</option>', esc_attr($loc), selected($c['wp_locale'], $loc, false), esc_html($loc));
            }
            echo '</select></td>';

            printf('<td><input type="text" name="%s[hreflang]" value="%s" style="width:100%%;"></td>',
                $n, esc_attr($c['hreflang']));
            printf('<td style="text-align:center;"><input type="checkbox" name="%s[_delete]" value="1"></td>', $n);
            echo '</tr>';
        }

        // Một dòng trống để thêm ngôn ngữ mới.
        echo '<tr><td></td>'
           . '<td><input type="text" name="new_code" value="" placeholder="vd: ko" style="width:100%;" disabled></td>'
           . '<td colspan="6"><span class="description">Thêm ngôn ngữ mới: hiện chưa hỗ trợ trực tiếp ở đây — '
           . 'dùng filter <code>pi_multilang_language_config</code> hoặc bổ sung vào mặc định trong '
           . '<code>Pi_Multilang_Core::default_config_for_host()</code>.</span></td></tr>';

        echo '</tbody></table>';
        echo '<p><button class="button button-primary">Lưu cấu hình</button></p>';
        echo '</form>';

        echo '<hr><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" '
           . 'onsubmit="return confirm(\'Xoá cấu hình tuỳ chỉnh và quay về mặc định theo môi trường?\');">';
        wp_nonce_field(self::ACTION_RS);
        echo '<input type="hidden" name="action" value="' . esc_attr(self::ACTION_RS) . '">';
        echo '<button class="button">Xoá tuỳ chỉnh, dùng mặc định</button> '
           . '<span class="description">Không xoá bản dịch — chỉ xoá cấu hình URL.</span>';
        echo '</form>';

        echo '</div>';
    }
}
