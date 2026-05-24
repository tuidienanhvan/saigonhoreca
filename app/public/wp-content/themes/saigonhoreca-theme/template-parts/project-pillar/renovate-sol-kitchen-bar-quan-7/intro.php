<?php
/**
 * Project Pillar — renovate-sol-kitchen-bar-quan-7
 * Section #2: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-intro-section">
  <div class="sgh-intro-container">
    <div class="sgh-intro-row">
      
      <!-- CỘT TRÁI: TEXT & CONTEXT -->
      <div class="sgh-intro-col sgh-intro-col--text">
        <div class="sgh-intro-badge">
          <span class="sgh-intro-badge__accent">//</span>
          <?php echo esc_html__('BỐI CẢNH DỰ ÁN', 'saigonhoreca'); ?>
        </div>
        
        <h2 class="sgh-intro-title">
          <?php echo esc_html__('Sự Đồng Hành Cùng Tác Phẩm Michelin', 'saigonhoreca'); ?>
        </h2>
        
        <div class="sgh-intro-body">
          <blockquote class="sgh-intro-quote">
            <?php echo esc_html__('Nơi nghệ thuật ẩm thực Mỹ Latinh gặp gỡ giải pháp vận hành bếp công nghiệp đỉnh cao.', 'saigonhoreca'); ?>
          </blockquote>
          <p class="sgh-intro-paragraph">
            <?php echo esc_html__('Tiếp nối sự thành công của nhà hàng Sol quận 1 khi lọt vào Michelin Guide 2023, Saigon Horeca vinh hạnh được chủ đầu tư Sol Kitchen & Bar tin tưởng lựa chọn là đơn vị uy tín triển khai hệ thống bếp cao cấp cho nhà hàng tại quận 7, HCMC.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>
      
      <!-- CỘT PHẢI: 3D PERSPECTIVE SAFARI MOCKUP -->
      <div class="sgh-intro-col sgh-intro-col--media">
        <div class="sgh-intro-mockup-wrapper">
          <!-- Ambient Glow sau Mockup -->
          <div class="sgh-intro-ambient-glow" aria-hidden="true"></div>
          
          <!-- Browser Mockup -->
          <div class="sgh-intro-browser">
            <!-- Header của trình duyệt Safari -->
            <div class="sgh-intro-browser__header">
              <div class="sgh-intro-browser__buttons">
                <span class="sgh-intro-browser__btn sgh-intro-browser__btn--red"></span>
                <span class="sgh-intro-browser__btn sgh-intro-browser__btn--yellow"></span>
                <span class="sgh-intro-browser__btn sgh-intro-browser__btn--green"></span>
              </div>
              <div class="sgh-intro-browser__address">
                <span class="sgh-intro-browser__lock">🔒</span>
                guide.michelin.com/vn/vi/ho-chi-minh/sol-kitchen-bar
              </div>
            </div>
            <!-- Nội dung trình duyệt chứa screenshot -->
            <div class="sgh-intro-browser__viewport">
              <img src="<?php echo sgh_img('2024/06/Sol0D7-01.jpg'); ?>" alt="<?php echo esc_attr__('Đánh giá Michelin Guide của Sol Kitchen & Bar', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</section>
