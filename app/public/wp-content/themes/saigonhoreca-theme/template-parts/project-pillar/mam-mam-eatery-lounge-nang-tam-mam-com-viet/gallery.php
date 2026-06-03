<?php
/**
 * Project Pillar — mam-mam-eatery-lounge-nang-tam-mam-com-viet
 * Section #6: gallery slot — closing statement ("lời kết") block.
 * Per project-pillar-standard §6, the closing philosophy is merged into the
 * gallery slot rather than a separate related/cta file.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-gallery-section-mam">
  <div class="pp-gallery-section-mam__tray" aria-hidden="true"></div>
  <div class="pp-container-shared">
    <div class="pp-gallery-section-mam__closing scroll-reveal">
      <span class="pp-gallery-section-mam__mark" aria-hidden="true">“ ”</span>
      <blockquote class="pp-gallery-section-mam__quote">
        <?php echo esc_html__('Đưa ẩm thực Việt ra thế giới – bắt đầu từ một mâm cơm, một gian bếp, và cả tấm lòng của người làm bếp.', 'saigonhoreca'); ?>
      </blockquote>
      <p class="pp-gallery-section-mam__sign"><?php echo esc_html__('Mâm Mâm Eatery & Lounge × Saigon Horeca', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>
