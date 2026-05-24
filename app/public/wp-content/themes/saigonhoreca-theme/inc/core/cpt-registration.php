<?php
/**
 * Custom Post Types + Taxonomies (zero-plugin)
 *
 * saigonhoreca.vn organises content under URL patterns that don't fit
 * native WP `post`+`page` cleanly:
 *   /du-an/<slug>/                  case-study projects
 *   /san-pham/<slug>/               kitchen-equipment products
 *   /danh-muc-san-pham/<cat>/       product category archives
 *
 * Rather than depend on a CPT plugin (WooCommerce / CPT-UI / etc.) we
 * register lightweight native CPTs here, matching the saigonhoreca-theme
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
            'has_archive'         => 'du-an',
            'show_in_rest'        => true, // Gutenberg / REST API edit support
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-portfolio',
            'supports'            => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'],
            'rewrite'             => ['slug' => 'du-an', 'with_front' => false],
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
            'has_archive'         => 'san-pham',
            'show_in_rest'        => true,
            'menu_position'       => 6,
            'menu_icon'           => 'dashicons-cart',
            'supports'            => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'],
            'rewrite'             => ['slug' => 'san-pham', 'with_front' => false],
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
            'rewrite'           => ['slug' => 'danh-muc-du-an', 'with_front' => false],
        ]);

        // ── Taxonomy: product_category (Danh mục sản phẩm) ────────
        // The scraped site uses /danh-muc-san-pham/<slug>/ for these.
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
            'rewrite'           => ['slug' => 'danh-muc-san-pham', 'with_front' => false],
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
    }
}
add_action('init', 'shr_register_taxonomies', 0);

if (!function_exists('shr_flush_rewrite_on_theme_switch')) {
    /**
     * Flush rewrite rules when this theme is activated so the new CPT
     * slugs (/du-an/, /san-pham/, /danh-muc-san-pham/, /thuong-hieu/)
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
