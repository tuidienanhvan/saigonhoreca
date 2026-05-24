<?php
/**
 * Project Pillar — du-an-bep-cang-tin-cong-ty-nhat-nichiyo
 * Section #4: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-nic pp-text-nic--center">
      <span class="pp-text-nic__divider pp-text-nic__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-nic__title"><?php echo esc_html__('Thiết kế bếp căng tin của Nichiyo', 'saigonhoreca'); ?></h2>
      <div class="pp-text-nic__body">
      <p><?php echo esc_html__('Bếp căng tin này được thiết kế theo phong cách đơn giản và sạch sẽ. Với tông màu trắng và xám trung tính, không gian làm việc trở nên gọn gàng và thoải mái.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Mỗi chi tiết trong căn bếp được chọn lựa cẩn thận để tạo ra sự hài hòa và tiện ích cao. Thiết bị bếp và vật dụng bằng thép không gỉ được sắp xếp một cách thông minh, tối ưu hóa không gian và tăng hiệu suất làm việc.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Trong không gian bếp hạn chế của Nichiyo, Saigon Horeca đã thể hiện sự khéo léo thông qua việc bố trí tinh tế. Dù tuân thủ các quy định về kích thước nhà bếp, nhưng không làm hạn chế sự sáng tạo trong thiết kế. Điều này được thực hiện nhờ vào hệ thống thông gió được thiết kế tỉ mỉ, giúp không gian trở nên thông thoáng và thoải mái.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Bên cạnh đó, việc lưu trữ được tối ưu hóa và bố cục tiện dụng hỗ trợ các đầu bếp trong quá trình làm việc. Mỗi chi tiết, từ các vật dụng bằng thép không gỉ sáng bóng cho đến những kệ được đặt đúng vị trí, đều phản ánh sự hiệu quả và chuyên nghiệp trong quản lý không gian và thiết kế.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Tổng thể, sự khéo léo và tinh tế trong việc bố trí không chỉ làm cho không gian bếp trở nên gọn gàng và tiện nghi mà còn giúp tăng cường hiệu suất làm việc và sự thoải mái cho các đầu bếp. Đây thực sự là một minh chứng cho sự chuyên nghiệp và sự tận tâm của Saigon Horeca đối với khách hàng của mình.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Tất cả những điều này kết hợp với nhau để tạo ra một môi trường làm việc đơn giản, hiệu quả và thoải mái cho nhân viên.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-nic pp-gallery-nic--cols-3" style="margin-top:2rem;">
      <div class="pp-gallery-nic__item"><img src="<?php echo sgh_img('2024/03/nichiyo-du-an-2.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
      <div class="pp-gallery-nic__item"><img src="<?php echo sgh_img('2024/03/nichiyo-du-an-1.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
      <div class="pp-gallery-nic__item"><img src="<?php echo sgh_img('2024/03/nichiyo-du-an-3.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>
