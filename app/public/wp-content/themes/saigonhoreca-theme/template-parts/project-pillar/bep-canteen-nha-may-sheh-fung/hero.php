<?php
/**
 * Project Pillar — bep-canteen-nha-may-sheh-fung
 * Section #1: hero — Industrial Factory Canteen
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-shf">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (không JavaScript) -->
  <div class="pp-hero-shf__bg" style="background-image:url('<?php echo sgh_img('bep-canteen-nha-may-sheh-fung/bep-canteen-nha-may-sheh-fung-toan-canh-nha-may-tu-tren-cao.webp'); ?>');" aria-hidden="true"></div>
  <div class="pp-hero-shf__overlay" aria-hidden="true"></div>
  <div class="pp-hero-shf__stripe" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-hero-shf__content">
      <span class="pp-hero-shf__eyebrow">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
          <path d="M3 21h18M5 21V8l5-3 5 3v13M9 21v-4h2v4M14 12h3v3M14 12v9M17 12v9" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <?php echo esc_html__('Bếp Canteen Công Nghiệp', 'saigonhoreca'); ?>
      </span>

      <h1 class="pp-hero-shf__title">
        SHEH FUNG
        <span class="pp-hero-shf__title-sub"><?php echo esc_html__('Biểu tượng của sự hoàn hảo và tốc độ từ Saigon Horeca', 'saigonhoreca'); ?></span>
      </h1>

      <p class="pp-hero-shf__subtitle">
        <?php echo esc_html__('Dự án đẳng cấp với cam kết hoàn hảo của Saigon Horeca tại Canteen nhà máy lớn nhất nhì Bình Thuận Sheh Fung.', 'saigonhoreca'); ?>
      </p>

      <div class="pp-hero-shf__meta" aria-hidden="true">
        <span class="pp-hero-shf__meta-item"><span class="pp-hero-shf__meta-k">SECTOR</span><span class="pp-hero-shf__meta-v">Hàm Kiệm · Bình Thuận</span></span>
        <span class="pp-hero-shf__meta-item"><span class="pp-hero-shf__meta-k">SCOPE</span><span class="pp-hero-shf__meta-v">Canteen Central Kitchen</span></span>
        <span class="pp-hero-shf__meta-item"><span class="pp-hero-shf__meta-k">UNIT</span><span class="pp-hero-shf__meta-v">SAIGON HORECA</span></span>
      </div>
    </div>
  </div>

  <div class="pp-hero-shf__scroll" aria-hidden="true">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18">
      <path d="M12 5v14M5 12l7 7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
</section>
