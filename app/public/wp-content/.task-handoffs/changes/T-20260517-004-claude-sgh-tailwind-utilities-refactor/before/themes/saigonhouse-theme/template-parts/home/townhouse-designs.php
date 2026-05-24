<?php
/**
 * Template Part: Townhouse Designs
 * @package SaigonHouse
 */
$fp_id = (int) get_option('page_on_front');

$build_fallback_image = static function (int $index): string {
    $palettes = [['#0f766e','#0e7490'],['#0369a1','#1d4ed8'],['#b45309','#dc2626'],['#0f766e','#16a34a']];
    $p = $palettes[$index % count($palettes)];
    $label = 'NHA PHO ' . ($index + 1);
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 1000"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="'.$p[0].'"/><stop offset="100%" stop-color="'.$p[1].'"/></linearGradient></defs><rect width="800" height="1000" fill="url(#g)"/><circle cx="650" cy="180" r="170" fill="rgba(255,255,255,0.12)"/><rect x="140" y="190" width="520" height="620" rx="22" fill="rgba(255,255,255,0.18)"/><text x="400" y="520" text-anchor="middle" fill="rgba(255,255,255,0.9)" font-size="64" font-family="Arial" font-weight="700">'.$label.'</text></svg>';
    return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
};

// T-016: cap to 3 cards (was 4) — each card is ~22 DOM nodes.
$townhouses = [
    ['img' => $build_fallback_image(0), 'img_id' => 0, 'title' => 'Nhà Phố Lệch Tầng', 'tag' => 'Mới', 'url' => '#'],
    ['img' => $build_fallback_image(1), 'img_id' => 0, 'title' => 'Nhà Phố Tân Cổ Điển', 'tag' => 'Mới', 'url' => '#'],
    ['img' => $build_fallback_image(2), 'img_id' => 0, 'title' => 'Nhà Phố 2 Mặt Tiền', 'tag' => 'Mới', 'url' => '#'],
];

$th_query = new WP_Query([
    'post_type' => 'post', 'posts_per_page' => 16, 'no_found_rows' => true,
    'update_post_meta_cache' => false, 'update_post_term_cache' => false,
    'orderby' => 'date', 'order' => 'DESC',
    'tax_query' => [
        'relation' => 'AND',
        ['taxonomy' => 'category', 'field' => 'slug', 'terms' => 'thiet-ke-nha-pho'],
        ['taxonomy' => 'category', 'field' => 'slug', 'terms' => ['luat-xay-dung','cam-nang','bao-gia-theo-loai-nha'], 'operator' => 'NOT IN']
    ],
]);

if ($th_query->have_posts()) {
    $skip = ['dịch vụ','công ty','báo giá','bảng giá','chi phí'];
    $generic = ['thiết kế nhà phố đẹp','thiết kế nhà phố','nhà phố đẹp','mẫu nhà phố đẹp'];
    $i = 0;
    while ($th_query->have_posts() && $i < 3) {
        $th_query->the_post();
        $tl = mb_strtolower(get_the_title(), 'UTF-8');
        $is_skip = false;
        foreach ($skip as $kw) { if (mb_strpos($tl, $kw) !== false) { $is_skip = true; break; } }
        if (in_array($tl, $generic, true)) $is_skip = true;
        if ($is_skip) continue;
        $td = saigonhouse_get_post_thumbnail_data(get_the_ID(), 'large');
        $townhouses[$i] = ['img_id' => $td['id'], 'img' => $td['url'], 'title' => get_the_title(), 'tag' => 'Mới', 'url' => get_permalink()];
        $i++;
    }
    wp_reset_postdata();
}

$render_img = static function (array $item, string $alt): void {
    $class = 'sh-townhouse-img';
    $sizes = '(max-width: 767px) 100vw, (max-width: 1023px) 50vw, 25vw';
    if (!empty($item['img_id'])) {
        echo wp_get_attachment_image((int)$item['img_id'], 'large', false, ['class'=>$class,'alt'=>$alt,'loading'=>'lazy','decoding'=>'async','sizes'=>$sizes]);
        return;
    }
    $src = $item['img'] ?? '';
    $safe = strpos($src,'data:image/')===0 ? esc_attr($src) : esc_url($src);
    echo '<img src="'.$safe.'" alt="'.esc_attr($alt).'" class="'.esc_attr($class).'" loading="lazy" decoding="async" sizes="'.esc_attr($sizes).'">';
};
?>
<section class="sh-townhouse-section sgh-cv-auto" id="mau-nha-pho">
    <div class="sh-townhouse-bg-glow sh-townhouse-bg-glow--one" aria-hidden="true"></div>
    <div class="sh-townhouse-bg-glow sh-townhouse-bg-glow--two" aria-hidden="true"></div>
    <div class="sh-townhouse-grid" aria-hidden="true"></div>

    <div class="sh-townhouse-container">
        <!-- Header -->
        <div class="sh-townhouse-header" data-aos="bounce-up">
            <div class="sh-townhouse-header__inner">
                <div class="sh-townhouse-header__label">
                    <span class="sh-townhouse-header__line"></span>
                    <span class="sh-townhouse-header__label-text"><?php echo esc_html(get_post_meta($fp_id, '_sgh_town_label', true) ?: 'Bộ Sưu Tập 2026'); ?></span>
                </div>
                <h2 class="sh-townhouse-header__title">
                    <?php echo esc_html(get_post_meta($fp_id, '_sgh_town_title', true) ?: 'Kiến Trúc'); ?> <span class="sh-townhouse-header__title-accent"><?php echo esc_html(get_post_meta($fp_id, '_sgh_town_accent', true) ?: 'Nhà Phố'); ?>
                        <svg class="sh-townhouse-header__underline-svg" viewBox="0 0 100 15" preserveAspectRatio="none">
                            <path d="M0 10 Q 50 15 100 10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.3"></path>
                            <path d="M0 10 Q 50 15 100 10" stroke="currentColor" stroke-width="2" fill="none"></path>
                        </svg>
                    </span>
                </h2>
                <p class="sh-townhouse-header__desc"><?php echo esc_html(get_post_meta($fp_id, '_sgh_town_desc', true) ?: 'Tuyển tập những mẫu thiết kế nhà phố tối ưu công năng, chuẩn phong thủy và mang đậm dấu ấn cá nhân.'); ?></p>
            </div>
        </div>

        <!-- Cards -->
        <div class="sh-townhouse-scroll sh-hscroll">
            <?php foreach ($townhouses as $th_index => $item): ?>
            <a href="<?php echo esc_url($item['url']); ?>" class="sh-townhouse-link" data-aos="skate-in-right" data-aos-delay="<?php echo $th_index * 100; ?>">
                <div class="sh-townhouse-card">
                    <div class="sh-townhouse-media sgh-img-zoom sh-townhouse-media--decor">
                        <?php $render_img($item, $item['title']); ?>
                        <span class="sh-townhouse-badge"><?php echo esc_html($item['tag']); ?></span>
                    </div>

                    <div class="sh-townhouse-content">
                        <h3 class="sh-townhouse-title"><?php echo esc_html($item['title']); ?></h3>
                        <p class="sh-townhouse-sub">Thiết kế kiến trúc</p>
                        <span class="sh-townhouse-read">XEM CHI TIẾT</span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Mobile CTA -->
        <div class="sh-townhouse-mobile-cta">
            <a href="<?php echo esc_url(get_post_meta($fp_id, '_sgh_town_cta_url', true) ?: home_url('/thiet-ke-nha-pho')); ?>" class="sh-townhouse-mobile-btn"><?php echo esc_html(get_post_meta($fp_id, '_sgh_town_cta', true) ?: 'Xem Tất Cả Nhà Phố'); ?></a>
        </div>
    </div>
</section>
