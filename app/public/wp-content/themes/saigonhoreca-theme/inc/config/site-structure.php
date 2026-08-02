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
 *
 * ⚠️ 2026-08-02 — bản khai này KHÔNG phải danh sách đầy đủ. Site còn 5 category
 * do người dùng tạo (`thiet-bi-bep-cong-nghiep`, `saigon-horeca`,
 * `thiet-bi-bar-cafe`, `inox-kitchen-equipment`,
 * `thiet-bi-bep-theo-mo-hinh-kinh-doanh`) đang gắn bài thật — chúng phân loại
 * theo LOẠI THIẾT BỊ, khác trục phân loại của bản khai này. Mọi trình dọn dẹp
 * phải coi "không có trong bản khai" là BÌNH THƯỜNG, không phải rác.
 *
 * 2 entry `kien-thuc` / `du-an-noi-bat` đã gỡ: khai từ đầu nhưng chưa từng được
 * tạo (sync-categories phụ thuộc \PiDashboardV2\ nên luôn 400), 0 bài.
 */
function sgh_register_categories_structure(array $default): array {
    return array_merge($default, [
        'tin-tuc' => [
            'name'        => 'Tin Tức',
            'description' => 'Tin tức và sự kiện ngành horeca, F&B',
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
                // 2026-08-02: 4 mục dưới đây trước khai 'custom' = URL cứng, dù
                // cả 4 đều trỏ tới đối tượng CÓ THẬT trong DB. Khai đúng object
                // thì menu bám theo ID: đổi slug ở wp-admin link vẫn đúng.
                // `url` giữ lại làm đường lùi — normalize hạ về custom nếu đối
                // tượng biến mất (unpublish/xoá).
                [
                    'title'  => 'Tư vấn thiết kế bếp nhà hàng',
                    'type'   => 'post',
                    'slug'   => 'tu-van-thiet-ke-bep-nha-hang-bep-cong-nghiep',
                    'url'    => sgh_url('consult_kitchen'),
                ],
                [
                    'title'  => 'Tư vấn thiết kế quầy bar',
                    'type'   => 'post',
                    'slug'   => 'cung-cap-thiet-bi-tu-van-thiet-ke-cho-quay-bar',
                    'url'    => sgh_url('consult_bar'),
                ],
                [
                    'title'  => 'Hệ thống cấp khí tươi',
                    'type'   => 'taxonomy',
                    'object' => 'product_category',
                    'slug'   => 'he-thong-hut-khoi-cap-khi-tuoi-bep-nha-hang',
                    'url'    => home_url('/danh-muc-san-pham/he-thong-hut-khoi-cap-khi-tuoi-bep-nha-hang/'),
                ],
                [
                    'title'  => 'Hệ thống hút khói công nghiệp',
                    'type'   => 'taxonomy',
                    'object' => 'product_category',
                    'slug'   => 'he-thong-hut-khoi',
                    'url'    => home_url('/danh-muc-san-pham/he-thong-hut-khoi/'),
                ],
            ],
        ],
        // 2 mục này là ARCHIVE của CPT (`has_archive` bật trong
        // inc/engine/cpt-registration.php), không phải page — WP có sẵn kiểu
        // menu item `post_type_archive` cho đúng trường hợp này.
        [
            'title'  => 'Dự Án',
            'type'   => 'post_type_archive',
            'object' => 'project',
            'url'    => sgh_url('projects_index'),
        ],
        [
            'title'  => 'Sản Phẩm',
            'type'   => 'post_type_archive',
            'object' => 'product',
            'url'    => sgh_url('products_index'),
        ],
        [
            // 2026-08-02: từng khai 'page' (sai — không có Page slug `tin-tuc`),
            // rồi hạ tạm về 'custom'. Đúng bản chất: đây là CATEGORY `tin-tuc`
            // (168 bài), /tin-tuc/ chỉ là alias redirect sang /category/tin-tuc/.
            'title' => 'Tin Tức',
            'type'  => 'taxonomy',
            'object' => 'category',
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
// SITE STRUCTURE API
//
// 2026-08-02: gỡ sạch phụ thuộc `\PiDashboardV2\*`. Namespace đó KHÔNG tồn
// tại ở bất kỳ đâu trong wp-content — plugin `pi-dashboard-v2` chưa từng được
// cài trên site này. Mọi hàm dưới đây từng chỉ là vỏ bọc gọi sang class ma đó
// rồi trả stub 0, khiến các nút "Sync" trên dashboard im lặng không làm gì.
// Nay theme tự làm, giống cách sgh_dashboard_sync_menu() vẫn làm với menu.
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

if (!function_exists('sgh_get_pages_definition')) {
    function sgh_get_pages_definition() {
        return sgh_register_pages_structure([]);
    }
}

if (!function_exists('sgh_get_categories_definition')) {
    function sgh_get_categories_definition() {
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
    /** Tạo các category trong bản khai nếu thiếu. Idempotent, CHỈ THÊM. */
    function sgh_sync_categories() {
        $created = 0;
        $skipped = 0;
        $log     = [];

        foreach (sgh_get_categories_definition() as $slug => $def) {
            $slug = sanitize_title($slug);
            if (get_term_by('slug', $slug, 'category')) {
                $skipped++;
                $log[] = "= đã có: $slug";
                continue;
            }
            $res = wp_insert_term($def['name'] ?? $slug, 'category', [
                'slug'        => $slug,
                'description' => $def['description'] ?? '',
            ]);
            if (is_wp_error($res)) {
                $log[] = "! lỗi tạo '$slug': " . $res->get_error_message();
                continue;
            }
            $created++;
            $log[] = "+ đã tạo: $slug — " . ($def['name'] ?? $slug);
        }

        if (!$created) $log[] = 'Không có gì thay đổi.';
        return ['created' => $created, 'skipped' => $skipped, 'log' => $log];
    }
}

if (!function_exists('sgh_cleanup_stale_categories')) {
    /**
     * Xoá category "rác" — CHỈ những cái vừa KHÔNG có trong bản khai VỪA
     * KHÔNG có bài nào.
     *
     * ⚠️ Cố ý KHÔNG xoá theo kiểu "không có trong bản khai thì xoá". Bản khai
     * hiện chỉ liệt kê 3 category trong khi site có 8, và 5 cái ngoài bản khai
     * đang chứa bài thật (Thiết bị Bếp Công Nghiệp 5 bài, Saigon Horeca 3
     * bài...). Xoá máy móc theo bản khai sẽ mất nội dung thật.
     * Category có bài nhưng ngoài bản khai → chỉ BÁO CÁO, người quyết định.
     */
    function sgh_cleanup_stale_categories() {
        $defined = array_map('sanitize_title', array_keys(sgh_get_categories_definition()));
        $default = (int) get_option('default_category');
        $deleted = 0;
        $log     = [];

        foreach (get_categories(['hide_empty' => false]) as $cat) {
            if (in_array($cat->slug, $defined, true)) continue;
            if ((int) $cat->term_id === $default) { $log[] = "~ giữ (category mặc định): {$cat->slug}"; continue; }

            if ($cat->count > 0) {
                $log[] = "~ GIỮ (ngoài bản khai nhưng có {$cat->count} bài): {$cat->slug} — cần người xem xét";
                continue;
            }
            $res = wp_delete_term($cat->term_id, 'category');
            if (is_wp_error($res)) { $log[] = "! lỗi xoá {$cat->slug}: " . $res->get_error_message(); continue; }
            $deleted++;
            $log[] = "- đã xoá (rỗng, ngoài bản khai): {$cat->slug}";
        }

        if (!$deleted) $log[] = 'Không có category rỗng nào cần xoá.';
        return ['deleted' => $deleted, 'log' => $log];
    }
}

if (!function_exists('sgh_reset_all')) {
    /**
     * KHÔNG hỗ trợ. Đây là thao tác xoá hàng loạt toàn site (page, category,
     * menu). Bản cũ uỷ quyền cho \PiDashboardV2\SiteSync::resetAll() — class
     * không tồn tại nên nó luôn trả 0 và người bấm tưởng "không có gì để xoá".
     *
     * Cố ý KHÔNG viết lại: một nút bấm nhầm sẽ xoá sạch nội dung thật mà không
     * có đường lùi. Cần reset thì dùng công cụ riêng, có backup trước.
     */
    function sgh_reset_all() {
        return [
            'total_deleted' => 0,
            'log' => ['Reset toàn site KHÔNG được hỗ trợ trong theme — cố ý, để tránh xoá nhầm dữ liệu thật. Hãy backup rồi dùng công cụ riêng.'],
        ];
    }
}
