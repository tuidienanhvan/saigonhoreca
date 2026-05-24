<?php
/**
 * Project Pillar — roka-fella-tinh-hoa-am-thuc-nhat-ban
 * Section #2: intro
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-rf-intro">
  <div class="pp-watermark-bg-rf" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M40 20 H60 M50 20 V80 M40 80 H60" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-rf pp-ambient-glow-rf--top-right" aria-hidden="true"></div>

  <div class="pp__container">
    <div class="pp-grid-12-rf">
      
      <div class="pp-grid-12-rf__text--cols-5 rkf-intro__main">
        <div class="pp-glass-card-roka rkf-intro__glass-card">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          
          <header class="rkf-intro__header">
            <div class="pp-badge-rf">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              Roka Fella
            </div>
            <h2 class="pp-text-rf__title" style="font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 1rem;">
              <?php echo esc_html__('Tinh hoa ẩm thực Nhật Bản', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-rf__divider" aria-hidden="true"></div>
          </header>

          <div class="rkf-intro__body" style="font-size: 0.98rem; line-height: 1.75; color: var(--bc2);">
            <p><?php echo esc_html__('Nhắc đến xứ sở Mặt Trời Mọc, ai cũng biết đến sự đa dạng của văn hóa Nhật Bản. Nếu nói về văn hóa ẩm thực Nhật Bản, Sushi chính là món ăn nổi tiếng nhất, được chế biến từ hải sản tươi ngon như tôm, cá, bào ngư, cùng cơm nắm được ướp giấm và muối.', 'saigonhoreca'); ?></p>
            <p style="margin-bottom: 0;"><?php echo esc_html__('Tọa lạc tại trung tâm Quận 1, Roka Fella được xem là điểm đến lý tưởng để thực khách trải nghiệm những món ăn nổi tiếng của xứ sở Mặt Trời Mọc.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

      <div class="pp-grid-12-rf__media--cols-7 rkf-intro__side">
        <div class="pp-image-container-rf rkf-intro__image-container">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <div class="pp-image-tag-rf">TOKYO MODERN</div>
          <img src="<?php echo sgh_img('2020/12/roka.jpg'); ?>" alt="<?php echo esc_attr__('Roka Fella Interior Design', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-rf"><?php echo esc_html__('Không gian kiến trúc Nhật Bản hiện đại, đẳng cấp tại trung tâm Quận 1', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
