<?php
/**
 * Project Pillar — bep-an-truong-mam-non-tu-thuc-trinh-vuong
 * Section #4: partnership (Saigon Horeca — Integrated Split · soft frame · gentle bg)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-partner-tvg">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (background position:fixed, không JS) -->
  <div class="pp-partner-tvg__bg" style="background-image:url('<?php echo sgh_img('bep-an-truong-mam-non-tu-thuc-trinh-vuong/goi-y-thiet-ke-bep-truong-hoc.jpg'); ?>');"></div>
  <div class="pp-partner-tvg__overlay" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-partner-tvg__card">
      <header class="pp-partner-tvg__head">
        <div class="pp-partner-tvg__badge">
          <span class="pp-partner-tvg__badge-dot" aria-hidden="true"></span>
          <?php echo esc_html__('Đối tác tin cậy', 'saigonhoreca'); ?>
        </div>
        <h2 class="pp-partner-tvg__title"><?php echo esc_html__('Saigon Horeca: Hiệu quả bắt đầu từ thiết kế chuẩn', 'saigonhoreca'); ?></h2>
        <span class="pp-partner-tvg__divider" aria-hidden="true"></span>
      </header>

      <div class="pp-partner-tvg__body">
        <p class="pp-partner-tvg__lead"><?php echo esc_html__('Bếp ăn Trường Mầm Non Tư Thục Trinh Vương là một minh chứng rõ ràng cho triết lý: không chỉ "hoàn thành một công trình", mà xây dựng một hệ thống vận hành bền vững — nơi công năng, con người và trải nghiệm được kết nối hài hòa.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Với hơn 10 năm kinh nghiệm trong lĩnh vực thiết kế – thi công bếp công nghiệp, Saigon Horeca hiểu rằng: một bản vẽ đúng chuẩn không chỉ tạo nên hiệu quả nấu nướng, mà còn bảo vệ sức khỏe, tinh thần và uy tín của người vận hành.', 'saigonhoreca'); ?></p>
      </div>

      <div class="pp-partner-tvg__stats">
        <div class="pp-partner-tvg__stat">
          <span class="pp-partner-tvg__stat-value">10<small>+</small></span>
          <span class="pp-partner-tvg__stat-label"><?php echo esc_html__('Năm kinh nghiệm', 'saigonhoreca'); ?></span>
        </div>
        <div class="pp-partner-tvg__stat">
          <span class="pp-partner-tvg__stat-value">1</span>
          <span class="pp-partner-tvg__stat-label"><?php echo esc_html__('Chiều — luồng khép kín', 'saigonhoreca'); ?></span>
        </div>
        <div class="pp-partner-tvg__stat">
          <span class="pp-partner-tvg__stat-value">100<small>%</small></span>
          <span class="pp-partner-tvg__stat-label"><?php echo esc_html__('Custom inox theo kích thước thực tế', 'saigonhoreca'); ?></span>
        </div>
      </div>

      <p class="pp-partner-tvg__motto"><?php echo esc_html__('Thiết kế chuẩn — Vận hành chuẩn — Hiệu quả bền vững.', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>
