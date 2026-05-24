<?php
/**
 * Project Pillar — bambino-saigonhoreca
 * Section #2: with_gallery — Bespoke Cyberpunk Neon Glow Edition
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt pp-bambino-intro">
  <div class="pp__container">
    <div class="pp-bambino-intro__grid">
      <!-- Cột trái: Văn bản giới thiệu di sản bí ẩn -->
      <div class="pp-bambino-intro__text-col">
        <div class="pp-bambino-intro__badge">
          <span class="pp-bambino-intro__badge-dot"></span>
          Superclub Experience
        </div>
        <h2 class="pp-bambino-intro__title">
          <span class="pp-bambino-intro__dropcap">B</span>ambino
        </h2>
        
        <div class="pp-bambino-intro__info-chips">
          <span class="pp-bambino-intro__chip">📍 29-31 Tôn Thất Thiệp, Quận 1</span>
          <span class="pp-bambino-intro__chip">⏰ Bar Bino: 22:00 – 02:00</span>
          <span class="pp-bambino-intro__chip">⚡ Supper Club: Fri & Sat (22:00 – 02:00)</span>
        </div>

        <div class="pp-bambino-intro__body">
          <p class="pp-bambino-intro__lead">Bước vào thế giới của Bambino Superclub, nơi điều bình thường trở nên phi thường, và trời đêm hóa thân thành dòng năng lượng sống động nhịp nhàng.</p>
          <p>Nằm yên bên trong lòng sôi động của Thành phố Hồ Chí Minh, Superclub nay không chỉ là điểm đến giải trí về đêm; đó là một trải nghiệm đắm mình vào thế giới vượt lên trên bình thường, tạo nên những đêm khó quên và những khoảnh khắc sôi động.</p>
          <p><strong>Thiên đường giải trí:</strong> Thực đơn Bambino Superclub nổi bật với những món Ý, những nguyên liệu đặc biệt được nhập khẩu trực tiếp từ Italia. Từ lúc hoàng hôn cho đến khi bình minh, nhà hàng và quầy bar của Bambino luôn nhịp nhàng với làn sóng của âm nhạc, tiếng cười, và tinh thần sôi động của những người trẻ năng động.</p>
          
          <div class="pp-bambino-intro__quote-card">
            <p>“SAIGON HORECA tự hào tham gia thiết kế và phân phối các thiết bị nhà bếp bằng thép không gỉ cao cấp. Sự hợp tác của chúng tôi với BAMBINO không chỉ dừng lại ở chức năng cơ bản; nó hài hòa sự sáng tạo với tính thực tế.”</p>
            <span class="pp-bambino-intro__quote-author">— Saigon Horeca Editorial</span>
          </div>
        </div>
      </div>

      <!-- Cột phải: Khung ảnh cơ khí 3D Mechanical Frame -->
      <div class="pp-bambino-intro__media-col">
        <div class="pp-bambino-intro__frame-3d">
          <div class="pp-bambino-intro__frame-glow"></div>
          <div class="pp-bambino-intro__main-image-container">
            <img src="<?php echo sgh_img('2023/12/SGH-Portrait-3.png'); ?>" alt="Bambino Superclub" class="pp-bambino-intro__main-image" loading="lazy" decoding="async">
            <div class="pp-bambino-intro__frame-overlay"></div>
          </div>
          <div class="pp-bambino-intro__frame-corners">
            <span class="corner tl"></span>
            <span class="corner tr"></span>
            <span class="corner bl"></span>
            <span class="corner br"></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Gallery bên dưới: Neon Grid Panel -->
    <div class="pp-bambino-intro__gallery">
      <div class="pp-bambino-intro__gallery-header">
        <span class="line"></span>
        <h3>Bespoke Interior Gallery</h3>
        <span class="line"></span>
      </div>
      <div class="pp-bambino-intro__gallery-grid">
        <div class="pp-bambino-intro__gallery-item">
          <img src="<?php echo sgh_img('2023/12/SGH-Portrait-1.png'); ?>" alt="Bambino Portrait 1" loading="lazy" decoding="async">
          <div class="neon-border"></div>
        </div>
        <div class="pp-bambino-intro__gallery-item">
          <img src="<?php echo sgh_img('2023/12/SGH-Portrait-2.png'); ?>" alt="Bambino Portrait 2" loading="lazy" decoding="async">
          <div class="neon-border"></div>
        </div>
        <div class="pp-bambino-intro__gallery-item">
          <img src="<?php echo sgh_img('2023/12/SGH-Portrait.png'); ?>" alt="Bambino Portrait 3" loading="lazy" decoding="async">
          <div class="neon-border"></div>
        </div>
      </div>
    </div>
  </div>
</section>
