<?php
/**
 * Project Pillar — bling-bling-club
 * Section #3: split — Bespoke Cyberpunk Glamour Glow Edition
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-bbc-concept-section">
  <div class="pp__container">
    <div class="pp-bbc-concept__grid">
      <!-- Cột trái: Diamond Glitter Panels -->
      <div class="pp-bbc-concept__text-col">
        <div class="pp-bbc-concept__panels">
          <div class="pp-bbc-concept__panel pp-bbc-concept__panel--primary">
            <span class="glitter-line-top"></span>
            <div class="panel-content">
              <span class="panel-tag">Glamour Design</span>
              <h3 class="panel-title"><?php echo esc_html__('Ý tưởng thiết kế của Bling Bling Club', 'saigonhoreca'); ?></h3>
              <p><?php echo esc_html__('Phong cách thiết kế hiện đại và tinh tế của Bling Bling Club không chỉ thể hiện qua việc lựa chọn nội thất mà còn qua những chi tiết nhỏ nhất, từ hệ thống ánh sáng đến màu sắc và bố trí không gian.', 'saigonhoreca'); ?></p>
            </div>
          </div>

          <div class="pp-bbc-concept__panel pp-bbc-concept__panel--secondary">
            <span class="glitter-line-left"></span>
            <div class="panel-content">
              <span class="panel-tag">Execution & Harmony</span>
              <p><?php echo esc_html__('Sự hài hòa giữa ý tưởng sáng tạo và thực thi chuyên nghiệp từ Sài Gòn Horeca đã tạo ra một không gian cao cấp, nơi mà khách hàng trải nghiệm sự hòa hợp hoàn hảo giữa thiết kế và chất lượng thiết bị inox 304 vững chắc.', 'saigonhoreca'); ?></p>
              <div class="panel-features-gold">
                <span class="feat-pill">✦ Faceted Design</span>
                <span class="feat-pill">✦ Golden Haze Ambient</span>
                <span class="feat-pill">✦ Premium Inox 304</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột phải: Cyber Frame với Gold Ticks Ruler -->
      <div class="pp-bbc-concept__media-col">
        <div class="pp-bbc-concept__cyber-frame">
          <div class="pp-bbc-concept__ruler-x" aria-hidden="true">
            <span class="ruler-tick"></span>
            <span class="ruler-tick"></span>
            <span class="ruler-tick"></span>
            <span class="ruler-tick"></span>
            <span class="ruler-tick"></span>
          </div>
          <div class="pp-bbc-concept__image-wrapper">
            <img src="<?php echo sgh_img('2024/02/bling-bling-club-space-1-1.jpg'); ?>" alt="Thiết bị bếp công nghiệp Saigon Horeca tại Bling Bling Club" loading="lazy" decoding="async">
            <div class="gold-haze-glow"></div>
          </div>
          <div class="pp-bbc-concept__ruler-y" aria-hidden="true">
            <span class="ruler-tick"></span>
            <span class="ruler-tick"></span>
            <span class="ruler-tick"></span>
            <span class="ruler-tick"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
