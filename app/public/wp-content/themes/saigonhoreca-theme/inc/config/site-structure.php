<?php
/**
 * Site Structure Definition — SaigonHoreca Theme
 *
 * Registers Pages + Categories via Pi Dashboard filter hooks. Same plumbing
 * as the saigonhouse parent theme, but the structure mirrors saigonhoreca.vn
 * (Về Saigon Horeca / Dự Án / Sản Phẩm / Tin Tức / Liên Hệ) instead of the
 * saigonhouse architecture/construction menu.
 *
 * Legacy wrappers (sgh_get_pages_definition, sgh_sync_pages, etc.) kept for
 * backward compatibility with the Pi Dashboard plugin family.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// FILTER REGISTRATION
//
// ⚠️ 2026-08-02: 3 filter dưới đây hiện KHÔNG CÓ CONSUMER nào. Chúng được thêm
// cho plugin `pi-dashboard-v2` (namespace \PiDashboardV2) — plugin đó chưa từng
// được cài trên site này; grep toàn wp-content: 0 chỗ `apply_filters` các key
// này. Comment cũ ghi "primary path — Pi Dashboard reads these" là SAI SỰ THẬT
// và đã khiến người đọc (kể cả agent) tưởng có cơ chế sync đang chạy.
//
// Đường chạy THẬT: các wrapper ở cuối file (sgh_get_pages_definition,
// sgh_sync_pages...) gọi TRỰC TIẾP mấy callback này. Giữ add_filter lại chỉ để
// tương thích nếu plugin kia xuất hiện sau.
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

add_filter('pi_dashboard_pages_definition', 'sgh_register_pages_structure');
add_filter('pi_dashboard_categories_definition', 'sgh_register_categories_structure');
add_filter('pi_dashboard_permalink_structure', 'sgh_register_permalink_structure');

/**
 * SaigonHoreca canonical permalink structure.
 *
 * Saigonhoreca.vn uses clean /<slug>/ permalinks for blog posts (no date
 * prefix), so the theme enforces /%postname%/ — see also
 * `saigonhouse_enforce_permalink_structure()` in functions.php.
 */
function sgh_register_permalink_structure(string $default): string {
    return '/%postname%/';
}

/**
 * SaigonHoreca Pages structure — 3 Page thật của site.
 *
 * Đây là bộ Page DUY NHẤT site cần. Mọi khu khác KHÔNG dùng Page:
 *   /du-an/, /du-an/<slug>/    → CPT `project` (archive + single, 31 pillar)
 *   /san-pham/                 → CPT `product`
 *   /danh-muc-san-pham/<term>/ → taxonomy product_category
 *   /category/tin-tuc/         → category (xem ghi chú `tin-tuc` bên dưới)
 *
 * 2026-08-02: bỏ entry `tin-tuc`. Tin Tức là CATEGORY chứ không phải Page —
 * functions.php có template_redirect (priority 1) đẩy /tin-tuc/ →
 * /category/tin-tuc/, nên một Page slug `tin-tuc` sẽ vĩnh viễn bị che, không
 * bao giờ truy cập được. Khai nó ở đây chỉ khiến sync tạo ra page chết.
 *
 * `home` khai template front-page.php để ghi lại ý định, nhưng đó KHÔNG phải
 * page-template (không có header "Template Name") — WP tự chọn front-page.php
 * cho trang chủ qua template hierarchy. sgh_sync_pages() cố ý không gán nó.
 */
function sgh_register_pages_structure(array $default): array {
    return array_merge($default, [
        'home' => [
            'title'    => 'Trang Chủ',
            'template' => 'front-page.php',
        ],
        've-saigon-horeca' => [
            'title'    => 'Về Saigon Horeca',
            'template' => 'page-templates/page-gioi-thieu.php',
        ],
        'lien-he' => [
            'title'    => 'Liên Hệ',
            'template' => 'page-templates/page-lien-he.php',
        ],
    ]);
}

/**
 * SaigonHoreca Categories structure.
 *
 * Blog `category` taxonomy stays minimal — saigonhoreca.vn doesn't expose
 * per-category archives publicly. Product CPT has its own `product_category`
 * taxonomy registered in inc/core/cpt-registration.php.
 */
function sgh_register_categories_structure(array $default): array {
    return array_merge($default, [
        'tin-tuc' => [
            'name'        => 'Tin Tức',
            'description' => 'Tin tức và sự kiện ngành horeca, F&B',
        ],
        'kien-thuc' => [
            'name'        => 'Kiến Thức',
            'description' => 'Kiến thức về thiết bị bếp công nghiệp, quầy bar và vận hành nhà hàng',
        ],
        'du-an-noi-bat' => [
            'name'        => 'Dự Án Nổi Bật',
            'description' => 'Các dự án thiết kế bếp và horeca tiêu biểu',
        ],
    ]);
}

