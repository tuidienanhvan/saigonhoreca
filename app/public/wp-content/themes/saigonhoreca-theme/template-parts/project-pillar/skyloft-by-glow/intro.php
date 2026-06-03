<?php
/**
 * Project Pillar — skyloft-by-glow
 * Section #2: intro — split layout with image + editorial text.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-sky pp-intro-sky scroll-reveal">
  <div class="pp-sky-ambient-glow pp-sky-ambient-glow--tr" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-editorial-sky">
      <!-- Mảng trái: Heading, Subhead và Nhãn tọa độ kỹ thuật -->
      <div class="pp-editorial-sky__header">
        <span class="pp-editorial-sky__eyebrow"><?php echo esc_html__('01 / Không Gian Tầng Không', 'saigonhoreca'); ?></span>
        <h2 class="pp-editorial-sky__title"><?php echo esc_html__('Trải nghiệm Rooftop', 'saigonhoreca'); ?></h2>
        <div class="pp-editorial-sky__meta">
          <div class="pp-editorial-sky__meta-item">
            <span class="pp-editorial-sky__meta-label"><?php echo esc_html__('Tọa độ:', 'saigonhoreca'); ?></span>
            <span class="pp-editorial-sky__meta-value"><?php echo esc_html__('10.7783° N, 106.6975° E', 'saigonhoreca'); ?></span>
          </div>
          <div class="pp-editorial-sky__meta-item">
            <span class="pp-editorial-sky__meta-label"><?php echo esc_html__('Độ cao:', 'saigonhoreca'); ?></span>
            <span class="pp-editorial-sky__meta-value"><?php echo esc_html__('Tầng thượng President Place', 'saigonhoreca'); ?></span>
          </div>
        </div>
      </div>

      <!-- Mảng phải: Nội dung văn bản chi tiết thoáng đạt -->
      <div class="pp-editorial-sky__content">
        <div class="pp-editorial-sky__body">
          <p class="pp-editorial-sky__lead"><?php echo esc_html__('Skyloft by Glow là một rooftop bar tọa lạc trên tòa nhà President Place tại đường Nguyễn Du, Quận 1. Nơi đây sở hữu tầm nhìn 360 độ ôm trọn toàn cảnh kiến trúc Sài Gòn, trở thành một trong những tọa độ tuyệt mỹ nhất để ngắm hoàng hôn từ trên cao.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Khi hoàng hôn buông xuống và thành phố bắt đầu lên đèn, Skyloft lột xác thành điểm hẹn náo nhiệt và sôi động bậc nhất. Không gian là sự giao thoa hoàn hảo giữa những món cocktail thủ công đẳng cấp và nhịp điệu âm nhạc điện tử lôi cuốn từ Deep House, Vocal Sexy House đến những bản phối đầy bùng nổ khi về đêm, đưa thực khách hòa mình vào những vũ điệu say mê.', 'saigonhoreca'); ?></p>
        </div>
      </div>
    </div>
  </div>
</section>



