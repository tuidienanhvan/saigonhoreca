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
// FILTER REGISTRATION (primary path — Pi Dashboard reads these)
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
 * SaigonHoreca Pages structure.
 *
 * Core pages exist as imported entities (slug `home`, `tin-tuc`, `lien-he`,
 * `ve-saigon-horeca`). No special page templates — they render via the
 * default page.php and the_content() pours the scraped HTML body through.
 */
function sgh_register_pages_structure(array $default): array {
    return array_merge($default, [
        'home' => [
            'title'    => 'Trang Chủ',
            'template' => 'front-page.php',
        ],
        've-saigon-horeca' => [
            'title'    => 'Về Saigon Horeca',
            'template' => 'page.php',
        ],
        'lien-he' => [
            'title'    => 'Liên Hệ',
            'template' => 'page.php',
        ],
        'tin-tuc' => [
            'title'    => 'Tin Tức',
            'template' => 'page.php',
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
            'title' => 'Tin Tức',
            'type'  => 'page',
            'slug'  => 'tin-tuc',
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
    function sgh_sync_pages() {
        if (class_exists('\\PiDashboardV2\\SiteSync')) {
            $result = \PiDashboardV2\SiteSync::syncPages();
            return $result['data'] + ['log' => $result['log']];
        }
        return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'log' => ['Pi Dashboard plugin not active.']];
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
