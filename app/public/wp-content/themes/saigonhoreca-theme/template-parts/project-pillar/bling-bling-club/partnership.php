<?php
/**
 * Project Pillar — bling-bling-club
 * Section #4: bg_section — Bespoke Parallax GPU Edition
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bg-bbc">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt bằng phần cứng -->
  <div class="pp-section-bg-bbc__bg" style="background-image:url('<?php echo sgh_img('2024/02/SGH-product-bling3.jpg'); ?>');"></div>
  <div class="pp-section-bg-bbc__overlay" aria-hidden="true"></div>
  
  <div class="pp-section-bg-bbc__content">
    <span class="pp-text-bbc__divider pp-text-bbc__divider--center" aria-hidden="true"></span>
    <h2 class="pp-text-bbc__title"><?php echo esc_html__('Saigon Horeca và Bling Bling Club mang đến một trải nghiệm nightlife đầy ấn tượng.', 'saigonhoreca'); ?></h2>
    <div class="pp-text-bbc__body">
      <p><?php echo esc_html__('Sau nhiều cuộc họp giữa nhóm thiết kế của SGH và nhóm vận hành của Bling Bling, chúng tôi đã đồng ý về một kế hoạch cho trang thiết bị trong khu vực quầy bar ở sảnh trung tâm của câu lạc bộ.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Sau khi khảo sát và đánh giá, kế hoạch của SGH là phân phối các thiết bị lạnh đồng đều trong hai cánh của quầy bar phía trước. Cùng với mỗi tủ lạnh dưới quầy, chúng tôi đã thiết kế một quầy cocktail hoàn toàn chức năng để tạo ra các loại đồ uống có cồn và không có cồn một cách sáng tạo nhất. Ngoài ra, để có thể đáp ứng nhu cầu của một lượng lớn khách hàng, chúng tôi sắp xếp nhiều tủ được làm từ thép không gỉ 304 để có thể lưu trữ một lượng hàng hóa đủ đáp ứng nhu cầu của cả những khách hàng khó tính nhất, đồng thời đảm bảo rằng công việc sáng tạo của các người pha chế diễn ra một cách trơn tru nhất. Tất cả nhờ vào kiến thức, kinh nghiệm và kinh nghiệm của Saigon Horeca từ tất cả các dự án của khách hàng chúng tôi.', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>
