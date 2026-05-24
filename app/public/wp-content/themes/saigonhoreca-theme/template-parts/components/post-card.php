<?php
/**
 * Template Part: Post Card — Reusable component
 * Mobile: compact horizontal | Tablet+: vertical card
 * @package SaigonHoreca
 */

$post_id   = $args['post_id'] ?? get_the_ID();
$url       = $args['url'] ?? get_permalink($post_id);
$pi_seo    = function_exists('sgh_pi_get_post_seo') ? sgh_pi_get_post_seo($post_id) : [];
$title     = $args['title'] ?? ($pi_seo['seo_title'] ?: $pi_seo['og_title'] ?: get_the_title($post_id));
$excerpt   = $args['excerpt'] ?? ($pi_seo['seo_description'] ?: $pi_seo['og_description'] ?: wp_trim_words(get_the_excerpt($post_id), 15));
$author    = $args['author'] ?? get_the_author_meta('display_name', get_post_field('post_author', $post_id));
$loading   = $args['loading'] ?? 'lazy';
$fetchpriority = ($loading === 'eager') ? 'high' : null;

$raw_date  = $args['date'] ?? get_the_date('Y-m-d H:i:s', $post_id);
$day       = wp_date('d', strtotime($raw_date));
$month     = wp_date('n', strtotime($raw_date));
$year      = wp_date('Y', strtotime($raw_date));
$day_month = wp_date('d/m/Y', strtotime($raw_date));

$categories = $args['categories'] ?? get_the_category($post_id);
$cat_name = '';
$cat_url = '';
if (!empty($categories) && !is_wp_error($categories)) {
    if (is_object($categories[0])) {
        $cat_name = $categories[0]->name;
        $cat_url  = get_category_link($categories[0]->term_id);
    } elseif (is_array($categories[0])) {
        $cat_name = $categories[0]['name'];
        $cat_url  = $categories[0]['url'];
    }
}

if (isset($args['thumb_url'])) {
    $img_url = $args['thumb_url'];
    $img_id  = 0;
} else {
    $thumb_data = saigonhouse_get_post_thumbnail_data($post_id, 'large');
    $img_url    = $thumb_data['url'];
    $img_id     = $thumb_data['id'];
}
?>
<article class="sh-card">
    <!-- Thumbnail -->
    <div class="sh-card__thumb">
        <a href="<?php echo esc_url($url); ?>" class="sh-card__thumb-link">
            <?php if ($img_id > 0) : 
                $img_attrs = [
                    'class'   => 'sh-card__img',
                    'loading' => $loading,
                    'sizes'   => '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw',
                ];
                if ($fetchpriority) {
                    $img_attrs['fetchpriority'] = $fetchpriority;
                }
                echo wp_get_attachment_image($img_id, 'large', false, $img_attrs); 
            elseif (strpos($img_url, 'placeholder.svg') !== false) : ?>
                <div class="sh-card__placeholder">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.webp" alt="Saigon Horeca" class="sh-card__placeholder-logo" loading="<?php echo esc_attr($loading); ?>" <?php if ($fetchpriority) echo 'fetchpriority="high"'; ?>>
                    <span class="sh-card__placeholder-text">SaigonHoreca</span>
                </div>
            <?php else : ?>
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($title); ?>" class="sh-card__img" loading="<?php echo esc_attr($loading); ?>" <?php if ($fetchpriority) echo 'fetchpriority="high"'; ?>>
            <?php endif; ?>
        </a>
        <!-- Date Badge (desktop) -->
        <div class="sh-card__date-badge">
            <span class="sh-card__date-day"><?php echo esc_html($day); ?></span>
            <span class="sh-card__date-month">Thg <?php echo esc_html($month); ?>, <?php echo esc_html($year); ?></span>
        </div>
    </div>

    <!-- Content -->
    <div class="sh-card__body">
        <div class="sh-card__meta">
            <span class="sh-card__date-mobile"><?php echo esc_html($day_month); ?></span>
            <?php if (!empty($cat_name)) : ?>
                <a href="<?php echo esc_url($cat_url); ?>" class="sh-card__cat"><?php echo esc_html($cat_name); ?></a>
            <?php endif; ?>
        </div>

        <div class="sh-card__content">
            <h2 class="sh-card__title">
                <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($title); ?></a>
            </h2>
            <div class="sh-card__excerpt"><?php echo wp_kses_post($excerpt); ?></div>
        </div>

        <div class="sh-card__footer">
            <span class="sh-card__author">
                <?php echo sh_icon('user', 'sh-card__author-icon'); ?>
                <span class="sh-card__author-name"><?php echo esc_html($author); ?></span>
            </span>
            <a href="<?php echo esc_url($url); ?>" class="sh-card__readmore" aria-label="Đọc tiếp: <?php echo esc_attr($title); ?>">
                <span class="sh-card__readmore-text">Đọc tiếp</span>
                <?php echo sh_icon('arrow-right', 'sh-card__readmore-icon'); ?>
            </a>
        </div>
    </div>
</article>
