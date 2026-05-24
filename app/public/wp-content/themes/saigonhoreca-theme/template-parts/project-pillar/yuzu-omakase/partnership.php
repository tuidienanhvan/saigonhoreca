<?php
/**
 * Project Pillar — yuzu-omakase
 * Section #4: bg_section (Parallax Masking Effect)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bg-yzo" style="background-image:url('<?php echo sgh_img('2024/01/SGH-Yuzu-Omakase.jpg'); ?>');">
  <div class="pp-section-bg-yzo__overlay" aria-hidden="true"></div>
  
  <!-- Text Content (No frame, no background) -->
  <div class="pp-section-bg-yzo__content">
    <span class="pp-text-yzo__divider pp-text-yzo__divider--center" aria-hidden="true"></span>
    <h2 class="pp-text-yzo__title"><?php echo esc_html__('Sự hợp tác giữa Yuzu Omakase và Saigon Horeca', 'saigonhoreca'); ?></h2>
    <div class="pp-text-yzo__body">
      <p><?php echo esc_html__('Giai đoạn ý tưởng ban đầu bao gồm các cuộc thảo luận giữa 2 bên và phân tích để hiểu rõ về các yêu cầu cụ thể và nhu cầu vận hành của Yuzu Omakase. Đội ngũ của Saigon Horeca đã hợp tác chặt chẽ với các bên liên quan của Yuzu Omakase để nắm bắt giá trị cốt lõi, phong cách ẩm thực, quy trình làm việc và cách sử dụng không gian của nhà hàng.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Với chuyên môn và kinh nghiệm của mình, Saigon Horeca đảm bảo rằng thiết kế bếp tuân thủ các tiêu chuẩn và quy định ngành công nghiệp đồng thời tạo một môi trường khuyến khích sáng tạo ẩm thực cho các đầu bếp. Sự chú ý đến từng chi tiết của chúng tôi bao gồm hệ thống thông gió, giải pháp lưu trữ, xem xét về mặt tiện nghi và tối ưu hóa quy trình làm việc, tạo ra không gian bếp vừa đáp ứng đủ chức năng vừa gọn gàng, đẹp mắt.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Đặc biệt rằng, dịch vụ tư vấn và thiết kế của Saigon Horeca cho không gian bếp của nhà hàng không chỉ đáp ứng nhu cầu vận hành mà còn trở thành một phần không thể thiếu của sự độc đáo của ngành ẩm thực Omakase – một nghệ thuật ẩm thực đặc sắc và đề cao trải nghiệm nấu nướng trực tiếp.', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>
