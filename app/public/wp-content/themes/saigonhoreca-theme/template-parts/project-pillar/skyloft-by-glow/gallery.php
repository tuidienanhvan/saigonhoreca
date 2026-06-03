<?php
/**
 * Project Pillar — skyloft-by-glow
 * Section #6: gallery — advanced editorial visual storytelling.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-sky pp-gallery-section-sky scroll-reveal">
  <div class="pp-container-shared">
    
    <!-- Tiêu đề của Section Gallery để tạo tính liền mạch và dẫn dắt -->
    <div class="pp-gallery-sky__header scroll-reveal">
      <span class="pp-gallery-sky__subtitle"><?php echo esc_html__('VISUAL STORYTELLING // 06', 'saigonhoreca'); ?></span>
      <h2 class="pp-gallery-sky__title"><?php echo esc_html__('Không Gian Đêm & Trải Nghiệm Mixology', 'saigonhoreca'); ?></h2>
      <p class="pp-gallery-sky__intro-desc">
        <?php echo esc_html__('Mỗi góc nhìn tại Skyloft by Glow là sự tổng hòa giữa nghệ thuật bài trí không gian và kỹ thuật vận hành quầy bar chuyên sâu, mang lại trải nghiệm thị giác và vị giác đỉnh cao cho thực khách.', 'saigonhoreca'); ?>
      </p>
    </div>

    <!-- PHÂN KHU 1: THE GRAND VENUE (Lưới bất đối xứng: Ảnh Không gian ngang hoành tráng + Trích dẫn triết lý) -->
    <div class="pp-gallery-sky__row pp-gallery-sky__row--editorial scroll-reveal">
      <!-- Ảnh Đêm tiệc Rooftop (Ngang) chiếm 58% -->
      <div class="pp-gallery-sky__editorial-media">
        <div class="pp-image-container-shared pp-gallery-sky__frame-cad pp-gallery-sky__frame-cad--landscape">
          <img src="<?php echo sgh_img('skyloft-by-glow/skyloft-by-glow-dem-tiec-rooftop-anh-den-san-khau.webp'); ?>" alt="<?php echo esc_attr__('Đêm tiệc rooftop tại Skyloft by Glow', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-gallery-sky__img-meta">
            <span class="pp-gallery-sky__meta-tag"><?php echo esc_html__('CAP_01 // THE ATMOSPHERE', 'saigonhoreca'); ?></span>
          </div>
        </div>
        <div class="pp-gallery-sky__editorial-caption">
          <strong><?php echo esc_html__('Vận hành công suất tối đa:', 'saigonhoreca'); ?></strong>
          <?php echo esc_html__('Bố trí mặt bằng quầy bar và hệ thống bàn ghế tối ưu luồng giao thông của khách hàng lẫn nhân viên phục vụ, đảm bảo tốc độ phục vụ chuẩn xác ngay cả trong khung giờ đêm cao điểm náo nhiệt nhất.', 'saigonhoreca'); ?>
        </div>
      </div>

      <!-- Khối trích dẫn nghệ thuật pha chế (Quote Card) chiếm 42% -->
      <div class="pp-gallery-sky__text-card pp-gallery-sky__text-card--quote-new">
        <div class="pp-sky-card-glow-border"></div>
        <span class="pp-gallery-sky__card-eyebrow"><?php echo esc_html__('ART OF MIXOLOGY // PHILOSOPHY', 'saigonhoreca'); ?></span>
        <blockquote class="pp-gallery-sky__quote-new">
          “<?php echo esc_html__('Mỗi ly cocktail phục vụ tại Skyloft không đơn thuần là đồ uống, mà là một tác phẩm trình diễn ánh sáng, khói hương tự nhiên và hương vị đầy cảm xúc giữa tầng không trung.', 'saigonhoreca'); ?>”
        </blockquote>
        <div class="pp-gallery-sky__quote-author"><?php echo esc_html__('Saigon Horeca Specialist Team', 'saigonhoreca'); ?></div>
      </div>
    </div>

    <!-- PHÂN KHU 2: THE MIXOLOGY DUOLOGY (Cặp ảnh dọc song song hoàn hảo - Cân đối tuyệt đối) -->
    <div class="pp-gallery-sky__row pp-gallery-sky__row--duology scroll-reveal">
      
      <!-- Ảnh 1: Cocktail Cam trên mặt đá sáng -->
      <div class="pp-gallery-sky__duology-card">
        <div class="pp-image-container-shared pp-gallery-sky__frame-cad pp-gallery-sky__frame-cad--portrait">
          <img src="<?php echo sgh_img('skyloft-by-glow/skyloft-by-glow-cocktail-cam-tren-mat-da-sang.webp'); ?>" alt="<?php echo esc_attr__('Cocktail cam trên mặt đá sáng tại Skyloft by Glow', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-gallery-sky__img-meta">
            <span class="pp-gallery-sky__meta-tag"><?php echo esc_html__('CAP_02 // LIGHTING TECHNIQUE', 'saigonhoreca'); ?></span>
          </div>
        </div>
        <div class="pp-gallery-sky__editorial-caption">
          <strong><?php echo esc_html__('Mặt đá xuyên sáng nghệ thuật:', 'saigonhoreca'); ?></strong>
          <?php echo esc_html__('Giải pháp tích hợp hệ thống đèn LED cường độ chuẩn dưới mặt đá quầy bar giúp tôn vinh màu sắc tự nhiên của đồ uống, tạo hiệu ứng thị giác rực rỡ và lôi cuốn sự chú ý của thực khách tại chỗ ngồi.', 'saigonhoreca'); ?>
        </div>
      </div>

      <!-- Ảnh 2: Cocktail Đốt lửa tạo bùng nổ vị giác -->
      <div class="pp-gallery-sky__duology-card">
        <div class="pp-image-container-shared pp-gallery-sky__frame-cad pp-gallery-sky__frame-cad--portrait">
          <img src="<?php echo sgh_img('skyloft-by-glow/skyloft-by-glow-cocktail-dot-lua-tren-quay-bar.webp'); ?>" alt="<?php echo esc_attr__('Cocktail đốt lửa trên quầy bar Skyloft by Glow', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-gallery-sky__img-meta">
            <span class="pp-gallery-sky__meta-tag"><?php echo esc_html__('CAP_03 // BAR SAFETY', 'saigonhoreca'); ?></span>
          </div>
        </div>
        <div class="pp-gallery-sky__editorial-caption">
          <strong><?php echo esc_html__('Kỹ thuật đốt lửa và an toàn vận hành:', 'saigonhoreca'); ?></strong>
          <?php echo esc_html__('Thiết kế khoảng cách quầy bar và bề mặt inox 304 chống cháy chuyên dụng, đảm bảo các thao tác biểu diễn nhiệt độ cao của bartender diễn ra an toàn tuyệt đối và mãn nhãn thực khách.', 'saigonhoreca'); ?>
        </div>
      </div>

    </div>

    <!-- PHÂN KHU 3: LUMINOUS ARCHITECTURE (Hệ ảnh cuối hiển thị lớn, phóng khoáng, không bị ép dẹp) -->
    <div class="pp-gallery-sky__row pp-gallery-sky__row--luminous scroll-reveal">
      
      <!-- Ảnh 1: Ly cocktail đỏ rực rỡ (Ảnh dọc 3:4) - Chiếm 42% -->
      <div class="pp-gallery-sky__luminous-card pp-gallery-sky__luminous-card--portrait">
        <div class="pp-image-container-shared pp-gallery-sky__frame-cad pp-gallery-sky__frame-cad--portrait">
          <img src="<?php echo sgh_img('skyloft-by-glow/skyloft-by-glow-ly-cocktail-do-tai-quay-bar.webp'); ?>" alt="<?php echo esc_attr__('Ly cocktail đỏ tại quầy bar Skyloft by Glow', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-gallery-sky__img-meta">
            <span class="pp-gallery-sky__meta-tag"><?php echo esc_html__('CAP_04 // COLOR CONTRAST', 'saigonhoreca'); ?></span>
          </div>
        </div>
        <div class="pp-gallery-sky__editorial-caption">
          <strong><?php echo esc_html__('Độ tương phản màu sắc đỉnh cao:', 'saigonhoreca'); ?></strong>
          <?php echo esc_html__('Đồ uống mang sắc đỏ nồng nàn đặt trên nền quầy bar tối màu tạo nên điểm nhấn thị giác sắc nét, thể hiện sự am hiểu sâu sắc về nghệ thuật trình bày ẩm thực trong môi trường ánh sáng yếu.', 'saigonhoreca'); ?>
        </div>
      </div>

      <!-- Ảnh 2: Không gian laser (Ảnh ngang 16:10) - Chiếm 58% -->
      <div class="pp-gallery-sky__luminous-card pp-gallery-sky__luminous-card--landscape">
        <div class="pp-image-container-shared pp-gallery-sky__frame-cad pp-gallery-sky__frame-cad--landscape">
          <img src="<?php echo sgh_img('skyloft-by-glow/skyloft-by-glow-khong-gian-rooftop-anh-sang-laser.webp'); ?>" alt="<?php echo esc_attr__('Không gian rooftop Skyloft by Glow với ánh sáng laser', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-gallery-sky__img-meta">
            <span class="pp-gallery-sky__meta-tag"><?php echo esc_html__('CAP_05 // INTERIOR & SYSTEM', 'saigonhoreca'); ?></span>
          </div>
        </div>
        <div class="pp-gallery-sky__editorial-caption">
          <strong><?php echo esc_html__('Hệ thống ánh sáng Laser và không gian mở:', 'saigonhoreca'); ?></strong>
          <?php echo esc_html__('Sự kết hợp hoàn hảo giữa quầy bar phát sáng nghệ thuật và luồng sáng laser hiện đại, tạo nên một tổng thể không gian kiến trúc giải trí về đêm bùng nổ cảm xúc và tràn đầy năng lượng.', 'saigonhoreca'); ?>
        </div>
      </div>

    </div>

    <!-- PHÂN KHU 4: THE GRAND CONCLUSION (Khối chữ kết thúc nghệ thuật với SVG kỹ thuật & Lưới ô ly) -->
    <div class="pp-gallery-sky__row pp-gallery-sky__row--conclusion scroll-reveal">
      <div class="pp-gallery-sky__conclusion-canvas">
        
        <!-- Background Grid Pattern mờ ảo bằng SVG -->
        <div class="pp-gallery-sky__canvas-grid" aria-hidden="true">
          <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <pattern id="sky-conclusion-grid" width="40" height="40" patternUnits="userSpaceOnUse">
                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(0, 240, 255, 0.05)" stroke-width="1"/>
                <path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(180, 0, 255, 0.03)" stroke-width="0.5"/>
              </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#sky-conclusion-grid)" />
          </svg>
        </div>

        <!-- Khung viền góc kỹ thuật SVG cực kỳ tinh xảo chạy xung quanh Canvas -->
        <div class="pp-gallery-sky__canvas-corners" aria-hidden="true">
          <!-- Top-Left Corner -->
          <svg class="pp-corner-svg pp-corner-svg--tl" width="40" height="40" viewBox="0 0 40 40">
            <path d="M 0 40 L 0 0 L 40 0" fill="none" stroke="var(--sky-cyan)" stroke-width="1.5"/>
            <circle cx="0" cy="0" r="3" fill="var(--sky-cyan)"/>
            <path d="M 8 8 L 8 20 M 8 8 L 20 8" fill="none" stroke="rgba(0, 240, 255, 0.3)" stroke-width="1"/>
          </svg>
          <!-- Top-Right Corner -->
          <svg class="pp-corner-svg pp-corner-svg--tr" width="40" height="40" viewBox="0 0 40 40">
            <path d="M 0 0 L 40 0 L 40 40" fill="none" stroke="var(--sky-cyan)" stroke-width="1.5"/>
            <circle cx="40" cy="0" r="3" fill="var(--sky-cyan)"/>
            <path d="M 32 8 L 32 20 M 32 8 L 20 8" fill="none" stroke="rgba(0, 240, 255, 0.3)" stroke-width="1"/>
          </svg>
          <!-- Bottom-Left Corner -->
          <svg class="pp-corner-svg pp-corner-svg--bl" width="40" height="40" viewBox="0 0 40 40">
            <path d="M 0 0 L 0 40 L 40 40" fill="none" stroke="var(--sky-cyan)" stroke-width="1.5"/>
            <circle cx="0" cy="40" r="3" fill="var(--sky-cyan)"/>
            <path d="M 8 32 L 8 20 M 8 32 L 20 32" fill="none" stroke="rgba(0, 240, 255, 0.3)" stroke-width="1"/>
          </svg>
          <!-- Bottom-Right Corner -->
          <svg class="pp-corner-svg pp-corner-svg--br" width="40" height="40" viewBox="0 0 40 40">
            <path d="M 0 40 L 40 40 L 40 0" fill="none" stroke="var(--sky-cyan)" stroke-width="1.5"/>
            <circle cx="40" cy="40" r="3" fill="var(--sky-cyan)"/>
            <path d="M 32 32 L 32 20 M 32 32 L 20 32" fill="none" stroke="rgba(0, 240, 255, 0.3)" stroke-width="1"/>
          </svg>
        </div>

        <!-- Họa tiết trang trí kỹ thuật bên lề Canvas (SVG Ruler & Coordinates) -->
        <div class="pp-gallery-sky__canvas-ruler-left" aria-hidden="true">
          <svg width="10" height="120" viewBox="0 0 10 120">
            <line x1="0" y1="10" x2="10" y2="10" stroke="rgba(0, 240, 255, 0.4)" stroke-width="1"/>
            <line x1="0" y1="30" x2="6" y2="30" stroke="rgba(0, 240, 255, 0.2)" stroke-width="1"/>
            <line x1="0" y1="50" x2="6" y2="50" stroke="rgba(0, 240, 255, 0.2)" stroke-width="1"/>
            <line x1="0" y1="70" x2="6" y2="70" stroke="rgba(0, 240, 255, 0.2)" stroke-width="1"/>
            <line x1="0" y1="90" x2="6" y2="90" stroke="rgba(0, 240, 255, 0.2)" stroke-width="1"/>
            <line x1="0" y1="110" x2="10" y2="110" stroke="rgba(0, 240, 255, 0.4)" stroke-width="1"/>
            <line x1="0" y1="10" x2="0" y2="110" stroke="rgba(0, 240, 255, 0.3)" stroke-width="1"/>
          </svg>
        </div>
        <div class="pp-gallery-sky__canvas-ruler-right" aria-hidden="true">
          <svg width="10" height="120" viewBox="0 0 10 120">
            <line x1="10" y1="10" x2="0" y2="10" stroke="rgba(0, 240, 255, 0.4)" stroke-width="1"/>
            <line x1="10" y1="30" x2="4" y2="30" stroke="rgba(0, 240, 255, 0.2)" stroke-width="1"/>
            <line x1="10" y1="50" x2="4" y2="50" stroke="rgba(0, 240, 255, 0.2)" stroke-width="1"/>
            <line x1="10" y1="70" x2="4" y2="70" stroke="rgba(0, 240, 255, 0.2)" stroke-width="1"/>
            <line x1="10" y1="90" x2="4" y2="90" stroke="rgba(0, 240, 255, 0.2)" stroke-width="1"/>
            <line x1="10" y1="110" x2="0" y2="110" stroke="rgba(0, 240, 255, 0.4)" stroke-width="1"/>
            <line x1="10" y1="10" x2="10" y2="110" stroke="rgba(0, 240, 255, 0.3)" stroke-width="1"/>
          </svg>
        </div>

        <!-- Nội dung chữ 2 cột phong cách tạp chí đặc sắc -->
        <div class="pp-gallery-sky__conclusion-content">
          <!-- Cột Trái: Tiêu đề hoành tráng & Slogan -->
          <div class="pp-gallery-sky__conclusion-left-col">
            <span class="pp-gallery-sky__card-eyebrow"><?php echo esc_html__('ENDING CHAPTER // PROFESSIONAL SPECS', 'saigonhoreca'); ?></span>
            <h3 class="pp-gallery-sky__ending-title-new"><?php echo esc_html__('Năng Lực Thiết Kế & Thi Công Vượt Trội', 'saigonhoreca'); ?></h3>
            
            <!-- Họa tiết SVG trang trí nằm ngang dưới tiêu đề (SVG Divider) -->
            <div class="pp-gallery-sky__conclusion-divider" aria-hidden="true">
              <svg width="100" height="12" viewBox="0 0 100 12">
                <line x1="0" y1="6" x2="70" y2="6" stroke="var(--sky-cyan)" stroke-width="1.5"/>
                <circle cx="75" cy="6" r="2.5" fill="var(--sky-purple)"/>
                <line x1="80" y1="6" x2="100" y2="6" stroke="rgba(0, 240, 255, 0.4)" stroke-width="1"/>
              </svg>
            </div>
            
            <p class="pp-gallery-sky__conclusion-slogan">
              <?php echo esc_html__('Kiến tạo hệ sinh thái vận hành sky bar bền bỉ, trơn tru và thăng hoa vị giác giữa tầng không.', 'saigonhoreca'); ?>
            </p>
          </div>

          <!-- Cột Phải: Đoạn văn xuôi đầy đặn & Khung chữ ký Chief Engineer -->
          <div class="pp-gallery-sky__conclusion-right-col">
            <p class="pp-gallery-sky__ending-desc-new">
              <?php echo esc_html__('Đối với một công trình sky bar đẳng cấp quốc tế như Skyloft by Glow, từng chi tiết thiết kế đều phải đạt độ sắc nét và tính đột phá cao nhất. Saigon Horeca tự hào đã vượt qua những tiêu chuẩn kỹ thuật khắt khe, tích hợp hoàn hảo hệ thống thiết bị quầy bar chuyên dụng inox 304 cao cấp cùng hệ thống chiếu sáng nghệ thuật.', 'saigonhoreca'); ?>
            </p>
            <p class="pp-gallery-sky__ending-desc-new">
              <?php echo esc_html__('Chúng tôi không chỉ cung cấp giải pháp thiết bị, mà còn kiến tạo một cấu trúc vận hành ổn định, đáp ứng hoàn hảo tần suất hoạt động cực lớn của một mô hình sky bar hàng đầu Sài Gòn. Sự tin tưởng của chủ đầu tư chính là bảo chứng vàng cho năng lực chuyên môn và tinh thần đổi mới sáng tạo không giới hạn của Saigon Horeca.', 'saigonhoreca'); ?>
            </p>
            
            <!-- Khung chữ ký Chief Engineer Box với đường vẽ SVG -->
            <div class="pp-gallery-sky__signature-box-new">
              <div class="pp-sig-box-bg" aria-hidden="true">
                <svg width="100%" height="100%" viewBox="0 0 260 60" preserveAspectRatio="none">
                  <path d="M 0 0 L 260 0 L 260 50 L 245 60 L 0 60 Z" fill="rgba(10, 10, 15, 0.6)" stroke="rgba(0, 240, 255, 0.15)" stroke-width="1"/>
                  <line x1="245" y1="60" x2="245" y2="50" stroke="rgba(0, 240, 255, 0.3)" stroke-width="1"/>
                  <line x1="260" y1="50" x2="245" y2="50" stroke="rgba(0, 240, 255, 0.3)" stroke-width="1"/>
                </svg>
              </div>
              <div class="pp-sig-box-text">
                <span class="pp-gallery-sky__sig-title"><?php echo esc_html__('SAIGON HORECA', 'saigonhoreca'); ?></span>
                <span class="pp-gallery-sky__sig-sub"><?php echo esc_html__('Kitchen & Bar Specialist Team // 2026', 'saigonhoreca'); ?></span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</section>
