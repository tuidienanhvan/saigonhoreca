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
        <figure class="pp-gallery-moa__item pp-image-container-shared pp-gallery-moa__item--<?php echo esc_attr($img['role']); ?>">
          <span class="pp-gallery-moa__corner" aria-hidden="true"></span>
          <img
            src="<?php echo sgh_img($img['src']); ?>"
            alt="<?php echo esc_attr($img['alt']); ?>"
            loading="lazy"
            decoding="async"
            width="1366"
            height="768">
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
