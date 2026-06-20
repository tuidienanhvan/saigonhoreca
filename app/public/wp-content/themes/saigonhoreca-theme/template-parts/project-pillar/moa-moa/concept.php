<?php
/**
 * Project Pillar — moa-moa (Moa Moa Pasta Club)
 * Section #3: concept — triết lý bếp mở Ý (Italian open-kitchen) & dòng vận hành.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-moa pp-section-moa--alt pp-moa-concept">
  <div class="pp-concept-watermark-moa" aria-hidden="true">01</div>
  <div class="pp-ambient-glow-moa pp-ambient-glow-moa--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-moa">

      <div class="pp-grid-moa__media moa-concept__side">
        <div class="pp-image-container-shared moa-concept__image-container">
          <div class="moa-concept__image-decor" aria-hidden="true"></div>
          <img src="<?php echo sgh_img('moa-moa/moa-moa-khong-gian-an-uong-ban-ghe-go-view-thanh-pho.webp'); ?>" alt="<?php echo esc_attr__('Khu vực ăn uống trong nhà với bàn ghế gỗ và vách kính lớn nhìn ra phố Quận 1 tại Moa Moa', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Khu vực ăn uống ấm cúng với vách kính lớn nhìn ra phố Quận 1 lúc chạng vạng', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <div class="pp-grid-moa__text moa-concept__main">
        <div class="moa-concept__card">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>

          <header class="moa-concept__header">
            <div class="pp-badge-moa">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Triết lý bếp mở', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-moa__title">
              <?php echo esc_html__('Italian open-kitchen: gọn gàng, mạch lạc, liền mạch', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-moa__divider" aria-hidden="true"></div>
          </header>

          <div class="moa-concept__body">
            <p><?php echo esc_html__('Layout bếp được SGH tư vấn theo định hướng Italian open-kitchen: gọn gàng, mạch lạc, phân khu rõ ràng nhưng vẫn đảm bảo dòng di chuyển mượt mà. Không gian bếp phải đủ "mở" để kết nối với khu phục vụ, nhưng đồng thời đủ "chuẩn" để đáp ứng yêu cầu kỹ thuật của một nhà hàng pasta chuyên nghiệp.', 'saigonhoreca'); ?></p>
            <p style="margin-bottom: 0;"><?php echo esc_html__('Khác với nhiều mô hình Âu tổng hợp, bếp pasta đòi hỏi sự liền mạch giữa các công đoạn. Từ khu trộn bột, chế biến pasta tươi, luộc mì đến khu hoàn thiện món – tất cả phải được bố trí theo một trục logic, giảm thao tác thừa và đảm bảo nhịp độ phục vụ trong giờ cao điểm.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
