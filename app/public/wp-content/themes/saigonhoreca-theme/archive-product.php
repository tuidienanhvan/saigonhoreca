<?php
/**
 * CPT `product` archive template — /san-pham/ + product_category + product_brand.
 *
 * Orchestrates template-parts/archive-product/*. Production saigonhoreca.vn
 * used Astra's built-in WooCommerce archive (no Elementor on /san-pham/),
 * so this template is built fresh in the saigonhoreca-theme style: hero
 * banner + filter sidebar + responsive card grid + pagination.
 *
 * @package SaigonHoreca
 */

get_header(); ?>

<style id="sh-archive-layout-fix">
/* An toàn: khi sidebar không render (vd prod chưa có category), layout về 1 cột
   để grid sản phẩm chiếm full chiều rộng thay vì dồn vào cột 260px. */
.sh-archive__layout:has(> .sh-archive__main:only-child){grid-template-columns:1fr !important;}
</style>

<main id="primary" class="sh-archive sh-archive--product" tabindex="-1">
    <?php
    // 1. Hero — title + count + breadcrumb-style badge
    get_template_part('template-parts/archive-product/hero');
    ?>

    <div class="sh-archive__tabs">
        <button class="sh-archive__tab-btn is-active" data-target="products"><?php echo esc_html(sgh_t('archive_tab_products')); ?></button>
        <button class="sh-archive__tab-btn" data-target="categories"><?php echo esc_html(sgh_t('archive_tab_categories')); ?></button>
    </div>

    <div class="sh-archive__layout sh-active-tab--products">
        <?php
        // 2. Sidebar — category + brand filters
        get_template_part('template-parts/archive-product/filter-sidebar');
        ?>

        <div class="sh-archive__main">
            <?php
            // 2.5 Search form — moved from hero to keep hero cleaner
            get_template_part('template-parts/archive-product/search-form');

            // 3. Grid — WP_Query loop renders product cards
            get_template_part('template-parts/archive-product/grid');

            // 4. Pagination
            get_template_part('template-parts/archive-product/pagination');
            ?>
        </div>
    </div>
</main>

<script nonce="<?php echo esc_attr($GLOBALS['sgh_csp_nonce'] ?? ''); ?>">
document.addEventListener('DOMContentLoaded', function () {
    var tabs = document.querySelectorAll('.sh-archive__tab-btn');
    var layout = document.querySelector('.sh-archive__layout');
    if (!tabs.length || !layout) return;

    // Tối ưu logic: Khi đang ở trang danh mục/thương hiệu cụ thể, luôn đảm bảo hiển thị tab Sản phẩm mặc định
    // 'tax-product_cat' đã bỏ 2026-08-03 — taxonomy product_cat gỡ khỏi
    // cpt-registration.php (0 term, không gì ghi vào).
    var isTaxonomyPage = document.body.classList.contains('tax-product_category') ||
                         document.body.classList.contains('tax-product_brand');
    if (isTaxonomyPage) {
        var productsTab = document.querySelector('.sh-archive__tab-btn[data-target="products"]');
        var categoriesTab = document.querySelector('.sh-archive__tab-btn[data-target="categories"]');
        if (productsTab && categoriesTab) {
            categoriesTab.classList.remove('is-active');
            productsTab.classList.add('is-active');
            layout.classList.remove('sh-active-tab--categories');
            layout.classList.add('sh-active-tab--products');
        }
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('is-active'); });
            this.classList.add('is-active');

            var target = this.getAttribute('data-target');
            if (target === 'products') {
                layout.classList.remove('sh-active-tab--categories');
                layout.classList.add('sh-active-tab--products');
            } else {
                layout.classList.remove('sh-active-tab--products');
                layout.classList.add('sh-active-tab--categories');
            }
            
            // Smooth scroll to content top (with offset for sticky header + tabs)
            var headerOffset = 120;
            var elementPosition = layout.getBoundingClientRect().top;
            var offsetPosition = elementPosition + window.scrollY - headerOffset;
            
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        });
    });
});
</script>

<?php get_footer();
