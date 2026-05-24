<?php
/**
 * Project Pillar — du-an-vinh-hiep
 * Section #4: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split pp-split--reverse">
      <div class="pp-split__body">
        <span class="pp-text__divider" aria-hidden="true"></span>
        <h2 class="pp-text__title"><?php echo esc_html__('Showroom Coffee Lab – Khi inox trở thành ngôn ngữ của thương hiệu', 'saigonhoreca'); ?></h2>
        <div class="pp-text__body">
      <p><?php echo esc_html__('Điểm nhấn đặc biệt của dự Nam An ở khu vực showroom triển lãm – nơi Vĩnh Hiệp tiếp đón các đoàn khách tham quan và giới thiệu chiều sâu của cà phê Việt Nam.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Saigon Horeca được giao nhiệm vụ tư vấn và bố trí toàn bộ không gian thiết bị chế biến và phục vụ cà phê, với vật liệu chủ đạo là inox cao cấp. Nhưng inox ở đây không mang dáng dấp công nghiệp khô cứng, mà được xử lý hoàn thiện tinh xảo, đồng bộ và mang tính trưng bày.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Trung tâm của không gian là bàn cupping chuyên dụng cao cấp, được thi công hoàn toàn từ inox mạ màu, lựa chọn tông màu tương đồng với hệ nhận diện thương hiệu Vĩnh Hiệp. Đây không chỉ là bàn thử nếm, mà là một tuyên ngôn hình ảnh: sự nghiêm túc, chuẩn mực và đẳng cấp trong từng công đoạn đánh giá chất lượng cà phê.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Không gian Coffee Lab vì thế trở thành nơi mà kỹ thuật – thẩm mỹ – câu chuyện thương hiệu cùng tồn tại hài hòa.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split__media">
        <img src="<?php echo sgh_img('2025/01/sheh-fung-4.jpg'); ?>" alt="<?php echo esc_attr__('Bếp á đôi công nghiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
