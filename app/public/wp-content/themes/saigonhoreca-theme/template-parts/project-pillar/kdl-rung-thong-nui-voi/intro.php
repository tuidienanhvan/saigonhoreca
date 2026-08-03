<?php
/**
 * Project Pillar — kdl-rung-thong-nui-voi
 * Section #2: intro (Editorial asymmetric — story + forest gallery)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-kdl pp-intro-kdl">
  <div class="pp-leaf-watermark-kdl" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.2">
      <path d="M50 90 C50 60 30 40 20 20 C45 25 55 45 55 70 M50 90 C50 55 70 38 82 18 C58 24 48 46 48 72" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-kdl pp-ambient-glow-kdl--top-right" aria-hidden="true"></div>
  <div class="pp-ambient-glow-kdl pp-ambient-glow-kdl--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-12-kdl">

      <div class="pp-grid-12-kdl__text">
        <header class="pp-intro-kdl__header">
          <div class="pp-badge-kdl">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 3 L18 12 H14 L19 20 H5 L10 12 H6 Z"/>
            </svg>
            <?php echo esc_html__('Giới thiệu dự án', 'saigonhoreca'); ?>
          </div>
          <h2 class="pp-text-kdl__title"><?php echo esc_html__('KDL Rừng Thông Núi Voi', 'saigonhoreca'); ?></h2>
          <div class="pp-text-kdl__divider" aria-hidden="true"></div>
        </header>

        <div class="pp-intro-kdl__body">
          <p><?php echo esc_html__('Trốn phố về rừng, đắm chìm trong thiên nhiên hùng vĩ tại KDL Rừng Thông Núi Voi — điểm đến lý tưởng để du khách thưởng thức văn hóa ẩm thực Tây Nguyên độc đáo.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Với 6 nhà hàng riêng biệt nằm giữa khu rừng thông nguyên bản, mỗi nơi đều mang phong cách và thực đơn phong phú. Các món ăn truyền thống và hiện đại đều được chế biến từ nguyên liệu tươi ngon, đảm bảo an toàn vệ sinh thực phẩm — từ cơm lam, món nướng đậm đà đến rượu cần nồng nàn, tất cả đều được chế biến tinh tế và đầy nghệ thuật.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Với yêu cầu khắt khe về chất lượng thiết bị bếp và vị trí nằm trong rừng nguyên sinh, Saigon Horeca đã thiết kế và thi công hệ thống bếp cao cấp đáp ứng mọi yêu cầu từ chủ đầu tư, vận hành trơn tru và duy trì quy trình vệ sinh an toàn thực phẩm.', 'saigonhoreca'); ?></p>
        </div>

        <div class="pp-intro-kdl__stats">
          <div class="pp-intro-kdl__stat">
            <span class="pp-intro-kdl__stat-value">06</span>
            <span class="pp-intro-kdl__stat-label"><?php echo esc_html__('Nhà hàng riêng biệt', 'saigonhoreca'); ?></span>
          </div>
          <div class="pp-intro-kdl__stat">
            <span class="pp-intro-kdl__stat-value">400</span>
            <span class="pp-intro-kdl__stat-label"><?php echo esc_html__('Khách / nhà hàng lớn nhất', 'saigonhoreca'); ?></span>
          </div>
          <div class="pp-intro-kdl__stat">
            <span class="pp-intro-kdl__stat-value">100%</span>
            <span class="pp-intro-kdl__stat-label"><?php echo esc_html__('An toàn vệ sinh thực phẩm', 'saigonhoreca'); ?></span>
          </div>
        </div>
      </div>

      <div class="pp-grid-12-kdl__media pp-intro-kdl__side">
        <div class="pp-image-container-shared pp-intro-kdl__image-container">
          <div class="pp-image-border-decor-kdl" aria-hidden="true"></div>
          <img src="<?php echo sgh_img('du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca/kdl-rung-thong-nui-voi-ban-tiec-ngoai-troi-hoang-hon.webp'); ?>" alt="<?php echo esc_attr__('Bàn tiệc ngoài trời giữa rừng thông Núi Voi lúc hoàng hôn', 'saigonhoreca'); ?>" loading="lazy" width="600" height="800">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Bàn tiệc ngoài trời bên ly vang — không gian nhà hàng hòa mình giữa rừng thông lúc hoàng hôn', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>

  </div>
</section>
