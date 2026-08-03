<?php
/**
 * Project Pillar — renovate-sol-kitchen-bar-d7
 * Section #2: intro — "Michelin Legacy" editorial redesign.
 * Hand-drawn SVG illustrations: award medallion (laurel + star), star accents,
 * ornamental flourish divider, oversized quote mark, star-rating strip.
 * New scoped classes (.pp-rsq7-intro__*) — shared scaffolding left intact.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-solq7 pp-rsq7-intro">
  <div class="pp-solq7-ambient-glow pp-solq7-ambient-glow--tr" aria-hidden="true"></div>
  <div class="pp-solq7-ambient-glow pp-solq7-ambient-glow--bl pp-solq7-ambient-glow--ember" aria-hidden="true"></div>

  <!-- Giant faint star watermark -->
  <svg class="pp-rsq7-intro__wm" viewBox="0 0 100 100" aria-hidden="true">
  <path d="M50 4 L61 38 96 38 67 59 78 94 50 72 22 94 33 59 4 38 39 38 Z"/>
  </svg>

  <div class="pp-container-shared">

  <header class="pp-rsq7-intro__head">
  <span class="pp-rsq7-intro__eyebrow">
  <svg class="pp-rsq7-ic-star" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l2.9 6.3 6.9.8-5.1 4.7 1.4 6.8L12 17.8 5 21.4l1.4-6.8L1.3 9.8l6.9-.8L12 2z"/></svg>
  <?php echo esc_html__('BỐI CẢNH DỰ ÁN', 'saigonhoreca'); ?>
  <svg class="pp-rsq7-ic-star" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l2.9 6.3 6.9.8-5.1 4.7 1.4 6.8L12 17.8 5 21.4l1.4-6.8L1.3 9.8l6.9-.8L12 2z"/></svg>
  </span>

  <h2 class="pp-rsq7-intro__title">
  <?php echo esc_html__('Tiếp Nối Thành Công &', 'saigonhoreca'); ?>
  <em><?php echo esc_html__('Dấu Ấn Michelin Guide 2023', 'saigonhoreca'); ?></em>
  </h2>

  <svg class="pp-rsq7-intro__flourish" viewBox="0 0 240 18" aria-hidden="true" preserveAspectRatio="xMidYMid meet">
  <line x1="6" y1="9" x2="96" y2="9"/>
  <circle cx="104" cy="9" r="2.2"/>
  <path d="M120 1.5 L126 9 L120 16.5 L114 9 Z"/>
  <circle cx="136" cy="9" r="2.2"/>
  <line x1="144" y1="9" x2="234" y2="9"/>
  </svg>
  </header>

  <div class="pp-rsq7-intro__grid">

  <!-- LEFT — narrative -->
  <div class="pp-rsq7-intro__text">
  <blockquote class="pp-rsq7-intro__quote">
  <svg class="pp-rsq7-intro__qmark" viewBox="0 0 64 48" aria-hidden="true">
  <path d="M0 48V26C0 11 9 1 26 0l2 8C18 10 13 16 13 24h11v24H0Zm36 0V26C36 11 45 1 62 0l2 8C54 10 49 16 49 24h11v24H36Z"/>
  </svg>
  <span><?php echo esc_html__('Tiếp nối thành công của nhà hàng Sol Quận 1 — được vinh danh trong Michelin Guide 2023 — Saigon Horeca được tin tưởng triển khai hệ thống bếp cao cấp cho Sol Kitchen & Bar tại Quận 7.', 'saigonhoreca'); ?></span>
  </blockquote>

  <p class="pp-rsq7-intro__lead"><?php echo esc_html__('Saigon Horeca vinh hạnh được chủ đầu tư Sol Kitchen & Bar tin tưởng lựa chọn là đơn vị triển khai hệ thống bếp cao cấp cho nhà hàng tại Quận 7, TP. Hồ Chí Minh — kế thừa tinh thần và chuẩn mực vận hành đã làm nên tên tuổi của Sol Quận 1.', 'saigonhoreca'); ?></p>

  <ul class="pp-rsq7-intro__facts">
  <li><span class="pp-rsq7-intro__fact-k">2023</span><span class="pp-rsq7-intro__fact-v"><?php echo esc_html__('Michelin Guide Việt Nam', 'saigonhoreca'); ?></span></li>
  <li><span class="pp-rsq7-intro__fact-k">Q7</span><span class="pp-rsq7-intro__fact-v"><?php echo esc_html__('TP. Hồ Chí Minh', 'saigonhoreca'); ?></span></li>
  <li><span class="pp-rsq7-intro__fact-k">70m²</span><span class="pp-rsq7-intro__fact-v"><?php echo esc_html__('Khu bếp cao cấp', 'saigonhoreca'); ?></span></li>
  </ul>
  </div>

  <!-- RIGHT — image + award medallion -->
  <figure class="pp-rsq7-intro__media">
  <div class="pp-rsq7-intro__frame">
  <img src="<?php echo sgh_img('renovate-sol-kitchen-bar-quan-7/sol-kitchen-bar-q7-michelin-guide-mon-an-screenshot.webp'); ?>"
  alt="<?php echo esc_attr__('Trang Michelin Guide Việt Nam giới thiệu nhà hàng Sol Kitchen & Bar với các món ăn Latin: thăn bò nướng trên cơm nghệ, gỏi cá ceviche sốt vàng và góc quầy bar khắc chữ Sol', 'saigonhoreca'); ?>"
  loading="lazy" decoding="async">
  <div class="pp-rsq7-intro__frame-grad" aria-hidden="true"></div>

  <!-- Award medallion -->
  <div class="pp-rsq7-intro__medallion" role="img" aria-label="<?php echo esc_attr__('Michelin Guide Việt Nam 2023', 'saigonhoreca'); ?>">
  <svg viewBox="0 0 200 200" aria-hidden="true">
  <circle class="m-ring" cx="100" cy="100" r="92"/>
  <circle class="m-ring-dash" cx="100" cy="100" r="83"/>
  <!-- laurel left -->
  <g class="m-laurel">
  <path d="M73 158 C50 140 43 110 52 80"/>
  <path d="M58 96 C50 92 46 84 47 76"/>
  <path d="M55 112 C46 109 41 101 41 92"/>
  <path d="M55 130 C45 128 39 120 38 110"/>
  <path d="M59 146 C49 145 42 138 40 128"/>
  </g>
  <!-- laurel right (mirror) -->
  <g class="m-laurel" transform="translate(200,0) scale(-1,1)">
  <path d="M73 158 C50 140 43 110 52 80"/>
  <path d="M58 96 C50 92 46 84 47 76"/>
  <path d="M55 112 C46 109 41 101 41 92"/>
  <path d="M55 130 C45 128 39 120 38 110"/>
  <path d="M59 146 C49 145 42 138 40 128"/>
  </g>
  <path class="m-star" d="M100 60 l8.4 18.1 19.9 2.3 -14.7 13.5 4 19.6 -17.6-10.1 -17.6 10.1 4-19.6 -14.7-13.5 19.9-2.3z"/>
  <text class="m-top" x="100" y="150" text-anchor="middle">MICHELIN GUIDE</text>
  <text class="m-yr" x="100" y="170" text-anchor="middle">VIỆT NAM · 2023</text>
  </svg>
  </div>
  </div>

  <figcaption class="pp-rsq7-intro__cap">
  <svg class="pp-rsq7-ic-pin" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C8.1 2 5 5.1 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.9-3.1-7-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5Z"/></svg>
  <?php echo esc_html__('Sol Kitchen & Bar trên Michelin Guide 2023 — di sản ẩm thực Latin mà Sol Quận 7 tiếp nối.', 'saigonhoreca'); ?>
  </figcaption>
  </figure>

  </div>
  </div>
</section>
