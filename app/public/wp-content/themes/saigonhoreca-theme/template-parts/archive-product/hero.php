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
$page_title = $is_tax
    ? single_term_title('', false)
    : (post_type_archive_title('', false) ?: __('Sản Phẩm', 'saigonhoreca'));
?>

<section class="sh-archive__hero sh-archive__hero--compact category-hero-bg">
    <div class="sh-archive__hero-inner">
        <span class="sh-archive__badge"><?php _e('Sản phẩm', 'saigonhoreca'); ?></span>
        <h1 class="sh-archive__title"><?php echo esc_html($page_title); ?></h1>
        <?php if ($is_tax) :
            $term = get_queried_object();
            if (!empty($term->description)) : ?>
                <div class="sh-archive__desc"><?php echo wp_kses_post(wpautop($term->description)); ?></div>
            <?php endif;
        else : ?>
            <div class="sh-archive__desc"><?php _e('Thiết bị bếp công nghiệp, quầy bar và giải pháp horeca chuyên nghiệp.', 'saigonhoreca'); ?></div>
        <?php endif; ?>

        <p class="sh-archive__count">
            <?php printf(_n('Tổng <strong>%d</strong> sản phẩm', 'Tổng <strong>%d</strong> sản phẩm', $total, 'saigonhoreca'), $total); ?>
        </p>
    </div>
    <?php get_template_part('template-parts/components/wave-divider'); ?>
</section>
