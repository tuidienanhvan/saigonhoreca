<?php
/**
 * Universal single sidebar — DÙNG CHO CẢ post VÀ product.
 *
 * Thứ tự widgets (quan trọng nhất TRƯỚC):
 *   1. CTA hotline       — ưu tiên cao nhất
 *   2. Tin tức mới nhất  — 6 bài post (loại current)
 *   3. Đã xem            — JS localStorage (key tùy post_type)
 *   4. Sản phẩm nổi bật  — 4 product ngẫu nhiên
 *   5. Danh mục          — DƯỚI CÙNG vì dài (post cats hoặc product cats)
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

$hotline    = get_theme_mod('saigonhouse_hotline', '0961 868 968');
$hotline_t  = preg_replace('/[^0-9+]/', '', (string) $hotline);
$contact    = apply_filters('sh_sidebar_contact_url', home_url('/lien-he'));

$current_id = get_the_ID();
$ptype      = get_post_type($current_id);
$is_product = ($ptype === 'product');

// Storage key cho widget "Đã xem" — phân tách post vs product để không lẫn.
$viewed_storage_key = $is_product ? 'sgh:viewed-products' : 'sgh:viewed-posts';
$viewed_label       = $is_product ? __('Sản phẩm vừa xem', 'saigonhoreca') : __('Bài đã xem', 'saigonhoreca');
$viewed_empty       = $is_product ? __('Chưa có sản phẩm nào được xem.', 'saigonhoreca') : __('Chưa có bài viết nào được xem.', 'saigonhoreca');
$sidebar_label      = $is_product ? __('Thông tin sản phẩm', 'saigonhoreca') : __('Thông tin bài viết', 'saigonhoreca');
?>
<aside class="sh-single-product__sidebar" aria-label="<?php echo esc_attr($sidebar_label); ?>">

    <div class="sh-single-product__cta">
        <span class="sh-single-product__cta-eyebrow"><?php _e('Cần hỗ trợ?', 'saigonhoreca'); ?></span>
        <p class="sh-single-product__cta-desc">
            <?php _e('Liên hệ Saigon Horeca để được tư vấn và báo giá miễn phí.', 'saigonhoreca'); ?>
        </p>
        <?php if ($hotline) : ?>
            <a class="sh-single-product__cta-hotline" href="tel:<?php echo esc_attr($hotline_t); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                <?php echo esc_html($hotline); ?>
            </a>
        <?php endif; ?>
        <a class="sh-single-product__cta-btn" href="<?php echo esc_url($contact); ?>">
            <?php _e('Gửi yêu cầu tư vấn', 'saigonhoreca'); ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
    </div>

    <?php
    // Tin tức mới nhất — exclude current post
    $latest_news = new WP_Query([
        'post_type'           => 'post',
        'posts_per_page'      => 6,
        'post__not_in'        => $current_id ? [$current_id] : [],
        'ignore_sticky_posts' => 1,
        'no_found_rows'       => true,
    ]);
    if ($latest_news->have_posts()) : ?>
        <div class="sh-filter-block sh-sidebar-news">
            <h3 class="sh-filter-block__title"><?php _e('Tin tức mới nhất', 'saigonhoreca'); ?></h3>
            <ul class="sh-sidebar-news__list">
                <?php while ($latest_news->have_posts()) : $latest_news->the_post(); ?>
                    <li class="sh-sidebar-news__item">
                        <a href="<?php the_permalink(); ?>" class="sh-sidebar-news__link">
                            <?php if (has_post_thumbnail()) : ?>
                                <span class="sh-sidebar-news__thumb">
                                    <?php the_post_thumbnail('thumbnail', ['loading' => 'lazy', 'decoding' => 'async']); ?>
                                </span>
                            <?php endif; ?>
                            <span class="sh-sidebar-news__body">
                                <span class="sh-sidebar-news__title"><?php the_title(); ?></span>
                                <span class="sh-sidebar-news__date"><?php echo esc_html(get_the_date('d/m/Y')); ?></span>
                            </span>
                        </a>
                    </li>
                <?php endwhile; wp_reset_postdata(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php
    // Đã xem — storage key phân tách post / product.
    $current_title     = get_the_title();
    $current_url       = get_permalink();
    $current_thumb_id  = get_post_thumbnail_id($current_id);
    $current_thumb_url = $current_thumb_id ? wp_get_attachment_image_url($current_thumb_id, 'thumbnail') : '';
    ?>
    <div class="sh-filter-block sh-sidebar-viewed sh-sidebar-viewed--empty"
         data-current-id="<?php echo (int) $current_id; ?>"
         data-current-title="<?php echo esc_attr($current_title); ?>"
         data-current-url="<?php echo esc_url($current_url); ?>"
         data-current-thumb="<?php echo esc_url($current_thumb_url); ?>"
         data-storage-key="<?php echo esc_attr($viewed_storage_key); ?>">
        <h3 class="sh-filter-block__title"><?php echo esc_html($viewed_label); ?></h3>
        <ul class="sh-sidebar-viewed__list" aria-live="polite"></ul>
        <p class="sh-sidebar-viewed__empty"><?php echo esc_html($viewed_empty); ?></p>
    </div>

    <?php 
    // ── Sản phẩm nổi bật — 4 sản phẩm ngẫu nhiên để tăng chuyển đổi
    $featured_products = new WP_Query([
        'post_type'           => 'product',
        'posts_per_page'      => 4,
        'ignore_sticky_posts' => 1,
        'orderby'             => 'rand',
        'no_found_rows'       => true,
    ]);
    if ($featured_products->have_posts()) : ?>
        <div class="sh-filter-block sh-sidebar-products">
            <h3 class="sh-filter-block__title"><?php _e('Sản phẩm nổi bật', 'saigonhoreca'); ?></h3>
            <ul class="sh-sidebar-products__list">
                <?php while ($featured_products->have_posts()) : $featured_products->the_post(); 
                    $prod_id = get_the_ID();
                    $thumb_id = get_post_thumbnail_id($prod_id);
                    $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : '';
                    $brands = get_the_terms($prod_id, 'product_brand');
                    $brand_name = ($brands && !is_wp_error($brands)) ? $brands[0]->name : '';
                ?>
                    <li class="sh-sidebar-products__item">
                        <a href="<?php the_permalink(); ?>" class="sh-sidebar-products__link">
                            <span class="sh-sidebar-products__thumb">
                                <?php if ($thumb_url) : ?>
                                    <img src="<?php echo esc_url($thumb_url); ?>" alt="" loading="lazy" decoding="async" width="64" height="64">
                                <?php else : ?>
                                    <span class="sh-sidebar-viewed__thumb--no-img"></span>
                                <?php endif; ?>
                            </span>
                            <span class="sh-sidebar-products__body">
                                <span class="sh-sidebar-products__title"><?php the_title(); ?></span>
                                <?php if ($brand_name) : ?>
                                    <span class="sh-sidebar-products__brand"><?php echo esc_html($brand_name); ?></span>
                                <?php endif; ?>
                            </span>
                        </a>
                    </li>
                <?php endwhile; wp_reset_postdata(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php
    // ── Danh mục — DƯỚI CÙNG vì dài
    if ($is_product) {
        // Product: hierarchical từ archive-product filter-sidebar (giữ design đồng bộ /san-pham/)
        get_template_part('template-parts/archive-product/filter-sidebar');
    } else {
        // Post: flat list category bài viết, sort by count desc
        $post_cats = get_categories(['hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC']);
        if (!empty($post_cats) && !is_wp_error($post_cats)) : ?>
            <div class="sh-filter-block">
                <h3 class="sh-filter-block__title"><?php _e('Danh mục tin tức', 'saigonhoreca'); ?></h3>
                <ul class="sh-filter-block__list">
                    <?php foreach ($post_cats as $c) : ?>
                        <li class="sh-filter-block__item sh-filter-block__item--depth-0">
                            <a href="<?php echo esc_url(get_category_link($c->term_id)); ?>">
                                <span><?php echo esc_html($c->name); ?></span>
                                <span class="sh-filter-block__count">(<?php echo (int) $c->count; ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif;
    }
    ?>
</aside>

<script>
/* Bài đã xem (post version) — dùng key riêng `sgh:viewed-posts`.
   Dữ liệu bài viết hiện tại được PHP nhúng trực tiếp an toàn tuyệt đối.
   Hiển thị cả bài viết hiện tại kèm badge "Đang xem" cực kỳ chuyên nghiệp. */
