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

$hotline    = get_theme_mod('saigonhouse_hotline', '0901 304 365');
$hotline_t  = preg_replace('/[^0-9+]/', '', (string) $hotline);
$contact    = apply_filters('sh_sidebar_contact_url', function_exists('sgh_url') ? sgh_url('contact') : home_url('/lien-he'));

$current_id = get_the_ID();
$ptype      = get_post_type($current_id);
$is_product = ($ptype === 'product');

$lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';

// Storage key cho widget "Đã xem" — phân tách post vs product và ngôn ngữ để không lẫn tiêu đề.
$viewed_storage_key = ($is_product ? 'sgh:viewed-products' : 'sgh:viewed-posts') . ':' . $lang;
$viewed_badge       = ['vi' => 'Đang xem', 'en' => 'Now viewing', 'ja' => '閲覧中'][$lang] ?? 'Now viewing';
$viewed_label       = $is_product
    ? (['vi' => 'Sản phẩm vừa xem', 'en' => 'Recently Viewed Products', 'ja' => '最近閲覧した製品'][$lang] ?? 'Recently Viewed Products')
    : (['vi' => 'Bài đã xem', 'en' => 'Recently Viewed Posts', 'ja' => '最近読んだ記事'][$lang] ?? 'Recently Viewed Posts');
$viewed_empty       = $is_product
    ? (['vi' => 'Chưa có sản phẩm nào được xem.', 'en' => 'No products viewed yet.', 'ja' => '閲覧履歴はありません。'][$lang] ?? 'No products viewed yet.')
    : (['vi' => 'Chưa có bài viết nào được xem.', 'en' => 'No posts viewed yet.', 'ja' => '閲覧履歴はありません。'][$lang] ?? 'No posts viewed yet.');
$sidebar_label      = $is_product
    ? (['vi' => 'Thông tin sản phẩm', 'en' => 'Product Information', 'ja' => '製品情報'][$lang] ?? 'Product Information')
    : (['vi' => 'Thông tin bài viết', 'en' => 'Article Information', 'ja' => '記事情報'][$lang] ?? 'Article Information');

