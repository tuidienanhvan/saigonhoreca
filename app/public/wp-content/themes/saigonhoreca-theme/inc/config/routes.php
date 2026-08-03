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

if (!function_exists('sgh_bilingual_slug_map')) {
    /**
     * Bảng slug song ngữ (.vn Việt ↔ .com English). Mỗi entry: section_key
     * => [vi_slug, en_slug]. Nguồn dữ liệu DUY NHẤT cho engine/slug-map.php
     * (sgh_slug()/sgh_url()/sgh_translate_path()) — di chuyển ra khỏi
     * inc/engine/ để giữ engine thuần logic, không hardcode route/brand.
     */
    function sgh_bilingual_slug_map() {
        return [
            'projects_index'  => ['du-an',                                          'projects'],
            'projects_cat'    => ['danh-muc-du-an',                                 'project-category'],
            'about'           => ['ve-saigon-horeca',                               'about'],
            'products_index'  => ['san-pham',                                       'products'],
            'products_cat'    => ['danh-muc-san-pham',                              'product-category'],
            'news'            => ['tin-tuc',                                        'blog'],
            'contact'         => ['lien-he',                                        'contact'],
            'consult_kitchen' => ['tu-van-thiet-ke-bep-nha-hang-bep-cong-nghiep',   'kitchen-consulting'],
            'consult_bar'     => ['cung-cap-thiet-bi-tu-van-thiet-ke-cho-quay-bar', 'bar-equipment'],
        ];
    }
}

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
                // 2026-08-02: bỏ nhánh is_page_template('page-templates/page-project.php')
                // — template đó đã xoá (gọi template-parts/project/* không tồn tại,
                // tàn dư thiết kế cũ). Pillar thật là CPT `project` + per-slug
                // page-project-<slug>.php, đã phủ bởi 2 điều kiện còn lại.
                'when'   => fn() => is_singular('project') || sgh_is_pillar_page_template(),
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
     *
     * 2026-08-02: alias đó ĐÃ GỠ. Lệch tên là nguyên nhân gốc khiến Grand
     * Marble có 2 URL cùng trả 200 (post CPT + route ảo của pillar), trùng
     * nội dung với Google. Nay pillar đã đổi tên khớp post_name:
     *   single-project/grand-marble.php
     *   template-parts/project-pillar/grand-marble/
     * nên không còn gì để alias. Giữ nguyên cơ chế cho lần lệch sau.
     */
    function sgh_project_slug_aliases() {
        return [];
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
                'when'  => fn() => is_post_type_archive('project') || is_tax('project_category')
                    || (function_exists('sgh_slug') && is_page(sgh_slug('projects_index'))),
                'image' => 'renovate-sol-kitchen-bar-quan-7/Sol0D7-05.webp',
            ],
        ];
    }
}

if (!function_exists('sgh_project_consolidation_redirects')) {
    /**
     * Slug dự án đã nghỉ / trùng → slug chuẩn. Route trả 301 về slug chuẩn.
     *
     * ⚠️ 2026-08-02 — chuyển từ inc/engine/pillar-routes.php sang đây.
     * Đây là DỮ LIỆU RIÊNG CỦA SITE (tên dự án cụ thể), mà quy tắc của dự án
     * là inc/engine/ không được biết brand — lint `ENGINE PURITY` trong
     * scripts/build.mjs canh đúng điều đó và đang báo 2 vi phạm ở chỗ cũ.
     * engine/ giờ chỉ còn CƠ CHẾ 301, còn DANH SÁCH nằm ở config/.
     *
     * Thêm dự án mới thì khai ở đây, không đụng engine/.
     *
     * @return array<string,string> slug cũ => slug chuẩn
     */
    function sgh_project_consolidation_redirects() {
        return [
            // Bản trùng cũ của dự án cải tạo Sol Quận 7.
            'sol-kitchen-bar' => 'renovate-sol-kitchen-bar-quan-7',

            // Grand Marble từng có HAI URL cùng trả 200, cùng <title>:
            // slug ngắn (post CPT, layout chung) và slug dài (route ảo, pillar).
            // Không cái nào noindex, slug dài không canonical → trùng nội dung
            // với Google. Nay pillar đã đổi tên khớp post_name nên slug dài chỉ
            // còn là địa chỉ cũ cần 301.
            'grand-marble-thuong-hieu-banh-cao-cap-nhat-ban' => 'grand-marble',
        ];
    }
}
