<?php
/**
 * Project Pillar â€” spice-world-hotpot
 * Section #5: specs
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-swh pp-swh-specs scroll-reveal">
  <div class="pp-watermark-bg-swh" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M25 25 H75 M50 25 V75 M25 75 H75" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-swh pp-ambient-glow-swh--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-text-swh pp-text-swh--center">
      <span class="pp-text-swh__divider pp-text-swh__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-swh__title">
        <?php echo esc_html__('ThÃ´ng Sá»‘ Ká»¹ Thuáº­t', 'saigonhoreca'); ?>
        <span class="pp-text-swh__title-gold"><?php echo esc_html__('Há»‡ Thá»‘ng Báº¿p', 'saigonhoreca'); ?></span>
      </h2>
      <div class="pp-text-swh__body">
        <p><?php echo esc_html__('Há»‡ thá»‘ng báº¿p cÃ´ng nghiá»‡p táº¡i Spice World Hotpot Ä‘Æ°á»£c thiáº¿t káº¿ vá»›i cÃ¡c thÃ´ng sá»‘ ká»¹ thuáº­t cao cáº¥p, Ä‘Ã¡p á»©ng tiÃªu chuáº©n quá»‘c táº¿.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <div class="pp-specs-grid-swh">
      <div class="pp-spec-item-swh">
        <div class="pp-spec-icon-swh">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <path d="M9 9h6v6H9z"/>
          </svg>
        </div>
        <h3 class="pp-spec-title-swh"><?php echo esc_html__('Diá»‡n TÃ­ch Báº¿p', 'saigonhoreca'); ?></h3>
        <p class="pp-spec-value-swh">150mÂ²</p>
        <p class="pp-spec-desc-swh"><?php echo esc_html__('KhÃ´ng gian báº¿p rá»™ng rÃ£i, chia thÃ nh 4 khu vá»±c chÃ­nh', 'saigonhoreca'); ?></p>
      </div>

      <div class="pp-spec-item-swh">
        <div class="pp-spec-icon-swh">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2v20M2 12h20"/>
          </svg>
        </div>
        <h3 class="pp-spec-title-swh"><?php echo esc_html__('CÃ´ng Suáº¥t', 'saigonhoreca'); ?></h3>
        <p class="pp-spec-value-swh">500+ suáº¥t/ngÃ y</p>
        <p class="pp-spec-desc-swh"><?php echo esc_html__('Kháº£ nÄƒng phá»¥c vá»¥ hÃ ng trÄƒm thá»±c khÃ¡ch má»—i ngÃ y', 'saigonhoreca'); ?></p>
      </div>

      <div class="pp-spec-item-swh">
        <div class="pp-spec-icon-swh">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 6v6l4 2"/>
          </svg>
        </div>
        <h3 class="pp-spec-title-swh"><?php echo esc_html__('Thá»i Gian Thi CÃ´ng', 'saigonhoreca'); ?></h3>
        <p class="pp-spec-value-swh">45 ngÃ y</p>
        <p class="pp-spec-desc-swh"><?php echo esc_html__('HoÃ n thÃ nh trong vÃ²ng 45 ngÃ y ká»ƒ tá»« khá»Ÿi cÃ´ng', 'saigonhoreca'); ?></p>
      </div>

      <div class="pp-spec-item-swh">
        <div class="pp-spec-icon-swh">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 12l2 2 4-4"/>
            <circle cx="12" cy="12" r="10"/>
          </svg>
        </div>
        <h3 class="pp-spec-title-swh"><?php echo esc_html__('TiÃªu Chuáº©n', 'saigonhoreca'); ?></h3>
        <p class="pp-spec-value-swh">ISO 22000</p>
        <p class="pp-spec-desc-swh"><?php echo esc_html__('ÄÃ¡p á»©ng tiÃªu chuáº©n an toÃ n thá»±c pháº©m quá»‘c táº¿', 'saigonhoreca'); ?></p>
      </div>
    </div>
  </div>
</section>

