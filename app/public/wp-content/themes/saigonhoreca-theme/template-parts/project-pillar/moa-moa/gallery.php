<?php
/**
 * Project Pillar — moa-moa (Moa Moa Pasta Club)
 * Section #6: gallery — GOLD editorial asymmetric mosaic + đúc kết dự án.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

/*
 * Mosaic editorial roles (varied tile sizes — NOT equal squares):
 *  - feature : tile lớn dạng chân dung, span 2 hàng (aspect 4/5)
 *  - tall    : tile dọc chuẩn (aspect 4/5)
 *  - standard: tile ngang chuẩn (aspect 4/3)
 *  - banner  : tile toàn chiều rộng dạng điện ảnh (aspect 16/9)
 */
$gallery = [
  [
    'src'     => 'moa-moa/moa-moa-burrata-ca-chua-bi-hung-que-sot-balsamic.webp',
    'alt'     => __('Burrata và cà chua bi – khai vị tươi mát chuẩn Ý tại Moa Moa', 'saigonhoreca'),
    'caption' => __('Burrata & cà chua bi – khai vị tươi mát chuẩn Ý', 'saigonhoreca'),
    'role'    => 'feature',
  ],
  [
    'src'     => 'moa-moa/moa-moa-matcha-mousse-huong-nhai-va-vai.webp',
    'alt'     => __('Jasmine matcha mousse & lychee – tráng miệng tại Moa Moa', 'saigonhoreca'),
    'caption' => __('Jasmine matcha mousse & lychee – dấu ấn tráng miệng', 'saigonhoreca'),
    'role'    => 'standard',
  ],
  [
    'src'     => 'moa-moa/moa-moa-menu-do-uong-coffee-juice.webp',
    'alt'     => __('Thực đơn đồ uống được trình bày tinh tế tại Moa Moa', 'saigonhoreca'),
    'caption' => __('Thực đơn đồ uống được trình bày tinh tế', 'saigonhoreca'),
    'role'    => 'standard',
  ],
  [
    'src'     => 'moa-moa/moa-moa-cannelloni-nhan-thit-sot-ca-chua.webp',
    'alt'     => __('Cannelloni sốt cà chua – món Ý đặc trưng tại Moa Moa', 'saigonhoreca'),
    'caption' => __('Cannelloni sốt cà chua – món Ý đặc trưng tại Moa Moa', 'saigonhoreca'),
    'role'    => 'banner',
  ],
];
?>
<section class="pp-section-moa pp-moa-gallery">
  <div class="pp-gallery-watermark-moa" aria-hidden="true">02</div>
  <div class="pp-ambient-glow-moa pp-ambient-glow-moa--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <header class="moa-gallery__header">
      <div class="pp-badge-moa">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
        </svg>
        <?php echo esc_html__('Đúc kết dự án', 'saigonhoreca'); ?>
      </div>
      <h2 class="pp-text-moa__title">
        <?php echo esc_html__('Không chỉ xây bếp, mà kiến tạo nền tảng cho câu chuyện ẩm thực', 'saigonhoreca'); ?>
      </h2>
      <div class="pp-text-moa__divider" aria-hidden="true"></div>
      <p class="moa-gallery__lead">
        <?php echo esc_html__('Dự án Moa Moa không đơn thuần là việc cung cấp thiết bị. Đó là quá trình tư vấn – thiết kế – lắp đặt dựa trên sự thấu hiểu mô hình pasta restaurant, văn hoá ẩm thực Ý và áp lực vận hành thực tế tại trung tâm TP.HCM.', 'saigonhoreca'); ?>
      </p>
    </header>

    <div class="pp-gallery-moa" aria-label="<?php echo esc_attr__('Thư viện hình ảnh dự án Moa Moa Pasta Club', 'saigonhoreca'); ?>">

      <!-- Số chìm kỹ thuật trang trí editorial -->
      <span class="pp-gallery-moa__index" aria-hidden="true">MOA · 04</span>

      <?php foreach ($gallery as $i => $img): ?>
        <?php 
          // Các path viền cho 4 ảnh trong Gallery tương ứng với clipPath dough-4, 5, 6, 7
          $border_paths = [
            'M 12,8 C 38,1 70,4 88,12 C 98,22 99,50 95,78 C 90,92 72,98 48,97 C 25,96 8,88 4,65 C -4,48 2,22 12,8 Z', // dough-4
            'M 6,15 C 20,5 65,-2 85,8 C 98,18 96,48 94,72 C 92,90 72,99 50,97 C 28,95 10,85 4,62 C -4,42 1,22 6,15 Z', // dough-5
            'M 15,8 C 45,2 78,5 90,18 C 100,32 96,58 92,80 C 88,95 62,99 45,96 C 28,93 10,82 4,58 C -4,38 2,18 15,8 Z', // dough-6
            'M 5,10 C 30,3 70,5 92,12 C 98,22 99,52 97,75 C 95,92 75,98 50,97 C 25,96 8,88 3,65 C -3,45 1,22 5,10 Z'  // dough-7
          ];
          $current_path = isset($border_paths[$i]) ? $border_paths[$i] : $border_paths[0];
        ?>
        <figure class="pp-gallery-moa__item pp-image-container-shared pp-gallery-moa__item--<?php echo esc_attr($img['role']); ?> moa-gallery__item-custom">
          <span class="pp-gallery-moa__corner" aria-hidden="true"></span>
          <img
            src="<?php echo sgh_img($img['src']); ?>"
            alt="<?php echo esc_attr($img['alt']); ?>"
            loading="lazy"
            decoding="async"
            width="1366"
            height="768">
          <!-- Khung viền SVG vẽ nét khớp với clip path tương ứng trong Gallery -->
          <svg class="moa-gallery__image-border" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
            <path d="<?php echo esc_attr($current_path); ?>" vector-effect="non-scaling-stroke" />
          </svg>
          <span class="pp-gallery-moa__num" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $i + 1)); ?></span>
          <div class="pp-image-caption-shared"><?php echo esc_html($img['caption']); ?></div>
        </figure>
      <?php endforeach; ?>
    </div>

    <div class="moa-gallery__quote">
      <span class="moa-gallery__quote-line" aria-hidden="true"></span>
      <blockquote>
        <?php echo esc_html__('Với Moa Moa, Saigon Horeca tiếp tục khẳng định vai trò của mình: không chỉ xây bếp, mà kiến tạo nền tảng để ẩm thực được kể đúng câu chuyện của nó.', 'saigonhoreca'); ?>
      </blockquote>
      <span class="moa-gallery__quote-line" aria-hidden="true"></span>
    </div>
  </div>
</section>
