<?php
/**
 * Template Part: Hero Carousel (SwiperJS)
 * 
 * Component chạy carousel Hero banner ở trang chủ.
 * @var array $args Có thể truyền 'slides' (mảng các slide).
 * 
 * @package SaigonHouse
 */

$theme_uri = get_template_directory_uri();
$placeholder_image = $theme_uri . '/assets/images/placeholder.svg';

$slides = [];

if (isset($args['slides']) && is_array($args['slides']) && !empty($args['slides'])) {
    $slides = $args['slides'];
} else {
    // Hero slides: random posts each request (without ORDER BY RAND to keep query fast).
    $video_cat = get_category_by_slug('video-du-an');
    $exclude_cat_id = $video_cat ? $video_cat->term_id : 0;

    $query_args = [
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => 30,
        'orderby'                => 'date',
        'order'                  => 'DESC',
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'fields'                 => 'ids',
    ];

    if ($exclude_cat_id) {
        $query_args['category__not_in'] = [$exclude_cat_id];
    }

    $hero_post_ids = get_posts($query_args);

    if (is_array($hero_post_ids) && !empty($hero_post_ids)) {
        // LCP optimization: keep FIRST slide deterministic (most recent post)
        // so wp_head preload matches the actual LCP image. T-016: only one
        // extra slide (was 2) to keep the carousel light — every slide is
        // ~22 nodes of nested wrappers.
        $first_id = array_shift($hero_post_ids);
        shuffle($hero_post_ids);
        $hero_post_ids = array_merge([$first_id], array_slice($hero_post_ids, 0, 1));

        foreach ($hero_post_ids as $post_id) {
            $post_id = (int) $post_id;
            if ($post_id <= 0) {
                continue;
            }

            $thumb_data = saigonhouse_get_post_thumbnail_data($post_id, 'full');
            $title = get_the_title($post_id);
            $excerpt = get_the_excerpt($post_id);

            if (empty($excerpt)) {
                $raw_content = wp_strip_all_tags(strip_shortcodes((string) get_post_field('post_content', $post_id)));
                $excerpt = wp_trim_words($raw_content, 24, '...');
            } else {
                $excerpt = wp_trim_words($excerpt, 24, '...');
            }
            
            // Strip markdown asterisks that leaked into excerpt
            $excerpt = preg_replace('/(\*\*|\*)/', '', $excerpt);

            if ('' === trim((string) $title)) {
                $title = get_bloginfo('name');
            }
            if ('' === trim((string) $excerpt)) {
                $excerpt = get_bloginfo('description');
            }

            $slides[] = [
                'image'    => isset($thumb_data['url']) ? (string) $thumb_data['url'] : '',
                'image_id' => isset($thumb_data['id']) ? (int) $thumb_data['id'] : 0,
                'title'    => $title,
                'subtitle' => $excerpt,
                'btn_text' => 'Xem Chi Tiết',
                'link'     => get_permalink($post_id),
            ];
        }
    }

    // Fallback if there are no posts yet
    if (empty($slides)) {
        $slides[] = [
            'image'    => $placeholder_image,
            'image_id' => 0,
            'title'    => 'Chưa có bài viết',
            'subtitle' => 'Hãy thêm bài viết để Hero Carousel hiển thị nội dung tự động.',
            'btn_text' => 'Xem Trang Chủ',
            'link'     => home_url('/'),
        ];
    }

    // Keep 2 slides minimum for the carousel arrows/dots UX.
    // T-016: was 3 — dropped to 2 to save ~22 DOM nodes.
    while (count($slides) < 2) {
        $slides[] = $slides[count($slides) - 1];
    }

    // Optional override: if Hero background URL/ID is set in Front Page meta
    $front_page_id = (int) get_option('page_on_front');
    if ($front_page_id <= 0) {
        $front_page_id = (int) get_queried_object_id();
    }
    if ($front_page_id > 0) {
        for ($i = 1; $i <= 3; $i++) {
            $bg = get_post_meta($front_page_id, "_pi_hero_{$i}_bg", true);
            if (empty($bg)) {
                continue;
            }

            $resolved_image = '';
            $resolved_image_id = 0;

            if (is_numeric($bg)) {
                $resolved_image_id = (int) $bg;
                $resolved_image = wp_get_attachment_image_url($resolved_image_id, 'full');
            } else {
                $resolved_image = (string) $bg;
                $resolved_image_id = (int) attachment_url_to_postid($resolved_image);
            }

            if (!empty($resolved_image)) {
                $slides[$i - 1]['image'] = $resolved_image;
                $slides[$i - 1]['image_id'] = $resolved_image_id;
            }

            // Override title/subtitle/button if set in hero meta
            $hero_title = get_post_meta($front_page_id, "_pi_hero_{$i}_title", true);
            $hero_subtitle = get_post_meta($front_page_id, "_pi_hero_{$i}_subtitle", true);
            $hero_btn_text = get_post_meta($front_page_id, "_pi_hero_{$i}_btn_text", true);
            $hero_btn_link = get_post_meta($front_page_id, "_pi_hero_{$i}_btn_link", true);

            if (!empty($hero_title)) {
                $slides[$i - 1]['title'] = $hero_title;
            }
            if (!empty($hero_subtitle)) {
                $slides[$i - 1]['subtitle'] = $hero_subtitle;
            }
            if (!empty($hero_btn_text)) {
                $slides[$i - 1]['btn_text'] = $hero_btn_text;
            }
            if (!empty($hero_btn_link)) {
                $slides[$i - 1]['link'] = $hero_btn_link;
            }
        }
    }
}
?>
<section id="hero-carousel" class="sh-hero" aria-label="Slider giới thiệu Saigon House" data-aos="fade-in" data-aos-delay="50">
    <div id="sh-hero-slider" class="sh-hero__slider">
        <div class="sh-hero__slides">
            <?php foreach($slides as $index => $slide):
                $image = $slide['image'] ?? '';
                $image_id = (int) ($slide['image_id'] ?? 0);
                $title = $slide['title'] ?? '';
                $subtitle = $slide['subtitle'] ?? '';
                $btn_text = $slide['btn_text'] ?? 'Xem Chi Tiết';
                $link = $slide['link'] ?? '#';
                $is_active = ($index === 0);
                $active_class = $is_active ? 'active' : '';
                $initial_style = $is_active ? 'display:block;opacity:1;z-index:20;' : 'display:none;opacity:0;z-index:0;';
                if (!trim($title)) $title = get_bloginfo('name');
                if (!trim($subtitle)) $subtitle = get_bloginfo('description');
                if (!trim($btn_text)) $btn_text = 'Xem Chi Tiết';
                if (empty($link)) $link = home_url('/');
                if (empty($image)) { $image = $placeholder_image; $image_id = 0; }

                $loading_attr = ($index <= 1) ? 'eager' : 'lazy';
                $priority_attr = ($index === 0) ? ' fetchpriority="high"' : '';
                $decoding_attr = ($index <= 1) ? 'sync' : 'async';
                $srcset_attr = '';
                $dimension_attrs = '';
                if ($image_id > 0) {
                    $srcset = wp_get_attachment_image_srcset($image_id, 'full');
                    if ($srcset) $srcset_attr = ' srcset="' . esc_attr($srcset) . '"';
                    $meta = wp_get_attachment_image_src($image_id, 'full');
                    if (is_array($meta) && !empty($meta[0])) {
                        $image = $meta[0];
                        $dimension_attrs = ' width="' . (int) $meta[1] . '" height="' . (int) $meta[2] . '"';
                    }
                }
            ?>
            <div class="carousel-slide <?php echo $active_class; ?>" style="<?php echo esc_attr($initial_style); ?>" data-index="<?php echo $index; ?>">
                <img src="<?php echo esc_url($image); ?>" class="slide-bg sh-hero__bg-img" alt="<?php echo esc_attr($title); ?>" loading="<?php echo esc_attr($loading_attr); ?>" decoding="<?php echo esc_attr($decoding_attr); ?>" data-fallback="<?php echo esc_url($placeholder_image); ?>" onerror="if(this.dataset.fallback){this.src=this.dataset.fallback;this.dataset.fallback='';}" sizes="100vw"<?php echo $priority_attr . $srcset_attr . $dimension_attrs; ?> />
                <div class="sh-hero__overlay"></div>
                <div class="sh-hero__content-wrap">
                    <div class="sh-hero__content-container">
                        <div class="sh-hero__content-inner">
                            <div class="hero-content sh-hero__badge">
                                <span class="sh-hero__badge-dot"></span>
                                <span class="sh-hero__badge-text">Kiến Trúc & Xây Dựng Saigon House</span>
                            </div>
                            <?php if ($is_active): ?>
                                <h1 class="hero-content sh-hero__title"><?php echo esc_html($title); ?></h1>
                            <?php else: ?>
                                <h2 class="hero-content sh-hero__title"><?php echo esc_html($title); ?></h2>
                            <?php endif; ?>
                            <!-- <p class="hero-content sh-hero__desc"><?php echo esc_html($subtitle); ?></p> -->
                            <div class="hero-content sh-hero__buttons">
                                <a href="<?php echo esc_url($link); ?>" class="sh-hero__btn sh-hero__btn--primary">
                                    <span><?php echo esc_html($btn_text); ?></span>
                                    <?php echo sh_icon('arrow-right', 'sh-hero__btn-icon'); ?>
                                </a>
                                <a href="<?php echo home_url('/du-toan/'); ?>" class="sh-hero__btn sh-hero__btn--outline">Dự Toán Online</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <button id="hero-prev" class="sh-hero__nav sh-hero__nav--prev" aria-label="Slide trước" title="Slide trước">
            <?php echo sh_icon('chevron-left', 'sh-hero__nav-icon'); ?>
        </button>
        <button id="hero-next" class="sh-hero__nav sh-hero__nav--next" aria-label="Slide tiếp theo" title="Slide tiếp theo">
            <?php echo sh_icon('chevron-right', 'sh-hero__nav-icon'); ?>
        </button>

        <div class="sh-hero__dots">
            <?php foreach($slides as $index => $slide): ?>
                <button class="carousel-dot sh-hero__dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" aria-label="Đến slide <?php echo $index + 1; ?>" title="Slide <?php echo $index + 1; ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
