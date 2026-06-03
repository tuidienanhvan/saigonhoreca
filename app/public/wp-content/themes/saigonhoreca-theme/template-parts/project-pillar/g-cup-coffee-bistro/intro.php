<?php
/**
 * Project Pillar - g-cup-coffee-bistro
 * Section #2: intro (Asymmetric Cloud & Sharp Editorial Grid)
 * Được nâng cấp để ảnh to rõ, caption chuẩn chỉnh theo _caption.css
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-gcb pp-section-gcb--alt pp-gcb-intro-section" id="gcb-intro-anchor">
  <!-- Hoa văn CAD sóng mạng mờ nhạt nền -->
  <div class="pp-gcb-intro-wave-decor" aria-hidden="true">
    <svg viewBox="0 0 100 20" fill="none" stroke="currentColor" stroke-width="0.3" stroke-dasharray="1 2">
      <path d="M0 10 Q25 20 50 10 T100 10"/>
    </svg>
  </div>

  <div class="pp-container-shared">
    <div class="pp-gcb-editorial-grid">

      <!-- Hộp chữ sắc sảo viền niken mảnh -->
      <div class="pp-gcb-editorial-text scroll-reveal">
        <div class="pp-gcb-editorial-text-box">
          <span class="pp-text-gcb__divider-svg" aria-hidden="true">
            <svg viewBox="0 0 80 4" fill="none" stroke="var(--gold)" stroke-width="2">
              <path d="M0 2 Q20 4 40 2 T80 2" stroke-linecap="round"/>
            </svg>
          </span>
          
          <h2 class="pp-text-gcb__title">
            <?php echo esc_html__('Không gian G-Cup Bistro - Khi thiết kế & vận hành song hành', 'saigonhoreca'); ?>
          </h2>

          <div class="pp-text-gcb__body">
            <p class="pp-gcb-lead-p">
              <?php echo esc_html__('Tọa lạc tại Thủ Thiêm, G.Cup Coffee & Bistro cần một quầy bar gọn, đẹp và vận hành mượt trong mặt bằng shophouse cao cấp.', 'saigonhoreca'); ?>
            </p>
            <p class="pp-gcb-desc-p" style="margin-top: 0.75rem;">
              <?php echo esc_html__('Saigon Horeca tổ chức quầy bar một chiều, kết nối với bếp bánh phía trong để barista thao tác nhanh, sạch và ít di chuyển thừa.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
      </div>

      <!-- Khối media bất đối xứng ảnh bo mây đè chéo - To rõ, không móp méo -->
      <div class="pp-gcb-editorial-media scroll-reveal">
        <!-- Đường đo đạc định vị CAD SVG phía sau ảnh -->
        <div class="pp-gcb-media-cad-lines" aria-hidden="true">
          <svg viewBox="0 0 120 100" fill="none" stroke="color-mix(in srgb, var(--gold) 15%, transparent)" stroke-width="0.4">
            <line x1="5" y1="5" x2="115" y2="5"/>
            <line x1="5" y1="5" x2="5" y2="95"/>
            <line x1="115" y1="5" x2="115" y2="95"/>
            <path d="M2 5h6M112 5h6M5 2v6M5 92v6"/>
          </svg>
        </div>

        <!-- Ảnh chính: Không gian nội thất shophouse Metropole - Bo tròn đều sang trọng -->
        <div class="pp-image-container-shared pp-gcb-main-frame">
          <img src="<?php echo sgh_img('g-cup-coffee-bistro/g-cup-coffee-bistro-khong-gian-noi-that-shophouse.webp'); ?>" alt="<?php echo esc_attr__('Không gian nội thất hiện đại của G-Cup Coffee & Bistro tại Metropole Thủ Thiêm', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared">
            <?php echo esc_html__('Không gian nội thất hiện đại ấm cúng của G-Cup Coffee & Bistro tại shophouse Metropole Thủ Thiêm với hệ thống chiếu sáng nghệ thuật.', 'saigonhoreca'); ?>
          </div>
        </div>

        <!-- Ảnh phụ: Station thao tác - Lệch tầng tinh tế -->
        <div class="pp-image-container-shared pp-gcb-sub-frame">
          <img src="<?php echo sgh_img('g-cup-coffee-bistro/g-cup-coffee-bistro-quay-pha-che-chinh.webp'); ?>" alt="<?php echo esc_attr__('Hệ thống quầy pha chế chuyên nghiệp tại G-Cup Bistro', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared">
            <?php echo esc_html__('Hệ thống quầy pha chế chuyên nghiệp tích hợp khu vực rửa và sơ chế nhanh tiện lợi tối ưu diện tích.', 'saigonhoreca'); ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
