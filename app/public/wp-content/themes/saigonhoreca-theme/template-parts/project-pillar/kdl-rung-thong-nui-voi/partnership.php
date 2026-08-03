<?php
/**
 * Project Pillar — kdl-rung-thong-nui-voi
 * Section #4: partnership (Challenge & solution — Con Voi Vàng 400-guest kitchen)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-kdl pp-partnership-kdl">
  <div class="pp-leaf-watermark-kdl pp-leaf-watermark-kdl--left" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.2">
      <path d="M50 90 C50 60 30 40 20 20 C45 25 55 45 55 70 M50 90 C50 55 70 38 82 18 C58 24 48 46 48 72" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
  <div class="pp-ambient-glow-kdl pp-ambient-glow-kdl--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-12-kdl">

      <div class="pp-grid-12-kdl__text pp-partnership-kdl__main">
        <div class="pp-glass-card-kdl">
          <span class="pp-corner-leaf-kdl pp-corner-leaf-kdl--tl" aria-hidden="true"></span>
          <span class="pp-corner-leaf-kdl pp-corner-leaf-kdl--br" aria-hidden="true"></span>

          <header class="pp-partnership-kdl__header">
            <div class="pp-badge-kdl">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
              </svg>
              <?php echo esc_html__('Thách thức & Giải pháp', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-kdl__title"><?php echo esc_html__('Giải pháp bếp công nghiệp của Saigon Horeca', 'saigonhoreca'); ?></h2>
            <div class="pp-text-kdl__divider" aria-hidden="true"></div>
          </header>

          <div class="pp-partnership-kdl__body">
            <p class="pp-partnership-kdl__lead"><?php echo esc_html__('Với hàng chục nghìn lượt khách mỗi năm, yêu cầu từ chủ đầu tư là chất lượng thiết bị và tối ưu hóa quy trình vận hành.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Nhà hàng Con Voi Vàng đã hợp tác cùng Saigon Horeca để tối ưu hóa hệ thống bếp có sức chứa lên tới 400 khách. Hệ thống công suất mạnh mẽ, phục vụ nhanh chóng ngay cả vào giờ cao điểm, với thiết bị nhập khẩu từ thương hiệu uy tín — bền bỉ, an toàn và tiết kiệm năng lượng.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Hệ thống hút khói, khử mùi và cấp khí tươi giúp không gian bếp luôn thông thoáng; hệ thống thoát sàn đảm bảo sàn bếp luôn khô ráo và sạch sẽ, tuân thủ tiêu chuẩn vệ sinh an toàn thực phẩm.', 'saigonhoreca'); ?></p>
          </div>

          <div class="pp-partnership-kdl__stats">
            <div class="pp-partnership-kdl__stat">
              <span class="pp-partnership-kdl__stat-value">400</span>
              <span class="pp-partnership-kdl__stat-label"><?php echo esc_html__('Sức chứa tối đa (khách)', 'saigonhoreca'); ?></span>
            </div>
            <div class="pp-partnership-kdl__stat">
              <span class="pp-partnership-kdl__stat-value"><?php echo esc_html__('Khí tươi', 'saigonhoreca'); ?></span>
              <span class="pp-partnership-kdl__stat-label"><?php echo esc_html__('Hút khói & khử mùi liên tục', 'saigonhoreca'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="pp-grid-12-kdl__media pp-partnership-kdl__side">
        <div class="pp-image-container-shared pp-partnership-kdl__image-container pp-partnership-kdl__image-container--main">
          <div class="pp-image-border-decor-kdl" aria-hidden="true"></div>
          <img src="<?php echo sgh_img('du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca/kdl-rung-thong-nui-voi-cum-quat-hut-ong-gio-tren-mai.webp'); ?>" alt="<?php echo esc_attr__('Cụm ống hút khói và quạt thông gió công nghiệp trên mái', 'saigonhoreca'); ?>" loading="lazy" width="1229" height="691">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống hút khói và quạt thông gió công suất lớn cho khu bếp', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
