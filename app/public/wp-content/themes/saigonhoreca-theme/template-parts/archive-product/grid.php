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
            <?php get_template_part('template-parts/components/product-card'); ?>
        <?php endwhile; ?>
    <?php else : ?>
        <div class="sh-archive__empty">
            <div class="sh-archive__empty-icon"><?php echo sh_icon('package', ''); ?></div>
            <h2 class="sh-archive__empty-title"><?php _e('Chưa có sản phẩm', 'saigonhoreca'); ?></h2>
            <p class="sh-archive__empty-desc"><?php _e('Hiện tại danh mục này chưa có sản phẩm. Vui lòng quay lại sau.', 'saigonhoreca'); ?></p>
            <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="sh-archive__empty-btn">
                <?php echo sh_icon('package', ''); ?> <?php _e('Xem tất cả sản phẩm', 'saigonhoreca'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>
