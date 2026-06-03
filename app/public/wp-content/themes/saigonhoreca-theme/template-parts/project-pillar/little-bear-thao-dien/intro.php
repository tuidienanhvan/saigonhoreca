<?php
/**
 * Project Pillar - little-bear-thao-dien
 * Section #2: intro
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$meta_items = [
  [
    'label' => __('Địa điểm', 'saigonhoreca'),
    'value' => __('36 Nguyễn Bá Huân, Thảo Điền, TP.HCM', 'saigonhoreca'),
  ],
  [
    'label' => __('Khung giờ phục vụ', 'saigonhoreca'),
    'value' => __('17h30 - 21h00 | Thứ ba - Chủ nhật', 'saigonhoreca'),
  ],
  [
    'label' => __('Nhận đặt bàn tối', 'saigonhoreca'),
    'value' => __('Đến trước 17h00', 'saigonhoreca'),
  ],
];

$scope_items = [
  __('Thiết kế line bếp theo nhịp phục vụ dinner service', 'saigonhoreca'),
  __('Tích hợp thiết bị Rational, Hoshizaki và Fujimak', 'saigonhoreca'),
  __('Gia công inox riêng theo mặt bằng hiện hữu', 'saigonhoreca'),
];

$gallery = [
  [
    'src' => 'little-bear-thao-dien/little-bear-thao-dien-dau-bep-thao-tac.jpg',
    'alt' => __('Hình ảnh đầu bếp đang thao tác chế biến tinh tế tại line bếp nóng Little Bear Thảo Điền', 'saigonhoreca'),
    'class' => 'pp-intro-lb__gallery-item--feature',
    'caption' => __('Đầu bếp tập trung thao tác chuyên nghiệp tại khu vực bếp mở, tạo nên nhịp điệu phục vụ tối ưu.', 'saigonhoreca'),
  ],
  [
    'src' => 'little-bear-thao-dien/little-bear-thao-dien-khong-gian-bep-bar.jpg',
    'alt' => __('Không gian kết nối hài hòa giữa quầy bar mở và line bếp công nghiệp hiện đại', 'saigonhoreca'),
    'class' => 'pp-intro-lb__gallery-item--compact',
    'caption' => __('Bar station và bếp mở kết nối mượt mà, giúp thực khách trải nghiệm ẩm thực trực quan.', 'saigonhoreca'),
  ],
  [
    'src' => 'little-bear-thao-dien/little-bear-thao-dien-mat-tien-nha-hang.jpg',
    'alt' => __('Mặt tiền sang trọng và thanh nhã của nhà hàng Little Bear tại Thảo Điền', 'saigonhoreca'),
    'class' => 'pp-intro-lb__gallery-item--compact pp-intro-lb__gallery-item--entry',
    'caption' => __('Lối vào ấm cúng ngập tràn ánh sáng, dẫn dắt thực khách vào không gian ẩm thực tinh tế.', 'saigonhoreca'),
  ],
  [
    'src' => 'little-bear-thao-dien/little-bear-thao-dien-chi-tiet-bep-inox.jpg',
    'alt' => __('Chi tiết bề mặt inox gia công sắc sảo cùng thiết bị bếp cao cấp tại Little Bear', 'saigonhoreca'),
    'class' => 'pp-intro-lb__gallery-item--wide',
    'caption' => __('Hệ thống kệ inox 304 gia công sắc nét cùng các module thiết bị tối tân, sẵn sàng cho cường độ vận hành mỗi đêm.', 'saigonhoreca'),
  ],
];
?>
<section class="pp__section pp__section--alt pp-intro-lb scroll-reveal">
  <!-- Lưới kỹ thuật AutoCAD mờ chạy ngầm toàn nền -->
  <svg class="pp-lb-bg-grid" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
      <pattern id="lb-grid-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(245, 166, 35, 0.012)" stroke-width="1"/>
      </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#lb-grid-pattern)" />
  </svg>

  <!-- SVG Trục la bàn tọa độ kỹ sư xoay chậm mờ ảo dưới nền -->
  <svg class="pp-lb-compass-decor" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <circle cx="100" cy="100" r="80" fill="none" stroke="rgba(245, 166, 35, 0.025)" stroke-width="0.5" stroke-dasharray="4 2"/>
    <circle cx="100" cy="100" r="60" fill="none" stroke="rgba(245, 166, 35, 0.015)" stroke-width="0.5"/>
    <line x1="100" y1="10" x2="100" y2="190" stroke="rgba(245, 166, 35, 0.02)" stroke-width="0.5"/>
    <line x1="10" y1="100" x2="190" y2="100" stroke="rgba(245, 166, 35, 0.02)" stroke-width="0.5"/>
    <path d="M 100 20 L 97 30 L 103 30 Z" fill="rgba(245, 166, 35, 0.04)"/>
    <path d="M 100 180 L 97 170 L 103 170 Z" fill="rgba(245, 166, 35, 0.04)"/>
    <text x="100" y="15" font-family="monospace" font-size="6" fill="rgba(245, 166, 35, 0.08)" text-anchor="middle">N</text>
    <text x="100" y="193" font-family="monospace" font-size="6" fill="rgba(245, 166, 35, 0.08)" text-anchor="middle">S</text>
  </svg>

  <div class="pp-container-shared">
    <div class="pp-intro-lb__grid">
      <div class="pp-intro-lb__content">
        <span class="pp-intro-lb__divider" aria-hidden="true"></span>
        <span class="pp-intro-lb__eyebrow"><?php echo esc_html__('Restaurant profile', 'saigonhoreca'); ?></span>
        <h2 class="pp-intro-lb__title"><?php echo esc_html__('Little Bear Thảo Điền', 'saigonhoreca'); ?></h2>
        
        <div class="pp-intro-lb__lead">
          <p><?php echo esc_html__('Một không gian F&B nhỏ nhưng có nhịp vận hành rõ, nơi bếp mở, quầy bar và trải nghiệm khách được giữ trong cùng một mạch liên tục.', 'saigonhoreca'); ?></p>
        </div>

        <div class="pp-intro-lb__body">
          <div class="pp-lb-highlight-quote">
            <p>
              <span class="pp-lb-dropcap">B</span><?php echo esc_html__('ước vào Little Bear, khách gặp ngay chất liệu gạch ấm, cây xanh mềm và nhịp phục vụ gần gũi. Không gian nhỏ nhưng không hẹp; từng bề mặt, từng line thao tác đều cần gọn, chính xác và bền trước cường độ vận hành buổi tối.', 'saigonhoreca'); ?>
            </p>
          </div>
          <p><?php echo esc_html__('Saigon Horeca tham gia hoàn thiện căn bếp đa năng với thiết bị từ Rational, Hoshizaki, Fujimak cùng các hạng mục inox đặt riêng. Mục tiêu không chỉ là đủ công năng, mà còn là giữ cho khu bếp và quầy bar vận hành sạch, nhanh và đồng bộ với tinh thần của Little Bear.', 'saigonhoreca'); ?></p>
        </div>
      </div>

      <aside class="pp-intro-lb__rail" aria-label="<?php echo esc_attr__('Thông tin dự án', 'saigonhoreca'); ?>">
        <div class="pp-intro-lb__panel">
          <span class="pp-intro-lb__panel-label"><?php echo esc_html__('Vận hành thực tế', 'saigonhoreca'); ?></span>
          <div class="pp-intro-lb__meta">
            <?php foreach ($meta_items as $item) : ?>
              <div class="pp-intro-lb__meta-item">
                <span><?php echo esc_html($item['label']); ?></span>
                <strong><?php echo esc_html($item['value']); ?></strong>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="pp-intro-lb__panel pp-intro-lb__panel--soft">
          <span class="pp-intro-lb__panel-label"><?php echo esc_html__('Phạm vi Saigon Horeca', 'saigonhoreca'); ?></span>
          <ul class="pp-intro-lb__scope">
            <?php foreach ($scope_items as $item) : ?>
              <li><?php echo esc_html($item); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </</aside>
    </div>

    <!-- Gallery 4 ảnh dạng Asymmetric Metro Layout -->
    <div class="pp-intro-lb__gallery" aria-label="<?php echo esc_attr__('Hình ảnh Little Bear Thảo Điền', 'saigonhoreca'); ?>">
      <?php foreach ($gallery as $image) : ?>
        <figure class="pp-intro-lb__gallery-item <?php echo esc_attr($image['class']); ?>">
          <div class="pp-image-container-shared">
            <img src="<?php echo esc_url(sgh_img($image['src'])); ?>" alt="<?php echo esc_attr($image['alt']); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html($image['caption']); ?></div>
            <!-- SVG Bounding Box đứt nét kỹ thuật -->
            <svg class="pp-lb-bounding-box" width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <rect x="0.5" y="0.5" width="99" height="99" fill="none" stroke="var(--gold-dim)" stroke-width="1" stroke-dasharray="2 4"/>
              <path d="M0 10 L0 0 L10 0 M90 0 L100 0 L100 10 M100 90 L100 100 L90 100 M10 100 L0 100 L0 90" fill="none" stroke="var(--gold)" stroke-width="2"/>
            </svg>
          </div>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
