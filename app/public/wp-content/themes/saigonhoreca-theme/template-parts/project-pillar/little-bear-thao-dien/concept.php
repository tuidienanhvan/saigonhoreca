<?php
/**
 * Project Pillar — little-bear-thao-dien
 * Section #3: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt pp-concept-lb-sec scroll-reveal">
  <!-- Glow mờ nghệ thuật làm nền -->
  <div class="pp-concept-lb__glow" aria-hidden="true"></div>

  <!-- SVG Trục compa góc quay kỹ thuật mờ ảo -->
  <svg class="pp-lb-concept-svg-decor" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(245, 166, 35, 0.02)" stroke-width="0.5" stroke-dasharray="10 5"/>
    <circle cx="50" cy="50" r="30" fill="none" stroke="rgba(245, 166, 35, 0.01)" stroke-width="0.25"/>
    <path d="M 50 5 L 50 95 M 5 50 L 95 50" stroke="rgba(245, 166, 35, 0.015)" stroke-width="0.25"/>
    <path d="M 18.2 18.2 L 81.8 81.8 M 18.2 81.8 L 81.8 18.2" stroke="rgba(245, 166, 35, 0.01)" stroke-width="0.25" stroke-dasharray="1 1"/>
  </svg>

  <!-- Họa tiết Mesh Grille mờ ảo phía sau -->
  <svg class="pp-lb-mesh-decor" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
      <pattern id="lb-mesh-pattern" width="20" height="20" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
        <rect width="18" height="18" fill="none" stroke="rgba(245, 166, 35, 0.03)" stroke-width="1"/>
      </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#lb-mesh-pattern)" />
  </svg>

  <div class="pp-container-shared">
    <div class="pp-concept-lb__grid">
      
      <!-- Cột ảnh (7 cột bên trái trên màn lớn) -->
      <div class="pp-concept-lb__media-col">
        <div class="pp-image-container-shared pp-concept-lb__image-container">
          <div class="pp-concept-lb__border-decor" aria-hidden="true"></div>
          <img src="<?php echo sgh_img('little-bear-thao-dien/little-bear-thao-dien-bep-chinh-inox.jpg'); ?>" alt="<?php echo esc_attr__('Không gian quầy bar gỗ sồi và ô vòm bếp mở nghệ thuật dưới đèn neon ly rượu vang tại Little Bear Thảo Điền', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Góc nhìn nghệ thuật từ bàn tiệc gỗ tròn qua ô cửa vòm bếp mở, nơi thực khách vừa thưởng thức rượu vang vừa trực tiếp cảm nhận sinh khí chế biến món ăn đầy say đắm từ các đầu bếp.', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <!-- Cột chữ (Glassmorphism card đè chéo, 5 cột bên phải) -->
      <div class="pp-concept-lb__text-col">
        <div class="pp-concept-card-lb">
          <span class="pp-concept-card-lb__node" aria-hidden="true">03</span>
          <header class="pp-concept-card-lb__header">
            <span class="pp-concept-card-tag"><?php echo esc_html__('Concept Thiết Kế', 'saigonhoreca'); ?></span>
            <h2 class="pp-concept-card-lb__title"><?php echo esc_html__('Thiết kế của Little Bear', 'saigonhoreca'); ?></h2>
            <div class="pp-concept-card-lb__divider" aria-hidden="true"></div>
          </header>

          <div class="pp-concept-card-lb__body">
            <p><?php echo esc_html__('Bếp của Little Bear Thảo Điền mang phong cách kết hợp giữa sự tối giản, hiện đại và tinh tế. Saigon Horeca chủ yếu sử dụng vật liệu thép không gỉ, biến nơi đây thành một không gian làm việc chuyên nghiệp và tiện nghi cho các đầu bếp.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
