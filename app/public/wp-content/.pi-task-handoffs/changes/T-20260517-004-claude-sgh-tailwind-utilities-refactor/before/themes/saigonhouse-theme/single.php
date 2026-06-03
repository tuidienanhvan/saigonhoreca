<?php
/**
 * Single Post Template
 *
 * @package SaigonHouse
 */

get_header();
?>

<?php sh_breadcrumbs(); ?>

<div class="sh-single" data-aos="fade-up">
    <div class="sh-single__layout">

        <!-- Main Content -->
        <main id="primary" class="sh-single__main">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('sh-single__article'); ?> itemscope itemtype="https://schema.org/Article">

                    <!-- Post Header -->
                    <div class="sh-single__header" data-aos="fade-up" data-aos-delay="80">
                        <div class="sh-single__header-text">
                            <h1 class="sh-single__title" itemprop="headline"><?php the_title(); ?></h1>

                            <?php
                            $custom_author = get_post_meta(get_the_ID(), '_pi_custom_author', true) ?: get_the_author();
                            $custom_date = get_post_meta(get_the_ID(), '_pi_custom_date', true) ?: get_the_date();
                            
                            // Ưu tiên lấy từ meta được pi-api tính toán sẵn
                            $read_min = get_post_meta(get_the_ID(), '_pi_reading_time', true);
                            
                            if (empty($read_min)) {
                                $plain_text = wp_strip_all_tags(strip_shortcodes(get_the_content()));
                                $vn_word_count = preg_match_all('/\S+/u', $plain_text);
                                $read_min = max(1, round($vn_word_count / 200));
                            }
                            ?>
                            <div class="sh-single__meta">
                                <span class="sh-single__meta-item sh-single__meta-item--pill">
                                    <?php echo sh_icon('calendar', 'sh-single__meta-icon'); ?>
                                    <time itemprop="datePublished" datetime="<?php echo get_the_date('c'); ?>"><?php echo esc_html($custom_date); ?></time>
                                </span>
                                <meta itemprop="dateModified" content="<?php echo get_the_modified_date('c'); ?>">
                                <span class="sh-single__meta-item sh-single__meta-item--accent" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                    <?php echo sh_icon('user', 'sh-single__meta-icon'); ?>
                                    <span itemprop="name"><?php echo esc_html($custom_author); ?></span>
                                </span>
                                <?php if (function_exists('sh_get_post_views')): ?>
                                <span class="sh-single__meta-item sh-single__meta-item--accent">
                                    <?php echo sh_icon('eye', 'sh-single__meta-icon'); ?>
                                    <span class="views-count"><?php echo number_format_i18n(sh_get_post_views(get_the_ID())); ?></span> lượt xem
                                </span>
                                <?php endif; ?>
                                <span class="sh-single__meta-item sh-single__meta-item--pill">
                                    <?php echo sh_icon('help-circle', 'sh-single__meta-icon'); ?>
                                    <?php echo $read_min; ?> phút đọc
                                </span>
                            </div>

                            <?php if (has_excerpt()): ?>
                                <div class="sh-single__excerpt"><?php the_excerpt(); ?></div>
                            <?php endif; ?>
                        </div>

                        <?php if (has_post_thumbnail()): ?>
                            <div class="sh-single__featured-img">
                                <?php the_post_thumbnail('large', [
                                    'class'         => 'sh-single__thumbnail',
                                    'loading'       => 'eager',
                                    'fetchpriority' => 'high',
                                ]); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Table of Contents -->
                    <div id="toc-container" class="sh-single__toc" style="display:none;" data-aos="fade-up" data-aos-delay="120">
                        <div class="sh-single__toc-header">
                            <h3 class="sh-single__toc-title">
                                <?php echo sh_icon('list', 'sh-single__toc-icon'); ?> Nội dung bài viết
                            </h3>
                            <button id="toc-toggle" class="sh-single__toc-toggle">[Thu gọn]</button>
                        </div>
                        <ul id="toc-list" class="sh-single__toc-list"></ul>
                    </div>

                    <!-- Content -->
                    <div id="post-content" class="sh-single__content entry-content" data-aos="fade-up" data-aos-delay="160">
                        <?php the_content(); ?>
                    </div>

                    <!-- Tags -->
                    <?php $tags = get_the_tags(); if ($tags) : ?>
                        <div class="sh-single__tags" data-aos="fade-up" data-aos-delay="200">
                            <?php echo sh_icon('tag', 'sh-single__tags-icon'); ?>
                            <?php foreach($tags as $tag): ?>
                                <a href="<?php echo get_tag_link($tag->term_id); ?>" class="sh-single__tag">#<?php echo $tag->name; ?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Share -->
                    <?php get_template_part('template-parts/components/share-buttons'); ?>

                    <!-- Author Box -->
                    <div class="sh-single__author" data-aos="fade-up" data-aos-delay="240">
                        <div class="sh-single__author-avatar">
                            <?php echo get_avatar(get_the_author_meta('ID'), 200, '', '', ['class' => 'sh-single__author-img']); ?>
                        </div>
                        <div class="sh-single__author-info">
                            <h4 class="sh-single__author-name">Tác giả: <?php echo esc_html($custom_author); ?></h4>
                            <p class="sh-single__author-bio"><?php echo get_the_author_meta('description') ?: 'Chuyên gia tư vấn thiết kế và xây dựng tại Saigon House. Luôn cập nhật những xu hướng kiến trúc mới nhất.'; ?></p>
                            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="sh-single__author-link">Xem tất cả bài viết</a>
                        </div>
                    </div>

                    <!-- Related Posts -->
                    <?php
                    $current_id = get_the_ID();
                    $categories = wp_get_post_categories($current_id);
                    $post_tags = wp_get_post_tags($current_id, array('fields' => 'ids'));

                    $related = new WP_Query([
                        'category__in'        => $categories,
                        'post__not_in'        => [$current_id],
                        'posts_per_page'      => 3,
                        'ignore_sticky_posts' => 1,
                        'orderby'             => 'date',
                        'order'               => 'DESC',
                        'no_found_rows'       => true,
                    ]);

                    if (!$related->have_posts() && !empty($post_tags)) {
                        $related = new WP_Query([
                            'tag__in'             => $post_tags,
                            'post__not_in'        => [$current_id],
                            'posts_per_page'      => 3,
                            'ignore_sticky_posts' => 1,
                            'orderby'             => 'date',
                            'order'               => 'DESC',
                            'no_found_rows'       => true,
                        ]);
                    }

                    if ($related->have_posts()) : ?>
                        <div class="sh-single__related" data-aos="fade-up" data-aos-delay="280">
                            <h3 class="sh-single__related-title">Bài Viết Liên Quan</h3>
                            <div class="sh-single__related-grid">
                                <?php while ($related->have_posts()) : $related->the_post(); ?>
                                    <?php get_template_part('template-parts/components/post-card'); ?>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </article>

                <?php if (comments_open() || get_comments_number()) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>

            <?php endwhile; ?>
        </main>

        <!-- Sidebar -->
        <?php get_sidebar(); ?>

    </div>
</div>

<!-- Lightbox -->
<div id="sh-lightbox" class="sh-single__lightbox" role="dialog" aria-modal="true" aria-label="Xem ảnh phóng to" hidden>
    <div class="sh-single__lightbox-backdrop" aria-hidden="true"></div>

    <button id="sh-lightbox-close" type="button" class="sh-single__lightbox-close" aria-label="Đóng (Esc)">
        <?php echo sh_icon('x', 'sh-single__lightbox-icon'); ?>
    </button>

    <figure class="sh-single__lightbox-figure">
        <div class="sh-single__lightbox-loader" aria-hidden="true"></div>
        <img id="sh-lightbox-img" src="" alt="" class="sh-single__lightbox-img">
        <figcaption id="sh-lightbox-caption" class="sh-single__lightbox-caption"></figcaption>
    </figure>

    <div class="sh-single__lightbox-hint" aria-hidden="true">
        <span>Click bất kỳ đâu hoặc nhấn <kbd>Esc</kbd> để đóng</span>
    </div>
</div>

<?php
get_footer();
