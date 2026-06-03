<?php
/**
 * Project Pillar — godmother-friendship
 * Section #2: intro
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-gmf pp-intro-gmf scroll-reveal">
  <!-- AutoCAD background grid lines -->
  <div class="pp-intro-bg-grid-gmf" aria-hidden="true">
    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <pattern id="introGridPattern" width="60" height="60" patternUnits="userSpaceOnUse">
          <rect width="60" height="60" fill="none"/>
          <path d="M 60 0 L 0 0 0 60" fill="none" stroke="currentColor" stroke-width="0.5" stroke-opacity="0.04"/>
        </pattern>
      </defs>
      <rect width="100%" height="100%" fill="url(#introGridPattern)" />
    </svg>
  </div>

  <div class="pp-ambient-glow-gmf pp-ambient-glow-gmf--top-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-intro-layout-gmf">
      
      <!-- Cột chữ (100%): Typography & Editorial Content -->
      <div class="pp-intro-content-col-gmf">
        <!-- Brand Logo Badge -->
        <div class="pp-intro-symbol-badge-gmf" style="width: fit-content; margin-bottom: 1.5rem;">
          <img src="<?php echo sgh_img('godmother-friendship/godmother-friendship-logo-chu-trang-nen-cam-dat.webp'); ?>" alt="<?php echo esc_attr__('Godmother Friendship Logo', 'saigonhoreca'); ?>" class="pp-intro-symbol-img-gmf">
          <div class="pp-intro-symbol-text-gmf">
            <span><?php echo esc_html__('Est. 2019', 'saigonhoreca'); ?></span>
            <strong><?php echo esc_html__('GODMOTHER', 'saigonhoreca'); ?></strong>
          </div>
        </div>

        <span class="pp-intro-eyebrow-gmf"><?php echo esc_html__('Thương hiệu F&B nổi bật', 'saigonhoreca'); ?></span>
        <h2 class="pp-intro-title-gmf"><?php echo esc_html__('Brunch Saigon — Một trải nghiệm hoàn toàn mới', 'saigonhoreca'); ?></h2>
        <div class="pp-intro-divider-gmf" aria-hidden="true"></div>
        
        <div class="pp-intro-body-gmf">
          <p class="pp-intro-lead-gmf">
            <span class="pp-dropcap-gmf">B</span><?php echo esc_html__('runch là bữa ăn kết hợp giữa sáng và trưa, vừa no đủ lại vừa linh hoạt. Vào năm 2019, khi Sài Gòn chưa có nhà hàng nào theo phong cách này, GodMother ra đời, mang đến một trải nghiệm hoàn toàn mới.', 'saigonhoreca'); ?>
          </p>
          
          <blockquote class="pp-intro-quote-gmf">
            <p><?php echo esc_html__('Gặt hái những thành công nhất định, cuối năm 2020, GodMother mở thêm chi nhánh mới tại Tòa nhà Friendship Tower, Quận 1.', 'saigonhoreca'); ?></p>
          </blockquote>
        </div>

        <!-- Technical Specification Metadata Table -->
        <div class="pp-intro-meta-grid-gmf">
          <div class="pp-intro-meta-item-gmf">
            <span class="pp-intro-meta-label-gmf"><?php echo esc_html__('Vị trí', 'saigonhoreca'); ?></span>
            <strong class="pp-intro-meta-value-gmf"><?php echo esc_html__('Lê Lợi, Bến Nghé, Quận 1', 'saigonhoreca'); ?></strong>
          </div>
          <div class="pp-intro-meta-item-gmf">
            <span class="pp-intro-meta-label-gmf"><?php echo esc_html__('Thiết kế', 'saigonhoreca'); ?></span>
            <strong class="pp-intro-meta-value-gmf"><?php echo esc_html__('Modern & Cozy', 'saigonhoreca'); ?></strong>
          </div>
          <div class="pp-intro-meta-item-gmf">
            <span class="pp-intro-meta-label-gmf"><?php echo esc_html__('Hạng mục SGH', 'saigonhoreca'); ?></span>
            <strong class="pp-intro-meta-value-gmf"><?php echo esc_html__('Bar, Kiosk & Bếp Nóng', 'saigonhoreca'); ?></strong>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
