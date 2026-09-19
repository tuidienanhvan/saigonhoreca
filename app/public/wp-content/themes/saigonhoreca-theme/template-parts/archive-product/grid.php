<?php
/**
 * Archive product — grid.
 *
 * Renders the main WP_Query loop as a responsive card grid. Uses
 * components/product-card.php for product-specific card layout.
 * Empty state shows when no products match the current archive filter.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;
?>

<div class="sh-archive__grid">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('template-parts/_shared/product-card'); ?>
        <?php endwhile; ?>
    <?php else : ?>
        <div class="sh-archive__empty">
            <div class="sh-archive__empty-icon"><?php echo sh_icon('package', ''); ?></div>
            <h2 class="sh-archive__empty-title"><?php echo esc_html(sgh_t('archive_empty_title')); ?></h2>
            <p class="sh-archive__empty-desc"><?php echo esc_html(sgh_t('archive_empty_desc')); ?></p>
            <?php
            $current_lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';
            $archive_url = home_url($current_lang === 'vi' ? '/san-pham/' : "/{$current_lang}/products/");
            ?>
            <a href="<?php echo esc_url($archive_url); ?>" class="sh-archive__empty-btn">
                <?php echo sh_icon('package', ''); ?> <?php echo esc_html(sgh_t('archive_empty_btn')); ?>
            </a>
        </div>
    <?php endif; ?>
</div>
