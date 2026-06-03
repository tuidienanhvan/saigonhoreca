<?php
/**
 * Project Pillar — sol-kitchen-bar
 * Section #4: specs — technical collage + extraction-system feature list.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-sol pp-specs-sol scroll-reveal">
  <div class="pp-sol-ambient pp-sol-ambient--tr" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-specs-sol__layout">
      <div class="pp-specs-sol__collage scroll-reveal" aria-hidden="false">
        <span class="pp-specs-sol__coord pp-specs-sol__coord--tl">SYS_COORD 08M</span>
        <span class="pp-specs-sol__coord pp-specs-sol__coord--br">CFM / FRESH-AIR</span>
        <div class="pp-image-container-shared pp-specs-sol__img pp-specs-sol__img--base">
          <img src="<?php echo sgh_img('sol-kitchen-bar/chup-hut-8m-1-1.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống chụp hút khói 8m khu vực nấu Sol Kitchen & Bar', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống chụp hút 8m hút khói và cấp gió tươi song song trong khu vực nấu.', 'saigonhoreca'); ?></div>
        </div>
        <div class="pp-image-container-shared pp-specs-sol__img pp-specs-sol__img--over">
          <img src="<?php echo sgh_img('sol-kitchen-bar/chup-hut-2m-1.jpg'); ?>" alt="<?php echo esc_attr__('Chụp hút khói 2m khu vực rửa Sol Kitchen & Bar', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Chụp hút 2m khu vực rửa.', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <div class="pp-specs-sol__panel">
        <span class="pp-text-sol__eyebrow"><?php echo esc_html__('Hệ thống hút khói & cấp gió', 'saigonhoreca'); ?></span>
        <span class="pp-text-sol__divider" aria-hidden="true"></span>
        <h2 class="pp-text-sol__title"><?php echo esc_html__('Luồng khí trong lành cho khu bếp', 'saigonhoreca'); ?></h2>
        <div class="pp-text-sol__body">
          <p><?php echo esc_html__('Hệ thống chụp hút khói dài 8m được thiết kế để hút khói và cấp gió tươi song song trong khu vực nấu. Hệ thống này giúp luồng khí lưu thông liên tục, tạo không gian thoải mái cho các đầu bếp làm việc và hạn chế tình trạng bí khí, đảm bảo môi trường làm việc trong lành và thoáng đãng. Trong khu vực rửa, một hệ thống chụp hút khói dài 2m cũng được lắp đặt để hút khói và cấp gió tươi, đảm bảo khu vực này luôn sạch sẽ và không bị ẩm mốc.', 'saigonhoreca'); ?></p>
        </div>
        <ul class="pp-specs-sol__list">
          <li>
            <svg class="pp-specs-sol__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 12h4l3-8 4 16 3-8h4"/></svg>
            <span><?php echo esc_html__('Chụp hút khu nấu — dài 8m', 'saigonhoreca'); ?></span>
          </li>
          <li>
            <svg class="pp-specs-sol__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 3v6m0 0 3-3m-3 3-3-3M5 21h14a2 2 0 0 0 2-2v-5H3v5a2 2 0 0 0 2 2Z"/></svg>
            <span><?php echo esc_html__('Chụp hút khu rửa — dài 2m', 'saigonhoreca'); ?></span>
          </li>
          <li>
            <svg class="pp-specs-sol__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 12a8 8 0 1 1 16 0 8 8 0 0 1-16 0Z"/><path d="M12 8v4l3 2"/></svg>
            <span><?php echo esc_html__('Hút khói & cấp gió tươi song song', 'saigonhoreca'); ?></span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>
