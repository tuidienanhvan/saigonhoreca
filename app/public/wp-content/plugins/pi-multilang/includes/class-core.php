<?php
if (!defined('ABSPATH')) exit;

/**
 * Single source of truth for language <-> URL mapping.
 *
 * Rewritten after studying Polylang's actual "domain" linking mode
 * (PLL_Links_Domain — github.com/polylang/polylang, src/links-domain.php)
 * to fix a real bug: the previous version had TWO independent hardcoded
 * copies of the domain scheme (one in this class for detection, one in
 * class-frontend.php for URL rewriting) — fixing one and forgetting the
 * other is exactly what happened across 2 rounds of rework (detection got
 * fixed to saigonhoreca.com/.vn, but the URL rewriter kept generating
 * en.saigonhoreca.* — a domain that doesn't exist — because it was a
 * separate, independently-maintained copy of the same information).
 *
 * Polylang's fix for this class of bug: ONE config array (language code =>
 * host), and every other piece of logic — detecting the current language,
 * building a URL for language X — reads from that SAME array. There is no
 * second copy anywhere that could drift out of sync.
 *
 * This class extends that idea slightly to also support a PATH PREFIX per
 * language under the same host (needed for the agreed future roadmap:
 * saigonhoreca.com/ja/, /zh/, /ko/ — same domain, different language via
 * URL path — vs. saigonhoreca.vn and saigonhoreca.com being fully separate
 * domains). Adding 'ja' later is exactly one array entry, no other code
 * changes anywhere in the plugin.
 */
class Pi_Multilang_Core {

    const OPTION_KEY = 'pi_multilang_languages';

    public static function init() {
        self::resolve_current_language();

        // WordPress's `home`/`siteurl` options are fixed to ONE domain in the
        // database. Without the filters below, visiting saigonhoreca.com
        // would hit two real problems (neither was addressed in rounds 1-2):
        //
        // 1. redirect_canonical() (wp-includes/canonical.php) builds its
        //    "correct" URL from home_url()/get_permalink() and 301-redirects
        //    any request that doesn't match — since home_url() always
        //    resolves to the single hardcoded domain, a second domain would
        //    get bounced straight back to the primary one on every request,
        //    silently defeating the entire multi-domain setup at the exact
        //    moment it goes live. Guarded here by skipping the redirect
        //    entirely when the current host is one we explicitly recognize.
        // 2. Assets (theme CSS/JS via content_url, REST API via rest_url,
        //    uploads) would still load from the PRIMARY domain even on the
        //    secondary one — a cross-origin request that silently breaks
        //    custom web fonts and can trip browser CORS/security policies.
        //    Same category of fix Polylang's own domain-mode class applies
        //    (content_url/theme_root_uri/plugins_url/rest_url/upload_dir —
        //    see PLL_Links_Abstract_Domain in the real Polylang source).
        add_filter('redirect_canonical', [__CLASS__, 'skip_canonical_redirect_for_known_host'], 10, 2);
        add_filter('content_url', [__CLASS__, 'rewrite_to_current_host']);
        add_filter('theme_root_uri', [__CLASS__, 'rewrite_to_current_host']);
        add_filter('plugins_url', [__CLASS__, 'rewrite_to_current_host']);
        add_filter('rest_url', [__CLASS__, 'rewrite_to_current_host']);
    }

    /** Never let WP bounce a visitor between two domains we ourselves
     *  configured — only step aside for genuinely unrecognized hosts (typo
     *  URLs, old bookmarks to a retired domain, etc.), where WP's normal
     *  canonical-redirect behavior is still correct and desired. */
    public static function skip_canonical_redirect_for_known_host($redirect_url, $requested_url) {
        $host = self::current_host();
        foreach (self::get_language_config() as $c) {
            if ($c['host'] === $host) return false;
        }
        return $redirect_url;
    }

    /** Keep theme/plugin/REST asset URLs on whichever domain is currently
     *  serving the request, instead of the single domain WP's `home` option
     *  is fixed to — avoids cross-domain asset loads (broken custom fonts,
     *  CORS) on the secondary domain. Mirrors Polylang's own approach for
     *  domain-mode sites. Admin/cron/CLI keep the real siteurl untouched —
     *  there is only one wp-admin, it must not itself become domain-relative. */
    public static function rewrite_to_current_host($url) {
        if (is_admin() || (defined('DOING_CRON') && DOING_CRON) || (defined('WP_CLI') && WP_CLI)) {
            return $url;
        }
        return self::rewrite_url_for_language($url, PI_CURRENT_LANGUAGE);
    }

