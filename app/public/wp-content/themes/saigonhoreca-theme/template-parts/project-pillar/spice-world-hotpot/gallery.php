<?php
/**
 * Project Pillar â€” spice-world-hotpot
 * Section #6: gallery (storage & service)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-swh pp-swh-gallery scroll-reveal">
  <div class="pp-watermark-bg-swh" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M20 20 H80 M50 20 V80 M20 80 H80" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-swh pp-ambient-glow-swh--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-text-swh pp-text-swh--center">
      <span class="pp-text-swh__divider pp-text-swh__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-swh__title">
        <?php echo esc_html__('ThÆ° Viá»‡n Há»¬nh áº¢nh', 'saigonhoreca'); ?>
        <span class="pp-text-swh__title-gold"><?php echo esc_html__('Spice World Hotpot', 'saigonhoreca'); ?></span>
      </h2>
      <div class="pp-text-swh__body">
        <p><?php echo esc_html__('KhÃ¡m phÃ¡ khÃ´ng gian áº©m thá»±c Ä‘áº³ng cáº¥p vÃ  há»‡ thá»‘ng báº¿p cÃ´ng nghiá»‡p hiá»‡n Ä‘áº¡i táº¡i Spice World Hotpot.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <div class="pp-gallery-grid-swh">
      <div class="pp-gallery-item-swh pp-image-container-shared">
        <img src="<?php echo sgh_img('2024/02/spice-world-hotpot-SGH-4.jpeg'); ?>" alt="<?php echo esc_attr__('Spice World Gallery 1', 'saigonhoreca'); ?>" loading="lazy">
        <div class="pp-image-caption-shared"><?php echo esc_html__('KhÃ´ng gian nhÃ  hÃ ng sang trá»ng', 'saigonhoreca'); ?></div>
      </div>

      <div class="pp-gallery-item-swh pp-image-container-shared">
        <img src="<?php echo sgh_img('2024/02/spice-world-hotpot-SGH-5.jpeg'); ?>" alt="<?php echo esc_attr__('Spice World Gallery 2', 'saigonhoreca'); ?>" loading="lazy">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Khu vá»±c báº¿p chÃ­nh', 'saigonhoreca'); ?></div>
      </div>

      <div class="pp-gallery-item-swh pp-image-container-shared">
        <img src="<?php echo sgh_img('2024/02/spice-world-hotpot-SGH-6.jpeg'); ?>" alt="<?php echo esc_attr__('Spice World Gallery 3', 'saigonhoreca'); ?>" loading="lazy">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Thiáº¿t bá»‹ báº¿p cÃ´ng nghiá»‡p', 'saigonhoreca'); ?></div>
      </div>

      <div class="pp-gallery-item-swh pp-image-container-shared">
        <img src="<?php echo sgh_img('2024/02/spice-world-hotpot-SGH-7.jpeg'); ?>" alt="<?php echo esc_attr__('Spice World Gallery 4', 'saigonhoreca'); ?>" loading="lazy">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Há»‡ thá»‘ng hÃºt khÃ³i hiá»‡n Ä‘áº¡i', 'saigonhoreca'); ?></div>
      </div>

      <div class="pp-gallery-item-swh pp-image-container-shared">
        <img src="<?php echo sgh_img('2024/02/spice-world-hotpot-SGH-8.jpeg'); ?>" alt="<?php echo esc_attr__('Spice World Gallery 5', 'saigonhoreca'); ?>" loading="lazy">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Khu vá»±c sÆ¡ cháº¿ nguyÃªn liá»‡u', 'saigonhoreca'); ?></div>
      </div>

      <div class="pp-gallery-item-swh pp-image-container-shared">
        <img src="<?php echo sgh_img('2024/02/spice-world-hotpot-SGH-9.jpeg'); ?>" alt="<?php echo esc_attr__('Spice World Gallery 6', 'saigonhoreca'); ?>" loading="lazy">
        <div class="pp-image-caption-shared"><?php echo esc_html__('TrÃ¬nh diá»…n áº©m thá»±c', 'saigonhoreca'); ?></div>
      </div>
    </div>
  </div>
</section>

<?php /* T-034: merged from related.php (cÅ© section 7) */ ?>
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
              <?php echo esc_html__('HoÃ n thiá»‡n bÃ n giao', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-swh__title" style="font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 1rem;">
              <?php echo esc_html__('Cam káº¿t cháº¥t lÆ°á»£ng vá»¯ng bá»n', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-swh__divider" aria-hidden="true"></div>
          </header>

          <div class="swh-related__body" style="font-size: 0.98rem; line-height: 1.8; color: var(--bc2);">
            <p><?php echo esc_html__('Äá»ƒ táº¡o ra nhá»¯ng lÃ¡t thá»‹t cá»«u máº£nh vÃ  nhá»¯ng lÃ¡t thá»‹t bÃ² Ä‘Æ°á»£c cuá»™n Ä‘áº¹p máº¯t, khu vá»±c cáº¯t thá»‹t Ä‘Æ°á»£c trang bá»‹ má»™t mÃ¡y cáº¯t thá»‹t thÆ°Æ¡ng hiá»‡u Berjayja vÃ  má»™t tá»§ Ä‘Ã´ng Hoshizaki, Ä‘áº£m báº£o sá»± tÆ°Æ¡i má»›i cá»§a cÃ¡c miáº¿ng thá»‹t Ä‘Æ°á»£c chá»n lá»±a.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('ÄÆ°á»£c sá»± tin tÆ°á»Ÿng cá»§a Spice World Hot Pot Viá»‡t Nam, Saigon Horeca Ä‘Ã£ ná»— lá»±c háº¿t mÃ¬nh Ä‘á»ƒ hiá»‡n thá»±c hÃ³a tá»«ng chi tiáº¿t nhá» nháº¥t. Sá»± hÃ i lÃ²ng tuyá»‡t Ä‘á»‘i cá»§a Ä‘á»‘i tÃ¡c chÃ­nh lÃ  minh chá»©ng rÃµ nÃ©t nháº¥t cho hÃ nh trÃ¬nh 7 nÄƒm xÃ¢y dá»±ng vÃ  phÃ¡t triá»ƒn uy tÃ­n cá»§a chÃºng tÃ´i.', 'saigonhoreca'); ?></p>
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
          <img src="<?php echo sgh_img('2024/02/Spice-World-Hot-Pot-07.jpg'); ?>" alt="<?php echo esc_attr__('Thiáº¿t bá»‹ báº¿p cÃ´ng nghiá»‡p Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-swh"><?php echo esc_html__('Láº¯p Ä‘áº·t hoÃ n thiá»‡n há»‡ thá»‘ng thiáº¿t bá»‹ báº¿p cháº¥t lÆ°á»£ng cao', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>

