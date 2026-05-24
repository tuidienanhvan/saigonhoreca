<?php
/**
 * Home — Tin tức mới.
 * Dynamic grid of 5 most recent blog posts.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

// Lấy 5 bài MỚI NHẤT trong category "Tin tức" (slug=tin-tuc) — loại trừ
// dự án (slug=du-an). Phải có featured image. Nếu category chưa tồn tại
// (vd site mới deploy chưa migrate), fallback về tất cả bài.
$news_cat = get_term_by('slug', 'tin-tuc', 'category');
$args = [
    'post_type'           => 'post',
    'posts_per_page'      => 3,
    'no_found_rows'       => true,
    'ignore_sticky_posts' => true,
    'meta_query'          => [[
        'key'     => '_thumbnail_id',
        'compare' => 'EXISTS',
    ]],
];
if ($news_cat) {
    $args['category__in'] = [$news_cat->term_id];
}
$q = new WP_Query($args);
if (!$q->have_posts()) return;
?>
<section class="sh-latest-news">
    <div class="sh-latest-news__inner">
        <header class="sh-latest-news__header">
            <div>
                <span class="sh-latest-news__eyebrow">News</span>
                <h2 class="sh-latest-news__title"><?php esc_html_e('Tin tức mới', 'saigonhoreca'); ?></h2>
            </div>
            <a href="<?php echo esc_url(sgh_url('news')); ?>" class="sh-latest-news__view-all">
                <span><?php esc_html_e('Xem tất cả', 'saigonhoreca'); ?></span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </header>
        <div class="sh-latest-news__grid">
            <?php $i = 0; while ($q->have_posts()) : $q->the_post(); $i++; ?>
                <article class="sh-latest-news__card<?php echo $i === 1 ? ' sh-latest-news__card--featured' : ''; ?>">
                    <a href="<?php the_permalink(); ?>" class="sh-latest-news__link">
                        <div class="sh-latest-news__thumb">
                            <?php if (has_post_thumbnail()) :
                                the_post_thumbnail('large', [
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                                ]);
                            endif; ?>
                        </div>
                        <div class="sh-latest-news__body">
                            <time class="sh-latest-news__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <?php echo esc_html(get_the_date('d.m.Y')); ?>
                            </time>
                            <h3 class="sh-latest-news__card-title"><?php the_title(); ?></h3>
                            <?php if (has_excerpt()) : ?>
                                <p class="sh-latest-news__excerpt"><?php echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></p>
                            <?php endif; ?>
                            <span class="sh-latest-news__readmore"><?php esc_html_e('Đọc chi tiết', 'saigonhoreca'); ?></span>
                        </div>
                    </a>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
