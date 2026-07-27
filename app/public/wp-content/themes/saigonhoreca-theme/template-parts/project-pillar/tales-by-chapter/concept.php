<?php
/**
 * Project Pillar — tales-by-chapter
 * Section #3: concept — "Chương 02" Tầng 2, the kitchen as a creative workshop.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-tbc pp-concept-tbc">
  <div class="pp-chapter-mark-tbc pp-chapter-mark-tbc--left" aria-hidden="true">02</div>
  <div class="pp-ambient-glow-tbc pp-ambient-glow-tbc--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-concept-tbc__grid">

      <div class="pp-concept-tbc__media">
        <div class="pp-concept-tbc__frame pp-image-container-shared">
          <span class="pp-concept-tbc__tag" aria-hidden="true">TẦNG 02 · KITCHEN</span>
          <img src="<?php echo sgh_img('tales-by-chapter/tales-by-chapter-khu-bep-tang-2-bep-tu.jpg'); ?>" alt="<?php echo esc_attr__('Xưởng sáng tạo zero waste tại bếp tầng 2 Tales by Chapter', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Xưởng sáng tạo zero waste – nơi nguyên liệu được lắng nghe', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <div class="pp-concept-tbc__content">
        <div class="pp-concept-tbc__card">
          <header>
            <div class="pp-badge-tbc">
              <span class="pp-badge-tbc__num">Chương 02</span>
              <?php echo esc_html__('Tầng 2 · Gian bếp', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-tbc__title"><?php echo esc_html__('Nơi nguyên liệu được lắng nghe', 'saigonhoreca'); ?></h2>
            <span class="pp-text-tbc__divider" aria-hidden="true"></span>
          </header>

          <div class="pp-text-tbc__body">
            <p><?php echo esc_html__('Nếu tầng 1 là lời chào, thì tầng 2 là nơi Tales by Chapter thật sự "làm nghề". Toàn bộ không gian được dành cho bếp – nơi đầu bếp "trò chuyện" với nguyên liệu theo cách tôn trọng nhất. Ở đây, không có khái niệm phần bỏ đi. Mỗi lát cắt, mỗi lớp vỏ, mỗi quá trình lên men đều là một phần của câu chuyện.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Với một mô hình thuần chay zero waste, yêu cầu đặt ra cho bếp không chỉ là nấu ngon, mà là tận dụng trọn vẹn và sáng tạo không giới hạn. Saigon Horeca đã cùng Tales xây dựng không gian bếp như một xưởng sáng tạo, nơi thiết bị không dẫn dắt đầu bếp, mà âm thầm hỗ trợ phía sau.', 'saigonhoreca'); ?></p>
          </div>

          <ul class="pp-concept-tbc__notes">
            <li><?php echo esc_html__('Hệ thống bếp bố trí đầy đủ nhưng có chọn lọc', 'saigonhoreca'); ?></li>
            <li><?php echo esc_html__('Hút khói & khử mùi chuẩn xác giữa Quận 1', 'saigonhoreca'); ?></li>
            <li><?php echo esc_html__('Máy móc đóng vai trò hỗ trợ, không cầu kỳ', 'saigonhoreca'); ?></li>
            <li><?php echo esc_html__('Tận dụng trọn vẹn nguyên liệu tươi sạch', 'saigonhoreca'); ?></li>
          </ul>
        </div>
      </div>

    </div>
  </div>
</section>
