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
        'favicon-32x32.png',
        'favicon-16x16.png',
        'apple-touch-icon.png',
        'android-chrome-192x192.png',
        'android-chrome-512x512.png',
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
 */
function sgh_dashboard_add_menu_item($menu_id, $data, $parent_id = 0, $order = 0) {
    $args = [
        'menu-item-title'     => $data['title'],
        'menu-item-status'    => 'publish',
        'menu-item-parent-id' => $parent_id,
        'menu-item-position'  => $order
    ];

    if ($data['type'] === 'custom') {
        $args['menu-item-url']  = $data['url'];
        $args['menu-item-type'] = 'custom';
    } elseif ($data['type'] === 'page') {
        if (empty($data['id'])) return false;
        $args['menu-item-object-id'] = $data['id'];
        $args['menu-item-object']    = 'page';
        $args['menu-item-type']      = 'post_type';
    } elseif ($data['type'] === 'category' || $data['type'] === 'taxonomy') {
        if (isset($data['slug']) && !isset($data['id'])) {
            $term = get_term_by('slug', $data['slug'], 'category');
            if ($term) $data['id'] = $term->term_id;
            else return false;
        }
        $args['menu-item-object-id'] = $data['id'];
        $args['menu-item-object']    = 'category';
        $args['menu-item-type']      = 'taxonomy';
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
