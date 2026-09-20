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
            'projects_index'  => ['du-an',                                          'projects',            'ja' => 'project'],
            'projects_cat'    => ['danh-muc-du-an',                                 'project-category',    'ja' => 'project-category'],
            'about'           => ['ve-saigon-horeca',                               'about',               'ja' => 'about-saigon-horeca'],
            'products_index'  => ['san-pham',                                       'products',            'ja' => 'products'],
            'products_cat'    => ['danh-muc-san-pham',                              'product-category',    'ja' => 'product-category'],
            'news'            => ['tin-tuc',                                        'blog',                'ja' => 'blog'],
            'contact'         => ['lien-he',                                        'contact',             'ja' => 'contact-saigon-horeca'],
            'consult_kitchen' => ['tu-van-thiet-ke-bep-nha-hang-bep-cong-nghiep',   'kitchen-consulting',  'ja' => 'kitchen-consulting'],
            'consult_bar'     => ['cung-cap-thiet-bi-tu-van-thiet-ke-cho-quay-bar', 'bar-equipment',        'ja' => 'bar-equipment'],
            'product_brand'   => ['thuong-hieu',                                    'brand',                'ja' => 'brand'],
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
                'when'   => fn() => is_page_template('page-templates/page-about.php') || is_page('ve-saigon-horeca') || is_page('gioi-thieu'),
                'bundle' => 'about',
            ],
            [
                'when'   => fn() => is_page_template('page-templates/page-contact.php') || is_page('lien-he'),
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
                // is_tax('product_cat') đã bỏ 2026-08-03 — taxonomy gỡ khỏi
                // cpt-registration.php (0 term, importer ghi vào product_category).
                'when' => fn() => is_post_type_archive('product')
                    || is_tax('product_category')
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
                'when'  => fn() => is_page_template('page-templates/page-about.php') || is_page('ve-saigon-horeca') || is_page('gioi-thieu'),
                'image' => 'saigonhoreca/bia-bep-cong-nghiep-inox.webp',
            ],
            [
                'when'  => fn() => is_page_template('page-templates/page-contact.php') || is_page('lien-he'),
                'image' => 'saigonhoreca/Saigon-Horeca-thiet-bi-bep-cong-nghiep.webp',
            ],
            [
                'when'  => fn() => is_post_type_archive('project') || is_tax('project_category')
                    || (function_exists('sgh_slug') && is_page(sgh_slug('projects_index'))),
                'image' => 'renovate-sol-kitchen-bar-quan-7/renovate-sol-kitchen-bar-quan-7-thumbnail-project-cover-1024x576.webp',
            ],
        ];
    }
}

// [GỠ 2026-08-03] sgh_project_consolidation_redirects() — bảng 301 slug cũ.
//
// Quyết định của chủ site: KHÔNG giữ fallback. Mỗi dự án có đúng MỘT URL, không
// alias, không bảng ánh xạ phải nhớ. Slug cũ trả 404 thật thay vì âm thầm
// chuyển hướng.
//
// Gỡ kèm (nếu không thì chúng thành trang sống mồ côi khi mất 301):
//   single-project/sol-kitchen-bar.php|.css      — 0 post trong DB
//   template-parts/project-pillar/sol-kitchen-bar/
// và nhánh đọc bảng này trong inc/engine/pillar-routes.php.
//
// Đánh đổi đã nói rõ với user: 10 URL cũ (8 slug vừa rút gọn + 2 bản trùng cũ)
// nay 404. Nếu Google đã index thì mất thứ hạng của những URL đó.

