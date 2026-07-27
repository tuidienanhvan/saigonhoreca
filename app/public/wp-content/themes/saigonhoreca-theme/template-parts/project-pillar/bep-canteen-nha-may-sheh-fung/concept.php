<?php
/**
 * Project Pillar — bep-canteen-nha-may-sheh-fung
 * Section #3: concept — design philosophy split (media left / spec card right)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-shf pp-concept-shf">
  <div class="pp-ambient-glow-shf pp-ambient-glow-shf--left" aria-hidden="true"></div>
  <div class="pp-section-num-shf" aria-hidden="true">01</div>

  <div class="pp-container-shared">
    <div class="pp-split-shf">

      <div class="pp-split-shf__media pp-image-container-shared">
        <div class="pp-split-shf__media-frame" aria-hidden="true"></div>
        <img src="<?php echo sgh_img('bep-canteen-nha-may-sheh-fung/bep-canteen-nha-may-sheh-fung-ke-inox-nhieu-tang-ban-soan-chau-rua.webp'); ?>" alt="<?php echo esc_attr__('Bếp canteen Sheh Fung thiết kế tối giản, hiện đại, khoa học', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <span class="pp-split-shf__media-tag" aria-hidden="true">SHF · DESIGN UNIT</span>
        <div class="pp-image-caption-shared"><?php echo esc_html__('Bếp canteen thiết kế tối giản – hiện đại – khoa học', 'saigonhoreca'); ?></div>
      </div>

      <div class="pp-split-shf__body">
        <span class="pp-badge-shf">
          <span class="pp-badge-shf__dot" aria-hidden="true"></span>
          <?php echo esc_html__('Triết lý thiết kế', 'saigonhoreca'); ?>
        </span>
        <h2 class="pp-text-shf__title"><?php echo esc_html__('Sự tinh tế và tỉ mỉ của Saigon Horeca trong thiết kế bếp Canteen', 'saigonhoreca'); ?></h2>
        <span class="pp-text-shf__divider" aria-hidden="true"></span>

        <div class="pp-split-shf__copy">
          <p><?php echo esc_html__('Với sự hỗ trợ từ Saigon Horeca, hệ thống bếp canteen được trang bị thiết bị tối tân, thiết kế khoa học, đảm bảo sự linh hoạt trong vận hành và phục vụ được số lượng lớn nhân viên cùng lúc. Đây chính là minh chứng cho sự kết hợp hoàn hảo giữa tầm nhìn của Sheh Fung và sự chuyên nghiệp của Saigon Horeca, hướng đến việc nâng cao chất lượng cuộc sống và hiệu quả làm việc của toàn bộ đội ngũ.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Căn bếp tại Sheh Fung được thiết kế với phong cách kết hợp tối giản và hiện đại sử dụng gam màu trung tính cùng các vật liệu dễ vệ sinh như thép không gỉ và bề mặt chống bám bẩn, căn bếp không chỉ tạo cảm giác sạch sẽ, thoáng đãng mà còn đảm bảo an toàn vệ sinh thực phẩm. Không gian bếp được bố trí hợp lý, với các khu vực chức năng như chuẩn bị, nấu nướng, và phục vụ được sắp xếp liền mạch, tối ưu hóa luồng công việc và giảm thiểu sự chồng chéo.', 'saigonhoreca'); ?></p>
        </div>
      </div>

    </div>
  </div>
</section>
