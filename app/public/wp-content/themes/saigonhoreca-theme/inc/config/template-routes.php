<?php
/**
 * TRANG DO TEMPLATE DỰNG — bản khai duy nhất.
 *
 * ⚠️ 2026-08-03 — vì sao cần file này:
 *
 * Dashboard liệt kê trang bằng WP_Query(post_type=page) nên chỉ thấy bài CÓ THẬT
 * trong wp_posts — hiện 3 page. Các route do template dựng (/du-an/, /san-pham/,
 * /category/tin-tuc/, ...) KHÔNG có bản ghi nào trong wp_posts nên không bao giờ
 * hiện ra; người quản trị không biết chúng tồn tại, cũng không sửa được SEO.
 *
 * KHÔNG tạo Page cho archive được: page slug `du-an` sẽ đụng has_archive =>
 * 'du-an' của CPT project — hai thứ giành một URL. Đúng loại lỗi đã xử lý ở
 * Grand Marble (2 URL cùng 200) và ở category `du-an` (đã xoá).
 *
 * NƠI CHỨA NỘI DUNG khác nhau theo loại route — đây là điểm dễ hiểu nhầm:
 *   post     -> wp_posts.post_content        (page, dự án)  — có sẵn
 *   term     -> wp_term_taxonomy.description (category, product_category,
 *                                             product_brand) — WordPress có sẵn
 *   archive  -> wp_options                   (post_type_archive) — KHÔNG có
 *               chỗ native, phải tự lưu
 *
 * BỐN CONSUMER dùng chung bản khai này, để không sinh 4 danh sách lệch nhau:
 *   1. pi-api            -> /pi/v1/template-routes cho dashboard hiển thị
 *   2. sync-page-content -> biết render URL nào, ghi nội dung vào đâu
 *   3. seo-meta.php      -> meta description
 *   4. seo-meta.php      -> og:image fallback
 *
 * KHÔNG khai single.php / single-project/*.php — chúng render bài đã có sẵn
 * post_content và đã hiện trong danh sách Bài viết / Dự án.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('sgh_get_template_routes')) {
    /**
     * Mỗi entry:
     *   key      — định danh ổn định (khoá lưu option, khoá đối chiếu)
     *   label    — tên hiển thị trên dashboard
     *   url      — callable trả URL (gọi muộn: sgh_url() cần init xong)
     *   template — file PHP render, tính từ gốc theme
     *   store    — nơi chứa nội dung: 'post' | 'term' | 'option' | 'none'
     *   ref      — post_slug (store=post) | [taxonomy, term_slug] (store=term)
     *   when     — callable nhận biết đang ở route này
     *   desc     — meta description; '' = để nguồn khác lo
     *   image    — ảnh OG dưới uploads (qua sgh_img); '' = dùng mặc định
     *
     * @return array<int,array<string,mixed>>
     */
    function sgh_get_template_routes() {
        return [
            [
                'key'      => 'front_page',
                'label'    => 'Trang Chủ',
                'url'      => static fn() => home_url('/'),
                'template' => 'front-page.php',
                'store'    => 'post',
                'ref'      => 'home',
                'when'     => 'is_front_page',
                'desc'     => 'Saigon Horeca thiết kế, thi công và cung cấp thiết bị bếp công nghiệp, quầy bar trọn gói cho nhà hàng, khách sạn, quán café. Hơn 30 dự án đã bàn giao.',
                'image'    => 'saigonhoreca/Saigon-Horeca-thiet-bi-bep-cong-nghiep.webp',
            ],
            [
                'key'      => 'about',
                'label'    => 'Về Saigon Horeca',
                'url'      => static fn() => sgh_url('about'),
                'template' => 'page-templates/page-gioi-thieu.php',
                'store'    => 'post',
                'ref'      => 've-saigon-horeca',
                'when'     => static fn() => is_page_template('page-templates/page-gioi-thieu.php') || is_page('ve-saigon-horeca'),
                'desc'     => 'Giới thiệu Saigon Horeca — đơn vị tư vấn thiết kế và thi công bếp công nghiệp, quầy bar tại TP.HCM. Năng lực, quy trình và các dự án tiêu biểu đã thực hiện.',
                'image'    => 'saigonhoreca/bia-bep-cong-nghiep-inox.webp',
            ],
            [
                'key'      => 'contact',
                'label'    => 'Liên hệ',
                'url'      => static fn() => sgh_url('contact'),
                'template' => 'page-templates/page-lien-he.php',
                'store'    => 'post',
                'ref'      => 'lien-he',
                'when'     => static fn() => is_page_template('page-templates/page-lien-he.php') || is_page('lien-he'),
                'desc'     => 'Liên hệ Saigon Horeca để được tư vấn miễn phí thiết kế bếp công nghiệp và quầy bar. Hotline, địa chỉ showroom và biểu mẫu gửi yêu cầu báo giá.',
                'image'    => 'saigonhoreca/Saigon-Horeca-thiet-bi-bep-cong-nghiep.webp',
            ],

            // ── Từ đây: KHÔNG có post trong wp_posts ─────────────────────
            [
                'key'      => 'projects_archive',
                'label'    => 'Dự Án (danh sách)',
                'url'      => static fn() => sgh_url('projects_index'),
                'template' => 'archive-project.php',
                'store'    => 'option',
                'ref'      => 'project',
                'when'     => static fn() => is_post_type_archive('project'),
                'desc'     => 'Hơn 30 dự án bếp công nghiệp và quầy bar do Saigon Horeca thiết kế, thi công: nhà hàng, khách sạn, café, canteen nhà máy và trường học.',
                // Bản cũ (sgh_route_hero_preload_rules) trỏ
                // renovate-sol-kitchen-bar-quan-7/Sol0D7-05.webp — file KHÔNG
                // tồn tại, HTTP 404. Đổi sang ảnh cover có thật.
                'image'    => 'renovate-sol-kitchen-bar-quan-7/renovate-sol-kitchen-bar-quan-7-thumbnail-project-cover-1024x576.webp',
            ],
            [
                'key'      => 'products_archive',
                'label'    => 'Sản Phẩm (danh sách)',
                'url'      => static fn() => sgh_url('products_index'),
                'template' => 'archive-product.php',
                'store'    => 'option',
                'ref'      => 'product',
                'when'     => static fn() => is_post_type_archive('product'),
                'desc'     => 'Thiết bị bếp công nghiệp và quầy bar chính hãng: bếp Âu, lò nướng, tủ lạnh, máy rửa chén, thiết bị pha chế. Bảo hành chính hãng, lắp đặt toàn quốc.',
                'image'    => 'saigonhoreca/SGH-banner-01.webp',
            ],
            [
                'key'      => 'news_archive',
                'label'    => 'Tin Tức (chuyên mục)',
                'url'      => static fn() => sgh_url('news'),
                'template' => 'category.php',
                'store'    => 'term',
                'ref'      => ['category', 'tin-tuc'],
                'when'     => static fn() => is_category(),
                'desc'     => '',   // để trống -> lấy mô tả của chính term
                'image'    => 'saigonhoreca/bia-bep-cong-nghiep-inox.webp',
            ],
            [
                'key'      => 'product_category_archive',
                'label'    => 'Danh mục sản phẩm',
                'url'      => static fn() => home_url('/' . sgh_slug('products_cat') . '/'),
                'template' => 'taxonomy-product_category.php',
                'store'    => 'none',   // nhiều term, mỗi term tự có description
                'ref'      => 'product_category',
                'when'     => static fn() => is_tax('product_category'),
                'desc'     => '',
                'image'    => 'saigonhoreca/SGH-banner-01.webp',
            ],
            [
                'key'      => 'product_brand_archive',
                'label'    => 'Thương hiệu',
                'url'      => static fn() => home_url('/thuong-hieu/'),
                'template' => 'taxonomy-product_brand.php',
                'store'    => 'none',
                'ref'      => 'product_brand',
                'when'     => static fn() => is_tax('product_brand'),
                'desc'     => '',
                'image'    => 'saigonhoreca/SGH-banner-01.webp',
            ],
            [
                'key'      => 'search',
                'label'    => 'Kết quả tìm kiếm',
                'url'      => static fn() => home_url('/?s=bep'),
                'template' => 'search.php',
                'store'    => 'none',
                'ref'      => null,
                'when'     => 'is_search',
                'desc'     => '',
                'image'    => '',
            ],
            [
                'key'      => 'not_found',
                'label'    => 'Không tìm thấy (404)',
                'url'      => static fn() => home_url('/sgh-404-probe/'),
                'template' => '404.php',
                'store'    => 'none',
                'ref'      => null,
                'when'     => 'is_404',
                'desc'     => '',
                'image'    => '',
            ],
        ];
    }
}

