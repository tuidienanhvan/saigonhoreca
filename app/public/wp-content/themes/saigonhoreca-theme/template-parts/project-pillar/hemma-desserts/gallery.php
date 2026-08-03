<?php
/**
 * Project Pillar — hemma-desserts
 * Section #6: gallery — closing showcase + brand sign-off.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-hmd pp-section-hmd--alt pp-hmd-gallery">
  <div class="pp-hmd-watermark" aria-hidden="true">Bếp nhỏ</div>

  <div class="pp-container-shared">
    <div class="pp-text-hmd pp-text-hmd--center">
      <span class="pp-text-hmd__divider pp-text-hmd__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-hmd__title"><?php echo esc_html__('Bếp nhỏ, công năng lớn', 'saigonhoreca'); ?></h2>
      <div class="pp-text-hmd__body">
        <p><?php echo esc_html__('Dự án Hemma Desserts là minh chứng rõ ràng cho triết lý mà Saigon Horeca theo đuổi: biến mọi giới hạn của không gian thành cơ hội, để một góc nhỏ châu Âu giữa Thảo Điền vẫn vận hành chuẩn mực như một bếp bánh chuyên nghiệp.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <div class="pp-hmd-mosaic" role="group" aria-label="<?php echo esc_attr__('Thư viện hình ảnh dự án Hemma Desserts', 'saigonhoreca'); ?>">
      <span class="pp-hmd-mosaic__glow" aria-hidden="true"></span>
      <span class="pp-hmd-mosaic__index" aria-hidden="true">06</span>

      <figure class="pp-image-container-shared pp-gallery-hmd__item pp-hmd-mosaic__item">
        <img src="<?php echo sgh_img('hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien/hemma-desserts-tu-mat-under-counter-xe-ke-khay-nuong.jpg'); ?>" alt="<?php echo esc_attr__('Giá khay nướng inox và dãy tủ lạnh âm quầy khu bếp bánh Hemma Desserts', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="1024">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Khu bếp bánh với giá khay nướng inox và dãy tủ trữ lạnh chuyên dụng – lưu trữ và thao tác liền mạch', 'saigonhoreca'); ?></figcaption>
      </figure>

      <figure class="pp-image-container-shared pp-gallery-hmd__item pp-hmd-mosaic__item">
        <img src="<?php echo sgh_img('hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien/du-an-hemma-desserts-sgh.jpg'); ?>" alt="<?php echo esc_attr__('Dự án Hemma Desserts thực hiện bởi Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="1024">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Dự án Hemma Desserts – nơi ý tưởng và kỹ thuật gặp nhau', 'saigonhoreca'); ?></figcaption>
      </figure>
    </div>

    <div class="pp-hmd-signoff">
      <p class="pp-hmd-signoff__text"><?php echo esc_html__('Nếu bạn đang sở hữu một mặt bằng nhỏ, nhưng vẫn mong muốn vận hành quầy bar, bếp bánh hay nhà hàng chuyên nghiệp – hãy để Saigon Horeca giúp bạn tạo nên "bếp nhỏ, công năng lớn" của riêng mình.', 'saigonhoreca'); ?></p>
      <span class="pp-hmd-signoff__mark" aria-hidden="true">Saigon Horeca</span>
    </div>
  </div>
</section>
