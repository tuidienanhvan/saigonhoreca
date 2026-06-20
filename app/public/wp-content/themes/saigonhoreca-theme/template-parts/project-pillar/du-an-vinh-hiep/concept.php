<?php
/**
 * Project Pillar — du-an-vinh-hiep
 * Section #3: concept — Bếp lãnh đạo & nhân sự cấp cao
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-vhp pp-section-vhp--alt pp-concept-vhp">
  <div class="pp-watermark-vhp pp-watermark-vhp--left" aria-hidden="true">02</div>
  <div class="pp-ambient-glow-vhp pp-ambient-glow-vhp--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-concept-vhp__grid">

      <!-- Media — blueprint-framed -->
      <div class="pp-concept-vhp__media">
        <div class="pp-concept-vhp__hud" aria-hidden="true">
          <span class="pp-concept-vhp__coord pp-concept-vhp__coord--tl">KIT—A · LEADERSHIP DINING</span>
          <span class="pp-concept-vhp__coord pp-concept-vhp__coord--br">SCALE 1:50 · IND-GRADE</span>
        </div>
        <div class="pp-image-container-shared pp-concept-vhp__image">
          <svg class="pp-concept-vhp__corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" aria-hidden="true">
            <path d="M 0 14 L 0 0 L 14 0" stroke="var(--gold)" stroke-width="1.2"/>
            <path d="M 100 14 L 100 0 L 86 0" stroke="var(--gold)" stroke-width="1.2"/>
            <path d="M 0 86 L 0 100 L 14 100" stroke="var(--gold)" stroke-width="1.2"/>
            <path d="M 100 86 L 100 100 L 86 100" stroke="var(--gold)" stroke-width="1.2"/>
          </svg>
          <img src="<?php echo sgh_img('du-an-vinh-hiep/vinh-hiep-bep-tu-xao-hai-hong-chao-inox.webp'); ?>" alt="<?php echo esc_attr__('Bếp từ xào hai họng chảo lõm và bếp từ phẳng đôi inox phục vụ ban lãnh đạo Vĩnh Hiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Bếp compact – chính xác – sạch tuyệt đối cho ban lãnh đạo', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <!-- Text glass card -->
      <div class="pp-concept-vhp__text">
        <div class="pp-concept-vhp__card">
          <span class="pp-concept-vhp__corner pp-concept-vhp__corner--tl" aria-hidden="true"></span>
          <span class="pp-concept-vhp__corner pp-concept-vhp__corner--br" aria-hidden="true"></span>

          <header>
            <div class="pp-badge-vhp">
              <span class="pp-badge-vhp__diamond" aria-hidden="true"></span>
              <?php echo esc_html__('Bếp lãnh đạo', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-concept-vhp__title">
              <?php echo esc_html__('Chuẩn mực của vận hành tinh gọn', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-divider-vhp" aria-hidden="true"></div>
          </header>

          <div class="pp-concept-vhp__body">
            <p><?php echo esc_html__('Khác với bếp ăn tập thể thông thường, khu bếp mà Saigon Horeca tư vấn cho Vĩnh Hiệp được định hướng ngay từ đầu là bếp phục vụ ban lãnh đạo và đội ngũ nhân sự cấp cao tại nhà máy.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Không gian bếp được tổ chức theo tư duy compact – chính xác – sạch tuyệt đối, với các thiết bị bếp điện cao cấp nhằm đảm bảo an toàn, kiểm soát nhiệt tốt và phù hợp môi trường nhà máy hiện đại. Khu chế biến tích hợp bếp Á điện 3 họng, bếp Âu điện và thiết bị giữ nóng thức ăn, cho phép vận hành linh hoạt nhưng vẫn giữ được sự gọn gàng và chuẩn mực.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Song song đó, khu sơ chế được thiết kế như một "xưởng tiền xử lý" thu nhỏ, với bàn inox chuyên dụng, bồn chậu đi kèm bẫy mỡ – đảm bảo tiêu chuẩn vệ sinh, dễ làm sạch và kiểm soát rủi ro ngay từ gốc.', 'saigonhoreca'); ?></p>
            <p class="pp-concept-vhp__closing"><?php echo esc_html__('Ở dự án này, Saigon Horeca không chạy theo số lượng thiết bị, mà tập trung vào logic vận hành, để mỗi thao tác trong bếp đều liền mạch, tiết kiệm thời gian và giảm áp lực cho người sử dụng.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
