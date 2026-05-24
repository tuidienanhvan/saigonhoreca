<?php
/**
 * Dashboard Menu Definition — SaigonHoreca primary navigation.
 *
 * Admin sync consumes the same canonical navigation declared in
 * inc/config/site-structure.php, then resolves page slugs to WP menu item IDs.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

add_filter('pi_dashboard_menu_definition', function ($default) {
    return !empty($default) ? $default : sgh_dashboard_get_menu_definition();
});

/**
 * Resolve public navigation entries into the format expected by sync-setup.php.
 */
function sgh_dashboard_normalize_menu_item(array $item): array {
    $type = $item['type'] ?? 'custom';

    if ($type === 'page') {
        $page = !empty($item['slug']) ? get_page_by_path($item['slug']) : null;
        $normalized = $page
            ? [
                'title' => $item['title'],
                'type'  => 'page',
                'id'    => $page->ID,
            ]
            : [
                'title' => $item['title'],
                'type'  => 'custom',
                'url'   => $item['url'] ?? home_url('/'),
            ];
    } else {
        $normalized = [
            'title' => $item['title'],
            'type'  => 'custom',
            'url'   => $item['url'] ?? home_url('/'),
        ];
    }

    if (!empty($item['submenu']) && is_array($item['submenu'])) {
        $normalized['submenu'] = array_map('sgh_dashboard_normalize_menu_item', $item['submenu']);
    }

    return $normalized;
}

/**
 * Get menu structure definition array.
 */
function sgh_dashboard_get_menu_definition(): array {
    $items = function_exists('sgh_get_primary_navigation_definition')
        ? sgh_get_primary_navigation_definition()
        : [];

    if (empty($items)) {
        return [
            ['title' => 'Dự Án', 'type' => 'custom', 'url' => site_url('/du-an/')],
            ['title' => 'Sản Phẩm', 'type' => 'custom', 'url' => site_url('/san-pham/')],
            ['title' => 'Tin Tức', 'type' => 'custom', 'url' => site_url('/tin-tuc/')],
            ['title' => 'Liên hệ', 'type' => 'custom', 'url' => site_url('/lien-he/')],
        ];
    }

    return array_map('sgh_dashboard_normalize_menu_item', $items);
}
