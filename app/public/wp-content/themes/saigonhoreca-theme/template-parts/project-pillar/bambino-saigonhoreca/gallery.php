<?php
/**
 * Project Pillar — bambino-saigonhoreca
 * Section #6: Bar gallery layout
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="bb-bar-section pp-bambino-bar">
  <div class="bb-bar-container">
    <div class="bb-bar-grid">
      
      <!-- Cột trái: Card thông tin và thông số kỹ thuật tối ưu quầy bar -->
      <div class="bb-bar-content-card">
        <header class="pp-specs-header">
          <div class="pp-specs-label">
            <svg class="pp-specs-icon-label" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="2" y="2" width="12" height="12" rx="1" stroke="var(--gold)" stroke-width="1.2" stroke-dasharray="2 2" />
              <path d="M8 2V14M2 8H14" stroke="var(--gold)" stroke-width="0.8" />
            </svg>
            BẢN VẼ PHÂN KHU BAR
          </div>
          <h2 class="pp-text-section__title">
            <?php echo esc_html__('Bản sắc của Saigon Horeca hiện diện rõ rệt trong thiết kế nghệ thuật của quầy bar.', 'saigonhoreca'); ?>
          </h2>
          <div class="pp-specs-divider"></div>
        </header>

        <div class="pp-specs-intro">
          <p>Tại Bambino Superclub, không gian làm việc của Bartender được thiết kế mở trên diện tích rộng 23 mét vuông, phân chia khu vực lưu trữ và pha chế một cách thông minh, khoa học.</p>
        </div>

        <!-- Lưới phân khu và thiết bị bar -->
        <div class="pp-specs-grid">
          
          <!-- Phân khu 1: Quầy Bar Trước -->
          <div class="pp-specs-card">
            <div class="pp-specs-card__aside">
              <div class="pp-specs-card__icon-wrap">
                <!-- SVG tủ kệ & quầy lạnh -->
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="3" y="4" width="18" height="16" rx="2" stroke="var(--gold)" stroke-width="1.5" />
                  <line x1="3" y1="12" x2="21" y2="12" stroke="var(--gold)" stroke-width="1.2" />
                  <line x1="9" y1="4" x2="9" y2="20" stroke="var(--gold)" stroke-width="1.2" />
                  <circle cx="15" cy="8" r="1.5" fill="var(--gold)" />
                  <circle cx="15" cy="16" r="1.5" fill="var(--gold)" />
                </svg>
              </div>
              <span class="pp-specs-card__brand">SGH BAR</span>
            </div>
            <div class="pp-specs-card__main">
              <h4 class="pp-specs-card__title">Hệ Thống Quầy Lạnh Trưng Bày</h4>
              <p class="pp-specs-card__desc">Tổ chức tỉ mỉ các dòng tủ lạnh công nghiệp Fujimak (bàn lạnh 900mm và 1500mm), tủ inox 304 sản xuất bởi SGH, mang lại sự linh hoạt tuyệt đối cho mixology.</p>
            </div>
          </div>

          <!-- Phân khu 2: Vận Hành Vệ Sinh -->
          <div class="pp-specs-card">
            <div class="pp-specs-card__aside">
              <div class="pp-specs-card__icon-wrap">
                <!-- SVG rửa ly và máy đá -->
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="12" cy="12" r="8" stroke="var(--gold)" stroke-width="1.5" />
                  <path d="M12 8V16M8 12H16" stroke="var(--gold)" stroke-width="1" stroke-linecap="round" />
                  <path d="M17 7L7 17" stroke="rgba(212,175,55,0.4)" stroke-width="1" />
                </svg>
              </div>
              <span class="pp-specs-card__brand">WINTERHALTER</span>
            </div>
            <div class="pp-specs-card__main">
              <h4 class="pp-specs-card__title">Thiết Bị Rửa Cốc & Làm Đá Tốc Độ</h4>
              <p class="pp-specs-card__desc">Sử dụng máy làm đá Hoshizaki (220kg/ngày) và hệ thống rửa ly Winterhalter cao cấp với chu kỳ rửa siêu tốc chỉ 1 phút 30 giây, đáp ứng liên tục lượng khách VIP tinh tế.</p>
            </div>
          </div>

        </div>

        <p class="bb-bar-link">
          <a href="<?php echo esc_url(home_url('/du-an/')); ?>">
            <?php echo esc_html__('>> Khám phá thêm các dự án quầy bar cao cấp của Saigon Horeca', 'saigonhoreca'); ?>
          </a>
        </p>
      </div>
      
      <!-- Cột phải: Media với bản vẽ CAD bao quanh tuyệt mỹ -->
      <div class="bb-bar-media-col">
        <div class="pp-specs-media-wrapper pp-specs-media-wrapper--reverse">
          
          <!-- SVG Viền vẽ kỹ thuật CAD bao quanh ảnh cực chất -->
          <svg class="pp-specs-cad-frame" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="2" width="96" height="96" rx="6" stroke="rgba(212, 175, 55, 0.25)" stroke-width="0.5" stroke-dasharray="2 2" />
            <rect x="4" y="4" width="92" height="92" rx="4" stroke="rgba(212, 175, 55, 0.12)" stroke-width="0.75" />
            
            <!-- Góc bracket vector -->
            <path d="M 0 12 L 0 0 L 12 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 12 L 100 0 L 88 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 0 88 L 0 100 L 12 100" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 88 L 100 100 L 88 100" stroke="var(--gold)" stroke-width="1.2" />
            
            <!-- Thước đo kỹ thuật -->
            <line x1="50" y1="0" x2="50" y2="4" stroke="var(--gold)" stroke-width="0.5" />
            <line x1="0" y1="50" x2="4" y2="50" stroke="var(--gold)" stroke-width="0.5" />
            <line x1="100" y1="50" x2="96" y2="50" stroke="var(--gold)" stroke-width="0.5" />
          </svg>

          <!-- Ảnh chính -->
          <div class="bb-bar-media pp-image-container-shared">
            <img src="<?php echo sgh_img('bambino/bambino-quay-bar-va-may-rua-ly-winterhalter.jpg'); ?>" alt="Thiết bị quầy bar và hệ quầy chuẩn bị inox của Saigon Horeca tại Bambino" loading="lazy" decoding="async" width="600" height="800">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống quầy bar cocktail bọc đồng và quầy chuẩn bị inox (backbar) cao cấp', 'saigonhoreca'); ?></div>
          </div>
          
        </div>
      </div>

    </div>
  </div>
</section>

<?php /* T-034: merged from cta.php (cũ section 8) */ ?>
<section class="pp__section pp-bambino-cta">
  <div class="pp__container">
    <div class="sgh-bambino-cta-card">
      <!-- Cột trái: Văn bản & Nút trượt sáng Shimmer -->
      <div class="sgh-bambino-cta-content">
        <div class="sgh-bambino-cta-badge">
          <span class="pulse-dot"></span>
          Start Your Project
        </div>
        <h2 class="sgh-bambino-cta-title">Khởi Tạo Không Gian Ẩm Thực Độc Bản</h2>
        <p class="sgh-bambino-cta-desc">Bạn đang ấp ủ một concept nhà hàng, lounge hay superclub đẳng cấp? Hãy để Saigon Horeca đồng hành biến tầm nhìn nghệ thuật của bạn thành hiện thực kỹ thuật hoàn mỹ.</p>
        
        <div class="sgh-bambino-cta-features">
          <span class="feat-item">✦ Tư vấn concept độc bản</span>
          <span class="feat-item">✦ Thiết bị inox 304 chuẩn châu Âu</span>
          <span class="feat-item">✦ Vận hành tối ưu công năng</span>
        </div>

        <div class="sgh-bambino-cta-action">
          <a href="<?php echo esc_url(home_url('/lien-he')); ?>" class="sgh-bambino-cta-btn">
            <span class="btn-text">Liên Hệ Tư Vấn Ngay</span>
            <span class="btn-shimmer"></span>
          </a>
        </div>
      </div>

      <!-- Cột phải: Ảnh quầy bar thực tế ôm khít -->
      <div class="sgh-bambino-cta-media" style="background-image: url('<?php echo sgh_img('bambino/bambino-khong-gian-am-thuc-y-sang-trong.jpg'); ?>');">
        <div class="sgh-bambino-cta-media-overlay"></div>
      </div>

      <!-- Khung viền Parallel Neon Framer -->
      <div class="sgh-bambino-cta-framer">
        <span class="corner tl"></span>
        <span class="corner br"></span>
      </div>
    </div>
  </div>
</section>
