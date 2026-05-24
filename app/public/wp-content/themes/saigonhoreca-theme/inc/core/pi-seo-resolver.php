<?php
/**
 * Pi SEO resolver helpers.
 *
 * Canonical storage lives in WordPress meta/options written by Pi.
 * Theme code reads Pi-managed values first, then falls back to older aliases
 * or existing theme defaults.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('sgh_pi_seo_aliases')) {
    function sgh_pi_seo_aliases() {
        return [
            'seo_title'       => ['_pi_seo_title', '_pi_og_title'],
            'seo_description' => ['_pi_seo_description', '_pi_seo_desc', '_pi_og_description'],
            'og_title'        => ['_pi_og_title', '_pi_seo_title'],
            'og_description'  => ['_pi_og_description', '_pi_seo_description', '_pi_seo_desc'],
            'og_image'        => ['_pi_og_image', '_pi_og_image_url'],
            'focus_keyword'   => ['_pi_seo_focus_keyword'],
            'noindex'         => ['_pi_seo_noindex'],
        ];
    }
}

if (!function_exists('sgh_pi_get_post_meta_value')) {
    function sgh_pi_get_post_meta_value($post_id, $logical_key, $default = '') {
        $aliases = sgh_pi_seo_aliases();
        $keys = $aliases[$logical_key] ?? [$logical_key];

        foreach ($keys as $key) {
            $value = get_post_meta($post_id, $key, true);
            if ($value !== '' && $value !== null) {
                return $value;
            }
        }

        return $default;
    }
}

if (!function_exists('sgh_pi_get_post_seo')) {
    function sgh_pi_get_post_seo($post_id) {
        $noindex = sgh_pi_get_post_meta_value($post_id, 'noindex', '');

        return [
            'seo_title'       => (string) sgh_pi_get_post_meta_value($post_id, 'seo_title', ''),
            'seo_description' => (string) sgh_pi_get_post_meta_value($post_id, 'seo_description', ''),
            'og_title'        => (string) sgh_pi_get_post_meta_value($post_id, 'og_title', ''),
            'og_description'  => (string) sgh_pi_get_post_meta_value($post_id, 'og_description', ''),
            'og_image'        => (string) sgh_pi_get_post_meta_value($post_id, 'og_image', ''),
            'focus_keyword'   => (string) sgh_pi_get_post_meta_value($post_id, 'focus_keyword', ''),
            'noindex'         => in_array((string) $noindex, ['1', 'true', 'yes'], true),
        ];
    }
}

if (!function_exists('sgh_pi_has_custom_robots_txt')) {
    function sgh_pi_has_custom_robots_txt() {
        $custom = get_option('pi_seo_robots_txt', '');
        return is_string($custom) && trim($custom) !== '';
    }
}

if (!function_exists('sgh_pi_get_schema_rules')) {
    function sgh_pi_get_schema_rules() {
        $rules = get_option('sgh_seo_schema_rules', []);
        return is_array($rules) ? $rules : [];
    }
}

if (!function_exists('sgh_pi_has_schema_rules')) {
    function sgh_pi_has_schema_rules() {
        $rules = sgh_pi_get_schema_rules();
        if (empty($rules)) return false;

        foreach ($rules as $rule) {
            if (!is_array($rule)) continue;
            if (!array_key_exists('enabled', $rule) || !empty($rule['enabled'])) {
                return true;
            }
        }

        return false;
    }
}
