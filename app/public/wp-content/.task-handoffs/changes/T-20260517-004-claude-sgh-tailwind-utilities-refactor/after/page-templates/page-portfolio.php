<?php
/**
 * Template Name: Portfolio / Dự Án
 *
 * @package SaigonHouse
 */
get_header();

// Filter categories for the portfolio
$filter_cats = [
    'all'      => 'Tất Cả',
    'biet-thu' => 'Biệt Thự',
    'nha-pho'  => 'Nhà Phố',
    'noi-that' => 'Nội Thất',
    'cong-trinh' => 'Công Trình',
];

// Initial query — load all, JS handles filtering
$portfolio_query = new WP_Query([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 24,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => [
        [
            'key'     => '_thumbnail_id',
            'compare' => 'EXISTS',
        ],
    ],
    'no_found_rows'  => false,
]);
?>

<!-- CSS loaded via enqueue -->

<main id="sh-portfolio-page" data-aos="fade-up">

    <!-- ── Hero ── -->
    <section class="sh-port-hero" data-aos="fade-in">
        <div class="sh-port-hero-bg" aria-hidden="true"></div>
        <div class="sh-port-container" data-aos="fade-up" data-aos-delay="80">
            <?php
            $breadcrumb_fn = function_exists('sh_breadcrumb') ? 'sh_breadcrumb' : null;
            ?>
            <nav class="sh-port-breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo home_url('/'); ?>">Trang chủ</a>
                <span aria-hidden="true">›</span>
                <span><?php the_title(); ?></span>
            </nav>
            <h1 class="sh-port-hero-title">
                Danh Mục <span>Công Trình</span>
            </h1>
            <p class="sh-port-hero-lead">500+ công trình được thiết kế và thi công bởi đội ngũ Saigon House — từ biệt thự, nhà phố đến nội thất cao cấp.</p>

            <!-- Stats -->
            <div class="sh-port-stats">
                <div class="sh-port-stat">
                    <strong>500+</strong>
                    <span>Công trình</span>
                </div>
                <div class="sh-port-stat">
                    <strong>15+</strong>
                    <span>Năm kinh nghiệm</span>
                </div>
                <div class="sh-port-stat">
                    <strong>98%</strong>
                    <span>Khách hài lòng</span>
                </div>
                <div class="sh-port-stat">
                    <strong>24</strong>
                    <span>Tỉnh thành</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Filter & Gallery ── -->
    <section class="sh-port-gallery-section" data-aos="fade-up" data-aos-delay="120">
        <div class="sh-port-container">

            <!-- Filter Tabs -->
            <div class="sh-port-filters" role="tablist" aria-label="Lọc danh mục công trình">
                <?php foreach ($filter_cats as $slug => $label): ?>
                <button class="sh-port-filter-btn <?php echo $slug === 'all' ? 'is-active' : ''; ?>"
                        data-filter="<?php echo esc_attr($slug); ?>"
                        role="tab"
                        aria-selected="<?php echo $slug === 'all' ? 'true' : 'false'; ?>">
                    <?php echo esc_html($label); ?>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Result count -->
            <p class="sh-port-count" id="sh-port-count" aria-live="polite">
                Hiển thị <strong id="sh-port-count-num"><?php echo $portfolio_query->post_count; ?></strong> công trình
            </p>

            <!-- Masonry Grid -->
            <div class="sh-port-grid" id="sh-port-grid">
                <?php if ($portfolio_query->have_posts()): ?>
                <?php while ($portfolio_query->have_posts()): $portfolio_query->the_post(); ?>
                <?php
                    $post_id  = get_the_ID();
                    $img_url  = get_the_post_thumbnail_url($post_id, 'large');
                    $img_full = get_the_post_thumbnail_url($post_id, 'full') ?: $img_url;
                    if (!$img_url) continue;

                    // Build category slugs for data-cat attribute
                    $cats = get_the_category($post_id);
                    $cat_slugs = array_map(fn($c) => $c->slug, $cats);
                    $cat_data  = implode(' ', $cat_slugs);
                    $cat_name  = $cats ? $cats[0]->name : '';

                    // Assign random height class for masonry feel
                    $heights   = ['sh-port-item--sm', 'sh-port-item--md', 'sh-port-item--lg'];
                    $h_class   = $heights[$post_id % 3];
                ?>
                <div class="sh-port-item <?php echo $h_class; ?>"
                     data-cat="<?php echo esc_attr($cat_data); ?>"
                     data-title="<?php the_title_attribute(); ?>"
                     data-img="<?php echo esc_url($img_full); ?>"
                     data-aos="zoom-in-up"
                     data-aos-delay="<?php echo esc_attr(min(480, 80 + ($portfolio_query->current_post * 40))); ?>">

                    <div class="sh-port-item-inner">
                        <img src="<?php echo esc_url($img_url); ?>"
                             alt="<?php the_title_attribute(); ?>"
                             loading="lazy"
                             decoding="async"
                             class="sh-port-img">

                        <!-- Overlay -->
                        <div class="sh-port-overlay">
                            <div class="sh-port-overlay-content">
                                <?php if ($cat_name): ?>
                                <span class="sh-port-cat-badge"><?php echo esc_html($cat_name); ?></span>
                                <?php endif; ?>
                                <h3 class="sh-port-item-title"><?php the_title(); ?></h3>
                                <div class="sh-port-overlay-actions">
                                    <button class="sh-port-lightbox-btn"
                                            aria-label="Xem ảnh lớn: <?php the_title_attribute(); ?>"
                                            data-img="<?php echo esc_url($img_full); ?>"
                                            data-title="<?php the_title_attribute(); ?>">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                                    </button>
                                    <a href="<?php the_permalink(); ?>"
                                       class="sh-port-link-btn"
                                       aria-label="Xem chi tiết: <?php the_title_attribute(); ?>">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
                <?php else: ?>
                <p class="sh-port-empty">Chưa có công trình nào. Vui lòng quay lại sau.</p>
                <?php endif; ?>
            </div>

            <!-- Empty state (shown by JS when filter returns 0) -->
            <div class="sh-port-no-results" id="sh-port-no-results" hidden>
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8;margin:0 auto 16px;display:block;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <p>Không tìm thấy công trình cho danh mục này.</p>
            </div>

            <!-- Load More -->
            <?php if ($portfolio_query->max_num_pages > 1): ?>
            <div class="sh-port-loadmore-wrap" id="sh-port-loadmore-wrap">
                <button class="sh-port-loadmore-btn" id="sh-port-loadmore"
                        data-page="1"
                        data-max="<?php echo $portfolio_query->max_num_pages; ?>">
                    Xem Thêm Công Trình
                </button>
            </div>
            <?php endif; ?>

        </div>
    </section>

</main>

<!-- ── Lightbox ── -->
<div id="sh-lightbox" class="sh-lightbox" role="dialog" aria-modal="true" aria-label="Xem ảnh công trình" hidden>
    <button class="sh-lightbox-close" id="sh-lightbox-close" aria-label="Đóng">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <button class="sh-lightbox-prev" id="sh-lightbox-prev" aria-label="Ảnh trước">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <div class="sh-lightbox-content" id="sh-lightbox-content">
        <img src="" alt="" id="sh-lightbox-img" class="sh-lightbox-img">
        <p class="sh-lightbox-caption" id="sh-lightbox-caption"></p>
    </div>
    <button class="sh-lightbox-next" id="sh-lightbox-next" aria-label="Ảnh tiếp theo">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
    <div class="sh-lightbox-counter" id="sh-lightbox-counter" aria-live="polite"></div>
</div>

<script src="<?php echo get_template_directory_uri(); ?>/assets/js/gallery.js?v=<?php echo filemtime(get_template_directory() . '/assets/js/gallery.js'); ?>" defer></script>

<?php get_footer(); ?>
