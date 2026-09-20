<?php
/**
 * Home — Hero Carousel (Saigon Horeca)
 *
 * Cloned from saigonhoreca-theme/template-parts/home/hero-carousel.php so
 * motion logic stays identical (Ken Burns zoom 6s + cross-fade 1.2s +
 * `is-hero-ready` class trigger from `assets/js/hero-carousel.js`).
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

// Query 5 projects for the hero slider dynamically
$slides = [];
$q_hero = new WP_Query([
    'post_type'      => 'project',
    'post_status'    => 'publish',
    'posts_per_page' => 5,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

if ($q_hero->have_posts()) {
    while ($q_hero->have_posts()) {
        $q_hero->the_post();
        global $post;
        $post_slug = isset($post->post_name) ? $post->post_name : '';
        
        // Cover attachment (đã responsive sau khi sgh_project_cover_ensure chạy)
        // → ưu tiên dùng cho srcset; fallback URL folder nếu chưa có attachment.
        $thumb_id  = get_post_thumbnail_id();
        $thumb_src = '';
        if ($thumb_id) {
            $thumb_src = wp_get_attachment_image_url($thumb_id, 'large');
        }
        if (!$thumb_src && $post_slug && function_exists('sgh_project_cover_url')) {
            $thumb_src = sgh_project_cover_url($post_slug);
        }

        // Skip if no image is available
        if (!$thumb_src) {
            continue;
        }
        
        $title = '';
        if ($post_slug && function_exists('sgh_get_project_meta')) {
            $title = sgh_get_project_meta($post_slug, 'Title');
        }
        if (!$title) {
            $title = get_the_title();
        }
        
        $address = '';
        if ($post_slug && function_exists('sgh_get_project_meta')) {
            $address = sgh_get_project_meta($post_slug, 'Address');
        }
        if (!$address) {
            $address = function_exists('get_field') ? (string) get_field('address') : '';
        }
        
        $btn_text_1 = function_exists('sgh_home_t') ? sgh_home_t('hero_btn_project') : 'CHI TIẾT DỰ ÁN';
        $btn_text_2 = function_exists('sgh_home_t') ? sgh_home_t('hero_btn_contact') : 'LIÊN HỆ NGAY';
        
        $current_lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';
        if ($address) {
            if ($current_lang === 'ja') {
                $subtitle = $address . ' における高級業務用厨房設備・バー設備プロジェクト';
            } elseif ($current_lang === 'en') {
                $subtitle = 'Premium commercial kitchen and bar equipment project at ' . $address;
            } else {
                $subtitle = 'Dự án thiết bị bếp công nghiệp và quầy bar cao cấp tại ' . $address;
            }
        } else {
            $subtitle = function_exists('sgh_home_t') ? sgh_home_t('hero_subtitle_default') : 'Tư vấn thiết kế, sản xuất và thi công trọn gói bếp nhà hàng chuyên nghiệp.';
        }
        
        $slides[] = [
            'img' => $thumb_src,
            'thumb_id' => $thumb_id,
            'alt' => $title . ' — Saigon Horeca',
            'title' => $title,
            'subtitle' => $subtitle,
            'btn_text_1' => $btn_text_1,
            'btn_link_1' => get_permalink(),
            'btn_text_2' => $btn_text_2,
            'btn_link_2' => sgh_url('contact'),
        ];
    }
    wp_reset_postdata();
}

// Fallback slides if no projects found with thumbnails
if (empty($slides)) {
    $fallback_title = function_exists('sgh_home_t') ? sgh_home_t('hero_fallback_title') : 'BẾP & BAR CÔNG NGHIỆP THẬT BẢN SẮC';
    $fallback_subtitle = function_exists('sgh_home_t') ? sgh_home_t('hero_fallback_subtitle') : 'Tư vấn thiết kế, sản xuất thiết bị inox và thi công trọn gói hệ thống bếp công nghiệp nhà hàng, quầy bar cao cấp.';
    $btn_explore = function_exists('sgh_home_t') ? sgh_home_t('hero_btn_explore') : 'XEM DỰ ÁN';
    $btn_contact = function_exists('sgh_home_t') ? sgh_home_t('hero_btn_contact') : 'LIÊN HỆ NGAY';
    $slides = [
        [
            'img' => esc_url(sgh_img('saigonhoreca/SGH-banner-02.webp')),
            'alt' => 'Bespoke Kitchen & Bar — Saigon Horeca',
            'title' => $fallback_title,
            'subtitle' => $fallback_subtitle,
            'btn_text_1' => $btn_explore,
            'btn_link_1' => sgh_url('projects_index'),
            'btn_text_2' => $btn_contact,
            'btn_link_2' => sgh_url('contact'),
        ]
    ];
}
?>
<section class="sh-hero" aria-label="<?php esc_attr_e('Slider giới thiệu Saigon Horeca', 'saigonhoreca'); ?>">
    <?php
    $h1_text = function_exists('sgh_home_t') ? sgh_home_t('hero_h1_title') : '';
    if (!$h1_text) {
        $curr_l = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';
        if ($curr_l === 'ja') {
            $h1_text = 'Saigon Horeca — 高級業務用厨房設備・バー設備設計施工';
        } elseif ($curr_l === 'en') {
            $h1_text = 'Saigon Horeca — Commercial Kitchen & Bar Equipment Solutions';
        } else {
            $h1_text = 'Saigon Horeca — Thiết Bị Bếp Công Nghiệp & Quầy Bar Chuyên Nghiệp';
        }
    }
    ?>
    <h1 class="sr-only"><?php echo esc_html($h1_text); ?></h1>
    <div id="sh-hero-slider" class="sh-hero__slider">
        <div class="sh-hero__slides">
            <?php foreach ($slides as $index => $s) :
                $is_active     = ($index === 0);
                $active_class  = $is_active ? 'active' : '';
                $initial_style = $is_active ? 'display:block;opacity:1;z-index:20;' : 'display:none;opacity:0;z-index:0;';
                $loading_attr  = $is_active ? 'eager' : 'lazy';
                $priority_attr = $is_active ? ' fetchpriority="high"' : '';
                $decoding_attr = $is_active ? 'sync' : 'async';
            ?>
                <div class="carousel-slide <?php echo $active_class; ?>"
                     style="<?php echo esc_attr($initial_style); ?>"
                     data-index="<?php echo (int) $index; ?>">
                    <?php
                    $hero_srcset = !empty($s['thumb_id']) ? wp_get_attachment_image_srcset($s['thumb_id'], 'large') : '';
                    ?>
                    <img
                        src="<?php echo esc_url($s['img']); ?>"
                        <?php if ($hero_srcset) : ?>srcset="<?php echo esc_attr($hero_srcset); ?>" sizes="(max-width: 768px) 100vw, 1200px"<?php endif; ?>
                        alt="<?php echo esc_attr($s['alt']); ?>"
                        width="1200" height="675"
                        loading="<?php echo esc_attr($loading_attr); ?>"
                        decoding="<?php echo esc_attr($decoding_attr); ?>"<?php echo $priority_attr; ?>
                        class="slide-bg sh-hero__bg-img"
                    />
                    <div class="sh-hero__overlay"></div>
                    
                    <!-- Lớp phủ nội dung chữ động -->
                    <div class="sh-hero__content-box sh-hero__content-box--bottom">
                        <div class="sh-hero__container">
                            <div class="sh-hero__actions sh-hero__actions--bottom">
                                <?php if (!empty($s['btn_text_1'])) : ?>
                                    <a href="<?php echo esc_url($s['btn_link_1']); ?>" class="sh-hero__btn sh-hero__btn--primary">
                                        <?php echo esc_html($s['btn_text_1']); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($s['btn_text_2'])) : ?>
                                    <a href="<?php echo esc_url($s['btn_link_2']); ?>" class="sh-hero__btn sh-hero__btn--secondary">
                                        <?php echo esc_html($s['btn_text_2']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button id="hero-prev" class="sh-hero__nav sh-hero__nav--prev" aria-label="<?php esc_attr_e('Slide trước', 'saigonhoreca'); ?>" title="<?php esc_attr_e('Slide trước', 'saigonhoreca'); ?>">
            <svg class="sh-hero__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <button id="hero-next" class="sh-hero__nav sh-hero__nav--next" aria-label="<?php esc_attr_e('Slide tiếp theo', 'saigonhoreca'); ?>" title="<?php esc_attr_e('Slide tiếp theo', 'saigonhoreca'); ?>">
            <svg class="sh-hero__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>

        <div class="sh-hero__dots">
            <?php foreach ($slides as $index => $s) : ?>
                <button class="carousel-dot sh-hero__dot <?php echo $index === 0 ? 'active' : ''; ?>"
                        data-index="<?php echo (int) $index; ?>"
                        aria-label="<?php echo esc_attr(sprintf(__('Đến slide %d', 'saigonhoreca'), $index + 1)); ?>"
                        title="<?php echo esc_attr(sprintf(__('Slide %d', 'saigonhoreca'), $index + 1)); ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
