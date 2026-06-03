<?php
/**
 * /du-an/ — Những Dự Án nổi bật của Saigon Horeca.
 *
 * Hiển thị TẤT CẢ projects trong 1 grid duy nhất (không carousel, không pagination).
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$q = new WP_Query([
    'post_type'      => 'project',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
]);

$svg_sparkle = '<svg class="sh-archive-projects__sparkle" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2 L13.5 9 L20 10.5 L13.5 12 L12 19 L10.5 12 L4 10.5 L10.5 9 Z"/></svg>';
$svg_pin     = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 0-8 8c0 5.4 8 12 8 12s8-6.6 8-12a8 8 0 0 0-8-8z"/></svg>';
?>
<section class="sh-archive-projects" aria-label="Danh sách dự án Saigon Horeca">
    <svg class="sh-archive-projects__bg-pattern" aria-hidden="true" preserveAspectRatio="xMidYMid slice">
        <defs>
            <pattern id="shProjPattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                <circle cx="20" cy="20" r="1" fill="currentColor" opacity=".18"/>
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#shProjPattern)"/>
    </svg>

    <div class="sh-archive-projects__inner">

        <header class="sh-archive-projects__header">
            <span class="sh-archive-projects__eyebrow">
                <?php echo $svg_sparkle; ?>
                <span>Featured Projects</span>
                <?php echo $svg_sparkle; ?>
            </span>
            <h2 class="sh-archive-projects__title">Những Dự Án nổi bật của Saigon Horeca</h2>
            <span class="sh-archive-projects__divider" aria-hidden="true">
                <svg viewBox="0 0 220 12" fill="none" preserveAspectRatio="none">
                    <line x1="0" y1="6" x2="92" y2="6" stroke="currentColor" stroke-width="1.5" opacity=".5"/>
                    <path d="M110 1 L117 6 L110 11 L103 6 Z" fill="currentColor"/>
                    <line x1="128" y1="6" x2="220" y2="6" stroke="currentColor" stroke-width="1.5" opacity=".5"/>
                </svg>
            </span>
        </header>

        <?php if ($q->have_posts()) : ?>
            <div class="sh-archive-projects__grid">
                <?php $i = 0; while ($q->have_posts()) : $q->the_post(); $i++;
                    global $post;
                    $post_slug = isset($post->post_name) ? $post->post_name : '';
                    
                    // Prioritize template comment headers, fall back to DB/ACF
                    $title = '';
                    if ($post_slug && function_exists('sgh_get_project_meta')) {
                        $title = sgh_get_project_meta($post_slug, 'Title');
                    }
                    if (!$title) {
                        $title = get_the_title();
                    }

                    $address = '';
                    if ($post_slug && function_exists('sgh_get_project_meta')) {
                        $address = sgh_get_project_meta($post_slug, 'Address');
                    }
                    if (!$address) {
                        $address = function_exists('get_field') ? (string) get_field('address') : '';
                    }
                ?>
                    <a href="<?php the_permalink(); ?>" class="sh-archive-projects__card">
                        <?php /* Manual <img> with alt="" — WP's the_post_thumbnail() falls
                                back to post title when alt is empty in DB, causing
                                axe `image-redundant-alt` (alt duplicates the <h3> below).
                                The <h3> inside provides the accessible name for the link. */ ?>
                        <?php
                        $thumb_id  = get_post_thumbnail_id();
                        $thumb_src = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : '';
                        $thumb_srcset = $thumb_id ? wp_get_attachment_image_srcset($thumb_id, 'large') : '';
                        
                        // Retrieve custom project thumbnail configured directly in the single-project template comment
                        global $post;
                        if (isset($post->post_name)) {
                            $custom_thumb = sgh_get_project_thumbnail($post->post_name);
                            if ($custom_thumb) {
                                $thumb_src = $custom_thumb;
                                $thumb_srcset = '';
                            }
                        }
                        ?>
                        <?php if ($thumb_src) : ?>
                        <img class="sh-archive-projects__img"
                             src="<?php echo esc_url($thumb_src); ?>"
                             <?php if ($thumb_srcset) : ?>srcset="<?php echo esc_attr($thumb_srcset); ?>"<?php endif; ?>
                             sizes="(max-width: 768px) 100vw, 33vw"
                             width="1024" height="576"
                             alt=""
                             loading="lazy" decoding="async">
                        <?php endif; ?>
                        <span class="sh-archive-projects__num" aria-hidden="true">
                            <?php echo sprintf('%02d', $i); ?>
                        </span>
                        <div class="sh-archive-projects__card-info">
                            <span class="sh-archive-projects__card-accent" aria-hidden="true"></span>
                            <h3 class="sh-archive-projects__card-title"><?php echo esc_html($title); ?></h3>
                        </div>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="sh-archive-projects__empty">Chưa có dự án nào.</p>
        <?php endif; ?>
    </div>
</section>