/**
 * Canonical public navigation for SaigonHoreca.
 *
 * This is the source used by fallback header menus and by the admin menu sync.
 * The URLs intentionally point to canonical public routes only; legacy/root
 * aliases such as /bambino-saigonhoreca/ should redirect, not appear here.
 */
function sgh_get_primary_navigation_definition(): array {
    return [
        [
            'title' => 'Về Saigon Horeca',
            'type'  => 'page',
            'slug'  => 've-saigon-horeca',
            'url'   => sgh_url('about'),
            'submenu' => [
                [
                    'title' => 'Tư vấn thiết kế bếp nhà hàng',
                    'type'  => 'custom',
                    'url'   => sgh_url('consult_kitchen'),
                ],
                [
                    'title' => 'Tư vấn thiết kế quầy bar',
                    'type'  => 'custom',
                    'url'   => sgh_url('consult_bar'),
                ],
                [
                    'title' => 'Hệ thống cấp khí tươi',
                    'type'  => 'custom',
                    'url'   => home_url('/danh-muc-san-pham/he-thong-hut-khoi-cap-khi-tuoi-bep-nha-hang/'),
                ],
                [
                    'title' => 'Hệ thống hút khói công nghiệp',
                    'type'  => 'custom',
                    'url'   => home_url('/danh-muc-san-pham/he-thong-hut-khoi/'),
                ],
            ],
        ],
        [
            'title' => 'Dự Án',
            'type'  => 'custom',
            'url'   => sgh_url('projects_index'),
        ],
        [
            'title' => 'Sản Phẩm',
            'type'  => 'custom',
            'url'   => sgh_url('products_index'),
        ],
        [
            // 2026-08-02: đổi 'page' -> 'custom'. Không có Page slug `tin-tuc`
            // (Tin Tức là category, /tin-tuc/ bị redirect sang
            // /category/tin-tuc/). Để 'page' thì sgh_dashboard_normalize_menu_item()
            // âm thầm hạ cấp xuống 'custom' mỗi lần sync — khai đúng ngay từ đầu.
            'title' => 'Tin Tức',
            'type'  => 'custom',
            'url'   => sgh_url('news'),
        ],
        [
            'title' => 'Liên hệ',
            'type'  => 'page',
            'slug'  => 'lien-he',
            'url'   => sgh_url('contact'),
        ],
    ];
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// PLUGIN INTEGRATION FILTERS (provide SaigonHoreca-specific data to Pi plugins)
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

// Pi Chatbot — URL placeholders for AI chat responses
add_filter('pi_chatbot_url_placeholders', function ($defaults, $site) {
    return array_merge($defaults, [
        $site . '/lien-he/'          => '{{LINK_LIENHE}}',
        $site . '/ve-saigon-horeca/' => '{{LINK_GIOITHIEU}}',
        $site . '/du-an/'            => '{{LINK_DUAN}}',
        $site . '/san-pham/'         => '{{LINK_SANPHAM}}',
        $site . '/tin-tuc/'          => '{{LINK_TINTUC}}',
    ]);
}, 10, 2);

// Pi Chatbot — Contact info for AI system prompt
add_filter('pi_chatbot_contact_info', function () {
    $info = function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : [];
    return [
        'company_name' => $info['company_name'] ?? 'Saigon Horeca',
        'company_full' => $info['company_full'] ?? 'CÔNG TY TNHH SÀI GÒN HORECA',
        'address'      => $info['address'] ?? 'TP. Hồ Chí Minh',
        'hotline'      => $info['hotline'] ?? '0901 304 365',
        'website'      => home_url(),
    ];
});

// Pi Leads — Service options for contact form
add_filter('pi_leads_service_options', function () {
    return [
        'thiet-bi-bep-cong-nghiep' => 'Thiết bị bếp công nghiệp',
        'thiet-bi-quay-bar'        => 'Thiết bị quầy bar',
        'thiet-bi-lanh-cong-nghiep' => 'Thiết bị lạnh công nghiệp',
        'he-thong-hut-khoi'        => 'Hệ thống hút khói',
        'tu-van-thiet-ke-bep'      => 'Tư vấn thiết kế bếp nhà hàng',
        'khac'                     => 'Khác',
    ];
});

// Pi Leads — Brand color for email templates
add_filter('pi_leads_brand_color', function () {
    return '#F9C349'; // Saigon Horeca gold/yellow — matches production primary
});

add_filter('pi_leads_admin_email', function () {
    return function_exists('saigonhouse_contact') ? saigonhouse_contact('email_primary') : get_option('admin_email');
});

add_filter('pi_leads_contact_info', function () {
    return function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : [];
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// LEGACY WRAPPERS (backward compat — prefer Pi\Dashboard\SiteStructure directly)
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

if (!function_exists('sgh_get_pages_definition')) {
    function sgh_get_pages_definition() {
        if (class_exists('\\PiDashboardV2\\SiteStructure')) {
            return \PiDashboardV2\SiteStructure::getPages();
        }
        return sgh_register_pages_structure([]);
    }
}

if (!function_exists('sgh_get_categories_definition')) {
    function sgh_get_categories_definition() {
        if (class_exists('\\PiDashboardV2\\SiteStructure')) {
            return \PiDashboardV2\SiteStructure::getCategories();
        }
        return sgh_register_categories_structure([]);
    }
}

if (!function_exists('sgh_sync_pages')) {
    /**
     * Tạo các Page mà theme khai trong sgh_register_pages_structure().
     *
     * 2026-08-02: trước đây hàm này chỉ uỷ quyền cho `\PiDashboardV2\SiteSync`
     * — class KHÔNG tồn tại ở bất kỳ đâu trong wp-content (plugin
     * `pi-dashboard-v2` chưa từng được cài). Nên nút "Sync Pages" chỉ log
     * "Pi Dashboard plugin not active." và tạo 0 page, khiến site thiếu hẳn
     * Page trang chủ suốt thời gian dài mà không ai thấy lỗi.
     *
     * Nay tự làm trong theme, cùng cách `sgh_dashboard_sync_menu()` đã làm với
     * menu. Idempotent: page đã có thì đếm `skipped`, KHÔNG đụng nội dung.
     */
    function sgh_sync_pages() {
        if (class_exists('\\PiDashboardV2\\SiteSync')) {
            $result = \PiDashboardV2\SiteSync::syncPages();
            return $result['data'] + ['log' => $result['log']];
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $log     = [];

        foreach (sgh_get_pages_definition() as $slug => $def) {
            $slug  = sanitize_title($slug);
            $title = $def['title'] ?? ucfirst($slug);

            $page = get_page_by_path($slug, OBJECT, 'page');
            if ($page && $page->post_status !== 'trash') {
                $skipped++;
                $log[] = "= đã có: /$slug/ (ID {$page->ID})";
                continue;
            }

            $page_id = wp_insert_post([
                'post_type'   => 'page',
                'post_status' => 'publish',
                'post_title'  => $title,
                'post_name'   => $slug,
                'post_content' => '',
            ], true);

            if (is_wp_error($page_id)) {
                $log[] = "! lỗi tạo /$slug/: " . $page_id->get_error_message();
                continue;
            }

            $created++;
            $log[] = "+ đã tạo: /$slug/ — $title (ID $page_id)";

            // Chỉ gán khi template là page-template HỢP LỆ (có header
            // "Template Name") và file tồn tại. `front-page.php` bị khai cho
            // slug `home` nhưng KHÔNG phải page-template — WP tự dùng nó cho
            // trang chủ qua template hierarchy, gán vào meta là sai.
            $tpl = $def['template'] ?? '';
            if ($tpl && $tpl !== 'page.php' && $tpl !== 'front-page.php') {
                $valid = wp_get_theme()->get_page_templates();
                if (isset($valid[$tpl])) {
                    update_post_meta($page_id, '_wp_page_template', $tpl);
                    $updated++;
                    $log[] = "  ↳ gán template: $tpl";
                } else {
                    $log[] = "  ↳ bỏ qua template '$tpl' (không phải page-template hợp lệ)";
                }
            }
        }

        if (!$created && !$updated) {
            $log[] = 'Không có gì thay đổi — mọi page trong bản khai đã tồn tại.';
        }

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped, 'log' => $log];
    }
}

if (!function_exists('sgh_sync_categories')) {
    function sgh_sync_categories() {
        if (class_exists('\\PiDashboardV2\\SiteSync')) {
            $result = \PiDashboardV2\SiteSync::syncCategories();
            return $result['data'] + ['log' => $result['log']];
        }
        return ['created' => 0, 'skipped' => 0, 'log' => ['Pi Dashboard plugin not active.']];
    }
}

if (!function_exists('sgh_cleanup_stale_categories')) {
    function sgh_cleanup_stale_categories() {
        if (class_exists('\\PiDashboardV2\\SiteSync')) {
            $result = \PiDashboardV2\SiteSync::cleanupStaleCategories();
            return $result['data'] + ['log' => $result['log']];
        }
        return ['deleted' => 0, 'log' => ['Pi Dashboard plugin not active.']];
    }
}

if (!function_exists('sgh_reset_all')) {
    function sgh_reset_all() {
        if (class_exists('\\PiDashboardV2\\SiteSync')) {
            $result = \PiDashboardV2\SiteSync::resetAll();
            return $result['data'] + ['log' => $result['log']];
        }
        return ['total_deleted' => 0, 'log' => ['Pi Dashboard plugin not active.']];
    }
}
