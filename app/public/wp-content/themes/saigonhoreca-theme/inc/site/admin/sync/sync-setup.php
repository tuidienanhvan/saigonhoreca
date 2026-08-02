<?php
/**
 * Dashboard Sync: Favicons & Menu
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/**
 * Copy theme favicons to WordPress root directory
 */
function sgh_dashboard_sync_favicons() {
    $log = ['━━━ SYNC FAVICONS TO ROOT ━━━'];
    $theme_dir = get_template_directory();
    $root_dir = ABSPATH;

    $files = [
        'favicon.ico',
        'favicon-32x32.webp',
        'favicon-16x16.webp',
        'apple-touch-icon.webp',
        'android-chrome-192x192.webp',
        'android-chrome-512x512.webp',
        'site.webmanifest'
    ];

    $copied = 0;
    $errors = 0;

    if (!is_writable($root_dir)) {
        $log[] = "Root directory is not writable: " . $root_dir;
        return ['success' => false, 'message' => 'Lỗi phân quyền thư mục gốc.', 'log' => $log];
    }

    foreach ($files as $file) {
        $source = $theme_dir . '/assets/images/' . $file;
        $dest = $root_dir . $file;

        if (file_exists($source)) {
            if (is_writable(dirname($dest)) && copy($source, $dest)) {
                $log[] = "Sync: {$file} -> " . basename($dest);
                $copied++;
            } else {
                $log[] = "Failed to copy {$file} to root.";
                $errors++;
            }
        } else {
            $log[] = "Source file missing: assets/images/{$file}";
        }
    }

    $log[] = "Copied: {$copied}, Errors: {$errors}";
    return ['success' => $errors === 0, 'message' => "Sync {$copied} favicon(s).", 'log' => $log];
}

/**
 * Add a single menu item to a WordPress nav menu.
 *
 * ⚠️ 2026-08-02 — tổng quát hoá. Bản cũ hardcode đúng 2 object: `page` và
 * `category`. Mọi thứ khác (bài viết, product_category, CPT) buộc phải khai
 * `custom` = URL cứng, nên đổi slug ở wp-admin là menu chết âm thầm —
 * WordPress không cập nhật được vì nó không biết item trỏ tới cái gì.
 *
 * Nay xử lý theo 2 HỌ mà WP thật sự có, không theo danh sách tên cụ thể:
 *   - `post_type`  → object = post/page/project/... (`object` field)
 *   - `taxonomy`   → object = category/product_category/...
 * `page`/`category` giữ nguyên làm alias để bản khai cũ không vỡ.
 *
 * Item mong đợi: ['type' => 'post'|'page'|'post_type'|'category'|'taxonomy'|'custom',
 *                 'object' => <post_type|taxonomy slug>, 'id' => int]
 */
function sgh_dashboard_add_menu_item($menu_id, $data, $parent_id = 0, $order = 0) {
    $args = [
        'menu-item-title'     => $data['title'],
        'menu-item-status'    => 'publish',
        'menu-item-parent-id' => $parent_id,
        'menu-item-position'  => $order
    ];

    $type = $data['type'] ?? 'custom';

    // Gom về 3 họ WP hiểu, kèm object mặc định cho các alias cũ.
    $family = 'custom';
    $object = $data['object'] ?? '';
    if ($type === 'post_type' || $type === 'page' || $type === 'post') {
        $family = 'post_type';
        if (!$object) $object = ($type === 'post_type') ? 'page' : $type;
    } elseif ($type === 'taxonomy' || $type === 'category') {
        $family = 'taxonomy';
        if (!$object) $object = 'category';
    } elseif ($type === 'post_type_archive') {
        // Archive của CPT: WP không có object-id, chỉ có tên post type.
        $family = 'post_type_archive';
    }

    if ($family === 'post_type_archive') {
        if (!$object || !post_type_exists($object)) return false;
        $args['menu-item-object-id'] = 0;
        $args['menu-item-object']    = $object;
        $args['menu-item-type']      = 'post_type_archive';
    } elseif ($family === 'custom') {
        $args['menu-item-url']  = $data['url'] ?? home_url('/');
        $args['menu-item-type'] = 'custom';
    } else {
        // Chưa có id thì tra từ slug — theo đúng object đã xác định ở trên.
        if (empty($data['id']) && !empty($data['slug'])) {
            if ($family === 'post_type') {
                $obj = get_page_by_path($data['slug'], OBJECT, $object);
                if ($obj) $data['id'] = $obj->ID;
            } else {
                $term = get_term_by('slug', $data['slug'], $object);
                if ($term) $data['id'] = $term->term_id;
            }
        }
        if (empty($data['id'])) return false;

        $args['menu-item-object-id'] = $data['id'];
        $args['menu-item-object']    = $object;
        $args['menu-item-type']      = $family;
    }

    $id = wp_update_nav_menu_item($menu_id, 0, $args);
    return is_wp_error($id) ? false : $id;
}

