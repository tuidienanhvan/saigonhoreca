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
 *
 * ⚠️ 2026-08-02 — tổng quát hoá cùng lúc với sgh_dashboard_add_menu_item().
 * Bản cũ chỉ resolve được `page`; mọi type khác rơi thẳng vào nhánh `else` và
 * biến thành URL cứng — kể cả khi bản khai nói rõ nó trỏ tới bài viết nào.
 *
 * Quy tắc chung: type nào cũng thử resolve slug -> ID theo đúng họ của nó.
 * Resolve được thì menu bám theo ID (đổi slug ở wp-admin, link tự đúng);
 * không resolve được thì hạ cấp về `custom` + URL, chứ không làm hỏng sync.
 */
function sgh_dashboard_normalize_menu_item(array $item): array {
    $type = $item['type'] ?? 'custom';

    // Suy ra họ + object đích. Không liệt kê tên cụ thể — chỉ 3 họ WP có.
    $family = null;
    $object = $item['object'] ?? '';
    if ($type === 'post_type' || $type === 'page' || $type === 'post') {
        $family = 'post_type';
        if (!$object) $object = ($type === 'post_type') ? 'page' : $type;
    } elseif ($type === 'taxonomy' || $type === 'category') {
        $family = 'taxonomy';
        if (!$object) $object = 'category';
    } elseif ($type === 'post_type_archive') {
        // Archive CPT không có ID để resolve — chỉ cần post type tồn tại.
        if ($object && post_type_exists($object)) {
            $normalized = [
                'title'  => $item['title'],
                'type'   => 'post_type_archive',
                'object' => $object,
            ];
            if (!empty($item['submenu']) && is_array($item['submenu'])) {
                $normalized['submenu'] = array_map(__FUNCTION__, $item['submenu']);
            }
            return $normalized;
        }
    }

    $resolved_id = 0;
    if ($family && !empty($item['slug'])) {
        if ($family === 'post_type') {
            $obj = get_page_by_path($item['slug'], OBJECT, $object);
            // Chỉ nhận nội dung đang publish — menu không trỏ vào draft/trash.
            if ($obj && $obj->post_status === 'publish') $resolved_id = $obj->ID;
        } else {
            $term = get_term_by('slug', $item['slug'], $object);
            if ($term && !is_wp_error($term)) $resolved_id = $term->term_id;
        }
    }

    $normalized = $resolved_id
        ? [
            'title'  => $item['title'],
            'type'   => $family,
            'object' => $object,
            'id'     => $resolved_id,
        ]
        : [
            'title' => $item['title'],
            'type'  => 'custom',
            'url'   => $item['url'] ?? home_url('/'),
        ];

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