$cta_eyebrow = ['vi' => 'Cần hỗ trợ?', 'en' => 'Need Assistance?', 'ja' => 'ご相談・お見積り'][$lang] ?? 'Need Assistance?';
$cta_desc    = [
    'vi' => 'Liên hệ Saigon Horeca để được tư vấn và báo giá miễn phí.',
    'en' => 'Contact Saigon Horeca for professional consultation and free quotation.',
    'ja' => 'Saigon Horecaへのお問い合わせ、専門コンサルティング・無料見積もりはこちら。',
][$lang] ?? 'Contact Saigon Horeca for professional consultation and free quotation.';
$cta_btn     = ['vi' => 'Gửi yêu cầu tư vấn', 'en' => 'Request Consultation', 'ja' => '相談・見積もりを依頼'][$lang] ?? 'Request Consultation';
$news_title  = ['vi' => 'Tin tức mới nhất', 'en' => 'Latest News', 'ja' => '最新ニュース'][$lang] ?? 'Latest News';
$feat_title  = ['vi' => 'Sản phẩm nổi bật', 'en' => 'Featured Products', 'ja' => '注目の製品'][$lang] ?? 'Featured Products';
?>
<aside class="sh-single-product__sidebar" aria-label="<?php echo esc_attr($sidebar_label); ?>">

    <div class="sh-single-product__cta">
        <span class="sh-single-product__cta-eyebrow"><?php echo esc_html($cta_eyebrow); ?></span>
        <p class="sh-single-product__cta-desc">
            <?php echo esc_html($cta_desc); ?>
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
            <?php echo esc_html($cta_btn); ?>
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
            <h3 class="sh-filter-block__title"><?php echo esc_html($news_title); ?></h3>
            <ul class="sh-sidebar-news__list">
                <?php while ($latest_news->have_posts()) : $latest_news->the_post(); ?>
                    <li class="sh-sidebar-news__item">
                        <a href="<?php the_permalink(); ?>" class="sh-sidebar-news__link">
                            <?php if (has_post_thumbnail()) : ?>
                                <span class="sh-sidebar-news__thumb">
                                    <?php the_post_thumbnail('medium', ['loading' => 'lazy', 'decoding' => 'async']); ?>
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
         data-storage-key="<?php echo esc_attr($viewed_storage_key); ?>"
         data-badge-viewing="<?php echo esc_attr($viewed_badge); ?>">
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
            <h3 class="sh-filter-block__title"><?php echo esc_html($feat_title); ?></h3>
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
    // ── Danh mục — Gọn gàng trên trang đơn, không render 18.000px cây danh mục của archive
    if ($is_product) {
        $cat_tax = taxonomy_exists('product_category') ? 'product_category' : (taxonomy_exists('product_cat') ? 'product_cat' : '');
        $prod_cats = [];
        if ($cat_tax) {
            $current_terms = wp_get_post_terms($current_id, $cat_tax);
            $parent_id = (!is_wp_error($current_terms) && !empty($current_terms)) ? (int)$current_terms[0]->parent : 0;
            $prod_cats = get_terms([
                'taxonomy'   => $cat_tax,
                'hide_empty' => true,
                'parent'     => $parent_id,
                'number'     => 8,
                'orderby'    => 'count',
                'order'      => 'DESC',
            ]);
            if (empty($prod_cats) || is_wp_error($prod_cats)) {
                $prod_cats = get_terms([
                    'taxonomy'   => $cat_tax,
                    'hide_empty' => true,
                    'number'     => 8,
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                ]);
            }
        }
        $cat_title = ['vi' => 'Danh mục liên quan', 'en' => 'Related Categories', 'ja' => '関連カテゴリー'][$lang] ?? 'Related Categories';
        $view_all_lbl = ['vi' => 'Xem tất cả sản phẩm', 'en' => 'Explore All Products', 'ja' => 'すべての製品を見る'][$lang] ?? 'Explore All Products';
        $all_products_url = function_exists('sgh_url') ? sgh_url('products') : home_url('/san-pham/');
        if (!empty($prod_cats) && !is_wp_error($prod_cats)) : ?>
            <div class="sh-filter-block sh-sidebar-categories">
                <h3 class="sh-filter-block__title"><?php echo esc_html($cat_title); ?></h3>
                <ul class="sh-filter-block__list">
                    <?php foreach ($prod_cats as $c) : 
                        $c_name = function_exists('sgh_translate_term_name') ? sgh_translate_term_name($c->name, $cat_tax, $lang) : $c->name;
                        $c_link = get_term_link($c);
                    ?>
                        <li class="sh-filter-block__item sh-filter-block__item--depth-0">
                            <a href="<?php echo esc_url($c_link); ?>">
                                <span><?php echo esc_html($c_name); ?></span>
                                <span class="sh-filter-block__count">(<?php echo (int) $c->count; ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="sh-sidebar-categories__footer" style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--color-border-subtle, #262626);">
                    <a href="<?php echo esc_url($all_products_url); ?>" class="sh-sidebar-categories__all-link" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.8125rem; color: var(--color-primary, #d97706); font-weight: 500; text-decoration: none;">
                        <span><?php echo esc_html($view_all_lbl); ?></span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        <?php endif;
    } else {
        // Post: flat list category bài viết, sort by count desc
        $post_cats = get_categories(['hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => 8]);
        $news_cat_title = ['vi' => 'Danh mục tin tức', 'en' => 'News Categories', 'ja' => 'ニュースカテゴリー'][$lang] ?? 'News Categories';
        if (!empty($post_cats) && !is_wp_error($post_cats)) : ?>
            <div class="sh-filter-block">
                <h3 class="sh-filter-block__title"><?php echo esc_html($news_cat_title); ?></h3>
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

<script nonce="<?php echo esc_attr($GLOBALS['sgh_csp_nonce'] ?? ''); ?>">
/* Bài / Sản phẩm đã xem — Tự động lưu và hiển thị danh sách vừa xem từ localStorage.
   Hỗ trợ cả post và product CPT dựa trên data-storage-key tương ứng. */
(function () {
    var block = document.querySelector('.sh-sidebar-viewed');
    if (!block) return;
    var KEY = block.dataset.storageKey;
    var badgeLabel = block.dataset.badgeViewing || 'Now viewing';
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
            + (isCurrent ? '<span class="sh-sidebar-viewed__badge">' + esc(badgeLabel) + '</span>' : '')
            + '</span>';

        return '<li class="' + itemClass + '">'
            + '<a class="sh-sidebar-viewed__link" href="' + esc(p.url) + '"' + (isCurrent ? ' onclick="event.preventDefault(); window.scrollTo({top: 0, behavior:\'smooth\'});"' : '') + '>'
            + t 
            + titleHtml
            + '</a></li>';
    }).join('');
})();
</script>
