<?php
/**
 * Project Pillar — skyloft-by-glow
 * Section #4: specs — split layout with technical drawings + experience description.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-sky pp-section-sky--alt pp-specs-sky scroll-reveal">
  <div class="pp-sky-ambient-glow pp-sky-ambient-glow--tr" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-split-sky pp-specs-sky__layout">
      <!-- Cột trái: Khối chữ Blueprint Kỹ thuật nghệ thuật -->
      <div class="pp-specs-sky__text-panel">
        <div class="pp-specs-sky__bg-blueprint" aria-hidden="true"></div>
        <div class="pp-sky-ornament" aria-hidden="true"></div>
        <span class="pp-specs-sky__eyebrow"><?php echo esc_html__('SYSTEM SPECIFICATIONS // M&E', 'saigonhoreca'); ?></span>
        <h2 class="pp-text-sky__title"><?php echo esc_html__('Bản vẽ kỹ thuật & luồng vận hành', 'saigonhoreca'); ?></h2>
        
        <div class="pp-specs-sky__body-wrap">
          <p class="pp-specs-sky__intro-para"><?php echo esc_html__('Hệ quầy bar Skyloft by Glow được tổ chức theo nhịp phục vụ cao điểm: tủ mát, tủ đông, rinser, speed rail và khu thao tác cocktail đặt trong tầm với để bartender duy trì tốc độ ra món ổn định.', 'saigonhoreca'); ?></p>
          
          <div class="pp-specs-sky__boxes">
            <div class="pp-specs-sky__box-item">
              <span class="pp-specs-sky__box-num">01</span>
              <div class="pp-specs-sky__box-content">
                <h4 class="pp-specs-sky__box-title"><?php echo esc_html__('Bảo quản nguyên liệu', 'saigonhoreca'); ?></h4>
                <p class="pp-specs-sky__box-desc"><?php echo esc_html__('Counter chiller và counter freezer bố trí hai đầu line, hỗ trợ bảo quản nguyên liệu lạnh ngay tại quầy.', 'saigonhoreca'); ?></p>
              </div>
            </div>
            
            <div class="pp-specs-sky__box-item">
              <span class="pp-specs-sky__box-num">02</span>
              <div class="pp-specs-sky__box-content">
                <h4 class="pp-specs-sky__box-title"><?php echo esc_html__('Phục vụ cao điểm', 'saigonhoreca'); ?></h4>
                <p class="pp-specs-sky__box-desc"><?php echo esc_html__('Speed rail, insulated well và rinser được lặp theo module, giảm giao cắt thao tác trong giờ đông khách.', 'saigonhoreca'); ?></p>
              </div>
            </div>
            
            <div class="pp-specs-sky__box-item">
              <span class="pp-specs-sky__box-num">03</span>
              <div class="pp-specs-sky__box-content">
                <h4 class="pp-specs-sky__box-title"><?php echo esc_html__('Thi công cao độ', 'saigonhoreca'); ?></h4>
                <p class="pp-specs-sky__box-desc"><?php echo esc_html__('Mặt đứng thiết bị thể hiện rõ khu cocktail station, sink & rinser và hệ tủ kéo mát dưới quầy.', 'saigonhoreca'); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột phải: 2 ảnh bằng nhau tăm tắp kết hợp lưới vẽ CAD nghệ thuật -->
      <div class="pp-split-sky__media pp-specs-sky__media scroll-reveal">
        <div class="pp-specs-sky__drawing-container">
          <!-- Ảnh 1: Bản vẽ mặt bằng -->
          <div class="pp-specs-sky__drawing-wrapper">
            <span class="pp-specs-sky__drawing-label"><?php echo esc_html__('01 // BẢN VẼ BỐ TRÍ THIẾT BỊ MẶT BẰNG', 'saigonhoreca'); ?></span>
            <div class="pp-image-container-shared pp-specs-sky__frame-cad">
              <img src="<?php echo sgh_img('skyloft-by-glow/skyloft-by-glow-ban-ve-bo-tri-thiet-bi-quay-bar.webp'); ?>" alt="<?php echo esc_attr__('Bản vẽ bố trí thiết bị quầy bar Skyloft by Glow', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared"><?php echo esc_html__('Bản vẽ mặt bằng thể hiện tủ mát, tủ đông, line DJ, rinser và speed rail được bố trí tối ưu.', 'saigonhoreca'); ?></div>
            </div>
          </div>

          <!-- Ảnh 2: Bản vẽ mặt đứng -->
          <div class="pp-specs-sky__drawing-wrapper">
            <span class="pp-specs-sky__drawing-label"><?php echo esc_html__('02 // BẢN VẼ CHI TIẾT MẶT ĐỨNG HỆ THỐNG', 'saigonhoreca'); ?></span>
            <div class="pp-image-container-shared pp-specs-sky__frame-cad">
              <img src="<?php echo sgh_img('skyloft-by-glow/skyloft-by-glow-ban-ve-mat-dung-thiet-bi-quay-bar.webp'); ?>" alt="<?php echo esc_attr__('Bản vẽ mặt đứng thiết bị quầy bar Skyloft by Glow', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared"><?php echo esc_html__('Mặt đứng thiết bị mô tả cocktail station, sink & rinser và tủ kéo mát dưới quầy.', 'saigonhoreca'); ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


