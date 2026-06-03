<?php
/**
 * Project Pillar — sol-kitchen-bar
 * Section #2: intro — asymmetric storytelling (text + framed image).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-sol pp-intro-sol scroll-reveal">
  <div class="pp-sol-ambient pp-sol-ambient--tr" aria-hidden="true"></div>
  <div class="pp-sol-grid" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-intro-sol__layout">
      <div class="pp-intro-sol__body pp-story-card-sol">
        <div class="pp-sol-ornament" aria-hidden="true"></div>
        <span class="pp-text-sol__eyebrow"><?php echo esc_html__('Sol quận 7 · Saigon Horeca', 'saigonhoreca'); ?></span>
        <span class="pp-text-sol__divider" aria-hidden="true"></span>
        <h2 class="pp-text-sol__title"><?php echo esc_html__('Tiếp nối thành công Michelin', 'saigonhoreca'); ?></h2>
        <div class="pp-text-sol__body">
          <p><span class="pp-intro-sol__dropcap"><?php echo esc_html__('T', 'saigonhoreca'); ?></span><?php echo esc_html__('iếp nối sự thành công của nhà hàng Sol quận 1 khi lọt vào Michelin Guide 2023, Saigon Horeca vinh hạnh được chủ đầu tư Sol Kitchen & Bar tin tưởng lựa chọn là đơn vị uy tín triển khai hệ thống bếp cao cấp cho nhà hàng tại quận 7, HCMC.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-intro-sol__media scroll-reveal">
        <div class="pp-image-container-shared pp-frame-sol">
          <img src="<?php echo sgh_img('sol-kitchen-bar/Sol0D7-01.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống bếp cao cấp Sol Kitchen & Bar quận 7 do Saigon Horeca triển khai', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Khu bếp Sol quận 7 sau cải tạo — bố trí thiết bị gọn gàng, sẵn sàng cho nhịp vận hành của một nhà hàng đạt chuẩn Michelin.', 'saigonhoreca'); ?></div>
        </div>
      </div>
    </div>
  </div>
</section>
