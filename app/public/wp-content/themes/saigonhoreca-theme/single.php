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
 *   - Layout:  template-parts/single/layout.css
 *   - Sidebar: template-parts/single/sidebar.css (universal)
 *   - Bundle:  theme-single.css (post) hoặc theme-product.css (product)
 *
 * Sidebar markup: template-parts/single/sidebar.php (universal)
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
                    $sku        = get_post_meta($post_id, '_sku', true);
                    $source_url = get_post_meta($post_id, '_source_url', true);
                    $brands = wp_get_post_terms($post_id, 'product_brand');
                    $pcats  = wp_get_post_terms($post_id, 'product_category');
                    $schema = 'https://schema.org/Product';
                    $article_extra_class = ' sh-single__article--product';
                } else {
                    $custom_author = get_post_meta($post_id, '_pi_custom_author', true) ?: get_the_author() ?: 'Saigon Horeca';
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
                    <?php if ($is_product) : ?>
                        <?php get_template_part('template-parts/single/product-hero', null, [
                            'post_id'    => $post_id,
                            'sku'        => $sku,
                            'brands'     => $brands,
                            'pcats'      => $pcats,
                            'source_url' => $source_url,
                        ]); ?>
                    <?php else : ?>
                        <div class="sh-single__header">
                            <!-- Hàng 1: Tiêu đề Full 100% -->
                            <h1 class="sh-single__title" itemprop="headline"><?php the_title(); ?></h1>

                            <!-- Hàng 2: Chia 2 Cột (Trái: Meta & Excerpt, Phải: Ảnh) -->
                            <div class="sh-single__header-grid">
                                <div class="sh-single__header-info">
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
                                        <?php
                                        $lang_code    = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';
                                        $lbl_views    = ['vi' => 'lượt xem', 'en' => 'views', 'ja' => '回閲覧'][$lang_code] ?? 'lượt xem';
                                        $lbl_read_min = ['vi' => 'phút đọc', 'en' => 'min read', 'ja' => '分で読めます'][$lang_code] ?? 'phút đọc';
                                        if (function_exists('sh_get_post_views')) : ?>
                                            <span class="sh-single__meta-item sh-single__meta-item--accent">
                                                <?php echo sh_icon('eye', 'sh-single__meta-icon'); ?>
                                                <span class="views-count"><?php echo number_format_i18n(sh_get_post_views($post_id)); ?></span> <?php echo esc_html($lbl_views); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="sh-single__meta-item sh-single__meta-item--pill">
                                            <?php echo sh_icon('help-circle', 'sh-single__meta-icon'); ?>
                                            <?php echo (int) $read_min; ?> <?php echo esc_html($lbl_read_min); ?>
                                        </span>
                                    </div>

                                    <?php if (function_exists('sgh_the_excerpt')) : ?>
                                        <?php sgh_the_excerpt($post_id, 35); ?>
                                    <?php elseif (has_excerpt()) : ?>
                                        <div class="sh-single__excerpt"><?php the_excerpt(); ?></div>
                                    <?php endif; ?>
                                </div>

                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="sh-single__featured-img">
                                        <?php the_post_thumbnail('large', [
                                            'class'         => 'sh-single__thumbnail',
                                            'loading'       => 'eager',
                                            'fetchpriority' => 'high',
                                        ]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ═══ TOC (chỉ post — sản phẩm không cần) ═══ -->
                    <?php if (!$is_product) : ?>
                        <?php get_template_part('template-parts/single/components/toc'); ?>
                    <?php endif; ?>

                    <!-- ═══ CONTENT ═══ -->
                    <div id="post-content" class="sh-single__content entry-content"<?php echo $is_product ? ' itemprop="description"' : ''; ?>>
                        <?php the_content(); ?>
                    </div>

                    <?php if (!$is_product) : ?>
                        <!-- ═══ TAGS (chỉ post) ═══ -->
                        <?php get_template_part('template-parts/single/components/tags'); ?>

                        <!-- ═══ SHARE BUTTONS (chỉ post) ═══ -->
                        <?php get_template_part('template-parts/_shared/share-buttons'); ?>

                        <!-- ═══ POST NAVIGATION (prev/next, chỉ post) ═══ -->
                        <?php get_template_part('template-parts/single/components/post-nav'); ?>

                        <!-- ═══ AUTHOR BOX (chỉ post) ═══ -->
                        <?php get_template_part('template-parts/single/components/author-box', null, ['author' => $custom_author]); ?>

                        <!-- ═══ RELATED POSTS (chỉ post) ═══ -->
                        <?php get_template_part('template-parts/single/components/related-posts', null, ['post_id' => $post_id]); ?>

                    <?php else : /* PRODUCT */ ?>
                        <!-- ═══ RELATED PRODUCTS (chỉ product) ═══ -->
                        <?php get_template_part('template-parts/single/components/related-products', null, ['post_id' => $post_id, 'pcats' => $pcats]); ?>
                    <?php endif; ?>
                </article>

                <?php if (!$is_product && (comments_open() || get_comments_number())) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>

            <?php endwhile; ?>
        </main>

        <!-- Sidebar — universal cho post + product -->
        <?php get_template_part('template-parts/single/sidebar'); ?>

    </div>
</div>

<?php if (!$is_product) : /* Lightbox chỉ cần cho post content có nhiều ảnh inline */ ?>
<?php get_template_part('template-parts/single/components/lightbox'); ?>
<?php endif; ?>

<?php get_footer();
