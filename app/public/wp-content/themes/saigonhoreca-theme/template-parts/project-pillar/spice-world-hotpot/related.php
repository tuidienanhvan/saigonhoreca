<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #7: related (execution summary)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-swh-related">
  <div class="pp-watermark-bg-swh" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M25 20 L37 80 L49 20" stroke-linecap="round"/>
      <path d="M59 20 H79 M69 20 V80 M59 80 H79" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-swh pp-ambient-glow-swh--bottom-left" aria-hidden="true"></div>

  <div class="pp__container">
    <div class="pp-grid-12-swh">
      
      <div class="pp-grid-12-swh__text--cols-5 swh-related__main">
        <div class="pp-glass-card-swh swh-related__glass-card">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          
          <header class="swh-related__header">
            <div class="pp-badge-swh">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Hoàn thiện bàn giao', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-swh__title" style="font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 1rem;">
              <?php echo esc_html__('Cam kết chất lượng vững bền', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-swh__divider" aria-hidden="true"></div>
          </header>

          <div class="swh-related__body" style="font-size: 0.98rem; line-height: 1.8; color: var(--bc2);">
            <p><?php echo esc_html__('Để tạo ra những lát thịt cừu mảnh và những lát thịt bò được cuộn đẹp mắt, khu vực cắt thịt được trang bị một máy cắt thịt thương hiệu Berjayja và một tủ đông Hoshizaki, đảm bảo sự tươi mới của các miếng thịt được chọn lựa.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Được sự tin tưởng của Spice World Hot Pot Việt Nam, Saigon Horeca đã nỗ lực hết mình để hiện thực hóa từng chi tiết nhỏ nhất. Sự hài lòng tuyệt đối của đối tác chính là minh chứng rõ nét nhất cho hành trình 7 năm xây dựng và phát triển uy tín của chúng tôi.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

      <div class="pp-grid-12-swh__media--cols-7 swh-related__side">
        <div class="pp-image-container-swh swh-related__image-container">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <div class="pp-image-tag-swh">EXECUTION</div>
          <img src="<?php echo sgh_img('2024/02/Spice-World-Hot-Pot-07.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-swh"><?php echo esc_html__('Lắp đặt hoàn thiện hệ thống thiết bị bếp chất lượng cao', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
