<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #4: PARTNERSHIP — Vertical Reveal Card Layout
 * Card trung tâm kiểu thiệp mời sang trọng, ảnh cinematic 21:9 phía trên + text dưới
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-partnership-card-trd">
  <div class="pp__container pp-container-shared">
    <div class="pp-partnership-card-trd__card">
      <!-- Cinematic widescreen image header -->
      <div class="pp-partnership-card-trd__visual">
        <figure class="pp-image-container-shared pp-partnership-card-trd__figure">
          <img src="<?php echo sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-ban-mat-ngan-keo-undercounter.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống bàn mát dưới quầy tích hợp ngăn kéo tiện dụng tại The Royal', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống bàn mát dưới quầy (under-counter refrigerator) tích hợp ngăn kéo tiện dụng giúp bảo quản nguyên liệu tươi ngon ngay tầm tay đầu bếp.', 'saigonhoreca'); ?></figcaption>
        </figure>
      </div>

      <!-- Inner text frame with double border -->
      <div class="pp-partnership-card-trd__inner">
        <span class="pp-partnership-card-trd__collab-label"><?php echo esc_html__('SAIGON HORECA × THE ROYAL', 'saigonhoreca'); ?></span>
        <span class="pp-text-trd__divider pp-text-trd__divider--center" aria-hidden="true"></span>
        <h2 class="pp-text-trd__title"><?php echo esc_html__('The Royal – Dự án bếp công nghiệp cao cấp do Saigon Horeca thực hiện', 'saigonhoreca'); ?></h2>
        <div class="pp-text-trd__body">
          <p><?php echo wp_kses_post(sprintf(esc_html__('Ngay từ cái nhìn đầu tiên, không gian bếp tại The Royal gây ấn tượng mạnh với sự %1$s – từ khu sơ chế, chế biến, nấu nướng đến khu ra món. Tất cả các thiết bị đều được Saigon Horeca sử dụng chất liệu inox cao cấp, mang lại sự %2$s và đảm bảo vệ sinh an toàn thực phẩm tuyệt đối.', 'saigonhoreca'), '<strong>' . esc_html__('bố trí khoa học, tối ưu hóa quy trình vận hành một chiều', 'saigonhoreca') . '</strong>', '<strong>' . esc_html__('bền bỉ, dễ dàng vệ sinh', 'saigonhoreca') . '</strong>')); ?></p>
        </div>
      </div>

      <!-- 4 L-bracket corners -->
      <span class="pp-partnership-card-trd__corner pp-partnership-card-trd__corner--tl" aria-hidden="true"></span>
      <span class="pp-partnership-card-trd__corner pp-partnership-card-trd__corner--tr" aria-hidden="true"></span>
      <span class="pp-partnership-card-trd__corner pp-partnership-card-trd__corner--bl" aria-hidden="true"></span>
      <span class="pp-partnership-card-trd__corner pp-partnership-card-trd__corner--br" aria-hidden="true"></span>
    </div>
  </div>
</section>
