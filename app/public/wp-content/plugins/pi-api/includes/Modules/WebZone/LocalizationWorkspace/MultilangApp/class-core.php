<?php
if (!defined('ABSPATH')) exit;

/**
 * Nguồn chân lý duy nhất cho ánh xạ ngôn ngữ <-> URL.
 *
 * Hai chế độ, CÙNG một cấu hình:
 *   - Theo tên miền  (production): saigonhoreca.vn = vi, saigonhoreca.com = en
 *   - Theo tiền tố   (local):      saigonhoreca.local = vi, /en/ = en, /ja/ = ja
 * Mỗi ngôn ngữ là một cặp (host, path_prefix). Tiền tố rỗng nghĩa là ngôn ngữ
 * đó NẰM Ở GỐC của host — tức là ngôn ngữ mặc định của host đó. Không có cờ
 * `is_default` riêng: production có HAI mặc định cùng lúc (vi trên .vn, en trên
 * .com) nên mọi biến "mặc định" toàn cục đều sai với một trong hai. Bất biến:
 * mỗi host có đúng MỘT ngôn ngữ tiền tố rỗng.
 *
 * Vì sao KHÔNG dùng rewrite rule cho tiền tố:
 * WP::parse_request() (wp-includes/class-wp.php:177-201) lấy path từ home_url(),
 * dựng regex |^en|i rồi tự cắt khỏi REQUEST_URI — đó là cơ chế WordPress dùng
 * cho cài đặt trong thư mục con. Chỉ cần filter home_url gắn tiền tố là WP tự
 * định tuyến. Không cần add_rewrite_rule, không cần flush_rewrite_rules, và
 * REQUEST_URI giữ nguyên sự thật cho mọi thứ khác đọc nó.
 *
 * Vì sao KHÔNG cắt tiền tố khỏi $_SERVER['REQUEST_URI']:
 * inc/engine/pillar-routes.php:146-151 dựng $request_url từ REQUEST_URI rồi so
 * với $canonical_url từ home_url(). Cắt REQUEST_URI mà vẫn gắn tiền tố vào
 * home_url thì hai vế lệch nhau vĩnh viễn -> 301 vô hạn. File đó không được sửa.
 *
 * PHỤ THUỘC THỨ TỰ NẠP: init() chạy ở file scope của plugin (pi-multilang.php),
 * tức trước functions.php của theme. Đừng chuyển sang hook `plugins_loaded` —
 * class-legacy-bridge.php dựa vào việc định nghĩa hàm sgh_* TRƯỚC theme.
 */
class Pi_Multilang_Core {

    const OPTION_KEY = 'pi_multilang_languages';

    /** @var array|null Config đã chuẩn hoá, memoize theo request. */
    private static $config_cache = null;
    /** @var string|null Host hiện tại, memoize — chống đệ quy qua home_url(). */
    private static $host_cache = null;
    /** @var bool Định tuyến theo tiền tố có áp dụng cho request NÀY không. */
    private static $routing = false;
    /** @var string|null Path+query cần 301 về, xử lý ở `init` khi đã có pluggable. */
    private static $pending_redirect = null;