    /**
     * The single source of truth. Stored as a WP option (falls back to this
     * default) so it's configurable without a code deploy — exactly what
     * "config-driven" means here. `path_prefix` empty string = this
     * language lives at the host's root; non-empty = a URL path segment
     * under that host (e.g. 'ja' for saigonhoreca.com/ja/).
     *
     * @return array<string, array{host:string, path_prefix:string}>
     */
    public static function get_language_config() {
        $default = [
            'vi' => ['host' => 'saigonhoreca.vn',  'path_prefix' => ''],
            'en' => ['host' => 'saigonhoreca.com', 'path_prefix' => ''],
            // Future, per the agreed roadmap — same host as 'en', add a path
            // prefix, nothing else changes:
            // 'ja' => ['host' => 'saigonhoreca.com', 'path_prefix' => 'ja'],
            // 'zh' => ['host' => 'saigonhoreca.com', 'path_prefix' => 'zh'],
            // 'ko' => ['host' => 'saigonhoreca.com', 'path_prefix' => 'ko'],
        ];

        // Local dev override: LocalWP serves this site at saigonhoreca.local
        // (both language "domains" collapse onto the same local host), so
        // there's no way to tell vi/en apart by host alone in dev. Map 'en'
        // to a path prefix under the local host instead — visiting
        // saigonhoreca.local/en/ selects English locally; production keeps
        // the real two-domain split untouched. Detected automatically from
        // the request host, not hardcoded as a permanent local-only species
        // of the config (keeps this whole method single-sourced).
        $host = self::current_host();
        if ($host === 'saigonhoreca.local') {
            $default = [
                'vi' => ['host' => 'saigonhoreca.local', 'path_prefix' => ''],
                'en' => ['host' => 'saigonhoreca.local', 'path_prefix' => 'en'],
            ];
        }

        $stored = get_option(self::OPTION_KEY, null);
        $config = is_array($stored) ? $stored : $default;

        // Allows a wp-config.php constant or another plugin to override for
        // a specific environment without touching the DB option.
        return apply_filters('pi_multilang_language_config', $config);
    }

    private static function current_host() {
        $host = isset($_SERVER['HTTP_HOST']) ? strtolower((string) $_SERVER['HTTP_HOST']) : '';
        return preg_replace('/:\d+$/', '', $host); // strip a port if present
    }

    private static function current_path() {
        $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
        $path = (string) wp_parse_url($uri, PHP_URL_PATH);
        return trim($path, '/');
    }

    /**
     * Detect the current language from host + path against get_language_config(),
     * preferring the LONGEST matching path_prefix first so a more specific
     * entry (e.g. 'ja' under saigonhoreca.com) wins over that same host's
     * root entry (e.g. 'en').
     */
    public static function resolve_current_language() {
        $host = self::current_host();
        $path = self::current_path();
        $config = self::get_language_config();

        $candidates = array_filter($config, function ($c) use ($host) {
            return isset($c['host']) && $c['host'] === $host;
        });
        uasort($candidates, function ($a, $b) {
            return strlen($b['path_prefix']) - strlen($a['path_prefix']);
        });

        $lang = 'vi'; // ultimate fallback if nothing matches at all
        foreach ($candidates as $code => $c) {
            $prefix = trim($c['path_prefix'], '/');
            if ($prefix === '' || $path === $prefix || strpos($path, $prefix . '/') === 0) {
                $lang = $code;
                break;
            }
        }

        define('PI_CURRENT_LANGUAGE', $lang);
    }

    /** Base URL (scheme://host/path_prefix/) for a given language code. */
    public static function get_language_base_url($lang) {
        $config = self::get_language_config();
        if (empty($config[$lang])) return home_url('/');
        $scheme = is_ssl() ? 'https' : 'http';
        $url = $scheme . '://' . $config[$lang]['host'] . '/';
        $prefix = trim($config[$lang]['path_prefix'], '/');
        if ($prefix !== '') $url .= $prefix . '/';
        return $url;
    }

    /**
     * Rewrite ANY permalink to point at a different language's host/path
     * prefix, preserving everything else in the URL. Used by post_link/
     * page_link/post_type_link filters, the language switcher, hreflang,
     * and canonical — ONE implementation, so all of them stay consistent
     * with each other and with resolve_current_language() by construction.
     */
    public static function rewrite_url_for_language($url, $lang) {
        $config = self::get_language_config();
        if (empty($config[$lang])) return $url;

        $parsed = wp_parse_url($url);
        if (!$parsed || empty($parsed['path'])) return $url;

        $path = trim($parsed['path'], '/');

        // Strip a leading path prefix belonging to ANY configured language
        // (including the target's own, in case $url was already built for
        // another language) so prefixes never stack up (.../en/ja/...).
        foreach ($config as $c) {
            $prefix = trim($c['path_prefix'], '/');
            if ($prefix === '') continue;
            if ($path === $prefix) { $path = ''; break; }
            if (strpos($path, $prefix . '/') === 0) { $path = substr($path, strlen($prefix) + 1); break; }
        }

        $base = rtrim(self::get_language_base_url($lang), '/');
        $query = !empty($parsed['query']) ? '?' . $parsed['query'] : '';
        return $base . ($path !== '' ? '/' . $path : '') . '/' . $query;
    }
}
