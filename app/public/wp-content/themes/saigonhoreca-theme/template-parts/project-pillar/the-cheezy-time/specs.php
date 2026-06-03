<?php
/**
 * Project Pillar — the-cheezy-time
 * Section #4: specs — Nâng cấp gộp 2 ảnh so le cùng cột chữ quy chuẩn
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-tct pp-specs-tct">
  <div class="pp-container-shared">
    <div class="pp-grid-12-tct">
      
      <!-- Cột trái: Blueprint Bản vẽ & Thực tế hoàn thiện xếp chồng so le nghệ thuật -->
      <div class="pp-specs-side-tct">
        <!-- Blueprint Coordinates & Stamp -->
        <div class="pp-specs-coord-tct pp-specs-coord-tct--tl" aria-hidden="true">SYS_COORD: 10.0371° N, 105.7879° E</div>
        <div class="pp-specs-coord-tct pp-specs-coord-tct--tr" aria-hidden="true">THE_CHEEZY_TIME_04 / CAD_v2.0</div>
        <div class="pp-specs-coord-tct pp-specs-coord-tct--bl" aria-hidden="true">SCALE: 1:50 @ A3</div>
        <div class="pp-specs-coord-tct pp-specs-coord-tct--br" aria-hidden="true">SAIGON HORECA © 2026</div>

        <!-- Cụm collage 2 ảnh (Bản vẽ & Thực tế) -->
        <div class="pp-specs-collage">
          
          <!-- Ảnh sau: Bản vẽ thiết kế -->
          <div class="pp-specs-collage-item pp-specs-collage-item--blueprint">
            <div class="pp-tct-luxury-frame pp-tct-luxury-frame--specs">
              <span class="pp-tct-corner pp-tct-corner--tl"></span>
              <span class="pp-tct-corner pp-tct-corner--tr"></span>
              <span class="pp-tct-corner pp-tct-corner--bl"></span>
              <span class="pp-tct-corner pp-tct-corner--br"></span>
              <div class="pp-image-container-shared">
                <img src="<?php echo sgh_img('the-cheezy-time/the-cheezy-time-exhaust-hood.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống tum hút khói inox The Cheezy Time', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
                <div class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống tum hút mùi inox dạng đục lỗ tiêu âm chuyên dụng, đảm bảo tối ưu việc lưu thông không khí trong gian bếp mở.', 'saigonhoreca'); ?></div>
              </div>
            </div>
          </div>

          <!-- Ảnh trước: Thực tế hoàn thiện thi công -->
          <div class="pp-specs-collage-item pp-specs-collage-item--real">
            <div class="pp-tct-luxury-frame pp-tct-luxury-frame--specs">
              <span class="pp-tct-corner pp-tct-corner--tl"></span>
              <span class="pp-tct-corner pp-tct-corner--tr"></span>
              <span class="pp-tct-corner pp-tct-corner--bl"></span>
              <span class="pp-tct-corner pp-tct-corner--br"></span>
              <div class="pp-image-container-shared">
                <img src="<?php echo sgh_img('the-cheezy-time/the-cheezy-time-hood-filters.jpg'); ?>" alt="<?php echo esc_attr__('Phin lọc mỡ inox của hệ thống hút khói', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
                <div class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống phin lọc mỡ inox xếp lớp và máng hứng mỡ tích hợp giúp tối ưu hóa công tác vệ sinh định kỳ.', 'saigonhoreca'); ?></div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Cột phải (5 cột): Glass card thông số & tính năng -->
      <div class="pp-specs-main-tct">
        <div class="pp-glass-card-tct">
          <header class="pp-specs-header-tct">
            <div class="pp-badge-tct">
              <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px; margin-right:6px;">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Thông số kỹ thuật', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-tct__title" style="font-size: clamp(1.5rem, 2.5vw, 2rem); margin-top: 0.5rem; margin-bottom: 1rem;">
              <?php echo esc_html__('Quy chuẩn vận hành bếp Âu & Pizza', 'saigonhoreca'); ?>
            </h2>
          </header>

          <div class="pp-specs-body-tct">
            <div class="pp-specs-feature-tct">
              <div class="pp-specs-icon-wrapper-tct">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:20px; height:20px;">
                  <path d="M20 7h-9m3 4H5m16 4h-9m6 4H8" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="pp-specs-feature-content-tct">
                <h4 style="margin:0 0 0.25rem 0; font-family:var(--font-display); font-size:1.05rem; color:var(--gold);"><?php echo esc_html__('Quy hoạch hai nhịp nấu', 'saigonhoreca'); ?></h4>
                <p style="margin:0; font-size:0.92rem; line-height:1.6; color:var(--bc2);"><?php echo esc_html__('Sắp xếp hợp lý khu bếp Âu và khu bếp pizza để song song hoạt động, tách biệt luồng nhiệt nhưng kết nối mạch lạc, không chồng chéo hay triệt tiêu năng suất lẫn nhau.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <div class="pp-specs-feature-tct" style="margin-top: 1.5rem;">
              <div class="pp-specs-icon-wrapper-tct">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:20px; height:20px;">
                  <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                  <line x1="8" y1="21" x2="16" y2="21" stroke-linecap="round"/>
                  <line x1="12" y1="17" x2="12" y2="21" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="pp-specs-feature-content-tct">
                <h4 style="margin:0 0 0.25rem 0; font-family:var(--font-display); font-size:1.05rem; color:var(--gold);"><?php echo esc_html__('Tủ bảo quản & Soạn chia Hoshizaki', 'saigonhoreca'); ?></h4>
                <p style="margin:0; font-size:0.92rem; line-height:1.6; color:var(--bc2);"><?php echo esc_html__('Hệ thống thiết bị lạnh bảo quản nguyên liệu tươi sống chuẩn Âu và các loại đế pizza luôn ở nhiệt độ tối ưu nhất, duy trì chất lượng ẩm thực cao cấp nhất.', 'saigonhoreca'); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
