<?php
/**
 * Project Pillar — du-an-bep-cang-tin-cong-ty-nhat-nichiyo
 * Section #2: intro — centered storytelling + product gallery (san-pham).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-nichiyo pp-section-nichiyo--alt pp-intro-nichiyo scroll-reveal reveal-fade">
  <div class="pp-nichiyo-glow pp-nichiyo-glow--tr" aria-hidden="true"></div>
  <div class="pp-container-shared">
    <div class="pp-intro-nichiyo__head">
      <span class="pp-text-nichiyo__divider pp-text-nichiyo__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-nichiyo__title"><?php echo esc_html__('Nichiyo — 53 năm Monozukuri trong từng sản phẩm', 'saigonhoreca'); ?></h2>
      <div class="pp-text-nichiyo__body">
        <p><?php echo esc_html__('Công ty TNHH Nichiyo đánh dấu cột mốc 53 năm thành lập với sứ mệnh vững chắc là một trong những đơn vị hàng đầu trong lĩnh vực thương mại sản xuất thực phẩm, thuộc Tập đoàn Warabeya Nichiyo. Điều đặc biệt là, Nichiyo không chỉ là một doanh nghiệp thương mại thông thường mà còn là biểu tượng của sự đổi mới, sáng tạo và cam kết không ngừng nâng cao chất lượng cuộc sống thông qua thực phẩm.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Với tinh thần không ngừng khám phá và lựa chọn những sản phẩm thực phẩm đa dạng từ khắp nơi trên thế giới, Nichiyo không chỉ đáp ứng nhu cầu của thị trường mà còn tạo ra những trải nghiệm ẩm thực đầy sáng tạo và độc đáo cho khách hàng. Điều này được thể hiện qua việc không ngừng tìm kiếm và áp dụng những công nghệ tiên tiến nhất trong quá trình sản xuất, kiểm soát chất lượng, để mang lại những sản phẩm an toàn, chất lượng và dinh dưỡng cao.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <div class="pp-gallery-nichiyo pp-gallery-nichiyo--cols-3">
      <div class="pp-gallery-nichiyo__item scroll-reveal reveal-up delay-100">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('du-an-bep-cang-tin-cong-ty-nhat-nichiyo/nichiyo-san-pham-3.jpg'); ?>" alt="<?php echo esc_attr__('Sản phẩm thực phẩm Nichiyo', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Dòng sản phẩm thực phẩm tuyển chọn của Nichiyo, đại diện cho tiêu chuẩn chất lượng và an toàn nghiêm ngặt.', 'saigonhoreca'); ?></div>
        </div>
      </div>
      <div class="pp-gallery-nichiyo__item scroll-reveal reveal-up delay-200">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('du-an-bep-cang-tin-cong-ty-nhat-nichiyo/nichiyo-san-pham-1.jpg'); ?>" alt="<?php echo esc_attr__('Sản phẩm thực phẩm Nichiyo', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Mỗi sản phẩm phản ánh tinh thần Monozukuri — chế tác tỉ mỉ và cam kết với chất lượng cuộc sống.', 'saigonhoreca'); ?></div>
        </div>
      </div>
      <div class="pp-gallery-nichiyo__item scroll-reveal reveal-up delay-300">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('du-an-bep-cang-tin-cong-ty-nhat-nichiyo/nichiyo-san-pham-2.jpg'); ?>" alt="<?php echo esc_attr__('Sản phẩm thực phẩm Nichiyo', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Danh mục đa dạng từ khắp nơi trên thế giới, mang đến trải nghiệm ẩm thực sáng tạo và độc đáo.', 'saigonhoreca'); ?></div>
        </div>
      </div>
    </div>
  </div>
</section>