(function () {
    var block = document.querySelector('.sh-sidebar-viewed[data-storage-key="sgh:viewed-posts"]');
    if (!block) return;
    var KEY = block.dataset.storageKey;
    var MAX = 6;
    var currentId    = parseInt(block.dataset.currentId || '0', 10);
    var currentTitle = block.dataset.currentTitle || '';
    var currentUrl   = block.dataset.currentUrl || '';
    var currentThumb = block.dataset.currentThumb || '';

    var list;
    try { list = JSON.parse(localStorage.getItem(KEY)) || []; }
    catch (e) { list = []; }
    if (!Array.isArray(list)) list = [];

    if (currentId && currentTitle) {
        list = list.filter(function (p) { return p && p.id !== currentId; });
        list.unshift({
            id:    currentId,
            title: currentTitle,
            url:   currentUrl,
            thumb: currentThumb,
            ts:    Date.now()
        });
        if (list.length > MAX + 2) list = list.slice(0, MAX + 2);
        try { localStorage.setItem(KEY, JSON.stringify(list)); } catch (e) {}
    }

    var render = list.slice(0, MAX);
    var ul = block.querySelector('.sh-sidebar-viewed__list');
    if (!render.length) { block.classList.add('sh-sidebar-viewed--empty'); return; }
    block.classList.remove('sh-sidebar-viewed--empty');

    function esc(s) { return String(s).replace(/[<>&"]/g, function (c) {
        return { '<':'&lt;', '>':'&gt;', '&':'&amp;', '"':'&quot;' }[c];
    }); }

    ul.innerHTML = render.map(function (p) {
        if (!p) return '';
        var isCurrent = (p.id === currentId);
        var itemClass = 'sh-sidebar-viewed__item' + (isCurrent ? ' sh-sidebar-viewed__item--current' : '');
        
        var t = p.thumb
            ? '<span class="sh-sidebar-viewed__thumb"><img src="' + esc(p.thumb) + '" alt="" loading="lazy" decoding="async" width="64" height="64"></span>'
            : '<span class="sh-sidebar-viewed__thumb sh-sidebar-viewed__thumb--no-img"></span>';
            
        var titleHtml = '<span class="sh-sidebar-viewed__body">'
            + '<span class="sh-sidebar-viewed__title">' + esc(p.title) + '</span>'
            + (isCurrent ? '<span class="sh-sidebar-viewed__badge">Đang xem</span>' : '')
            + '</span>';

        return '<li class="' + itemClass + '">'
            + '<a class="sh-sidebar-viewed__link" href="' + esc(p.url) + '"' + (isCurrent ? ' onclick="event.preventDefault(); window.scrollTo({top: 0, behavior:\'smooth\'});"' : '') + '>'
            + t 
            + titleHtml
            + '</a></li>';
    }).join('');
})();
</script>
