<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #4: specs — partnership note + technical specs card + equipment frame.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-hwa pp-section-hwa--alt pp-specs-hwa scroll-reveal">
  <div class="pp-hwa-ambient-glow pp-hwa-ambient-glow--tr" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-split-hwa pp-specs-hwa__layout">
      <div class="pp-split-hwa__body">
        <div class="pp-hwa-ornament" aria-hidden="true"></div>
        <div class="pp-text-hwa__badge"><span class="pp-text-hwa__badge-accent">//</span> <?php echo esc_html__('UY TÍN ĐỐI TÁC', 'saigonhoreca'); ?></div>
        <h2 class="pp-text-hwa__title"><?php echo esc_html__('Sự Hợp Tác Giữa Heiwa Sushi & Saigon Horeca', 'saigonhoreca'); ?></h2>
        <div class="pp-text-hwa__body">
          <p><?php echo esc_html__('Sự hợp tác chặt chẽ giữa Heiwa Sushi và Saigon Horeca là minh chứng rõ ràng cho tầm quan trọng của việc quy hoạch và thiết lập hệ thống bếp chuyên nghiệp trong thành công rực rỡ của một nhà hàng Omakase. Với kinh nghiệm chuyên môn dày dặn, Saigon Horeca đã đồng hành lựa chọn và lắp đặt các dòng thiết bị nhà bếp tốt nhất, kiến tạo nên trải nghiệm trọn vẹn và an tâm tuyệt đối.', 'saigonhoreca'); ?></p>
        </div>

        <div class="pp-specs-hwa__card">
          <div class="pp-hwa-watermark-num" aria-hidden="true">03</div>
          <div class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></div>
          <div class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></div>
          <div class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></div>
          <div class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></div>
          <h3 class="pp-specs-hwa__card-title"><?php echo esc_html__('Thông số kỹ thuật lắp đặt', 'saigonhoreca'); ?></h3>
          <div class="pp-specs-hwa__card-grid">
            <div class="pp-specs-hwa__card-item">
              <span class="pp-specs-hwa__card-label"><?php echo esc_html__('Thiết bị lạnh chính', 'saigonhoreca'); ?></span>
              <span class="pp-specs-hwa__card-value"><?php echo esc_html__('Hoshizaki Japan', 'saigonhoreca'); ?></span>
            </div>
            <div class="pp-specs-hwa__card-item">
              <span class="pp-specs-hwa__card-label"><?php echo esc_html__('Hệ thống quầy bar', 'saigonhoreca'); ?></span>
              <span class="pp-specs-hwa__card-value"><?php echo esc_html__('Gỗ tự nhiên & Kính', 'saigonhoreca'); ?></span>
            </div>
            <div class="pp-specs-hwa__card-item">
              <span class="pp-specs-hwa__card-label"><?php echo esc_html__('Chất liệu cơ khí', 'saigonhoreca'); ?></span>
              <span class="pp-specs-hwa__card-value"><?php echo esc_html__('Inox 304 tiêu chuẩn', 'saigonhoreca'); ?></span>
            </div>
            <div class="pp-specs-hwa__card-item">
              <span class="pp-specs-hwa__card-label"><?php echo esc_html__('Tiêu chuẩn vệ sinh', 'saigonhoreca'); ?></span>
              <span class="pp-specs-hwa__card-value"><?php echo esc_html__('Omakase Fresh Standard', 'saigonhoreca'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="pp-split-hwa__media pp-specs-hwa__media scroll-reveal">
        <div class="pp-specs-hwa__coord pp-specs-hwa__coord--tl" aria-hidden="true">SYS_COORD / KITCHEN</div>
        <div class="pp-specs-hwa__coord pp-specs-hwa__coord--br" aria-hidden="true">CAD_v2.0 / SCALE 1:50</div>
        <div class="pp-image-container-shared pp-frame-hwa pp-specs-hwa__frame">
          <img src="<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-gian-bep-chuan-bi-inox-tiet-kiem-dien.webp'); ?>" alt="<?php echo esc_attr__('Hệ thống thiết bị bếp inox công nghiệp cao cấp tại Heiwa Sushi - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống thiết bị bếp inox công nghiệp được Saigon Horeca lựa chọn và lắp đặt, đảm bảo vận hành Omakase trọn vẹn và an tâm tuyệt đối.', 'saigonhoreca'); ?></div>
        </div>
      </div>
    </div>
  </div>
</section>
