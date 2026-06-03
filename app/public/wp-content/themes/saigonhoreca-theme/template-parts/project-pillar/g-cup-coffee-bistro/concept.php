<?php
/**
 * Project Pillar — g-cup-coffee-bistro
 * Section #3: concept — Giải pháp bếp một chiều & Thách thức shophouse
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-gcb pp-concept-section-gcb pp-section-gcb--alt">
  <div class="pp-container-shared">

    <!-- Họa tiết SVG bản vẽ mặt cắt thông gió shophouse mờ nền -->
    <svg class="pp-gcb-bg-fluid" viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="position: absolute; top: 15%; left: 5%; width: 45%; height: 70%; opacity: 0.05; pointer-events: none; z-index: 1;">
      <path d="M50 450 C 150 350, 220 520, 320 400 C 420 280, 350 180, 450 120 C 550 60, 580 120, 650 80" stroke="var(--gold)" stroke-width="1.2" stroke-dasharray="6 4"/>
      <circle cx="450" cy="120" r="30" stroke="var(--gold)" stroke-width="0.5" stroke-dasharray="2 3"/>
      <circle cx="450" cy="120" r="50" stroke="var(--gold)" stroke-width="0.5"/>
    </svg>

    <!-- Cấu trúc Layout nghệ thuật chồng lớp: Ảnh Trái bo góc chéo / Hộp chữ Niken đè chéo bên Phải -->
    <div class="pp-gcb-concept-editorial">
      
      <!-- Khối ảnh lớn bên trái (Bếp Á xào bùng lửa cực kỳ động) -->
      <div class="pp-gcb-concept-visual scroll-reveal">
        <div class="pp-gcb-concept-photo">
          <!-- Đường line trang trí màu vàng chạy dọc theo mép ảnh -->
          <span class="pp-gcb-vertical-line" aria-hidden="true"></span>
          
          <div class="pp-image-container-shared" style="aspect-ratio: 16 / 10;">
            <img src="<?php echo sgh_img('g-cup-coffee-bistro/g-cup-coffee-bistro-dau-bep-xao-bep-a-cong-nghiep.webp'); ?>" alt="<?php echo esc_attr__('Đầu bếp xào bùng lửa trên bếp gas Á công nghiệp G-Cup Bistro', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Gian bếp nóng với bếp gas Á họng lò công suất lớn, nơi tạo nên những món ăn nóng hổi bùng nổ hương vị.', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Khối chữ Hộp Niken sắc sảo đè chéo nghệ thuật bên phải -->
      <div class="pp-gcb-concept-text scroll-reveal">
        <div class="pp-gcb-concept-glass-card pp-gcb-niken-card">
          <!-- Các vạch nẹp góc kim loại mỏng vàng kim -->
          <span class="gcb-niken-corner gcb-niken-corner--tl"></span>
          <span class="gcb-niken-corner gcb-niken-corner--tr"></span>
          <span class="gcb-niken-corner gcb-niken-corner--bl"></span>
          <span class="gcb-niken-corner gcb-niken-corner--br"></span>

          <!-- Icon ngọn lửa SVG đục lỗ tiêu âm kỹ thuật -->
          <div class="gcb-concept-fire-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M12 2C10 6 7 8 7 12s2.5 7 5 7 5-3 5-7-3-6-5-10z" stroke-linecap="round"/>
            </svg>
          </div>

          <span class="pp-gcb-concept-tag"><?php echo esc_html__('VẬN HÀNH THÔNG MINH', 'saigonhoreca'); ?></span>
          <h3 class="pp-gcb-card-heading" style="font-size: clamp(1.5rem, 2.8vw, 2.1rem); margin-top: 0.5rem; margin-bottom: 1.25rem; line-height: 1.25; color: var(--bc); font-family: var(--font-display);">
            <?php echo esc_html__('Bố trí bếp bánh & bếp nấu một chiều', 'saigonhoreca'); ?>
          </h3>
          
          <div class="pp-gcb-concept-card-body">
            <div class="pp-gcb-highlight-quote" style="border-left: 2px solid var(--gold); padding-left: 1.25rem; margin-bottom: 1.5rem;">
              <p style="font-size: 0.98rem; line-height: 1.7; font-weight: 500; font-style: italic; color: var(--bc); margin: 0;">
                <?php echo esc_html__('“Đằng sau một không gian thưởng thức nghệ thuật là cấu trúc bếp một chiều khép kín, tối ưu hóa từng mét vuông mặt bằng shophouse.”', 'saigonhoreca'); ?>
              </p>
            </div>
            
            <p class="pp-gcb-desc-p" style="font-size: 0.92rem; line-height: 1.8; color: var(--bc2); margin-bottom: 1rem;">
              <?php echo esc_html__('Bếp bánh và bếp nấu tại G-Cup được Saigon Horeca bố trí thành hai phân khu độc lập nhưng kề sát, giúp mùi vị các món ăn không bị pha lẫn, giữ trọn vẹn hương vị thành phẩm.', 'saigonhoreca'); ?>
            </p>
            <p class="pp-gcb-desc-p" style="font-size: 0.92rem; line-height: 1.8; color: var(--bc2); margin: 0;">
              <?php echo esc_html__('Toàn bộ hệ thống bàn mát, tủ lạnh quầy bar inox Hoshizaki được thiết kế âm quầy tinh tế, nâng đỡ hoàn hảo cho luồng công việc liên tục từ sáng đến tối muộn.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>
