<?php
/**
 * Template Part: Product Card — CPT `product` archive grid card.
 *
 * Minimal dark-luxe card: thumbnail, category tag, title, short excerpt,
 * "Xem chi tiết" CTA. No date badge / author (not relevant for products).
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

$post_id = $args['post_id'] ?? get_the_ID();
$url     = $args['url']     ?? get_permalink($post_id);
$title   = $args['title']   ?? get_the_title($post_id);
$excerpt = $args['excerpt'] ?? wp_trim_words(get_the_excerpt($post_id) ?: get_the_content(null, false, $post_id), 18);

// Primary category
$cats     = wp_get_post_terms($post_id, 'product_category', ['number' => 1]);
$cat_name = (!is_wp_error($cats) && !empty($cats)) ? $cats[0]->name : '';
$cat_url  = (!is_wp_error($cats) && !empty($cats)) ? get_term_link($cats[0]) : '';

// Primary brand
$brands    = wp_get_post_terms($post_id, 'product_brand', ['number' => 1]);
$brand_name = (!is_wp_error($brands) && !empty($brands)) ? $brands[0]->name : '';

// Thumbnail
if (isset($args['thumb_url'])) {
    $img_url = $args['thumb_url'];
    $img_id  = 0;
} elseif (function_exists('saigonhouse_get_post_thumbnail_data')) {
    $thumb   = saigonhouse_get_post_thumbnail_data($post_id, 'medium_large');
    $img_url = $thumb['url'];
    $img_id  = $thumb['id'];
} else {
    $img_id  = (int) get_post_thumbnail_id($post_id);
    $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'medium_large') : '';
}
?>
<article class="sh-product-card">
    <a href="<?php echo esc_url($url); ?>" class="sh-product-card__thumb-link" tabindex="-1" aria-hidden="true">
        <div class="sh-product-card__thumb">
            <?php if ($img_id > 0) : ?>
                <?php echo wp_get_attachment_image($img_id, 'medium_large', false, [
                    'class'   => 'sh-product-card__img',
                    'loading' => 'lazy',
                    'sizes'   => '(max-width: 640px) 90vw, (max-width: 1024px) 45vw, 30vw',
                ]); ?>
            <?php elseif (!empty($img_url)) : ?>
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($title); ?>" class="sh-product-card__img" loading="lazy">
            <?php else : ?>
                <div class="sh-product-card__placeholder">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/logo.webp" alt="Saigon Horeca" class="sh-product-card__placeholder-logo" loading="lazy">
                </div>
            <?php endif; ?>
            <?php if (!empty($brand_name)) : ?>
                <span class="sh-product-card__brand-badge"><?php echo esc_html($brand_name); ?></span>
            <?php endif; ?>
        </div>
    </a>

    <div class="sh-product-card__body">
        <?php if (!empty($cat_name)) : ?>
            <a href="<?php echo esc_url($cat_url); ?>" class="sh-product-card__cat"><?php echo esc_html($cat_name); ?></a>
        <?php endif; ?>

        <h3 class="sh-product-card__title">
            <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($title); ?></a>
        </h3>

        <?php if (!empty($excerpt)) : ?>
            <p class="sh-product-card__excerpt"><?php echo esc_html(wp_strip_all_tags($excerpt)); ?></p>
        <?php endif; ?>

        <a href="<?php echo esc_url($url); ?>" class="sh-product-card__cta">
            <?php echo sh_icon('eye', 'sh-product-card__cta-icon'); ?>
            <span><?php _e('Xem chi tiết', 'saigonhoreca'); ?></span>
        </a>
    </div>
</article>
