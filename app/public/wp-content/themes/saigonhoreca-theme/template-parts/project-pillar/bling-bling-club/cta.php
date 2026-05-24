<?php
/**
 * Project Pillar — bling-bling-club
 * Section #8: CTA — Bespoke Faceted Glassmorphism CTA
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-bbc-cta">
  <div class="pp__container">
    <div class="sgh-bbc-cta-card">
      <!-- Cột trái: Văn bản & Nút trượt sáng Shimmer màu vàng Champagne -->
      <div class="sgh-bbc-cta-content">
        <div class="sgh-bbc-cta-badge">
          <span class="glitter-dot"></span>
          Start Your Project
        </div>
        <h2 class="sgh-bbc-cta-title">Kiến Tạo Đẳng Cấp Thượng Lưu Cho Quán Bar Của Bạn</h2>
        <p class="sgh-bbc-cta-desc">Bạn đang tìm kiếm một đơn vị tư vấn thiết kế và phân phối thiết bị bếp bar inox 304 cao cấp chuẩn quốc tế? Hãy để Saigon Horeca nâng tầm dự án F&B của bạn lên vị thế dẫn đầu thị trường.</p>
        
        <div class="sgh-bbc-cta-features">
          <span class="feat-item">✦ Thiết kế bếp bar một chiều tối ưu</span>
          <span class="feat-item">✦ Thiết bị quầy bar inox 304 xước mờ tinh xảo</span>
          <span class="feat-item">✦ Tư vấn giải pháp kỹ thuật chuyên sâu</span>
        </div>

        <div class="sgh-bbc-cta-action">
          <a href="<?php echo esc_url(home_url('/lien-he')); ?>" class="sgh-bbc-cta-btn">
            <span class="btn-text">Liên Hệ Tư Vấn Ngay</span>
            <span class="btn-shimmer"></span>
          </a>
        </div>
      </div>

      <!-- Cột phải: Ảnh thi công thực tế ôm khít -->
      <div class="sgh-bbc-cta-media" style="background-image: url('<?php echo sgh_img('2024/02/bling-bling-club-space-1-1.jpg'); ?>');">
        <div class="sgh-bbc-cta-media-overlay"></div>
      </div>

      <!-- Khung viền Parallel Gold Framer -->
      <div class="sgh-bbc-cta-framer">
        <span class="corner tl"></span>
        <span class="corner br"></span>
      </div>
    </div>
  </div>
</section>