/**
 * Sync primary navigation menu from site-structure definition.
 */
function sgh_dashboard_sync_menu() {
    $log = [];
    $errors = 0;
    $created = 0;

    $menu_name = 'Primary Menu';
    $menu_obj  = wp_get_nav_menu_object($menu_name);

    if (!$menu_obj) {
        $menu_id = wp_create_nav_menu($menu_name);
        $log[] = "Tạo menu mới: $menu_name";
    } else {
        $menu_id = $menu_obj->term_id;
    }

    $old_items = wp_get_nav_menu_items($menu_id);
    $old_count = $old_items ? count($old_items) : 0;
    if ($old_items) {
        foreach ($old_items as $oi) wp_delete_post($oi->ID, true);
    }
    if ($old_count > 0) $log[] = "Xóa {$old_count} items cũ";

    $structure  = sgh_dashboard_get_menu_definition();
    $menu_order = 1;

    foreach ($structure as $item) {
        $parent_id = sgh_dashboard_add_menu_item($menu_id, $item, 0, $menu_order++);

        if ($parent_id) {
            $created++;
            if (!empty($item['submenu'])) {
                foreach ($item['submenu'] as $sub) {
                    $sid = sgh_dashboard_add_menu_item($menu_id, $sub, $parent_id, $menu_order++);
                    if ($sid) $created++; else $errors++;
                }
            }
            // DEAD CODE: không bản khai nào có key `submenu_slugs`, và từ
            // 2026-08-02 normalize trả 'taxonomy' chứ không còn 'category'
            // nên điều kiện này không bao giờ đúng. Giữ nguyên (không xoá) vì
            // vô hại; đừng dựa vào nó để tự tạo category.
            if (!empty($item['submenu_slugs']) && $item['type'] === 'category') {
                foreach ($item['submenu_slugs'] as $slug) {
                    $term = get_term_by('slug', $slug, 'category');
                    if (!$term) {
                        $new_term = wp_insert_term(ucfirst(str_replace('-', ' ', $slug)), 'category', ['slug' => $slug]);
                        if (!is_wp_error($new_term)) {
                            $term = get_term($new_term['term_id'], 'category');
                            $log[] = "Tạo category: $slug";
                        } else {
                            $errors++;
                            continue;
                        }
                    }
                    if ($term) {
                        $sid = sgh_dashboard_add_menu_item($menu_id, ['title' => $term->name, 'type' => 'taxonomy', 'id' => $term->term_id], $parent_id, $menu_order++);
                        if ($sid) $created++; else $errors++;
                    }
                }
            }
        } else {
            $errors++;
            $log[] = "Lỗi: " . $item['title'];
        }
    }

    $locations = get_theme_mod('nav_menu_locations');
    $locations['primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);

    $log[] = "Menu: {$created} items tạo" . ($errors > 0 ? ", {$errors} lỗi" : "");
    return compact('created', 'errors', 'old_count', 'log');
}