    public static function init() {
        if (defined('PI_CURRENT_LANGUAGE')) return;   // idempotent

        // Chốt host TRƯỚC mọi thứ. current_host() có nhánh dự phòng gọi
        // home_url(); một khi filter home_url tồn tại và nó đọc config (vốn
        // gọi current_host()) thì nhánh đó thành đệ quy vô hạn dưới CLI/cron.
        self::$host_cache = self::detect_host();

        $uri  = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        $path = (string) strtok($uri, '?');

        self::$routing = !self::is_excluded_context($path);

        // Ngôn ngữ phải được chốt MỘT lần, trước mọi filter, vì nó là hằng số.
        self::resolve_current_language(self::$routing);

        if (self::$routing) {
            self::$pending_redirect = self::default_prefix_redirect_target($path, $uri);
        }

        // wp_safe_redirect() nằm trong pluggable.php, nạp ở wp-settings.php:603
        // — tức SAU khi plugin được include. Không gọi được ở đây.
        add_action('init', [__CLASS__, 'maybe_redirect_default_prefix'], 0);
        add_action('init', [__CLASS__, 'maybe_detect_japanese_visitor'], 5);

        add_filter('locale', [__CLASS__, 'filter_locale'], 999);
        add_filter('home_url', [__CLASS__, 'filter_home_url'], 30, 2);

        // Giữ redirect_canonical im lặng trên các host ta tự cấu hình.
        // CẢNH BÁO cho người sửa sau: đây KHÔNG chỉ là fix đa tên miền. Khi
        // ngôn ngữ có tiền tố, redirect_canonical() dựng $requested_url từ
        // REQUEST_URI (có tiền tố) và so với permalink của bài (theo ngôn ngữ
        // riêng của bài) — lệch nhau là nó 301, và có thể lặp. Đừng bỏ filter này.
        add_filter('redirect_canonical', [__CLASS__, 'skip_canonical_redirect_for_known_host'], 10, 2);

        // Asset + REST: CHỈ đổi host, KHÔNG gắn tiền tố ngôn ngữ. Trước đây
        // dùng chung đường với permalink nên sinh ra
        // https://saigonhoreca.local/en/wp-json/... và
        // .../en/wp-content/themes/... — cả hai đều 404. Đo được ở baseline.
        add_filter('content_url',    [__CLASS__, 'rewrite_asset_url']);
        add_filter('theme_root_uri', [__CLASS__, 'rewrite_asset_url']);
        add_filter('plugins_url',    [__CLASS__, 'rewrite_asset_url']);
        add_filter('rest_url',       [__CLASS__, 'rewrite_rest_url']);
    }

    /* ================================================================== *
     * Ngữ cảnh
     * ================================================================== */

