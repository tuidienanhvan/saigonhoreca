<?php
/**
 * Template Part: Villa Designs (Bento Grid)
 * @package SaigonHouse
 */

$placeholder_image = get_template_directory_uri() . '/assets/images/placeholder.svg';
$fp_id = (int) get_option('page_on_front');

$villas = [
    ['title' => 'Harmony Riverside Villa', 'desc' => 'Thiết kế mở với hồ bơi vô cực và sân vườn nhiệt đới, kiến tạo chuẩn mực sống nghỉ dưỡng.', 'img' => $placeholder_image, 'img_id' => 0, 'url' => '#'],
    ['title' => 'Biệt Thự Tân Cổ Điển', 'desc' => '', 'img' => $placeholder_image, 'img_id' => 0, 'url' => '#'],
    ['title' => 'Biệt Thự Mái Thái', 'desc' => '', 'img' => $placeholder_image, 'img_id' => 0, 'url' => '#'],
    ['title' => 'Biệt Thự Phố 2025', 'desc' => '', 'img' => $placeholder_image, 'img_id' => 0, 'url' => '#'],
    ['title' => 'Thư viện thiết kế', 'desc' => '', 'img' => $placeholder_image, 'img_id' => 0, 'url' => home_url('/category/thiet-ke-biet-thu/')]
];

// Query real posts
$vi_query = new WP_Query([
    'post_type' => 'post', 'posts_per_page' => 16, 'no_found_rows' => true,
    'update_post_meta_cache' => false, 'update_post_term_cache' => false,
    'orderby' => 'date', 'order' => 'DESC',
    'tax_query' => [
        'relation' => 'AND',
        ['taxonomy' => 'category', 'field' => 'slug', 'terms' => 'thiet-ke-biet-thu'],
        ['taxonomy' => 'category', 'field' => 'slug', 'terms' => ['luat-xay-dung', 'cam-nang', 'bao-gia-theo-loai-nha'], 'operator' => 'NOT IN']
    ],
]);

if ($vi_query->have_posts()) {
    $skip_keywords = ['dịch vụ', 'công ty', 'báo giá', 'bảng giá', 'chi phí', 'xây biệt thự'];
    $i = 0;
    while ($vi_query->have_posts() && $i < 5) {
        $vi_query->the_post();
        $title_lower = mb_strtolower(get_the_title(), 'UTF-8');
        $is_marketing = false;
        foreach ($skip_keywords as $kw) {
            if (mb_strpos($title_lower, $kw) !== false) { $is_marketing = true; break; }
        }
        if ($is_marketing) continue;

        $thumb_data = saigonhouse_get_post_thumbnail_data(get_the_ID(), 'large');
        $villas[$i]['img_id'] = $thumb_data['id'];
        $villas[$i]['img'] = $thumb_data['url'];
        if ($i < 4) {
            $villas[$i]['title'] = get_the_title();
            $villas[$i]['url'] = get_permalink();
            $villas[$i]['desc'] = wp_trim_words(get_the_excerpt(), 15, '...');
        } else {
            $villas[$i]['url'] = get_permalink();
            $villas[$i]['title'] = get_the_title();
        }
        $i++;
    }
    wp_reset_postdata();
}

