<?php
/**
 * Domain configuration — saigonhoreca (song ngữ .vn/.com)
 *
 * Nguồn duy nhất cho hostname production. Consumer hiện tại (slug-map.php,
 * seo-schema.php, HTTPS rewrite trong functions.php) vẫn dùng literal string
 * — TODO theo dõi trong docs/ARCHITECTURE.md để dần chuyển sang đọc từ đây,
 * mỗi lần 1 consumer + HTML diff xác nhận byte-identical trước khi merge.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

define('SGH_DOMAIN_VN', 'saigonhoreca.vn');
define('SGH_DOMAIN_COM', 'saigonhoreca.com');
define('SGH_DOMAIN_LOCAL_SUFFIX', '.local');

if (!function_exists('sgh_is_english_host')) {
    /** Kiểm tra 1 host bất kỳ (không phải request hiện tại) có phải domain .com không. */
    function sgh_is_english_host($host) {
        return substr((string) $host, -4) === '.com';
    }
}

if (!function_exists('sgh_is_known_production_host')) {
    function sgh_is_known_production_host($host) {
        $host = strtolower((string) $host);
        return preg_match('/^(www\.)?(' . preg_quote(SGH_DOMAIN_VN, '/') . '|' . preg_quote(SGH_DOMAIN_COM, '/') . ')$/', $host) === 1;
    }
}
