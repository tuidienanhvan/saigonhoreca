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

// Lấy danh sách 6 dự án tiêu biểu mới nhất
$featured = [];
$q_fp = new WP_Query([
    'post_type'      => 'project',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

if ($q_fp->have_posts()) {
  while ($q_fp->have_posts()) {
        $q_fp->the_post();
  global $post;
        $post_slug = isset($post->post_name) ? $post->post_name : '';
        
        $thumb_src = '';
  if (function_exists('sgh_project_cover_ensure')) {
            $thumb_id = sgh_project_cover_ensure($post->ID);
        } else {
            $thumb_id = get_post_thumbnail_id();
        }
  if ($thumb_id) {
            $thumb_src = wp_get_attachment_image_url($thumb_id, 'large');
        }
  if (!$thumb_src && function_exists('sgh_project_cover_url') && $post_slug) {
            $thumb_src = sgh_project_cover_url($post_slug);
        }
        
        $title = get_the_title();
        
        $address = '';
  if ($post_slug && function_exists('sgh_get_project_meta')) {
            $address = sgh_get_project_meta($post_slug, 'Address');
        }
  if (!$address) {
            $address = function_exists('get_field') ? (string) get_field('address') : '';
        }
        
  if ($thumb_src) {
            $featured[] = [
                'src'     => $thumb_src,
                'title'   => $title,
                'address' => $address,
                'url'     => get_permalink(),
            ];
        }
    }
  wp_reset_postdata();
}

$stats = [
    ['number' => '80+',  'label' => __('Khách Hàng', 'saigonhoreca')],
    ['number' => '100+', 'label' => __('Dự Án', 'saigonhoreca')],
    ['number' => '200+', 'label' => __('Thiết Kế', 'saigonhoreca')],
];
?>
<section class="sh-featured-projects">
  <div class="sh-featured-projects__inner">

  <!-- Intro: accent + title + desc + CTA -->
  <div class="sh-featured-projects__head">
  <span class="sh-featured-projects__accent" aria-hidden="true"></span>
  <h2 class="sh-featured-projects__title">
  <?php esc_html_e('Những dự án tiêu biểu của Saigon Horeca', 'saigonhoreca'); ?>
  </h2>
  <p class="sh-featured-projects__desc">
  Hệ thống bếp công nghiệp, quầy bar và giải pháp horeca cho hơn 50 nhà hàng, khách sạn, cà phê cao cấp tại Việt Nam — từ chuỗi F&amp;B Michelin đến boutique restaurants.
  </p>
  <a href="<?php echo esc_url(sgh_url('projects_index')); ?>" class="sh-featured-projects__cta">
  <span><?php esc_html_e('Xem tất cả dự án', 'saigonhoreca'); ?></span>
  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true" style="flex:0 0 auto;">
  <path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>
  </a>
  </div>

  <!-- Grid: 6 project cards (3 cols × 2 rows on desktop).
  Cinematic: ảnh GRAYSCALE + dim → hover FULL MÀU + scale.
             Đã loại bỏ HTML text overlay vì thumbnail đã có sẵn chữ (baked-in text). -->
  <div class="sh-featured-projects__grid">
  <?php
  foreach ($featured as $i => $item) : 
                // Ảnh từ CPT query đã trả URL chuẩn
                $img_path = $item['src'];
                
                // Trích xuất path tương đối cho -mobile fallback
                $uploads_base_url = function_exists('sgh_uploads_base_url') ? sgh_uploads_base_url() : wp_get_upload_dir()['baseurl'];
                $relative_path = str_replace($uploads_base_url . '/', '', $img_path);
                
                $img_ext = pathinfo($relative_path, PATHINFO_EXTENSION);
                $img_base = substr($relative_path, 0, -(strlen($img_ext) + 1));
                $img_mobile = "{$img_base}-mobile.{$img_ext}";
                
                $uploads_dir = function_exists('sgh_uploads_base_dir')
                    ? sgh_uploads_base_dir()
                    : wp_normalize_path(wp_get_upload_dir()['basedir']);
                $local_mobile_exists = file_exists($uploads_dir . '/' . $img_mobile);
                
                $srcset_attr = '';
  if ($local_mobile_exists) {
                    $srcset_attr = ' srcset="' . esc_url(sgh_img($img_mobile)) . ' 450w, ' . esc_url($img_path) . ' 600w" sizes="(max-width: 576px) 450px, 600px"';
                }
                $delay = ($i % 3) * 150;
                $delay_class = $delay > 0 ? " delay-{$delay}" : "";
            ?>
  <a href="<?php echo esc_url($item['url']); ?>"
  class="sh-featured-projects__card duration-1800<?php echo $delay_class; ?>">
  <img src="<?php echo esc_url($img_path); ?>"
  <?php echo $srcset_attr; ?>
  alt="<?php echo esc_attr($item['title']); ?>"
  width="600" height="400"
  loading="lazy" decoding="async"
  class="sh-featured-projects__img">
  </a>
  <?php endforeach; ?>
  </div>

  <!-- Stats strip: 80+ / 100+ / 200+ -->
  <div class="sh-featured-projects__stats">
  <?php foreach ($stats as $s) : ?>
  <div class="sh-featured-projects__stat">
  <div class="sh-featured-projects__stat-num"><?php echo esc_html($s['number']); ?></div>
  <div class="sh-featured-projects__stat-label"><?php echo esc_html($s['label']); ?></div>
  </div>
  <?php endforeach; ?>
  </div>

  </div>
</section>
