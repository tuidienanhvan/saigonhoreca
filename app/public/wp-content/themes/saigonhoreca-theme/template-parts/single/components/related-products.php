<?php
/**
 * single.php part — khối sản phẩm liên quan (.sh-single__related) cho product CPT.
 * Chứa luôn WP_Query lọc theo taxonomy product_category.
 * Nhận $args['post_id'] và $args['pcats'] (mảng term product_category) từ single.php.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : get_the_ID();
$pcats   = isset($args['pcats']) ? $args['pcats'] : wp_get_post_terms($post_id, 'product_category');

if (!is_wp_error($pcats) && !empty($pcats)) {
    $related = new WP_Query([
        'post_type'           => 'product',
        'posts_per_page'      => 4,
        'post__not_in'        => [$post_id],
        'ignore_sticky_posts' => 1,
        'no_found_rows'       => true,
        'tax_query'           => [[
            'taxonomy' => 'product_category',
            'field'    => 'term_id',
            'terms'    => wp_list_pluck($pcats, 'term_id'),
        ]],
    ]);
    if ($related->have_posts()) : ?>
        <div class="sh-single__related">
            <h3 class="sh-single__related-title"><?php echo esc_html(sgh_t('product_related_title')); ?></h3>
            <div class="sh-single__related-grid">
                <?php while ($related->have_posts()) : $related->the_post(); ?>
                    <a class="sh-product-card" href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium', ['class' => 'sh-product-card__img', 'loading' => 'lazy']); ?>
                        <?php endif; ?>
                        <h4 class="sh-product-card__title"><?php the_title(); ?></h4>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    <?php endif;
}
?>
