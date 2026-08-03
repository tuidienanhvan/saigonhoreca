<?php
/**
 * Custom Post Types + Taxonomies (zero-plugin)
 *
 * The production site organises content under URL patterns that don't fit
 * native WP `post`+`page` cleanly (case-study projects, kitchen-equipment
 * products, product category archives) — see inc/config/routes.php
 * `sgh_bilingual_slug_map()` for the actual route slugs consumed below.
 *
 * Rather than depend on a CPT plugin (WooCommerce / CPT-UI / etc.) we
 * register lightweight native CPTs here, matching this theme's
 * "zero-plugin" architecture. Public-facing labels are in Vietnamese.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('shr_register_cpts')) {
    function shr_register_cpts() {
        // PRODUCTION SAFETY: nếu plugin khác (WooCommerce) đã đăng ký
        // CPT 'product' / 'project' rồi thì BỎ QUA, không override. Theme
        // archive-product.php / archive-project.php vẫn render cho CPT
        // do plugin owner đăng ký — template lookup theo post_type là OK.
        $skip_project = post_type_exists('project');
        $skip_product = post_type_exists('product');

        // Route slug DUY NHẤT lấy từ inc/config/routes.php — không hardcode
        // literal lần thứ 2 ở đây (single source of truth với sgh_slug()).
        $map          = function_exists('sgh_bilingual_slug_map') ? sgh_bilingual_slug_map() : [];
        $slug_project = $map['projects_index'][0] ?? 'project';
        $slug_product = $map['products_index'][0] ?? 'product';

        // ── CPT: project (Dự án) ──────────────────────────────────
        if (!$skip_project) register_post_type('project', [
            'labels' => [
                'name'               => 'Dự án',
                'singular_name'      => 'Dự án',
                'menu_name'          => 'Dự án',
                'add_new_item'       => 'Thêm dự án mới',
                'edit_item'          => 'Chỉnh sửa dự án',
                'view_item'          => 'Xem dự án',
                'all_items'          => 'Tất cả dự án',
                'search_items'       => 'Tìm dự án',
                'not_found'          => 'Không có dự án nào',
                'not_found_in_trash' => 'Không có dự án trong thùng rác',
            ],
            'public'              => true,
            'has_archive'         => $slug_project,
            'show_in_rest'        => true, // Gutenberg / REST API edit support
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-portfolio',
            'supports'            => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'],
            'rewrite'             => ['slug' => $slug_project, 'with_front' => false],
            'capability_type'     => 'post',
            'hierarchical'        => false,
        ]);

        // ── CPT: product (Sản phẩm) ───────────────────────────────
        if (!$skip_product) register_post_type('product', [
            'labels' => [
                'name'               => 'Sản phẩm',
                'singular_name'      => 'Sản phẩm',
                'menu_name'          => 'Sản phẩm',
                'add_new_item'       => 'Thêm sản phẩm mới',
                'edit_item'          => 'Chỉnh sửa sản phẩm',
                'view_item'          => 'Xem sản phẩm',
                'all_items'          => 'Tất cả sản phẩm',
                'search_items'       => 'Tìm sản phẩm',
                'not_found'          => 'Không có sản phẩm nào',
                'not_found_in_trash' => 'Không có sản phẩm trong thùng rác',
            ],
            'public'              => true,
            'has_archive'         => $slug_product,
            'show_in_rest'        => true,
            'menu_position'       => 6,
            'menu_icon'           => 'dashicons-cart',
            'supports'            => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'],
            'rewrite'             => ['slug' => $slug_product, 'with_front' => false],
            'capability_type'     => 'post',
            'hierarchical'        => false,
        ]);
    }
}
add_action('init', 'shr_register_cpts', 0);

if (!function_exists('shr_register_taxonomies')) {
    function shr_register_taxonomies() {
        // PRODUCTION SAFETY: skip nếu taxonomy đã được plugin (WooCommerce
        // dùng `product_cat` chứ không phải `product_category`, nhưng vẫn
        // check để chắc) hay theme khác đăng ký rồi.
        $skip_pcat   = taxonomy_exists('project_category');
        $skip_prdcat = taxonomy_exists('product_category');
        $skip_brand  = taxonomy_exists('product_brand');

        $map           = function_exists('sgh_bilingual_slug_map') ? sgh_bilingual_slug_map() : [];
        $slug_proj_cat = $map['projects_cat'][0] ?? 'project-category';
        $slug_prod_cat = $map['products_cat'][0] ?? 'product-category';

        // ── Taxonomy: project_category (Hạng mục dự án) ──────────
        if (!$skip_pcat) register_taxonomy('project_category', ['project'], [
            'labels' => [
                'name'              => 'Hạng mục dự án',
                'singular_name'     => 'Hạng mục dự án',
                'search_items'      => 'Tìm hạng mục',
                'all_items'         => 'Tất cả hạng mục',
                'edit_item'         => 'Sửa hạng mục',
                'update_item'       => 'Cập nhật hạng mục',
                'add_new_item'      => 'Thêm hạng mục mới',
                'new_item_name'     => 'Tên hạng mục mới',
                'menu_name'         => 'Hạng mục',
            ],
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'public'            => true,
            'rewrite'           => ['slug' => $slug_proj_cat, 'with_front' => false],
        ]);

        // ── Taxonomy: product_category (Danh mục sản phẩm) ────────
        // Route slug lấy từ sgh_bilingual_slug_map()['products_cat'].
        if (!$skip_prdcat) register_taxonomy('product_category', ['product'], [
            'labels' => [
                'name'              => 'Danh mục sản phẩm',
                'singular_name'     => 'Danh mục sản phẩm',
                'search_items'      => 'Tìm danh mục',
                'all_items'         => 'Tất cả danh mục',
                'edit_item'         => 'Sửa danh mục',
                'update_item'       => 'Cập nhật danh mục',
                'add_new_item'      => 'Thêm danh mục mới',
                'new_item_name'     => 'Tên danh mục mới',
                'menu_name'         => 'Danh mục',
            ],
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'public'            => true,
            'rewrite'           => ['slug' => $slug_prod_cat, 'with_front' => false],
        ]);

        // ── Taxonomy: product_brand (Thương hiệu) ─────────────────
        if (!$skip_brand) register_taxonomy('product_brand', ['product'], [
            'labels' => [
                'name'              => 'Thương hiệu',
                'singular_name'     => 'Thương hiệu',
                'search_items'      => 'Tìm thương hiệu',
                'all_items'         => 'Tất cả thương hiệu',
                'menu_name'         => 'Thương hiệu',
            ],
            'hierarchical'      => false,
            'show_in_rest'      => true,
            'public'            => true,
            'rewrite'           => ['slug' => 'thuong-hieu', 'with_front' => false],
        ]);

        // [GỠ 2026-08-03] Taxonomy `product_cat` (WooCommerce legacy).
        //
        // Từng đăng ký lại để "hồi sinh" danh mục cũ sau khi tắt WooCommerce.
        // Kiểm tra trên DB thật: product_cat có **0 term**, product_category có
        // 80 term / 388 lượt gán — toàn bộ sản phẩm đã nằm ở taxonomy chuẩn.
        // Trình import cũng KHÔNG ghi vào đây: pi-migrate/pi-api đọc CỘT CSV tên
        // `product_cat` nhưng ghi ra taxonomy `product_category`
        // (class-catalog-importer.php:399-406, comment nói rõ "never written here").
        //
        // Nên nó chỉ còn tạo prefix URL /product-category/ trùng vai với
        // /danh-muc-san-pham/ của product_category. Gỡ kèm:
        //   - filter template_include phục vụ riêng nó
        //   - taxonomy-product_cat.php
        //   - nhánh is_tax('product_cat') trong inc/config/routes.php
        //   - kiểm tra body class 'tax-product_cat' trong archive-product.php
        // Cần lại thì git revert commit này.
    }
}
add_action('init', 'shr_register_taxonomies', 0);

if (!function_exists('shr_flush_rewrite_on_theme_switch')) {
    /**
     * Flush rewrite rules when this theme is activated so the new CPT
     * slugs (project/product archives + their category taxonomies)
     * are routed correctly without requiring the user to manually re-save
     * permalinks in wp-admin.
     */
    function shr_flush_rewrite_on_theme_switch() {
        shr_register_cpts();
        shr_register_taxonomies();
        flush_rewrite_rules();
    }
}
add_action('after_switch_theme', 'shr_flush_rewrite_on_theme_switch');

