<?php
/**
 * single.php part — khối bài viết liên quan (.sh-single__related) cho post.
 * Chứa luôn WP_Query: ưu tiên cùng category, fallback sang cùng tag.
 * ID bài hiện tại nhận qua $args['post_id'].
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : get_the_ID();

$categories = wp_get_post_categories($post_id);
$post_tags  = wp_get_post_tags($post_id, ['fields' => 'ids']);
$related    = new WP_Query([
    'category__in'        => $categories,
    'post__not_in'        => [$post_id],
    'posts_per_page'      => 3,
    'ignore_sticky_posts' => 1,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'no_found_rows'       => true,
]);
if (!$related->have_posts() && !empty($post_tags)) {
    $related = new WP_Query([
        'tag__in'             => $post_tags,
        'post__not_in'        => [$post_id],
        'posts_per_page'      => 3,
        'ignore_sticky_posts' => 1,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'no_found_rows'       => true,
    ]);
}
if ($related->have_posts()) :
    $lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';
    $related_titles = [
        'vi' => 'Bài Viết Liên Quan',
        'en' => 'Related Articles',
        'ja' => '関連記事',
    ];
?>
    <div class="sh-single__related">
        <h3 class="sh-single__related-title"><?php echo esc_html($related_titles[$lang] ?? $related_titles['vi']); ?></h3>
        <div class="sh-single__related-grid">
            <?php while ($related->have_posts()) : $related->the_post(); ?>
                <?php get_template_part('template-parts/_shared/post-card'); ?>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
<?php endif; ?>