if (!function_exists('sgh_route_description_rules')) {
    /**
     * Mô tả SEO khai sẵn theo route — cho các trang KHÔNG có post_content.
     *
     * ⚠️ 2026-08-03 — vì sao cần: 3 page dùng page-template (Trang Chủ, Về
     * Saigon Horeca, Liên hệ) có post_content RỖNG, giao diện do file PHP dựng.
     * Bộ dựng description không có gì để lấy -> rơi vào pad_min() và cả 3 trang
     * nhận CÙNG một đoạn quảng cáo chung. Google Search Console báo trùng meta
     * description, và snippet không mô tả đúng từng trang.
     *
     * Nội dung ở đây là DỮ LIỆU RIÊNG CỦA SITE nên đặt tại config/, không đặt
     * trong inc/engine/ (lint ENGINE PURITY).
     *
     * Mỗi rule: ['when' => callable():bool, 'desc' => string].
     * Rule ĐẦU TIÊN khớp sẽ thắng — giữ thứ tự từ cụ thể tới tổng quát.
     * Chỉ dùng khi trang chưa có mô tả riêng (excerpt / _pi_seo).
     *
     * @return array<int,array{when:callable,desc:string}>
     */
    function sgh_route_description_rules() {
        $lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';

        if ($lang === 'ja') {
            return [
                [
                    'when' => 'is_front_page',
                    'desc' => 'Saigon Horeca（サイゴンホレカ）は、レストラン、ホテル、カフェ向けに業務用厨房機器およびバー設備の設計・施工・導入をトータルサポート。施工実績30件以上。',
                ],
                [
                    'when' => fn() => is_page_template('page-templates/page-about.php') || is_page('ve-saigon-horeca') || is_page('about'),
                    'desc' => 'Saigon Horeca（サイゴンホレカ）の企業情報 — ベトナムにおける業務用厨房設備・バー設備の設計・施工会社。企業理念、施工実績、サービス概要をご案内。',
                ],
                [
                    'when' => fn() => is_page_template('page-templates/page-contact.php') || is_page('lien-he') || is_page('contact'),
                    'desc' => 'Saigon Horeca（サイゴンホレカ）へのお問い合わせ。業務用厨房およびバー設備の無料設計相談、ショールーム住所、お見積もり依頼はこちら。',
                ],
            ];
        }

        if ($lang === 'en') {
            return [
                [
                    'when' => 'is_front_page',
                    'desc' => 'Saigon Horeca specializes in turnkey commercial kitchen and bar equipment design, installation, and supply for restaurants, hotels, and cafés. Over 30 delivered projects.',
                ],
                [
                    'when' => fn() => is_page_template('page-templates/page-about.php') || is_page('ve-saigon-horeca') || is_page('about'),
                    'desc' => 'About Saigon Horeca — Professional commercial kitchen and bar design, procurement, and installation in Vietnam. Company profile, core values, and key projects.',
                ],
                [
                    'when' => fn() => is_page_template('page-templates/page-contact.php') || is_page('lien-he') || is_page('contact'),
                    'desc' => 'Contact Saigon Horeca for free consultation on commercial kitchen and bar design. Hotline, showroom address, and quote inquiry form.',
                ],
            ];
        }

        return [
            [
                'when' => 'is_front_page',
                'desc' => 'Saigon Horeca thiết kế, thi công và cung cấp thiết bị bếp công nghiệp, quầy bar trọn gói cho nhà hàng, khách sạn, quán café. Hơn 30 dự án đã bàn giao.',
            ],
            [
                'when' => fn() => is_page_template('page-templates/page-about.php') || is_page('ve-saigon-horeca'),
                'desc' => 'Giới thiệu Saigon Horeca — đơn vị tư vấn thiết kế và thi công bếp công nghiệp, quầy bar tại TP.HCM. Năng lực, quy trình và các dự án tiêu biểu đã thực hiện.',
            ],
            [
                'when' => fn() => is_page_template('page-templates/page-contact.php') || is_page('lien-he'),
                'desc' => 'Liên hệ Saigon Horeca để được tư vấn miễn phí thiết kế bếp công nghiệp và quầy bar. Hotline, địa chỉ showroom và biểu mẫu gửi yêu cầu báo giá.',
            ],
        ];
    }
}
