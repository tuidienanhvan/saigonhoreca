<?php
/**
 * Project Pillar — kdl-rung-thong-nui-voi
 * Section #5: specs (Kitchen functional design & technical commitments)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-kdl pp-section-kdl--alt pp-specs-kdl">
  <div class="pp-ambient-glow-kdl pp-ambient-glow-kdl--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-12-kdl pp-grid-12-kdl--media-first">

      <div class="pp-grid-12-kdl__media pp-specs-kdl__side">
        <div class="pp-specs-kdl__coord pp-specs-kdl__coord--tl" aria-hidden="true">RUNG THONG NUI VOI / LAYOUT_v1.0</div>
        <div class="pp-specs-kdl__coord pp-specs-kdl__coord--br" aria-hidden="true">SAIGON HORECA</div>

        <div class="pp-image-container-shared pp-specs-kdl__image-container">
          <div class="pp-image-border-decor-kdl" aria-hidden="true"></div>
          <img src="<?php echo sgh_img('du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca/kdl-rung-thong-nui-voi-ong-hut-khoi-than-cong-trinh.webp'); ?>" alt="<?php echo esc_attr__('Hệ thống ống hút khói và thông gió inox lắp ngoài công trình', 'saigonhoreca'); ?>" loading="lazy" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống hút khói và thông gió inox lắp dọc thân công trình, giữ khu bếp luôn thông thoáng', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <div class="pp-grid-12-kdl__text pp-specs-kdl__main">
        <div class="pp-glass-card-kdl pp-specs-kdl__card">
          <span class="pp-corner-leaf-kdl pp-corner-leaf-kdl--tl" aria-hidden="true"></span>
          <span class="pp-corner-leaf-kdl pp-corner-leaf-kdl--br" aria-hidden="true"></span>

          <header class="pp-specs-kdl__header">
            <div class="pp-badge-kdl">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16v16H4Z M9 4v16 M4 9h5 M15 4v16 M15 9h5 M4 15h5 M15 15h5"/>
              </svg>
              <?php echo esc_html__('Thiết kế công năng', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-kdl__title"><?php echo esc_html__('Hệ thống bếp tối ưu & bền bỉ', 'saigonhoreca'); ?></h2>
            <div class="pp-text-kdl__divider" aria-hidden="true"></div>
          </header>

          <div class="pp-specs-kdl__features">
            <div class="pp-specs-kdl__feature">
              <div class="pp-specs-kdl__feature-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9M14 11H5m16 4h-9m6 4H8"/></svg>
              </div>
              <div class="pp-specs-kdl__feature-content">
                <h4><?php echo esc_html__('Layout bếp tối ưu', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Bố trí khoa học, quy trình làm việc rõ ràng, đảm bảo môi trường làm việc sạch sẽ và an toàn, giúp tiết kiệm thời gian và công sức cho nhân viên.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <div class="pp-specs-kdl__feature">
              <div class="pp-specs-kdl__feature-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c1.5 3-1.5 4 0 7M8 6c1 2-1 2.6 0 4.5M16 6c1 2-1 2.6 0 4.5M4 14h16l-1.5 6H5.5Z"/></svg>
              </div>
              <div class="pp-specs-kdl__feature-content">
                <h4><?php echo esc_html__('Thông gió & thoát sàn', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Hệ thống hút khói, khử mùi và cấp khí tươi giữ không gian luôn thông thoáng; hệ thống thoát sàn giữ sàn bếp luôn khô ráo, sạch sẽ.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <div class="pp-specs-kdl__feature">
              <div class="pp-specs-kdl__feature-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6Z M9 12l2 2 4-4"/></svg>
              </div>
              <div class="pp-specs-kdl__feature-content">
                <h4><?php echo esc_html__('Bảo trì định kỳ', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Sau 1 năm hoạt động bền bỉ, chương trình bảo dưỡng — bảo trì định kỳ nâng cao tuổi thọ thiết bị, đảm bảo hệ thống bếp luôn vận hành trơn tru.', 'saigonhoreca'); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
