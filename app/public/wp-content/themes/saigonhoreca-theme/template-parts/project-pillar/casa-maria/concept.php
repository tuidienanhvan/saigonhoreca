<?php
/**
 * Project Pillar — casa-maria
 * Section #3: concept — Quầy bar Wine & Cafe
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-cm pp-concept-section-cm pp-section-cm--alt">
  <div class="pp-container-shared">

    <!-- Họa tiết SVG sóng vang & cafe uốn lượn mờ ảo chạy dưới nền -->
    <svg class="pp-concept-bg-fluid" viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="position: absolute; top: 10%; right: 5%; width: 50%; height: 80%; opacity: 0.06; pointer-events: none; z-index: 1;">
      <path d="M100 500 C 200 400, 300 600, 400 450 C 500 300, 350 150, 500 100 C 650 50, 700 200, 800 150" stroke="var(--gold)" stroke-width="1.5" stroke-dasharray="6 4"/>
      <path d="M150 550 C 250 450, 320 520, 420 400 C 520 280, 450 180, 550 120 C 650 60, 680 120, 750 80" stroke="var(--gold)" stroke-width="0.8"/>
      <circle cx="500" cy="100" r="40" stroke="var(--gold)" stroke-width="0.5" stroke-dasharray="3 3"/>
      <circle cx="500" cy="100" r="70" stroke="var(--gold)" stroke-width="0.5" stroke-dasharray="4 6"/>
    </svg>

    <!-- Cấu trúc Layout nghệ thuật chồng lớp: Ảnh Trái rộng / Card chữ đè nhẹ bên Phải -->
    <div class="pp-concept-editorial-layout">
      
      <!-- Khối ảnh lớn -->
      <div class="pp-concept-visual-wrapper">
        <div class="pp-concept-photo-container">
          <!-- Đường line trang trí dọc theo mép ảnh -->
          <span class="pp-concept-vertical-line" aria-hidden="true"></span>
          
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img('casa-maria/casa-maria-quay-bar-wine-cafe.jpg'); ?>" alt="<?php echo esc_attr__('Quầy bar Wine & Cafe với tủ rượu vang và máy pha cafe', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Quầy bar Wine & Cafe – Trái tim cảm xúc và kết nối của Casa Maria', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Khối chữ kính mờ đè chéo nghệ thuật -->
      <div class="pp-concept-text-wrapper">
        <div class="pp-concept-glass-card">
          <span class="pp-concept-card-tag"><?php echo esc_html__('Điểm Chạm Cảm Xúc', 'saigonhoreca'); ?></span>
          <h3 class="pp-cm-card-heading" style="font-size: clamp(1.8rem, 3vw, 2.4rem); margin-top: 0.75rem; margin-bottom: 1.5rem; line-height: 1.2; color: var(--bc);">
            <?php echo esc_html__('Concept Quầy Bar Wine & Cafe', 'saigonhoreca'); ?>
          </h3>
          
          <div class="pp-concept-card-body">
            <div class="pp-cm-highlight-quote" style="border-left: 2px solid var(--gold); padding-left: 1.25rem; margin-bottom: 1.5rem;">
              <p style="font-size: 1.08rem; line-height: 1.7; font-weight: 500; font-style: italic; color: var(--bc);">
                <span class="pp-cm-dropcap" style="font-size: 2.8rem; float: left; line-height: 0.8; margin-right: 0.5rem; color: var(--gold); font-family: var(--font-display);">Q</span><?php echo esc_html__('uầy bar không chỉ là nơi pha chế, mà là điểm giao hòa của hương vị và cảm xúc: nơi khách đến thưởng vang, thưởng cafe, chia sẻ những câu chuyện.', 'saigonhoreca'); ?>
              </p>
              <div style="clear: both;"></div>
            </div>
            
            <p class="pp-cm-desc-p" style="font-size: 0.98rem; line-height: 1.8; color: var(--bc2); margin: 0;">
              <?php echo esc_html__('Saigon Horeca thiết kế bar với tư duy mở, nơi bartender không chỉ pha chế mà còn giao tiếp và kết nối. Hệ thống thiết bị bar, khu bảo quản rượu vang và pha cafe được tính toán để đảm bảo sự nhất quán cao độ về chất lượng ly vang luôn đúng độ lạnh, ly cafe luôn tròn hương vị.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>