if (!function_exists('sgh_archive_content_option')) {
    /** Khoá option chứa nội dung của một post_type_archive. */
    function sgh_archive_content_option($post_type) {
        return 'sgh_archive_content_' . sanitize_key($post_type);
    }
}

if (!function_exists('sgh_get_template_route')) {
    /** Lấy một route theo key. */
    function sgh_get_template_route($key) {
        foreach (sgh_get_template_routes() as $r) {
            if ($r['key'] === $key) return $r;
        }
        return null;
    }
}

if (!function_exists('sgh_current_template_route')) {
    /** Route khớp ngữ cảnh hiện tại (rule đầu tiên khớp thắng). */
    function sgh_current_template_route() {
        foreach (sgh_get_template_routes() as $r) {
            if (empty($r['when']) || !is_callable($r['when'])) continue;
            if (($r['when'])()) return $r;
        }
        return null;
    }
}

if (!function_exists('sgh_route_og_shot_url')) {
    /**
     * URL ảnh chụp thật của một route, nếu dashboard đã chụp.
     *
     * Bản đồ key -> attachment ID do dashboard ghi qua
     * POST /pi/v1/template-routes/og-map (xem Pi_Api_Template_Routes).
     * WordPress không tự chụp được — PHP không có engine render HTML; đã thử
     * imagegrabwindow() + COM IE và chỉ ra ảnh trắng trên Windows 11.
     *
     * @param string $key key của route.
     * @return string URL ảnh, hoặc '' nếu chưa chụp / ảnh đã bị xoá.
     */
    function sgh_route_og_shot_url($key) {
        if (!$key) return '';
        $map = get_option('sgh_template_route_og_map', []);
        if (!is_array($map) || empty($map[$key])) return '';

        $id = (int) $map[$key];
        // Ảnh có thể đã bị xoá khỏi thư viện sau khi ghi bản đồ — kiểm lại,
        // đừng để og:image trỏ vào ID chết (đúng lỗi og-default.jpg 404 cũ).
        if ($id <= 0 || get_post_type($id) !== 'attachment') return '';

        $url = wp_get_attachment_url($id);
        return $url ?: '';
    }
}
