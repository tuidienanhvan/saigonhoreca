<?php
/**
 * Archive product — hero (compact).
 *
 * Compact hero: badge nhỏ + title + 1-line desc + count.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

global $wp_query;
$total = (int) ($wp_query->found_posts ?? 0);
$is_tax = is_tax('product_category') || is_tax('product_brand');

if (is_search()) {
    $page_title = sprintf(sgh_t('archive_search_title'), get_search_query());
} elseif ($is_tax) {
    $page_title = single_term_title('', false);
} else {
    $page_title = sgh_t('archive_product_title');
}
?>

<section class="sh-archive__hero sh-archive__hero--compact category-hero-bg">
    <div class="sh-archive__hero-inner">
        <span class="sh-archive__badge"><?php echo esc_html(sgh_t('archive_product_badge')); ?></span>
        <h1 class="sh-archive__title"><?php echo esc_html($page_title); ?></h1>
        <?php if (is_search()) : ?>
            <div class="sh-archive__desc"><?php printf(sgh_t('archive_search_desc'), $total); ?></div>
        <?php elseif ($is_tax) :
            $term = get_queried_object();
            if (!empty($term->description)) : ?>
                <div class="sh-archive__desc"><?php echo wp_kses_post(wpautop($term->description)); ?></div>
            <?php endif;
        else : ?>
            <div class="sh-archive__desc"><?php echo esc_html(sgh_t('archive_product_desc')); ?></div>
        <?php endif; ?>

        <p class="sh-archive__count">
            <?php printf(sgh_t('archive_product_count'), $total); ?>
        </p>
    </div>
    <?php get_template_part('template-parts/_shared/wave-divider'); ?>
</section>
