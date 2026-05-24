<?php
/**
 * Project Pillar — bambino-saigonhoreca
 * Section #8: CTA — Bespoke Integrated Split CTA Card
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
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
      <div class="sgh-bambino-cta-media" style="background-image: url('<?php echo sgh_img('2024/01/bambino-saigon-horeca-1.jpg'); ?>');">
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