    /**
     * Những ngữ cảnh KHÔNG được áp dụng tiền tố. Phải nhận diện được ngay ở
     * thời điểm plugin nạp — sớm hơn nhiều so với lúc WP dựng xong query.
     */
    private static function is_excluded_context($path) {
        if ($path === '') return true;
        if (is_admin()) return true;                                   // wp-admin/ + admin-ajax.php
        if (defined('DOING_CRON') && DOING_CRON) return true;
        if (defined('WP_CLI') && WP_CLI) return true;
        if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) return true;
        if (defined('WP_INSTALLING') && WP_INSTALLING) return true;
        if (isset($GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php') return true;

        // REST: hằng REST_REQUEST chỉ được định nghĩa ở parse_request, quá muộn.
        if (isset($_GET['rest_route'])) return true;
        if (preg_match('#^/(index\.php/)?wp-json(/|$)#', $path)) return true;

        // Hạ tầng: không bao giờ viết lại thành đường dẫn asset hợp lệ.
        if (preg_match('#^/(wp-content|wp-includes|wp-admin|wp-cron\.php|xmlrpc\.php|wp-comments-post\.php)(/|$)#', $path)) {
            return true;
        }
        return false;
    }

    /** Định tuyến tiền tố có đang bật cho request này không. */
    public static function is_routing() {
        return self::$routing;
    }

    /* ================================================================== *
     * Cấu hình
     * ================================================================== */

    /**
     * Mặc định theo host, KHÔNG đọc DB. Local có 3 ngôn ngữ dạng tiền tố;
     * mọi host khác giữ đúng sơ đồ 2 tên miền của production — production
     * không được tự mọc thêm ngôn ngữ vì một thay đổi ở local.
     */
    public static function default_config_for_host($host) {
        if ($host !== '' && substr($host, -6) === '.local') {
            return [
                'vi' => ['label' => 'Tiếng Việt', 'host' => $host, 'path_prefix' => '',   'wp_locale' => 'vi',    'hreflang' => 'vi'],
                'en' => ['label' => 'English',    'host' => $host, 'path_prefix' => 'en', 'wp_locale' => 'en_US', 'hreflang' => 'en'],
                'ja' => ['label' => '日本語',      'host' => $host, 'path_prefix' => 'ja', 'wp_locale' => 'ja',    'hreflang' => 'ja'],
            ];
        }
        return [
            'vi' => ['label' => 'Tiếng Việt', 'host' => 'saigonhoreca.vn',  'path_prefix' => '',   'wp_locale' => 'vi',    'hreflang' => 'vi'],
            'en' => ['label' => 'English',    'host' => 'saigonhoreca.com', 'path_prefix' => '',   'wp_locale' => 'en_US', 'hreflang' => 'en'],
            'ja' => ['label' => '日本語',      'host' => 'saigonhoreca.com', 'path_prefix' => 'ja', 'wp_locale' => 'ja',    'hreflang' => 'ja'],
        ];
    }

    /**
     * Chuẩn hoá + điền khuyết. Dùng chung cho cả đọc option lẫn lưu form, để
     * không bao giờ có hai bộ quy tắc validate lệch nhau.
     */
    public static function normalize_config($in) {
        if (!is_array($in)) return [];
        $out = [];
        foreach ($in as $code => $c) {
            $code = strtolower(trim((string) $code));
            if (!preg_match('/^[a-z]{2,3}(-[a-z0-9]{2,8})?$/', $code)) continue;
            if (!is_array($c)) continue;

            $host = isset($c['host']) ? strtolower(trim((string) $c['host'])) : '';
            if ($host !== '') {
                // Bóc scheme/port/path nếu người dùng dán nguyên URL vào.
                $parsed = wp_parse_url('//' . ltrim($host, '/'), PHP_URL_HOST);
                if (is_string($parsed) && $parsed !== '') $host = $parsed;
            }
            if (!preg_match('/^[a-z0-9.\-]+$/', $host)) continue;

            $prefix = isset($c['path_prefix']) ? strtolower(trim((string) $c['path_prefix'], '/ ')) : '';
            if (!preg_match('/^[a-z0-9\-]{0,12}$/', $prefix)) $prefix = '';

            $locale = isset($c['wp_locale']) ? trim((string) $c['wp_locale']) : '';
            if (!preg_match('/^[a-z]{2,3}(_[A-Z]{2})?$/', $locale)) $locale = $code;

            $label = isset($c['label']) ? trim((string) $c['label']) : '';
            if ($label === '') $label = strtoupper($code);

            $hreflang = isset($c['hreflang']) ? trim((string) $c['hreflang']) : '';
            if (!preg_match('/^[a-zA-Z]{2,3}(-[a-zA-Z0-9]{2,8})*$/', $hreflang)) $hreflang = $code;

            $out[$code] = [
                'label'       => $label,
                'host'        => $host,
                'path_prefix' => $prefix,
                'wp_locale'   => $locale,
                'hreflang'    => $hreflang,
            ];
        }
        return $out;
    }

    /** Config có nêu host này không — cái chốt bảo vệ môi trường. */
    public static function config_has_host($config, $host) {
        foreach ((array) $config as $c) {
            if (isset($c['host']) && $c['host'] === $host) return true;
        }
        return false;
    }

    /**
     * Config hiệu dụng.
     *
     * Thứ tự QUAN TRỌNG: tính mặc định theo host TRƯỚC, rồi mới đọc option, và
     * chỉ nhận option nếu nó thực sự nêu host hiện tại. Bản cũ đọc option sau
     * khi đã dựng override `.local`, nên chỉ cần option tồn tại là đặc case
     * local bị vứt âm thầm. Đây cũng là thứ giữ cho việc copy DB giữa local và
     * production không kéo theo sơ đồ URL của môi trường kia: config production
     * nêu .vn/.com, không khớp .local -> local tự rơi về mặc định của nó.
     */
    public static function get_language_config() {
        if (self::$config_cache !== null) return self::$config_cache;

        $host   = self::current_host();
        $config = self::default_config_for_host($host);

        $stored = function_exists('get_option') ? get_option(self::OPTION_KEY, null) : null;
        if (is_array($stored) && !empty($stored)) {
            $stored = self::normalize_config($stored);
            if (!empty($stored) && self::config_has_host($stored, $host)) {
                $config = $stored;
            }
        }

        $config = apply_filters('pi_multilang_language_config', $config);
        self::$config_cache = is_array($config) ? $config : [];
        return self::$config_cache;
    }

    public static function flush_config_cache() {
        self::$config_cache = null;
    }

    /** Host hiện tại có nằm trong config không. */
    public static function host_is_configured() {
        return self::config_has_host(self::get_language_config(), self::current_host());
    }

    /**
     * Host này có chạy theo tiền tố đường dẫn không (tức có ít nhất một ngôn
     * ngữ tiền tố khác rỗng trên chính host đó). Production trả false — đó là
     * công tắc cấu trúc tắt toàn bộ hành vi tiền tố ở môi trường đó.
     */
    public static function host_uses_prefixes() {
        $host = self::current_host();
        foreach (self::get_language_config() as $c) {
            if (isset($c['host']) && $c['host'] === $host && trim($c['path_prefix'], '/') !== '') {
                return true;
            }
        }
        return false;
    }

    /* ================================================================== *
     * Host / path
     * ================================================================== */

    private static function detect_host() {
        $host = isset($_SERVER['HTTP_HOST']) ? strtolower((string) $_SERVER['HTTP_HOST']) : '';
        if ($host === '' && function_exists('home_url')) {
            // WP-CLI / cron / indexer không có HTTP_HOST. Lấy host WP được cấu
            // hình thay vì trả rỗng — rỗng sẽ không khớp entry nào và rơi về
            // config production ngay cả trên máy local.
            $host = strtolower((string) wp_parse_url(home_url(), PHP_URL_HOST));
        }
        return preg_replace('/:\d+$/', '', $host);
    }

    private static function current_host() {
        if (self::$host_cache === null) self::$host_cache = self::detect_host();
        return self::$host_cache;
    }

    private static function current_path() {
        $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
        return trim((string) strtok($uri, '?'), '/');
    }

    /** Tiền tố hiệu dụng của một ngôn ngữ ('' = ngôn ngữ mặc định của host). */
    public static function prefix_for($lang) {
        $config = self::get_language_config();
        return isset($config[$lang]['path_prefix']) ? trim($config[$lang]['path_prefix'], '/') : '';
    }

    /* ================================================================== *
     * Nhận diện ngôn ngữ
     * ================================================================== */

    /**
     * @param bool $use_path Có xét tiền tố đường dẫn không. Với ngữ cảnh bị
     *   loại trừ (admin/REST/cron/CLI/login) thì chỉ xét host, để một đoạn path
     *   lạc không thể lật ngôn ngữ của wp-admin. Trên production mọi tiền tố
     *   đều rỗng nên hai nhánh cho kết quả y hệt nhau.
     */
    public static function resolve_current_language($use_path = true) {
        if (defined('PI_CURRENT_LANGUAGE')) return PI_CURRENT_LANGUAGE;

        $host   = self::current_host();
        $path   = $use_path ? self::current_path() : '';
        $config = self::get_language_config();

        $candidates = [];
        foreach ($config as $code => $c) {
            if (isset($c['host']) && $c['host'] === $host) $candidates[$code] = $c;
        }
        // Tiền tố dài nhất thắng, để 'ja' không bị 'j' hay entry gốc nuốt mất.
        uasort($candidates, function ($a, $b) {
            return strlen($b['path_prefix']) - strlen($a['path_prefix']);
        });

        $lang = null;
        foreach ($candidates as $code => $c) {
            $prefix = trim($c['path_prefix'], '/');
            if ($prefix === '') { if ($lang === null) $lang = $code; continue; }
            // So khớp NGUYÊN ĐOẠN, không phải strpos: '/english/' không được
            // tính là tiền tố 'en'.
            if ($path === $prefix || strpos($path, $prefix . '/') === 0) { $lang = $code; break; }
        }
        if ($lang === null) {
            $keys = array_keys($config);
            $lang = !empty($keys) ? $keys[0] : 'vi';
        }

        define('PI_CURRENT_LANGUAGE', $lang);
        return $lang;
    }

    /* ================================================================== *
     * Dựng URL
     * ================================================================== */

    /** URL gốc (scheme://host/tiền-tố/) của một ngôn ngữ. */
    public static function get_language_base_url($lang) {
        $config = self::get_language_config();
        if (empty($config[$lang])) return home_url('/');
        $scheme = is_ssl() ? 'https' : 'http';
        $url    = $scheme . '://' . $config[$lang]['host'] . '/';
        $prefix = trim($config[$lang]['path_prefix'], '/');
        if ($prefix !== '') $url .= $prefix . '/';
        return $url;
    }

    /**
     * Đưa BẤT KỲ URL nào về host/tiền tố của ngôn ngữ $lang, giữ nguyên phần
     * còn lại. Dùng chung cho permalink, chuyển ngôn ngữ, hreflang, canonical
     * và filter home_url — MỘT bản cài đặt, nên chúng không thể lệch nhau.
     *
     * Idempotent: vòng bóc tiền tố ở dưới chạy trước khi gắn tiền tố mới, nên
     * gọi bao nhiêu lần cũng ra một kết quả (điều kiện bắt buộc để dùng làm
     * filter home_url, vì seo-schema.php và breadcrumbs.php gọi
     * home_url($_SERVER['REQUEST_URI']) — URI vốn đã có tiền tố).
     *
     * @param bool $apply_prefix    false = chỉ đổi host (asset, REST).
     * @param bool $translate_slugs true = đổi luôn từng đoạn slug sang alias của
     *   ngôn ngữ đích. MẶC ĐỊNH false để mọi caller sẵn có giữ nguyên hành vi
     *   byte-for-byte — chỉ `Pi_Multilang_Frontend::rewrite_url_domain()` bật nó.
     *   KHÔNG bật trong filter_home_url: home_url() còn phục vụ asset, sitemap,
     *   REST và các đường dẫn không được phép đổi slug.
     */
    public static function rewrite_url_for_language($url, $lang, $apply_prefix = true, $translate_slugs = false) {
        $config = self::get_language_config();
        if (empty($config[$lang])) return $url;

        $parsed = wp_parse_url($url);
        if (!$parsed || !isset($parsed['host'])) return $url;

        // Path rỗng phải coi như '/'. Bản cũ return sớm ở đây, nên home_url()
        // (không tham số, path rỗng) không bao giờ được gắn tiền tố — mà đó
        // chính là giá trị WP::parse_request() dùng để cắt tiền tố. Thiếu nó
        // là toàn bộ định tuyến chết.
        $raw_path = isset($parsed['path']) ? $parsed['path'] : '';
        // GIỮ NGUYÊN dạng dấu / cuối. Path rỗng (home_url() trần) phải ra URL
        // KHÔNG có dấu / cuối, vì nhiều nơi nối chuỗi kiểu home_url() . '/foo'.
        $had_trailing = ($raw_path !== '' && substr($raw_path, -1) === '/');
        $path         = trim($raw_path, '/');

        // Bóc tiền tố của BẤT KỲ ngôn ngữ nào đang có, kể cả của chính ngôn ngữ
        // đích, để tiền tố không chồng lên nhau (.../en/ja/...).
        foreach ($config as $c) {
            $prefix = trim($c['path_prefix'], '/');
            if ($prefix === '') continue;
            if ($path === $prefix) { $path = ''; break; }
            if (strpos($path, $prefix . '/') === 0) { $path = substr($path, strlen($prefix) + 1); break; }
        }

        // Dịch slug SAU khi đã bóc tiền tố, TRƯỚC khi gắn tiền tố mới — để lớp
        // dịch chỉ nhìn thấy path nghiệp vụ, không lẫn tiền tố ngôn ngữ.
        // translate_path_to_lang() idempotent theo cấu trúc nên gọi chồng vô hại.
        if ($translate_slugs && $path !== ''
            && class_exists('Pi_Multilang_Slugs')
            && Pi_Multilang_Slugs::flag('slug_aliases')) {
            $path = trim(Pi_Multilang_Slugs::translate_path_to_lang($path, $lang), '/');
        }

        $scheme = isset($parsed['scheme']) ? $parsed['scheme'] : (is_ssl() ? 'https' : 'http');
        $base   = $scheme . '://' . $config[$lang]['host'];

        if ($apply_prefix) {
            $prefix = trim($config[$lang]['path_prefix'], '/');
            if ($prefix !== '') $base .= '/' . $prefix;
        }

        $out = $base . ($path !== '' ? '/' . $path : '');

        // Bản cũ thêm '/' VÔ ĐIỀU KIỆN, sinh ra
        // .../wp-content/themes//saigonhoreca-theme (đo được trong HTML đã
        // render, trên cả production) và sẽ biến home_url('/wp-sitemap.xml')
        // thành /wp-sitemap.xml/ -> 404.
        if ($had_trailing && substr($out, -1) !== '/') $out .= '/';

        if (!empty($parsed['query']))    $out .= '?' . $parsed['query'];
        if (!empty($parsed['fragment'])) $out .= '#' . $parsed['fragment'];
        return $out;
    }

    /* ================================================================== *
     * Filter
     * ================================================================== */

    /**
     * Ánh xạ ngôn ngữ hiện tại -> locale của WordPress. Priority 999 để thắng
     * filter `locale` của theme (inc/engine/slug-map.php:34) — filter đó là
     * closure ẩn danh nên không remove theo tên được, chỉ có thể vượt bằng
     * priority. Nó trả 'en_US' cho host .com và 'vi' cho mọi host còn lại;
     * config của ta trả đúng hai giá trị đó trên production, nên production
     * không đổi dù filter này thắng.
     */
    public static function filter_locale($locale) {
        if (!self::host_is_configured()) return $locale;   // host lạ: không đụng
        if (!defined('PI_CURRENT_LANGUAGE')) return $locale;
        $config = self::get_language_config();
        $code   = PI_CURRENT_LANGUAGE;
        return !empty($config[$code]['wp_locale']) ? $config[$code]['wp_locale'] : $locale;
    }

    /**
     * Gắn tiền tố ngôn ngữ vào home_url. Đây là toàn bộ cơ chế định tuyến:
     * WP::parse_request() đọc path của home_url() rồi tự cắt nó khỏi REQUEST_URI.
     *
     * Thoát sớm khi tiền tố rỗng chính là bảo đảm CẤU TRÚC cho production —
     * production không bao giờ có tiền tố, nên filter này là no-op ở đó. Đó là
     * đảm bảo chắc hơn một phép kiểm tra host (host có thể đổi, cấu trúc thì không).
     */
    public static function filter_home_url($url, $path = '') {
        static $busy = false;
        if ($busy || !self::$routing) return $url;
        if (!defined('PI_CURRENT_LANGUAGE')) return $url;
        if (self::prefix_for(PI_CURRENT_LANGUAGE) === '') return $url;
        if (self::is_infra_path((string) $path)) return $url;

        $busy = true;
        $out  = self::rewrite_url_for_language($url, PI_CURRENT_LANGUAGE);
        $busy = false;
        return $out;
    }

    /** Đường dẫn hạ tầng thì không bao giờ gắn tiền tố ngôn ngữ. */
    private static function is_infra_path($path) {
        if ($path === '') return false;   // home_url() trần: PHẢI được gắn tiền tố
        $p = '/' . ltrim($path, '/');
        return (bool) preg_match(
            '#^/(wp-admin|wp-content|wp-includes|wp-json|wp-login\.php|wp-cron\.php|xmlrpc\.php|wp-comments-post\.php)(/|$)#',
            $p
        );
    }

    /** Asset: chỉ đổi host, tuyệt đối không gắn tiền tố. */
    public static function rewrite_asset_url($url) {
        if (is_admin() || (defined('DOING_CRON') && DOING_CRON) || (defined('WP_CLI') && WP_CLI)) {
            return $url;
        }
        if (!defined('PI_CURRENT_LANGUAGE')) return $url;
        return self::rewrite_url_for_language($url, PI_CURRENT_LANGUAGE, false);
    }

    /**
     * REST: cũng chỉ đổi host. Cần riêng một hàm vì get_rest_url() dựng URL
     * bằng home_url('/'), nên tiền tố đã bị filter home_url gắn vào TRƯỚC khi
     * tới đây — phải bóc ra, không chỉ đơn giản là bỏ qua.
     */
    public static function rewrite_rest_url($url) {
        if (is_admin() || (defined('DOING_CRON') && DOING_CRON) || (defined('WP_CLI') && WP_CLI)) {
            return $url;
        }
        if (!defined('PI_CURRENT_LANGUAGE')) return $url;
        return self::rewrite_url_for_language($url, PI_CURRENT_LANGUAGE, false);
    }

    /**
     * Đừng để WP nảy khách qua lại giữa các host/ngôn ngữ do CHÍNH TA cấu hình.
     *
     * KHÔNG PHẢI chỉ là fix đa tên miền — đừng "dọn dẹp" filter này. Khi ngôn
     * ngữ có tiền tố, redirect_canonical() dựng $requested_url từ REQUEST_URI
     * (có tiền tố) rồi so với permalink của bài (theo ngôn ngữ RIÊNG của bài),
     * hai vế lệch nhau là nó 301 — và có thể lặp vô hạn.
     */
    public static function skip_canonical_redirect_for_known_host($redirect_url, $requested_url) {
        if (self::host_is_configured()) return false;
        return $redirect_url;
    }

    /* ================================================================== *
     * 301 cho tiền tố của ngôn ngữ mặc định
     * ================================================================== */

    /**
     * /vi/lien-he/ khi vi là mặc định -> 301 về /lien-he/, để mỗi trang chỉ có
     * một URL. Quy tắc chung: đoạn đầu trùng MÃ một ngôn ngữ mà tiền tố hiệu
     * dụng của nó rỗng. Trả về null nếu không cần chuyển hướng.
     */
    private static function default_prefix_redirect_target($path, $full_uri) {
        // CHỈ áp dụng khi host này thật sự chạy theo tiền tố. Trên production
        // (.com) 'en' cũng là mã ngôn ngữ có tiền tố rỗng, nên nếu thiếu điều
        // kiện này thì /en/lien-he/ trên .com — một đường dẫn bình thường —
        // sẽ bị 301 về /lien-he/. Đã bắt được đúng ca đó khi diff baseline.
        if (!self::host_uses_prefixes()) return null;

        $trimmed = trim($path, '/');
        if ($trimmed === '') return null;

        $first = strtok($trimmed, '/');
        if ($first === false || $first === '') return null;

        $config = self::get_language_config();
        $host   = self::current_host();
        if (!isset($config[$first])) return null;
        if ($config[$first]['host'] !== $host) return null;
        if (trim($config[$first]['path_prefix'], '/') !== '') return null;   // có tiền tố thật -> không phải ca này

        $rest  = ltrim(substr($trimmed, strlen($first)), '/');
        $qpos  = strpos($full_uri, '?');
        $query = ($qpos !== false) ? substr($full_uri, $qpos) : '';

        // Giữ dấu / cuối như URL gốc để không tạo thêm một lần chuyển hướng nữa.
        $slash = (substr($path, -1) === '/' && $rest !== '') ? '/' : '';
        return '/' . $rest . $slash . $query;
    }

    public static function maybe_redirect_default_prefix() {
        if (self::$pending_redirect === null) return;
        $target = self::$pending_redirect;
        self::$pending_redirect = null;
        wp_safe_redirect(home_url($target), 301);
        exit;
    }

    /**
     * Tự động nhận diện khách truy cập từ Nhật Bản (GeoIP / Cloudflare / Browser Language).
     *
     * Quy tắc an toàn SEO & UX:
     * 1. Tuyệt đối KHÔNG chuyển hướng bot tìm kiếm (Googlebot, Bingbot...) để tránh cloaking.
     * 2. Chỉ kiểm tra khi vào trang chủ (path rỗng) lần đầu tiên, không can thiệp deep link.
     * 3. Tôn trọng cookie lựa chọn của người dùng (sgh_lang_pref) nếu họ đã chủ động chọn ngôn ngữ.
     * 4. Dùng 302 Temporary Redirect (không dùng 301) để không làm bẩn bộ nhớ cache trình duyệt/CDN.
     */
    public static function maybe_detect_japanese_visitor() {
        if (!self::$routing) return;
        if (defined('PI_CURRENT_LANGUAGE') && PI_CURRENT_LANGUAGE === 'ja') return;
        if (isset($_SERVER['REQUEST_METHOD']) && !in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'], true)) return;

        // Ghi nhận cookie nếu người dùng đổi ngôn ngữ chủ động qua tham số URL (?sgh_lang=vi|en|ja)
        if (!empty($_GET['sgh_lang']) && in_array($_GET['sgh_lang'], ['vi', 'en', 'ja'], true)) {
            $chosen = sanitize_text_field($_GET['sgh_lang']);
            if (!headers_sent()) {
                setcookie('sgh_lang_pref', $chosen, time() + (30 * 86400), '/', '', is_ssl(), false);
            }
            $_COOKIE['sgh_lang_pref'] = $chosen;
        }

        // Chỉ xét trang chủ (root path)
        $path = self::current_path();
        if ($path !== '') return;

        // Nếu người dùng đã có cookie lựa chọn ngôn ngữ khác tiếng Nhật -> tôn trọng lựa chọn
        if (!empty($_COOKIE['sgh_lang_pref']) && $_COOKIE['sgh_lang_pref'] !== 'ja') {
            return;
        }

        // Tuyệt đối không can thiệp bot tìm kiếm / crawlers
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
        if ($ua === '' || preg_match('/bot|crawl|spider|slurp|facebookexternalhit|whatsapp|twitter|lighthouse/i', $ua)) {
            return;
        }

        // Host cho phép kích hoạt GeoIP chuyển sang tiếng Nhật:
        // Áp dụng trên saigonhoreca.com (domain quốc tế), saigonhoreca.vn, và local test
        $host = self::current_host();
        $allowed_hosts = apply_filters('pi_multilang_geoip_hosts', ['saigonhoreca.com', 'saigonhoreca.vn', 'saigonhoreca.local']);
        if (!in_array($host, $allowed_hosts, true)) {
            return;
        }

        // Kiểm tra tín hiệu Nhật Bản: Cloudflare HTTP_CF_IPCOUNTRY == 'JP', geo_test param, hoặc Accept-Language
        $is_japan = false;
        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY']) && strtoupper($_SERVER['HTTP_CF_IPCOUNTRY']) === 'JP') {
            $is_japan = true;
        } elseif (!empty($_GET['geo_test']) && strtoupper($_GET['geo_test']) === 'JP') {
            $is_japan = true;
        } elseif (empty($_COOKIE['sgh_lang_pref']) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) && preg_match('/^ja\b/i', trim($_SERVER['HTTP_ACCEPT_LANGUAGE']))) {
            $is_japan = true;
        }

        if ($is_japan) {
            if (!headers_sent()) {
                setcookie('sgh_lang_pref', 'ja', time() + (30 * 86400), '/', '', is_ssl(), false);
            }
            $target_url = self::get_language_base_url('ja');
            wp_safe_redirect($target_url, 302);
            exit;
        }
    }
}
