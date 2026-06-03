<?php
/**
 * Home — Featured Projects (gộp 3 phần thành 1 section).
 *
 * Đã merge:
 *   - intro (title + desc + CTA "Xem tất cả dự án")
 *   - grid 6 dự án tiêu biểu (CPT `project` với fallback ảnh production)
 *   - stats strip 80+ / 100+ / 200+
 *
 * Cùng 1 section nền đen, giống production saigonhoreca.vn.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

// Curated featured projects — unique hero images, 6 visually diverse Gemini-polished pillars.
// [src, title, address, url]
// Skip CPT query (project posts have unreliable/duplicate thumbnails on local).
$q = null;

$featured = [
    [
        'src'     => 'saigonhoreca/the-royal-sgh-10.webp',
        'title'   => 'Amdang Typhoon',
        'address' => 'Asian Luxe Restaurant, HCMC',
        'url'     => sgh_url('projects_index', 'amdang-typhoon/'),
    ],
    [
        'src'     => 'saigonhoreca/the-royal-sgh-8.webp',
        'title'   => 'Heiwa Sushi Omakase',
        'address' => 'Japanese Omakase, HCMC',
        'url'     => sgh_url('projects_index', 'heiwa-sushi-omakase/'),
    ],
    [
        'src'     => 'skyloft-by-glow/skyloft-by-glow-quay-bar-rooftop-dem.webp',
        'title'   => 'Skyloft by Glow',
        'address' => 'Rooftop Bar, President Place, Quận 1, HCMC',
        'url'     => sgh_url('projects_index', 'skyloft-by-glow/'),
    ],
    [
        'src'     => 'saigonhoreca/SGH-mammam.webp',
        'title'   => 'Grand Marble',
        'address' => 'Thương hiệu bánh cao cấp Nhật Bản',
        'url'     => sgh_url('projects_index', 'grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/'),
    ],
    [
        'src'     => 'saigonhoreca/the-royal-sgh-10.webp',
        'title'   => 'The Royal — All Day Dining',
        'address' => '41-47 Đông Du, Bến Nghé, Quận 1, HCMC',
        'url'     => sgh_url('projects_index', 'the-royal-all-day-dining/'),
    ],
    [
        'src'     => 'saigonhoreca/the-royal-sgh-10.webp',
        'title'   => 'GodMother Friendship',
        'address' => 'Bake & Brunch, Thảo Điền, HCMC',
        'url'     => sgh_url('projects_index', 'godmother-friendship/'),
    ],
];

$stats = [
    ['number' => '80+',  'label' => __('Khách Hàng', 'saigonhoreca')],
    ['number' => '100+', 'label' => __('Dự Án', 'saigonhoreca')],
    ['number' => '200+', 'label' => __('Thiết Kế', 'saigonhoreca')],
];
?>
<section class="sh-featured-projects">
    <div class="sh-featured-projects__inner">

        <!-- Intro: accent + title + desc + CTA -->
        <div class="sh-featured-projects__head scroll-reveal reveal-letter-wide duration-1800">
            <span class="sh-featured-projects__accent" aria-hidden="true"></span>
            <h2 class="sh-featured-projects__title">
                <?php esc_html_e('Những dự án tiêu biểu của Saigon Horeca', 'saigonhoreca'); ?>
            </h2>
            <p class="sh-featured-projects__desc">
                Hệ thống bếp công nghiệp, quầy bar và giải pháp horeca cho hơn 50 nhà hàng, khách sạn, cà phê cao cấp tại Việt Nam — từ chuỗi F&amp;B Michelin đến boutique restaurants.
            </p>
            <a href="<?php echo esc_url(sgh_url('projects_index')); ?>" class="sh-featured-projects__cta">
                <span><?php esc_html_e('Xem tất cả dự án', 'saigonhoreca'); ?></span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>

        <!-- Grid: 6 project cards (3 cols × 2 rows on desktop).
             Cinematic: ảnh GRAYSCALE + dim → hover FULL MÀU + scale.
             Info bar dưới mỗi card: avatar (S logo) + title gold +
             address dim — theme black + gold, không card trắng. -->
        <div class="sh-featured-projects__grid">
            <?php
            $avatar = $uri . '/assets/images/logo.webp';
            foreach ($featured as $i => $item) : 
                // Automatically retrieve project slug from URL to fetch custom templates thumbnail dynamically
                $slug = '';
                if (preg_match('#/du-an/([a-z0-9\-]+)/?#', $item['url'], $m)) {
                    $slug = $m[1];
                }
                
                $custom_thumb = '';
                if ($slug && function_exists('sgh_get_project_thumbnail')) {
                    $custom_thumb = sgh_get_project_thumbnail($slug);
                }
                
                $img_path = $custom_thumb ? str_replace(sgh_uploads_base_url() . '/', '', $custom_thumb) : $item['src'];
                $img_ext = pathinfo($img_path, PATHINFO_EXTENSION);
                $img_base = substr($img_path, 0, -(strlen($img_ext) + 1));
                $img_mobile = "{$img_base}-mobile.{$img_ext}";
                
                $uploads_dir = function_exists('sgh_uploads_base_dir')
                    ? sgh_uploads_base_dir()
                    : wp_normalize_path(wp_get_upload_dir()['basedir']);
                $local_mobile_exists = file_exists($uploads_dir . '/' . $img_mobile);
                
                $srcset_attr = '';
                if ($local_mobile_exists) {
                    $srcset_attr = ' srcset="' . esc_url(sgh_img($img_mobile)) . ' 450w, ' . esc_url(sgh_img($img_path)) . ' 600w" sizes="(max-width: 576px) 450px, 600px"';
                }
                $delay = ($i % 3) * 150;
                $delay_class = $delay > 0 ? " delay-{$delay}" : "";
            ?>
                <a href="<?php echo esc_url($item['url']); ?>"
                   class="sh-featured-projects__card scroll-reveal reveal-spring-up duration-1800<?php echo $delay_class; ?>">
                    <img src="<?php echo esc_url(sgh_img($item['src'])); ?>"
                         <?php echo $srcset_attr; ?>
                         alt="<?php echo esc_attr($item['title']); ?>"
                         width="600" height="400"
                         loading="lazy" decoding="async"
                         class="sh-featured-projects__img">
                    <div class="sh-featured-projects__card-info">
                        <span class="sh-featured-projects__card-avatar" aria-hidden="true">
                            <img src="<?php echo esc_url($avatar); ?>" alt="" width="25" height="25" loading="lazy" decoding="async">
                        </span>
                        <div class="sh-featured-projects__card-text">
                            <h3 class="sh-featured-projects__card-title"><?php echo esc_html($item['title']); ?></h3>
                            <p class="sh-featured-projects__card-addr"><?php echo esc_html($item['address']); ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Stats strip: 80+ / 100+ / 200+ -->
        <div class="sh-featured-projects__stats scroll-reveal reveal-3d-fold-up duration-1800 delay-300">
            <?php foreach ($stats as $s) : ?>
                <div class="sh-featured-projects__stat">
                    <div class="sh-featured-projects__stat-num"><?php echo esc_html($s['number']); ?></div>
                    <div class="sh-featured-projects__stat-label"><?php echo esc_html($s['label']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