/**
 * Auto-fix conflicting attachment slugs that overlap with product categories.
 * When an image attachment has the exact same slug as a product category,
 * WordPress canonical redirect might point to the image instead of the product list.
 */
add_action('admin_init', function () {
    // Check if we already did this check recently to save DB queries
    if (get_transient('sgh_slug_conflict_fixed')) {
        return;
    }

    global $wpdb;
    $taxonomies = ['product_category', 'product_cat'];
    $terms = get_terms([
        'taxonomy' => $taxonomies,
        'hide_empty' => false
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        set_transient('sgh_slug_conflict_fixed', 1, DAY_IN_SECONDS);
        return;
    }

    $fixed = false;
    foreach ($terms as $t) {
        $attachments = $wpdb->get_results($wpdb->prepare(
            "SELECT ID, post_name FROM $wpdb->posts WHERE post_name = %s AND post_type = 'attachment'",
            $t->slug
        ));

        foreach ($attachments as $att) {
            $new_slug = $att->post_name . '-img';
            $wpdb->update(
                $wpdb->posts,
                ['post_name' => $new_slug],
                ['ID' => $att->ID]
            );
            $fixed = true;
        }
    }

    if ($fixed) {
        flush_rewrite_rules();
    }
    set_transient('sgh_slug_conflict_fixed', 1, DAY_IN_SECONDS);
});

