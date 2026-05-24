<?php
/**
 * Project Pillar — bling-bling-club
 * Section #2: with_gallery — Bespoke Cyberpunk Glamour Glow Edition
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt pp-bbc-intro-section">
  <div class="pp__container">
    <div class="pp-bbc-intro__grid">
      <!-- Cột trái: Diamond Glamour Editorial Grid -->
      <div class="pp-bbc-intro__text-col">
        <div class="pp-bbc-intro__badge">
          <span class="glitter-dot"></span>
          Premium Nightlife Hub
        </div>
        <div class="pp-text-bbc pp-text-bbc--left">
          <h2 class="pp-text-bbc__title"><?php echo esc_html__('Bling Bling Club', 'saigonhoreca'); ?></h2>
          <span class="pp-text-bbc__divider" aria-hidden="true"></span>
          
          <div class="pp-bbc-intro__chips">
            <span class="pp-bbc-intro__chip">📍 75 Xuân Thuỷ, Thảo Điền, Q.2</span>
            <span class="pp-bbc-intro__chip">⏰ Club: 21:00 – 03:00 (Daily)</span>
          </div>

          <div class="pp-text-bbc__body">
            <p class="pp-bbc-intro__lead"><?php echo esc_html__('Bling Bling Club là một biểu tượng giải trí về đêm mới được xây dựng và bắt đầu hoạt động vào cuối năm 2023, tọa lạc tại trung tâm thảo điền đầy phong cách.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Bling Bling Club tự hào sở hữu không gian lớn nhất và được thiết kế tỉ mỉ nhất ở khu vực Thảo Điền. Bầu không khí của câu lạc bộ được tạo ra theo phong cách hiện đại, kết hợp hài hòa giữa hệ thống ánh sáng và âm thanh hiện đại bậc nhất.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Tầng trệt là khu vực chính của câu lạc bộ, với một sàn nhảy rộng lớn và hệ thống âm thanh cao cấp. Tầng hai là khu vực VIP, mang lại sự riêng tư, sang trọng và sự tinh tế vượt trội.', 'saigonhoreca'); ?></p>
            
            <div class="pp-bbc-intro__glass-card">
              <span class="neon-line-gold"></span>
              <p>“Bling Bling Club sở hữu một quầy bar thanh lịch phục vụ đa dạng các loại đồ uống cao cấp. Hơn nữa, sự tập trung vào chất lượng rõ ràng không chỉ xuất hiện trong khu vực quầy bar được lựa chọn cẩn thận mà còn trong hệ thống nhà bếp công nghiệp riêng biệt do Saigon Horeca thiết kế, phục vụ hoàn hảo cho cả khu vực trệt và EM Restaurant chuyên nghiệp ở tầng ba dành riêng cho khách VIP.”</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột phải: 3D Faceted Diamond Frame cho ảnh lớn đại diện -->
      <div class="pp-bbc-intro__media-col">
        <div class="pp-bbc-intro__diamond-frame">
          <div class="pp-bbc-intro__glow-haze"></div>
          <div class="pp-bbc-intro__img-container">
            <img src="<?php echo sgh_img('2024/02/SGH-product-bling3.jpg'); ?>" alt="Bling Bling Club Interior" class="pp-bbc-intro__main-img" loading="lazy" decoding="async">
            <div class="pp-bbc-intro__img-overlay"></div>
          </div>
          <div class="pp-bbc-intro__frame-borders">
            <span class="border-angle tl"></span>
            <span class="border-angle tr"></span>
            <span class="border-angle bl"></span>
            <span class="border-angle br"></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Gallery bên dưới: Faceted Clip-path Gallery với Gold Glow -->
    <div class="pp-bbc-intro__gallery-section">
      <div class="pp-bbc-intro__gallery-title">
        <span class="star-sparkle">✦</span>
        <h3>FACETED EXPERIENCE GALLERY</h3>
        <span class="star-sparkle">✦</span>
      </div>
      
      <div class="pp-gallery-bbc pp-gallery-bbc--cols-3">
        <div class="pp-gallery-bbc__item">
          <img src="<?php echo sgh_img('2024/02/SGH-product-bling2.jpg'); ?>" alt="Bling Bling Club 2" loading="lazy" decoding="async">
          <div class="gold-glamour-border"></div>
        </div>
        <div class="pp-gallery-bbc__item">
          <img src="<?php echo sgh_img('2024/02/SGH-product-bling1.jpg'); ?>" alt="Bling Bling Club 1" loading="lazy" decoding="async">
          <div class="gold-glamour-border"></div>
        </div>
        <div class="pp-gallery-bbc__item">
          <img src="<?php echo sgh_img('2023/12/SGH-Portrait.png'); ?>" alt="Bling Bling Club 3" loading="lazy" decoding="async">
          <div class="gold-glamour-border"></div>
        </div>
      </div>
    </div>
  </div>
</section>
