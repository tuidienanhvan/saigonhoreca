<?php
/**
 * Project Pillar — du-an-bep-cang-tin-cong-ty-nhat-nichiyo
 * Section #2: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-nic pp-text-nic--center">
      <div class="pp-text-nic__body">
      <p><?php echo esc_html__('Công ty TNHH Nichiyo đánh dấu cột mốc 53 năm thành lập với sứ mệnh vững chắc là một trong những đơn vị hàng đầu trong lĩnh vực thương mại sản xuất thực phẩm, thuộc Tập đoàn Warabeya Nichiyo. Điều đặc biệt là, Nichiyo không chỉ là một doanh nghiệp thương mại thông thường mà còn là biểu tượng của sự đổi mới, sáng tạo và cam kết không ngừng nâng cao chất lượng cuộc sống thông qua thực phẩm.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Với tinh thần không ngừng khám phá và lựa chọn những sản phẩm thực phẩm đa dạng từ khắp nơi trên thế giới, Nichiyo không chỉ đáp ứng nhu cầu của thị trường mà còn tạo ra những trải nghiệm ẩm thực đầy sáng tạo và độc đáo cho khách hàng. Điều này được thể hiện qua việc không ngừng tìm kiếm và áp dụng những công nghệ tiên tiến nhất trong quá trình sản xuất, kiểm soát chất lượng, để mang lại những sản phẩm an toàn, chất lượng và dinh dưỡng cao.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-nic pp-gallery-nic--cols-3" style="margin-top:2rem;">
      <div class="pp-gallery-nic__item"><img src="<?php echo sgh_img('2024/03/nichiyo-san-pham-3.jpg'); ?>" alt="nichiyo-san-pham-3" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-nic__item"><img src="<?php echo sgh_img('2024/03/nichiyo-san-pham-1.jpg'); ?>" alt="nichiyo-san-pham-1" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-nic__item"><img src="<?php echo sgh_img('2024/03/nichiyo-san-pham-2.jpg'); ?>" alt="nichiyo-san-pham-2" loading="lazy" decoding="async"></div>
    </div>
  </div>
</section>
