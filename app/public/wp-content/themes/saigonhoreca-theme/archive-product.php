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

<main id="primary" class="sh-archive sh-archive--product" tabindex="-1">
    <?php
    // 1. Hero — title + count + breadcrumb-style badge
    get_template_part('template-parts/archive-product/hero');
    ?>

    <div class="sh-archive__layout">
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

<?php get_footer();
