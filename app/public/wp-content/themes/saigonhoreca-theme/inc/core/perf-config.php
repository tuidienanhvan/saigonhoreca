<?php
/**
 * Performance configuration helpers.
 *
 * Exposes filter/constant overrides for key optimization knobs.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Read an integer from supported constants.
 *
 * @param array<int,string> $names Constant names.
 * @param int $default Default value.
 * @return int
 */
function sh_perf_read_int_constant(array $names, $default) {
    foreach ($names as $name) {
        if (defined($name)) {
            return (int) constant($name);
        }
    }

    return (int) $default;
}

/**
 * Read a boolean from supported constants.
 *
 * @param array<int,string> $names Constant names.
 * @param bool $default Default value.
 * @return bool
 */
function sh_perf_read_bool_constant(array $names, $default) {
    foreach ($names as $name) {
        if (!defined($name)) {
            continue;
        }

        $value = constant($name);
        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower(trim((string) $value));
        if (in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
            return true;
        }
        if (in_array($normalized, ['0', 'false', 'no', 'off'], true)) {
            return false;
        }
    }

    return (bool) $default;
}

/**
 * Hero rotation window in minutes.
 *
 * Filter name: hero_rotation_window_minutes
 * Constant names supported: HERO_ROTATION_WINDOW_MINUTES, hero_rotation_window_minutes
 *
 * @return int
 */
function sh_get_hero_rotation_window_minutes() {
    $value = sh_perf_read_int_constant(
        ['HERO_ROTATION_WINDOW_MINUTES', 'hero_rotation_window_minutes'],
        30
    );

    $value = (int) apply_filters('hero_rotation_window_minutes', $value);
    return max(5, $value);
}

/**
 * Homepage cache TTL in seconds.
 *
 * Filter name: homepage_cache_ttl_seconds
 * Constant names supported: HOMEPAGE_CACHE_TTL_SECONDS, homepage_cache_ttl_seconds
 *
 * @return int
 */
function sh_get_homepage_cache_ttl_seconds() {
    $value = sh_perf_read_int_constant(
        ['HOMEPAGE_CACHE_TTL_SECONDS', 'homepage_cache_ttl_seconds'],
        900
    );

    $value = (int) apply_filters('homepage_cache_ttl_seconds', $value);
    return max(60, $value);
}

/**
 * Whether third-party tracking should be deferred.
 *
 * Filter name: defer_third_party_tracking
 * Constant names supported: DEFER_THIRD_PARTY_TRACKING, defer_third_party_tracking
 *
 * @return bool
 */
function sh_should_defer_third_party_tracking() {
    $value = sh_perf_read_bool_constant(
        ['DEFER_THIRD_PARTY_TRACKING', 'defer_third_party_tracking'],
        true
    );

    return (bool) apply_filters('defer_third_party_tracking', $value);
}

/**
 * GTM container ID (filter-only override).
 *
 * @return string
 */
function sh_get_gtm_container_id() {
    return (string) apply_filters('sh_gtm_container_id', 'GTM-PVSPDW5B');
}