$render_villa_image = static function(array $item, string $alt, string $class, string $sizes): void {
    if (!empty($item['img_id'])) {
        echo wp_get_attachment_image((int) $item['img_id'], 'large', false, [
            'class' => $class, 'alt' => $alt, 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => $sizes,
        ]);
        return;
    }
    echo '<img src="' . esc_url($item['img'] ?? '') . '" alt="' . esc_attr($alt) . '" class="' . esc_attr($class) . '" loading="lazy" decoding="async" sizes="' . esc_attr($sizes) . '">';
};
?>
<section class="sh-villa" id="villa-designs">
    <div class="sh-villa__bg-grid sh-villa__bg-grid--minor"></div>
    <div class="sh-villa__bg-grid sh-villa__bg-grid--major"></div>

    <div class="sh-villa__container">
        <!-- Header -->
        <div class="sh-villa__header" data-aos="fade-up">
            <div class="sh-villa__header-left">
                <span class="sh-villa__tag"><?php echo esc_html(get_post_meta($fp_id, '_sgh_villa_tag', true) ?: 'Kiến Trúc'); ?></span>
                <h2 class="sh-villa__title"><?php echo esc_html(get_post_meta($fp_id, '_sgh_villa_title', true) ?: 'DI SẢN'); ?> <br><span class="sh-villa__title-accent"><?php echo esc_html(get_post_meta($fp_id, '_sgh_villa_accent', true) ?: 'XANH'); ?></span></h2>
            </div>
            <div class="sh-villa__header-right">
                <p class="sh-villa__desc">
                    "<?php echo esc_html(get_post_meta($fp_id, '_sgh_villa_desc', true) ?: 'Nơi thiên nhiên giao hòa trong từng hơi thở, kiến tạo chuẩn mực sống thượng lưu giữa lòng phố thị.'); ?>"
                </p>
                <div class="sh-villa__specs">
                    <div><span class="sh-villa__spec-label"><?php echo esc_html(get_post_meta($fp_id, '_sgh_villa_spec1_label', true) ?: 'Diện Tích'); ?></span><span class="sh-villa__spec-value"><?php echo esc_html(get_post_meta($fp_id, '_sgh_villa_spec1_value', true) ?: '300m²'); ?></span></div>
                    <div><span class="sh-villa__spec-label"><?php echo esc_html(get_post_meta($fp_id, '_sgh_villa_spec2_label', true) ?: 'Phong Cách'); ?></span><span class="sh-villa__spec-value"><?php echo esc_html(get_post_meta($fp_id, '_sgh_villa_spec2_value', true) ?: 'Modern'); ?></span></div>
                </div>
            </div>
        </div>

        <!-- Bento Grid -->
        <div class="sh-villa__grid">
            <!-- Featured (large) -->
            <a href="<?php echo esc_url($villas[0]['url']); ?>" class="sh-villa__item sh-villa__item--featured sgh-img-zoom" data-aos="elastic-zoom">
                <?php $render_villa_image($villas[0], $villas[0]['title'], 'sh-villa__img', '(max-width: 1023px) 100vw, 50vw'); ?>
                <div class="sh-villa__overlay"></div>
                <div class="sh-villa__item-content">
                    <span class="sh-villa__badge">Nổi Bật</span>
                    <h3 class="sh-villa__item-title sh-villa__item-title--lg"><?php echo esc_html($villas[0]['title']); ?></h3>
                    <?php if (!empty($villas[0]['desc'])): ?>
                    <p class="sh-villa__item-desc"><?php echo esc_html($villas[0]['desc']); ?></p>
                    <?php endif; ?>
                </div>
            </a>

            <!-- Scrollable items (mobile: h-scroll, desktop: grid cells) -->
            <div class="sh-villa__scroll sh-hscroll">
                <?php for ($v = 1; $v <= 3; $v++): ?>
                <a href="<?php echo esc_url($villas[$v]['url']); ?>" class="sh-villa__item sh-villa__item--small sgh-img-zoom" data-aos="elastic-zoom" data-aos-delay="<?php echo $v * 100; ?>">
                    <?php $render_villa_image($villas[$v], $villas[$v]['title'], 'sh-villa__img', '(max-width: 639px) 60vw, (max-width: 1023px) 45vw, 25vw'); ?>
                    <div class="sh-villa__overlay"></div>
                    <div class="sh-villa__item-content">
                        <h4 class="sh-villa__item-title"><?php echo esc_html($villas[$v]['title']); ?></h4>
                    </div>
                </a>
                <?php endfor; ?>

                <!-- View All -->
                <a href="<?php echo esc_url($villas[4]['url']); ?>" class="sh-villa__item sh-villa__item--cta sgh-img-zoom" data-aos="elastic-zoom" data-aos-delay="400">
                    <?php $render_villa_image($villas[4], $villas[4]['title'], 'sh-villa__img sh-villa__img--dim', '(max-width: 639px) 60vw, (max-width: 1023px) 45vw, 25vw'); ?>
                    <div class="sh-villa__overlay sh-villa__overlay--dark"></div>
                    <div class="sh-villa__cta-content">
                        <div>
                            <span class="sh-villa__cta-label">Khám Phá</span>
                            <h3 class="sh-villa__cta-title"><?php echo esc_html(wp_trim_words($villas[4]['title'], 6, '')); ?></h3>
                        </div>
                        <div class="sh-villa__cta-footer">
                            <span class="sh-villa__cta-link">Xem chi tiết</span>
                            <div class="sh-villa__cta-arrow"><?php echo sh_icon('arrow-right', 'sh-villa__cta-arrow-icon'); ?></div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Mobile View All -->
        <div class="sh-villa__mobile-cta">
            <a href="<?php echo esc_url(get_post_meta($fp_id, '_sgh_villa_cta_url', true) ?: home_url('/category/thiet-ke-biet-thu/')); ?>" class="sh-villa__mobile-btn"><?php echo esc_html(get_post_meta($fp_id, '_sgh_villa_cta', true) ?: 'Xem Tất Cả Biệt Thự'); ?></a>
        </div>
    </div>
</section>
