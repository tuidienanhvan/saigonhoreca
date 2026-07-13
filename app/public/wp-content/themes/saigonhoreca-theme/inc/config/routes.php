<?php
/**
 * Route configuration — saigonhoreca
 *
 * Map route → CSS bundle + route → hero-preload image, dùng bởi engine
 * (inc/core/enqueue.php). Đây là nơi DUY NHẤT cần sửa khi thêm/đổi route
 * cho project mới — engine (sgh_theme_css_bundle, hero preload) chỉ đi bộ
 * qua danh sách này, không còn if/elseif hardcode.
 *
 * THỨ TỰ QUAN TRỌNG: rule đầu tiên khớp sẽ thắng — giữ nguyên thứ tự cũ
 * (front-page → about → contact → product → project/pillar → post →
 * product-archive → project-archive → generic-archive → mặc định 'theme.css').
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

if (!function_exists('sgh_route_css_rules')) {
    /**
     * Mỗi rule: ['when' => callable():bool, 'bundle' => string (tên file .css
     * không có 'theme-' prefix, hoặc callable():string|null cho case động).
     */
    function sgh_route_css_rules() {
        return [
            ['when' => 'is_front_page', 'bundle' => 'home'],
            [
                'when'   => fn() => is_page_template('page-templates/page-gioi-thieu.php') || is_page('ve-saigon-horeca') || is_page('gioi-thieu'),
                'bundle' => 'about',
            ],
            [
                'when'   => fn() => is_page_template('page-templates/page-lien-he.php') || is_page('lien-he'),
                'bundle' => 'contact',
            ],
            ['when' => fn() => is_singular('product'), 'bundle' => 'product'],
            [
                'when'   => fn() => is_singular('project') || is_page_template('page-templates/page-project.php') || sgh_is_pillar_page_template(),
                // Case động: ưu tiên bundle riêng theo slug, sgh_theme_css_bundle()
                // tự fallback về 'project' (bundle chung) nếu file riêng không tồn tại.
                'bundle' => fn() => sgh_current_project_slug() ? 'project-' . sgh_current_project_slug() : 'project',
            ],
            ['when' => fn() => is_singular('post'), 'bundle' => 'single'],
            [
                'when' => fn() => is_post_type_archive('product')
                    || is_tax('product_category')
                    || is_tax('product_cat')
                    || is_tax('product_brand')
                    || (is_search() && (get_query_var('post_type') === 'product' || (isset($_GET['post_type']) && $_GET['post_type'] === 'product'))),
                'bundle' => 'archive-product',
            ],
            [
                'when'   => fn() => is_post_type_archive('project') || is_tax('project_category'),
                'bundle' => 'archive-project',
            ],
            [
                'when'   => fn() => is_archive() || is_home() || is_search() || is_404(),
                'bundle' => 'archive',
            ],
        ];
    }
}

if (!function_exists('sgh_project_slug_aliases')) {
    /**
     * post_name (WP CPT `project` slug) → tên thư mục pillar/CSS-entrypoint
     * thật, khi 2 cái không khớp (post đổi slug sau khi đã tạo pillar).
     * Không đổi tên 34 thư mục pillar hiện có — chỉ alias để route đúng
     * bundle, tránh rơi vào fallback theme-project.css (1.13MB).
     *
     * Phát hiện 07/2026: /du-an/grand-marble/ (post_name thật) trỏ tới
     * pillar 'grand-marble-thuong-hieu-banh-cao-cap-nhat-ban' — thêm alias
     * tại đây thay vì đổi tên 12 file của dự án.
     */
    function sgh_project_slug_aliases() {
        return [
            'grand-marble' => 'grand-marble-thuong-hieu-banh-cao-cap-nhat-ban',
        ];
    }
}

if (!function_exists('sgh_resolve_project_slug')) {
    function sgh_resolve_project_slug($slug) {
        $aliases = sgh_project_slug_aliases();
        return $aliases[$slug] ?? $slug;
    }
}

if (!function_exists('sgh_route_hero_preload_rules')) {
    /**
     * Mỗi rule: ['when' => callable():bool, 'image' => relative path dưới
     * uploads env-aware — resolved qua sgh_img()].
     * front-page KHÔNG nằm trong danh sách này — nó dùng cơ chế riêng
     * (transient cache + featured-image lookup) trong enqueue.php.
     */
    function sgh_route_hero_preload_rules() {
        return [
            [
                'when'  => fn() => is_page_template('page-templates/page-gioi-thieu.php') || is_page('ve-saigon-horeca') || is_page('gioi-thieu'),
                'image' => 'saigonhoreca/bia-bep-cong-nghiep-inox.webp',
            ],
            [
                'when'  => fn() => is_page_template('page-templates/page-lien-he.php') || is_page('lien-he'),
                'image' => 'saigonhoreca/Saigon-Horeca-thiet-bi-bep-cong-nghiep.webp',
            ],
            [
                'when'  => fn() => is_post_type_archive('project') || is_tax('project_category') || is_page('du-an'),
                'image' => 'renovate-sol-kitchen-bar-quan-7/Sol0D7-05.webp',
            ],
        ];
    }
}
