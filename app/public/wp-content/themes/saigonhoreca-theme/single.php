<?php
/**
 * single.php — UNIVERSAL single template (post + product CPT).
 *
 * ONE FILE handles tất cả: blog post (`post`) và sản phẩm (`product`).
 * Phân nhánh bằng get_post_type() ở 2 chỗ:
 *   - Header meta box (post: author/date/read-time; product: SKU/brand/category)
 *   - After-content (post: tags + share + nav + author + related-posts;
 *                    product: related-products grid)
 *
 * project CPT vẫn dùng single-project.php riêng (hệ thống per-slug pillar
 * hoàn toàn khác, không share layout/CSS với single).
 *
 * CSS:
 *   - Layout:  template-parts/single-post/layout.css
 *   - Sidebar: template-parts/single-post/sidebar.css (universal)
 *   - Bundle:  theme-single.css (post) hoặc theme-product.css (product)
 *
 * Sidebar markup: template-parts/single-post/sidebar.php (universal)
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

get_header();

$post_type  = get_post_type();
$is_product = ($post_type === 'product');
$root_class = 'sh-single' . ($is_product ? ' sh-single--product' : '');
?>

<?php if (function_exists('sh_breadcrumbs')) sh_breadcrumbs(); ?>

<div class="<?php echo esc_attr($root_class); ?>">
    <div class="sh-single__layout">

        <main id="primary" class="sh-single__main" tabindex="-1">
            <?php while (have_posts()) : the_post();
                $post_id = get_the_ID();

                if ($is_product) {
                    $sku    = get_post_meta($post_id, '_sku', true);
                    $brands = wp_get_post_terms($post_id, 'product_brand');
                    $pcats  = wp_get_post_terms($post_id, 'product_category');
                    $schema = 'https://schema.org/Product';
                    $article_extra_class = ' sh-single__article--product';
                } else {
                    $custom_author = get_post_meta($post_id, '_pi_custom_author', true) ?: get_the_author();
                    $custom_date   = get_post_meta($post_id, '_pi_custom_date', true) ?: get_the_date();
                    $read_min      = get_post_meta($post_id, '_pi_reading_time', true);
                    if (empty($read_min)) {
                        $plain_text    = wp_strip_all_tags(strip_shortcodes(get_the_content()));
                        $vn_word_count = preg_match_all('/\S+/u', $plain_text);
                        $read_min      = max(1, round($vn_word_count / 200));
                    }
                    $schema = 'https://schema.org/Article';
                    $article_extra_class = '';
                }
            ?>
                <article id="post-<?php the_ID(); ?>"
                    <?php post_class('sh-single__article' . $article_extra_class); ?>
                    itemscope itemtype="<?php echo esc_attr($schema); ?>">

                    <!-- ═══ HEADER ═══ -->
                    <div class="sh-single__header">
                        <div class="sh-single__header-text">
                            <h1 class="sh-single__title" itemprop="<?php echo $is_product ? 'name' : 'headline'; ?>"><?php the_title(); ?></h1>

                            <?php if ($is_product) : ?>
                                <div class="sh-single__meta">
                                    <?php if ($sku) : ?>
                                        <span class="sh-single__meta-item">
                                            <strong>SKU:</strong> <span itemprop="sku"><?php echo esc_html($sku); ?></span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!is_wp_error($brands) && !empty($brands)) : ?>
                                        <span class="sh-single__meta-item">
                                            <strong>Thương hiệu:</strong>
                                            <?php foreach ($brands as $i => $b) : ?>
                                                <a href="<?php echo esc_url(get_term_link($b)); ?>" itemprop="brand"><?php echo esc_html($b->name); ?></a><?php echo $i + 1 < count($brands) ? ', ' : ''; ?>
                                            <?php endforeach; ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!is_wp_error($pcats) && !empty($pcats)) : ?>
                                        <span class="sh-single__meta-item">
                                            <strong>Danh mục:</strong>
                                            <?php foreach ($pcats as $i => $c) : ?>
                                                <a href="<?php echo esc_url(get_term_link($c)); ?>"><?php echo esc_html($c->name); ?></a><?php echo $i + 1 < count($pcats) ? ', ' : ''; ?>
                                            <?php endforeach; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php else : ?>
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
                                    <?php if (function_exists('sh_get_post_views')) : ?>
                                        <span class="sh-single__meta-item sh-single__meta-item--accent">
                                            <?php echo sh_icon('eye', 'sh-single__meta-icon'); ?>
                                            <span class="views-count"><?php echo number_format_i18n(sh_get_post_views($post_id)); ?></span> lượt xem
                                        </span>
                                    <?php endif; ?>
                                    <span class="sh-single__meta-item sh-single__meta-item--pill">
                                        <?php echo sh_icon('help-circle', 'sh-single__meta-icon'); ?>
                                        <?php echo (int) $read_min; ?> phút đọc
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (has_excerpt()) : ?>
                                <div class="sh-single__excerpt"<?php echo $is_product ? ' itemprop="description"' : ''; ?>><?php the_excerpt(); ?></div>
                            <?php endif; ?>
                        </div>

                        <?php if (has_post_thumbnail()) : ?>
                            <div class="sh-single__featured-img">
                                <?php the_post_thumbnail('large', array_merge([
                                    'class'         => 'sh-single__thumbnail',
                                    'loading'       => 'eager',
                                    'fetchpriority' => 'high',
                                ], $is_product ? ['itemprop' => 'image'] : [])); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ═══ TOC (chỉ post — sản phẩm không cần) ═══ -->
                    <?php if (!$is_product) : ?>
                        <div id="toc-container" class="sh-single__toc" style="display:none;">
                            <div class="sh-single__toc-header">
                                <h3 class="sh-single__toc-title">
                                    <?php echo sh_icon('list', 'sh-single__toc-icon'); ?> Nội dung bài viết
                                </h3>
                                <button id="toc-toggle" class="sh-single__toc-toggle">[Thu gọn]</button>
                            </div>
                            <ul id="toc-list" class="sh-single__toc-list"></ul>
                        </div>
                    <?php endif; ?>

                    <!-- ═══ CONTENT ═══ -->
                    <div id="post-content" class="sh-single__content entry-content"<?php echo $is_product ? ' itemprop="description"' : ''; ?>>
                        <?php the_content(); ?>
                    </div>

                    <?php if (!$is_product) : ?>
                        <!-- ═══ TAGS (chỉ post) ═══ -->
                        <?php $tags = get_the_tags(); if ($tags) : ?>
                            <div class="sh-single__tags">
                                <?php echo sh_icon('tag', 'sh-single__tags-icon'); ?>
                                <?php foreach ($tags as $tag) : ?>
                                    <a href="<?php echo get_tag_link($tag->term_id); ?>" class="sh-single__tag">#<?php echo $tag->name; ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- ═══ SHARE BUTTONS (chỉ post) ═══ -->
                        <?php get_template_part('template-parts/components/share-buttons'); ?>

                        <!-- ═══ POST NAVIGATION (prev/next, chỉ post) ═══ -->
                        <?php
                        $prev_post = get_previous_post();
                        $next_post = get_next_post();
                        if ($prev_post || $next_post) : ?>
                            <div class="sh-post-nav">
                                <div class="sh-post-nav__col sh-post-nav__col--prev">
                                    <?php if ($prev_post) :
                                        $prev_thumb = get_the_post_thumbnail($prev_post->ID, 'thumbnail', ['class' => 'sh-post-nav__thumb-img', 'loading' => 'lazy', 'decoding' => 'async']); ?>
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
                                        $next_thumb = get_the_post_thumbnail($next_post->ID, 'thumbnail', ['class' => 'sh-post-nav__thumb-img', 'loading' => 'lazy', 'decoding' => 'async']); ?>
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

                        <!-- ═══ AUTHOR BOX (chỉ post) ═══ -->
                        <div class="sh-single__author">
                            <div class="sh-single__author-avatar">
                                <?php echo get_avatar(get_the_author_meta('ID'), 200, '', '', ['class' => 'sh-single__author-img']); ?>
                            </div>
                            <div class="sh-single__author-info">
                                <h4 class="sh-single__author-name">Tác giả: <?php echo esc_html($custom_author); ?></h4>
                                <p class="sh-single__author-bio"><?php echo get_the_author_meta('description') ?: 'Đội ngũ Saigon Horeca — chuyên tư vấn thiết bị bếp công nghiệp, quầy bar và giải pháp horeca cho nhà hàng, khách sạn.'; ?></p>
                                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="sh-single__author-link">Xem tất cả bài viết</a>
                            </div>
                        </div>

                        <!-- ═══ RELATED POSTS (chỉ post) ═══ -->
                        <?php
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
                        if ($related->have_posts()) : ?>
                            <div class="sh-single__related">
                                <h3 class="sh-single__related-title">Bài Viết Liên Quan</h3>
                                <div class="sh-single__related-grid">
                                    <?php while ($related->have_posts()) : $related->the_post(); ?>
                                        <?php get_template_part('template-parts/components/post-card'); ?>
                                    <?php endwhile; wp_reset_postdata(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php else : /* PRODUCT */ ?>
                        <!-- ═══ RELATED PRODUCTS (chỉ product) ═══ -->
                        <?php
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
                                    <h3 class="sh-single__related-title">Sản phẩm liên quan</h3>
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
                    <?php endif; ?>
                </article>

                <?php if (!$is_product && (comments_open() || get_comments_number())) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>

            <?php endwhile; ?>
        </main>

        <!-- Sidebar — universal cho post + product -->
        <?php get_template_part('template-parts/single-post/sidebar'); ?>

    </div>
</div>

<?php if (!$is_product) : /* Lightbox chỉ cần cho post content có nhiều ảnh inline */ ?>
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
<?php endif; ?>

<?php get_footer();
