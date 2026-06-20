<?php
/**
 * Project Pillar — moa-moa (Moa Moa Pasta Club)
 * Section #2: intro — editorial asymmetric layout with dropcap + 3D image showcase.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section id="moa-intro" class="pp-section-moa pp-moa-intro">
  <!-- Họa tiết sợi pasta / lúa mì uốn lượn chìm nghệ thuật -->
  <div class="pp-watermark-wheat-moa" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.2">
      <path d="M50 95 V35" stroke-linecap="round"/>
      <path d="M50 40 C40 36 36 28 38 20 C46 22 52 28 50 40Z M50 40 C60 36 64 28 62 20 C54 22 48 28 50 40Z" stroke-linejoin="round"/>
      <path d="M50 55 C41 51 37 44 39 36 M50 55 C59 51 63 44 61 36" stroke-linecap="round"/>
      <path d="M50 70 C41 66 37 59 39 51 M50 70 C59 66 63 59 61 51" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-moa pp-ambient-glow-moa--top-right" aria-hidden="true"></div>
  <div class="pp-ambient-glow-moa pp-ambient-glow-moa--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-moa">

      <div class="pp-grid-moa__text moa-intro__main">
        <header class="moa-intro__header">
          <div class="pp-badge-moa">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
            </svg>
            Moa Moa Pasta Club
          </div>
          <h2 class="pp-text-moa__title">
            <?php echo esc_html__('Thiết kế layout: Tối ưu vận hành cho pasta thủ công', 'saigonhoreca'); ?>
          </h2>
          <div class="pp-text-moa__divider" aria-hidden="true"></div>
        </header>

        <div class="moa-intro__body">
          <p><?php echo esc_html__('Giữa trung tâm Quận 1 sôi động, nơi nhịp sống thành phố chưa bao giờ chậm lại, Moa Moa Pasta Club xuất hiện như một khoảng dừng tinh tế dành cho những ai trân trọng ẩm thực Ý nguyên bản. Tọa lạc tại 40E Ngô Đức Kế, phường Bến Nghé, chỉ cách phố đi bộ Nguyễn Huệ vài bước chân, Moa Moa không chỉ là một nhà hàng pasta – mà là nơi thủ công, kỹ thuật và văn hoá ẩm thực giao thoa trong từng chi tiết.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Đồng hành cùng dự án này, Saigon Horeca không tiếp cận Moa Moa như một công trình bếp thông thường, mà như một bài toán không gian – vận hành – trải nghiệm, nơi mỗi quyết định thiết kế đều phải phục vụ cho tinh thần Italiano autentico.', 'saigonhoreca'); ?></p>
          <p style="margin-bottom: 0;"><?php echo esc_html__('Ẩm thực Ý, đặc biệt là pasta thủ công, luôn đặt quy trình chế biến vào vị trí trung tâm. Tại Moa Moa, bếp không bị giấu sau những bức tường kín, mà trở thành trái tim của nhà hàng – nơi thực khách có thể cảm nhận nhịp điệu làm bếp, từ trộn bột, cán mì, luộc pasta cho đến hoàn thiện món ăn.', 'saigonhoreca'); ?></p>
        </div>
      </div>

      <div class="pp-grid-moa__media moa-intro__side">
        <div class="pp-image-container-shared moa-intro__image-container">
          <img src="<?php echo sgh_img('moa-moa/moa-moa-bien-hieu-chao-mung-pasta-club-loi-vao.webp'); ?>" alt="<?php echo esc_attr__('Lối vào Moa Moa Pasta Club với biển hiệu thương hiệu', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('“Welcome to the Pasta Club” – dấu ấn thương hiệu Moa Moa ngay lối vào', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
