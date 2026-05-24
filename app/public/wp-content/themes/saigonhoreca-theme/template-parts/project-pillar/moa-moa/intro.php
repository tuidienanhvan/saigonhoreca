<?php
/**
 * Project Pillar — moa-moa
 * Section #2: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-text-mmo pp-text-mmo--center">
      <span class="pp-text-mmo__divider pp-text-mmo__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-mmo__title"><?php echo esc_html__('Thiết kế layout: Tối ưu vận hành cho pasta thủ công', 'saigonhoreca'); ?></h2>
      <div class="pp-text-mmo__body">
      <p><?php echo esc_html__('Giữa trung tâm Quận 1 sôi động, nơi nhịp sống thành phố chưa bao giờ chậm lại, Moa Moa Pasta Club xuất hiện như một khoảng dừng tinh tế dành cho những ai trân trọng ẩm thực Ý nguyên bản. Tọa lạc tại 40E Ngô Đức Kế, phường Bến Nghé, chỉ cách phố đi bộ Nguyễn Huệ vài bước chân, Moa Moa không chỉ là một nhà hàng pasta – mà là nơi thủ công, kỹ thuật và văn hoá ẩm thực giao thoa trong từng chi tiết.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Đồng hành cùng dự án này, Saigon Horeca không tiếp cận Moa Moa như một công trình bếp thông thường, mà như một bài toán không gian – vận hành – trải nghiệm, nơi mỗi quyết định thiết kế đều phải phục vụ cho tinh thần Italiano autentico.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Ẩm thực Ý, đặc biệt là pasta thủ công, luôn đặt quy trình chế biến vào vị trí trung tâm. Tại Moa Moa, bếp không bị giấu sau những bức tường kín, mà trở thành trái tim của nhà hàng – nơi thực khách có thể cảm nhận nhịp điệu làm bếp, từ trộn bột, cán mì, trụng pasta cho đến hoàn thiện món ăn.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Vì vậy, layout bếp được SGH tư vấn theo định hướng Italian open-kitchen: gọn gàng, mạch lạc, phân khu rõ ràng nhưng vẫn đảm bảo dòng di chuyển mượt mà. Không gian bếp phải đủ "mở" để kết nối với khu phục vụ, nhưng đồng thời đủ "chuẩn" để đáp ứng yêu cầu kỹ thuật của một nhà hàng pasta chuyên nghiệp.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Khác với nhiều mô hình Âu tổng hợp, bếp pasta đòi hỏi sự liền mạch giữa các công đoạn. Từ khu trộn bột, chế biến pasta tươi, trụng mì đến khu hoàn thiện món – tất cả phải được bố trí theo một trục logic, giảm thao tác thừa và đảm bảo nhịp độ phục vụ trong giờ cao điểm.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Saigon Horeca đã xây dựng layout xoay quanh thói quen vận hành thực tế của đầu bếp Ý, ưu tiên sự thuận tay, khoảng cách thao tác ngắn và khả năng làm việc liên tục. Đây chính là yếu tố giúp Moa Moa duy trì chất lượng món ăn ổn định, dù nằm giữa một trong những khu vực đông khách nhất TP.HCM.', 'saigonhoreca'); ?></p>
      </div>
    </div>
    <div class="pp-gallery-mmo pp-gallery-mmo--cols-4" style="margin-top:2rem;">
      <div class="pp-gallery-mmo__item"><img src="<?php echo sgh_img('2025/01/sheh-fung-6.jpg'); ?>" alt="sheh-fung (6)" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-mmo__item"><img src="<?php echo sgh_img('2025/01/sheh-fung-8.jpg'); ?>" alt="sheh-fung (8)" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-mmo__item"><img src="<?php echo sgh_img('2025/01/sheh-fung-9.jpg'); ?>" alt="sheh-fung (9)" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-mmo__item"><img src="<?php echo sgh_img('2025/01/sheh-fung-7.jpg'); ?>" alt="sheh-fung (7)" loading="lazy" decoding="async"></div>
      <div class="pp-gallery-mmo__item"><img src="<?php echo sgh_img('2026/03/du-an-thecheezytime-11.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768"></div>
    </div>
  </div>
</section>
