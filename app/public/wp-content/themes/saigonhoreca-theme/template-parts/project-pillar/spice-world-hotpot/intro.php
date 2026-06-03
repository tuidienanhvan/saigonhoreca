<?php
/**
 * Project Pillar â€” spice-world-hotpot
 * Section #2: intro
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-swh pp-swh-intro scroll-reveal">
  <div class="pp-watermark-bg-swh" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M40 20 H60 M50 20 V80 M40 80 H60" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-swh pp-ambient-glow-swh--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-12-swh">
      
      <div class="pp-grid-12-swh__text--cols-5 swh-intro__main">
        <header class="swh-intro__header">
          <div class="pp-badge-swh">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
            </svg>
            <?php echo esc_html__('ThÆ°Æ¡ng hiá»‡u quá»‘c táº¿', 'saigonhoreca'); ?>
          </div>
          <h2 class="pp-text-swh__title">
            SPICE WORLD HOTPOT
            <span class="pp-text-swh__title-gold"><?php echo esc_html__('áº¨m Thá»±c Láº©u Tá»© XuyÃªn ÄÃ­ch Thá»±c', 'saigonhoreca'); ?></span>
          </h2>
          <div class="pp-text-swh__divider" aria-hidden="true"></div>
          
          <address class="swh-intro__address">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <?php echo esc_html__('1061-1063-1065 Tráº§n HÆ°ng Äáº¡o, PhÆ°á»ng 5, Quáº­n 5, TPHCM', 'saigonhoreca'); ?>
          </address>
        </header>

        <div class="swh-intro__content">
          <div class="pp-glass-card-swh swh-intro__quote-box">
            <p class="swh-intro__lead"><?php echo esc_html__('Spice World Hot Pot lÃ  má»™t thÆ°Æ¡ng hiá»‡u láº©u ná»•i tiáº¿ng cÃ³ nguá»“n gá»‘c tá»« Sichuan, má»™t vÃ¹ng Ä‘áº¥t vá»›i truyá»n thá»‘ng áº©m thá»±c lÃ¢u Ä‘á»i táº¡i Trung Quá»‘c. ÄÆ°á»£c thÃ nh láº­p vÃ o nÄƒm 2003, Spice World Hot Pot lÃ  má»™t trong 10 thÆ°Æ¡ng hiá»‡u láº©u ná»•i tiáº¿ng nháº¥t Ä‘Æ°á»£c biáº¿t Ä‘áº¿n vá»›i hÆ°Æ¡ng vá»‹ Ä‘áº·c biá»‡t cá»§a nÆ°á»›c láº©u vÃ  cÃ¡c mÃ³n Äƒn kÃ¨m. Hiá»‡n táº¡i, thÆ°Æ¡ng hiá»‡u nÃ y cÃ³ máº·t táº¡i 10 quá»‘c gia trÃªn tháº¿ giá»›i, bao gá»“m Trung Quá»‘c, Hoa Ká»³, Singapore vÃ  Viá»‡t Nam.', 'saigonhoreca'); ?></p>
          </div>
          
          <div class="swh-intro__flags-wrapper">
            <h4 class="swh-intro__flags-title"><?php echo esc_html__('Máº NG LÆ¯á»šI QUá»C Táº¾ / INTERNATIONAL BRANCHES', 'saigonhoreca'); ?></h4>
            <div class="swh-intro__flags-list">
              <div class="swh-intro__flag-item">
                <div class="swh-intro__flag-img-box">
                  <img src="<?php echo sgh_img('2021/09/co-singapore.png'); ?>" alt="Singapore Flag" loading="lazy">
                </div>
                <span class="swh-intro__flag-name">Singapore</span>
              </div>
              <div class="swh-intro__flag-item">
                <div class="swh-intro__flag-img-box">
                  <img src="<?php echo sgh_img('2021/09/co-myanmar.png'); ?>" alt="Myanmar Flag" loading="lazy">
                </div>
                <span class="swh-intro__flag-name">Myanmar</span>
              </div>
              <div class="swh-intro__flag-item">
                <div class="swh-intro__flag-img-box">
                  <img src="<?php echo home_url('/wp-content/uploads/2021/09/co-thai-lan.png'); ?>" alt="Thailand Flag" loading="lazy">
                </div>
                <span class="swh-intro__flag-name"><?php echo esc_html__('ThÃ¡i Lan', 'saigonhoreca'); ?></span>
              </div>
              <div class="swh-intro__flag-item">
                <div class="swh-intro__flag-img-box">
                  <img src="<?php echo sgh_img('2021/09/co-trung-quoc.png'); ?>" alt="China Flag" loading="lazy">
                </div>
                <span class="swh-intro__flag-name"><?php echo esc_html__('Trung Quá»‘c', 'saigonhoreca'); ?></span>
              </div>
              <div class="swh-intro__flag-item">
                <div class="swh-intro__flag-img-box">
                  <img src="<?php echo sgh_img('2021/09/co-my-e1632216750335.png'); ?>" alt="United States Flag" loading="lazy">
                </div>
                <span class="swh-intro__flag-name"><?php echo esc_html__('Hoa Ká»³', 'saigonhoreca'); ?></span>
              </div>
              <div class="swh-intro__flag-item">
                <div class="swh-intro__flag-img-box">
                  <img src="<?php echo sgh_img('2021/09/co-viet-nam.png'); ?>" alt="Vietnam Flag" loading="lazy">
                </div>
                <span class="swh-intro__flag-name"><?php echo esc_html__('Viá»‡t Nam', 'saigonhoreca'); ?></span>
              </div>
            </div>
          </div>
          
          <div class="swh-intro__description">
            <p><?php echo esc_html__('Sá»± Ä‘á»™c Ä‘Ã¡o cá»§a nÆ°á»›c láº©u Ä‘áº¿n tá»« cÃ¡c nguyÃªn liá»‡u tá»± nhiÃªn Ä‘Æ°á»£c lá»±a chá»n ká»¹ cÃ ng tá»« nÆ¡i xuáº¥t xá»© cá»§a nÃ³, nhÆ° tiÃªu rá»«ng Ä‘Æ°á»£c chá»n lá»c Ä‘áº·c biá»‡t tá»« Hongya, Sichuan, vÃ  á»›t tá»« khu vá»±c nÃºi Quy ChÃ¢u vÃ  Sichuan, cÅ©ng nhÆ° cÃ¡c loáº¡i náº¥m khÃ¡c nhau. Táº¥t cáº£ cÃ¡c nguyÃªn liá»‡u Ä‘Æ°á»£c ninh trong khoáº£ng 4 Ä‘áº¿n 6 giá» Ä‘á»ƒ táº¡o ra hÆ°Æ¡ng vá»‹ Ä‘áº·c trÆ°ng cá»§a nÆ°á»›c láº©u Sichuan Mala trong hÃ¬nh dÃ¡ng cá»§a nhá»¯ng chÃº gáº¥u Teddy dá»… thÆ°Æ¡ng vÃ  mÃ¨o Hello Kitty.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Äá»‰nh cao áº©m thá»±c khÃ´ng chá»‰ Ä‘áº¿n tá»« hÆ°Æ¡ng vá»‹ mÃ  cÃ²n tá»« tráº£i nghiá»‡m trá»±c quan, khi Spice World thÃªm má»™t yáº¿u tá»‘ vui nhá»™n báº±ng cÃ¡ch káº¿t há»£p cÃ¡c nhÃ¢n váº­t Ä‘á»“ chÆ¡i tan cháº£y vÃ o láº©u, lÃ m vui máº¯t cho thá»±c khÃ¡ch trong khi dáº¡ dÃ y cá»§a há» mong muá»‘n thÃªm nhiá»u hÆ¡n.', 'saigonhoreca'); ?></p>
          </div>
          
          <div class="swh-intro__extra-description">
            <p><?php echo esc_html__('Táº¡i Spice World, cÃ¡c lÃ¡t thá»‹t bÃ² Wagyu tÆ°Æ¡i ngon Ä‘Æ°á»£c sáº¯p xáº¿p má»™t cÃ¡ch tinh táº¿ trong bá»™ vÃ¡y cá»§a bÃºp bÃª Barbie, káº¿t há»£p vá»›i khÃ´ng gian thiáº¿t káº¿ "Old World" Ä‘áº§y hoÃ i niá»‡m vá»›i nhá»¯ng bá»©c tÆ°á»ng Ä‘á» vÃ  tranh váº½ dÃ£ ngoáº¡i rá»«ng Sichuan.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

      <div class="pp-grid-12-swh__media--cols-7 swh-intro__side">
        <div class="pp-image-container-shared swh-intro__image-container">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <div class="pp-image-tag-swh">SICHUAN INTERIOR</div>
          <img src="<?php echo sgh_img('2024/02/spice-world-hotpot-SGH-1.jpeg'); ?>" alt="<?php echo esc_attr__('Spice World Interior', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-shared"><?php echo esc_html__('KhÃ´ng gian ná»™i tháº¥t Sichuan áº¥m Ã¡p káº¿t há»£p thÃ©p Ä‘en hiá»‡n Ä‘áº¡i', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>

