<?php
/**
 * Project Pillar — bambino-saigonhoreca
 * Section #3: split — Bespoke Cyberpunk Neon Glow Edition
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-bambino-concept">
  <div class="pp__container">
    <div class="pp-bambino-concept__grid">
      <!-- Cột trái: Thiết kế Asymmetric Double Panel Grid -->
      <div class="pp-bambino-concept__text-col">
        <div class="pp-bambino-concept__panels">
          <div class="pp-bambino-concept__panel pp-bambino-concept__panel--primary">
            <span class="neon-line-top"></span>
            <div class="panel-content">
              <span class="panel-tag">Concept & Soul</span>
              <h3 class="panel-title">Ý Tưởng Chủ Đạo Thiết Kế của Bambino</h3>
              <p>Qua trao đổi về concept nhà hàng kiểu Ý, Saigon Horeca đã tư vấn thiết kế nhà bếp cho Bambino với những nét đặc trưng cùng những thiết bị đầy đủ công năng và hiện đại.</p>
            </div>
            <div class="panel-corner-glow"></div>
          </div>

          <div class="pp-bambino-concept__panel pp-bambino-concept__panel--secondary">
            <span class="neon-line-left"></span>
            <div class="panel-content">
              <span class="panel-tag">Bespoke Solution</span>
              <p>Với sự kết hợp giữa thiết bị tự sản xuất và nhập khẩu trực tiếp từ Italy, không gian làm việc bên trong nhà bếp đã phản ánh rõ nét tinh thần nước Ý một cách mạnh mẽ.</p>
              <div class="panel-meta-list">
                <span class="meta-item">✓ Inox 304 cao cấp</span>
                <span class="meta-item">✓ Thiết bị Italy nhập khẩu</span>
                <span class="meta-item">✓ Quy trình bếp một chiều</span>
              </div>
            </div>
            <div class="panel-corner-glow"></div>
          </div>
        </div>
      </div>

      <!-- Cột phải: Khung ảnh Cyber Frame với Technical Ruler Overlay -->
      <div class="pp-bambino-concept__media-col">
        <div class="pp-bambino-concept__cyber-frame">
          <div class="pp-bambino-concept__ruler-x" aria-hidden="true">
            <span class="ruler-tick"></span>
            <span class="ruler-tick"></span>
            <span class="ruler-tick"></span>
            <span class="ruler-tick"></span>
            <span class="ruler-tick"></span>
          </div>
          <div class="pp-bambino-concept__image-wrapper">
            <img src="<?php echo sgh_img('2024/01/bambino-saigon-horeca-1.jpg'); ?>" alt="Thiết bị bếp công nghiệp Saigon Horeca tại Bambino" loading="lazy" decoding="async">
            <div class="cyber-glow"></div>
          </div>
          <div class="pp-bambino-concept__ruler-y" aria-hidden="true">
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
