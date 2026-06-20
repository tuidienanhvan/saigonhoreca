<?php
/**
 * Project Pillar — du-nam-an-an
 * Section #6: gallery — GOLD editorial asymmetric mosaic of the realised kitchen.
 * Uses real Nam An project photos (sourced from saigonhoreca.vn). @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

/* Uniform gallery grid: every tile equal 4/3 — clean tiling, zero voids. */
$nan_gallery = [
  [
    'src'     => 'du-nam-an-an/nam-an-mat-ngoai-toa-nha-anh-bia-du-an.webp',
    'alt'     => __('Mặt ngoài tòa nhà cơ sở Nam An tại Quận 7 nơi Saigon Horeca thi công bếp công nghiệp inox', 'saigonhoreca'),
    'caption' => __('Toàn cảnh cơ sở Nam An – Quận 7, TP.HCM', 'saigonhoreca'),
  ],
  [
    'src'     => 'du-nam-an-an/nam-an-tu-dong-mat-dung-hoshizaki-inox.webp',
    'alt'     => __('Cặp tủ đông và tủ mát công nghiệp inox Hoshizaki tại bếp Nam An', 'saigonhoreca'),
    'caption' => __('Tủ đông – tủ mát công nghiệp Hoshizaki', 'saigonhoreca'),
  ],
  [
    'src'     => 'du-nam-an-an/nam-an-ke-inox-de-ly-coc-chen-bat.webp',
    'alt'     => __('Kệ inox sơ chế đạt chuẩn vệ sinh, xếp úp ly cốc khô ráo', 'saigonhoreca'),
    'caption' => __('Kệ inox sơ chế đạt chuẩn vệ sinh, xếp úp ly cốc khô ráo', 'saigonhoreca'),
  ],
  [
    'src'     => 'du-nam-an-an/nam-an-khu-rua-bon-voi-xit-may-rua-chen.webp',
    'alt'     => __('Khu rửa – máy rửa chén công nghiệp, một chiều khép kín', 'saigonhoreca'),
    'caption' => __('Khu rửa – máy rửa chén công nghiệp, một chiều khép kín', 'saigonhoreca'),
  ],
];
?>
<section class="pp-section-nan pp-gallery-section-nan scroll-reveal">
  <div class="pp-ambient-glow-nan pp-ambient-glow-nan--bottom-left" aria-hidden="true"></div>
  <div class="pp-ambient-glow-nan pp-ambient-glow-nan--top-right" aria-hidden="true"></div>

  <!-- Số chìm kỹ thuật 06 (faint numeric watermark) -->
  <div class="pp-gallery-nan__watermark" aria-hidden="true">06</div>

  <div class="pp-container-shared">
    <header class="pp-gallery-section-nan__head">
      <span class="pp-badge-nan">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
          <rect x="3" y="4" width="18" height="16" rx="2"/><path d="m3 16 5-5 4 4 3-3 6 6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="9" r="1.4"/>
        </svg>
        <?php echo esc_html__('Thư viện dự án', 'saigonhoreca'); ?>
      </span>
      <h2 class="pp-text-nan__title pp-gallery-section-nan__title"><?php echo esc_html__('Gian bếp hoàn thiện – Nơi kỹ thuật gặp thấu cảm', 'saigonhoreca'); ?></h2>
      <span class="pp-text-nan__divider pp-text-nan__divider--center" aria-hidden="true"></span>
      <p class="pp-gallery-section-nan__lead"><?php echo esc_html__('Dự án Nam An An là một trong những dự án đặc biệt nhất mà Saigon Horeca đã thực hiện – không phải vì quy mô, mà vì tính nhân văn và trọng trách xã hội đi kèm. Không gian bếp không chỉ được xây dựng để nấu, mà để chăm sóc.', 'saigonhoreca'); ?></p>
    </header>

    <div class="pp-gallery-nan" aria-label="<?php echo esc_attr__('Thư viện hình ảnh dự án Nam An An', 'saigonhoreca'); ?>">
      <?php foreach ($nan_gallery as $img): ?>
        <figure class="pp-gallery-nan__item">
          <div class="pp-image-container-shared pp-gallery-nan__frame">
            <img src="<?php echo esc_url(sgh_img($img['src'])); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html($img['caption']); ?></div>
          </div>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
