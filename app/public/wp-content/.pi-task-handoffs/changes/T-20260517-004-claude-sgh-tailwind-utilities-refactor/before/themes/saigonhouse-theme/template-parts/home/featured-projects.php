<?php
/**
 * Template Part: Featured Projects (Dark Marquee Slider)
 * @package SaigonHouse
 */

$placeholder_image = get_template_directory_uri() . '/assets/images/placeholder.svg';

// Query featured projects
// T-016: cap to 3 posts (was 8). Marquee duplicates the set for the
// infinite-loop animation so 3×2 = 6 visible cards keeps the strip
// full at standard viewport widths.
$args = [
    'post_type' => 'post', 'posts_per_page' => 3, 'category_name' => 'du-an', 'tag' => 'tieu-bieu',
    'meta_query' => [['key' => '_thumbnail_id', 'compare' => 'EXISTS']],
    'orderby' => 'date', 'order' => 'DESC', 'no_found_rows' => true,
    'update_post_meta_cache' => false, 'update_post_term_cache' => false,
];
$fq = new WP_Query($args);
if (!$fq->have_posts()) { unset($args['tag']); $fq = new WP_Query($args); }
if (!$fq->have_posts()) { unset($args['category_name']); $fq = new WP_Query($args); }
if (!$fq->have_posts()) { unset($args['meta_query']); $fq = new WP_Query($args); }

$projects = [];
if ($fq->have_posts()) {
    while ($fq->have_posts()) {
        $fq->the_post();
        $cats = get_the_category();
        $img_id = (int) get_post_thumbnail_id();
        $projects[] = [
            'title' => get_the_title(), 'year' => get_the_date('Y'),
            'type' => !empty($cats) ? $cats[0]->name : 'Dự Án',
            'img' => $img_id > 0 ? wp_get_attachment_image_url($img_id, 'large') : $placeholder_image,
            'img_id' => $img_id, 'area' => 'Mới', 'url' => get_permalink(),
        ];
    }
    wp_reset_postdata();
}
if (empty($projects)) {
    $projects[] = ['title'=>'Chưa có dự án','year'=>date('Y'),'type'=>'Dự Án','img'=>$placeholder_image,'img_id'=>0,'area'=>'','url'=>home_url('/')];
}
$display_projects = array_merge($projects, $projects);

$render_img = static function(array $p, string $alt): void {
    $class = 'sh-fp__card-img';
    $sizes = '(max-width: 767px) 85vw, (max-width: 1023px) 450px, 480px';
    if (!empty($p['img_id'])) {
        echo wp_get_attachment_image((int)$p['img_id'], 'large', false, ['class'=>$class,'alt'=>$alt,'loading'=>'lazy','decoding'=>'async','sizes'=>$sizes]);
        return;
    }
    echo '<img src="'.esc_url($p['img']??'').'" class="'.esc_attr($class).'" alt="'.esc_attr($alt).'" loading="lazy" decoding="async" sizes="'.esc_attr($sizes).'">';
};
?>
<section class="sh-fp sgh-cv-auto">
    <!-- Decorative elements — T-016: compass + grid SVGs (12 child
         nodes) replaced with CSS-only versions via .sh-fp__decos
         backgrounds. Borders + spotlight kept as thin divs. -->
    <div class="sh-fp__noise"></div>
    <div class="sh-fp__decos sh-fp__decos--css"></div>
    <div class="sh-fp__border-top"></div>
    <div class="sh-fp__border-bottom"></div>
    <div class="sh-fp__spotlight"></div>

    <div class="sh-fp__container">
        <!-- Header -->
        <div class="sh-fp__header" data-aos="swing-in">
            <div class="sh-fp__header-line sh-fp__header-line--left"></div>
            <div class="sh-fp__header-line sh-fp__header-line--right"></div>
            <span class="sh-fp__badge">Năng Lực Thi Công</span>
            <h2 class="sh-fp__title">Dự Án <span class="sh-fp__title-accent">Tiêu Biểu</span></h2>
            <p class="sh-fp__subtitle">Tinh hoa kiến trúc & dấu ấn cá nhân trong từng công trình</p>
        </div>

        <!-- Marquee -->
        <div class="sh-fp__marquee-wrap">
            <div class="animate-marquee sh-fp__marquee-track">
                <?php foreach ($display_projects as $key => $p): ?>
                <a href="<?php echo esc_url($p['url']); ?>" class="sh-fp__card sgh-img-zoom" data-aos="elastic-zoom" data-aos-delay="<?php echo ($key % count($projects)) * 100; ?>">
                    <?php $render_img($p, $p['title']); ?>
                    <div class="sh-fp__card-overlay"></div>
                    <div class="sh-fp__card-info">
                        <div>
                            <div class="sh-fp__card-meta">
                                <span class="sh-fp__card-type"><?php echo esc_html($p['type']); ?></span>
                                <span class="sh-fp__card-year"><?php echo esc_html($p['year']); ?></span>
                            </div>
                            <h3 class="sh-fp__card-title"><?php echo esc_html($p['title']); ?></h3>
                            <p class="sh-fp__card-location">
                                <?php echo sh_icon('map-pin', 'sh-fp__card-loc-icon'); ?> Tp. Hồ Chí Minh
                                <span class="sh-fp__card-sep">|</span>
                                <?php echo sh_icon('layout', 'sh-fp__card-loc-icon'); ?> <?php echo esc_html($p['area']); ?>
                            </p>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Text Marquee -->
        <div class="sh-fp__text-marquee-wrap">
            <div class="animate-marquee-reverse sh-fp__text-marquee-track">
                <?php
                // T-016: text marquee duplicated 2× (was 3×) for the
                // infinite-loop animation. 6×2 = 12 spans is enough to
                // fill the visible viewport on any breakpoint.
                $kws = ['KIẾN TRÚC','NỘI THẤT','XÂY DỰNG','CẢNH QUAN','QUY HOẠCH','CẢI TẠO'];
                foreach (array_merge($kws, $kws) as $k):
                ?>
                <span class="sh-fp__text-marquee-item"><?php echo esc_html($k); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Background circles -->
    <div class="sh-fp__bg-circle sh-fp__bg-circle--lg"></div>
    <div class="sh-fp__bg-circle sh-fp__bg-circle--sm"></div>
</section>
