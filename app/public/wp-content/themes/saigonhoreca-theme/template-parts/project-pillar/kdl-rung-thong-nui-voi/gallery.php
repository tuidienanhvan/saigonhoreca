<?php
/**
 * Project Pillar — kdl-rung-thong-nui-voi
 * Section #6: gallery — GOLD editorial UNIFORM GRID
 * (every tile 4/3 · captions render BELOW each frame · zero empty gaps)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

/* Uniform editorial grid: every tile shares the same 4/3 aspect-ratio so the
   tiles tile cleanly with zero empty voids (no per-tile size modifiers). */
$images = [
  ['src' => 'du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca/kdl-rung-thong-nui-voi-mam-khai-vi-pho-mai-thit-nguoi.webp',                 'alt' => __('Mâm khai vị thịt nguội và rau tươi bày trên bàn gỗ', 'saigonhoreca'),         'caption' => __('Đặc sản bày trên bàn gỗ mộc bên ly vang đỏ', 'saigonhoreca')],
  ['src' => 'du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca/kdl-rung-thong-nui-voi-mam-mon-an-nhin-tu-tren-xuong.webp',                 'alt' => __('Các món ăn bày biện trên bàn gỗ', 'saigonhoreca'),          'caption' => __('Mâm món ăn bày biện tinh tế nhìn từ trên xuống', 'saigonhoreca')],
  ['src' => 'du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca/kdl-rung-thong-nui-voi-kien-truc-nha-hang-mai-doc.webp',                 'alt' => __('Kiến trúc nhà hàng mái dốc giữa rừng thông', 'saigonhoreca'),                'caption' => __('Kiến trúc nhà hàng mái dốc giữa rừng thông', 'saigonhoreca')],
];
?>
<section class="pp-section-kdl pp-gallery-section-kdl">
  <div class="pp-ambient-glow-kdl pp-ambient-glow-kdl--top-right" aria-hidden="true"></div>
  <div class="pp-ambient-glow-kdl pp-ambient-glow-kdl--bottom-left" aria-hidden="true"></div>

  <!-- Số chìm kỹ thuật làm watermark biên tập -->
  <div class="pp-gallery-watermark-kdl" aria-hidden="true">06</div>

  <div class="pp-container-shared">
    <header class="pp-gallery-section-kdl__header">
      <div class="pp-badge-kdl">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/>
        </svg>
        <?php echo esc_html__('Thư viện dự án', 'saigonhoreca'); ?>
      </div>
      <h2 class="pp-text-kdl__title"><?php echo esc_html__('Khu du lịch giữa rừng thông Núi Voi', 'saigonhoreca'); ?></h2>
      <div class="pp-text-kdl__divider pp-text-kdl__divider--center" aria-hidden="true"></div>
      <p class="pp-gallery-section-kdl__lead"><?php echo esc_html__('Saigon Horeca hân hạnh góp phần vào thành công của KDL Rừng Thông Núi Voi — từ không gian ẩm thực giữa thiên nhiên đến hệ thống bếp vận hành bền bỉ.', 'saigonhoreca'); ?></p>
    </header>

    <div class="pp-gallery-mosaic-kdl" aria-label="<?php echo esc_attr__('Thư viện ảnh KDL Rừng Thông Núi Voi', 'saigonhoreca'); ?>">
      <?php foreach ($images as $img): ?>
        <div class="pp-image-container-shared pp-gallery-mosaic-kdl__item">
          <span class="pp-gallery-mosaic-kdl__frame" aria-hidden="true"></span>
          <img src="<?php echo sgh_img($img['src']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html($img['caption']); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
