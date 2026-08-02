<?php
/**
 * Pi API Menu Endpoints
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Menu {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/menu/audit', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_audit'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/menu/sync', [
            'methods'  => 'POST',
            'callback' => [self::class, 'sync'],
            'permission_callback' => $admin
        ]);
    }

    /**
     * 2026-08-02: cả 2 route trước đây phụ thuộc `\PiDashboardV2\SiteStructure`
     * / `SiteSync` — namespace đó KHÔNG tồn tại trên site nào (plugin
     * `pi-dashboard-v2` chưa từng được cài), nên chúng luôn trả HTTP 400 và
     * hoàn toàn vô dụng.
     *
     * Nay chuyển sang dùng filter `pi_api_deploy_action_result` — đường đã
     * SỐNG và đang chạy thật (theme hook vào trong functions.php, dẫn tới
     * SGH_Dashboard::process_action()). Nhờ đó theme nào cũng cắm được, không
     * hardcode tên class của một plugin cụ thể.
     */
    public static function get_audit() {
        $menu_obj = wp_get_nav_menu_object('Primary Menu');
        $items    = $menu_obj ? wp_get_nav_menu_items($menu_obj->term_id) : [];
        $items    = (is_array($items)) ? $items : [];

        $locations = get_theme_mod('nav_menu_locations');
        $assigned  = isset($locations['primary']) ? (int) $locations['primary'] : 0;

        // Bản khai do theme cung cấp (nếu theme có). Không có thì trả rỗng chứ
        // không lỗi — audit vẫn cho biết trạng thái THỰC TẾ của menu.
        $definition = function_exists('sgh_dashboard_get_menu_definition')
            ? sgh_dashboard_get_menu_definition()
            : apply_filters('pi_dashboard_menu_definition', []);

        // Menu "dùng được" = đã gán location VÀ có item. Menu rỗng nhưng tồn
        // tại từng khiến front-end âm thầm chạy fallback hardcode.
        $usable = ($assigned && $menu_obj && $assigned === (int) $menu_obj->term_id && count($items) > 0);

        // Vị trí menu theo tên do theme đăng ký, không hardcode 'primary'.
        $registered   = get_registered_nav_menus();
        $location_key = '';
        if ($assigned) {
            foreach ((array) $locations as $key => $id) {
                if ((int) $id === $assigned) { $location_key = $key; break; }
            }
        }
        $menu_location = $location_key
            ? ($registered[$location_key] ?? $location_key)
            : 'Chưa gán';

        if (!$menu_obj)      $sync_status = 'Chưa có menu';
        elseif (!count($items)) $sync_status = 'Menu rỗng';
        elseif (!$assigned)  $sync_status = 'Chưa gán vị trí';
        else                 $sync_status = 'Ổn định';

        return new \WP_REST_Response([
            'definition'      => $definition,
            'definition_count'=> is_array($definition) ? count($definition) : 0,
            'actual_id'       => $menu_obj ? $menu_obj->term_id : null,
            'actual_count'    => count($items),
            'assigned_to'     => $assigned ?: null,
            'menu_location'   => $menu_location,
            // 'Ổn định' là giá trị frontend (MenuPage.jsx) coi là trạng thái tốt.
            'sync_status'     => $sync_status,
            'usable'          => $usable,
        ], 200);
    }

    public static function sync() {
        $result = apply_filters('pi_api_deploy_action_result', null, 'sync-menu');
        if ($result === null) {
            return new \WP_REST_Response([
                'message' => 'Theme không cung cấp trình đồng bộ menu (filter pi_api_deploy_action_result).',
            ], 400);
        }
        return new \WP_REST_Response($result, 200);
    }
}
