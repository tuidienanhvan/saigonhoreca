<?php
/**
 * single.php part — điều hướng bài trước / bài tiếp theo (.sh-post-nav).
 * Tự truy vấn get_previous_post()/get_next_post() dựa trên bài hiện tại.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

$prev_post = get_previous_post();
$next_post = get_next_post();
if ($prev_post || $next_post) : ?>
    <div class="sh-post-nav">
        <div class="sh-post-nav__col sh-post-nav__col--prev">
            <?php if ($prev_post) :
                $prev_thumb = get_the_post_thumbnail($prev_post->ID, 'medium', ['class' => 'sh-post-nav__thumb-img', 'loading' => 'lazy', 'decoding' => 'async']); ?>
                <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" class="sh-post-nav__link">
                    <div class="sh-post-nav__arrow">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    </div>
                    <div class="sh-post-nav__content">
                        <span class="sh-post-nav__label"><?php _e('Bài trước', 'saigonhoreca'); ?></span>
                        <span class="sh-post-nav__title"><?php echo esc_html(get_the_title($prev_post->ID)); ?></span>
                    </div>
                    <?php if ($prev_thumb) : ?>
                        <div class="sh-post-nav__thumb"><?php echo $prev_thumb; ?></div>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="sh-post-nav__col sh-post-nav__col--next">
            <?php if ($next_post) :
                $next_thumb = get_the_post_thumbnail($next_post->ID, 'medium', ['class' => 'sh-post-nav__thumb-img', 'loading' => 'lazy', 'decoding' => 'async']); ?>
                <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" class="sh-post-nav__link">
                    <?php if ($next_thumb) : ?>
                        <div class="sh-post-nav__thumb"><?php echo $next_thumb; ?></div>
                    <?php endif; ?>
                    <div class="sh-post-nav__content">
                        <span class="sh-post-nav__label"><?php _e('Bài tiếp theo', 'saigonhoreca'); ?></span>
                        <span class="sh-post-nav__title"><?php echo esc_html(get_the_title($next_post->ID)); ?></span>
                    </div>
                    <div class="sh-post-nav__arrow">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
