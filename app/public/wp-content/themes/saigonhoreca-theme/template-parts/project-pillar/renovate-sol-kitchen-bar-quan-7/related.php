<?php
/**
 * Project Pillar — renovate-sol-kitchen-bar-quan-7
 * Section #7: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-related-section">
  <div class="sgh-related-container">
    <div class="sgh-related-grid">
      
      <!-- Cột trái: Thuyết minh & Quote cam kết -->
      <div class="sgh-related-text">
        <div class="sgh-related-badge">
          <span class="sgh-related-badge__accent">//</span> <?php echo esc_html__('QUY TRÌNH & GIÁM SÁT', 'saigonhoreca'); ?>
        </div>
        
        <h3 class="sgh-related-title">
          <?php echo esc_html__('Kỹ Thuật Thi Công & Cam Kết Vững Chãi', 'saigonhoreca'); ?>
        </h3>
        
        <div class="sgh-related-body">
          <p class="sgh-related-paragraph">
            <?php echo esc_html__('Quy trình thi công tại hiện trường được Saigon Horeca triển khai theo các tiêu chuẩn kỹ thuật nghiêm ngặt và cẩn trọng nhất. Từng khớp nối, đường ống và chi tiết kỹ thuật của hệ thống bếp đều được đội ngũ kỹ sư giám sát chặt chẽ nhằm triệt tiêu hoàn toàn rủi ro vận hành và đảm bảo an toàn lao động tuyệt đối.', 'saigonhoreca'); ?>
          </p>
          <p class="sgh-related-paragraph">
            <?php echo esc_html__('Bằng sự chuyên nghiệp và tận tụy, chúng tôi bàn giao cho chủ đầu tư một công trình chỉn chu nhất, sẵn sàng đáp ứng tần suất phục vụ liên tục trong các khung giờ cao điểm tại Sol Kitchen & Bar.', 'saigonhoreca'); ?>
          </p>
        </div>

        <blockquote class="sgh-related-quote">
          <?php echo esc_html__('“Saigon Horeca cam kết mang đến dịch vụ kỹ thuật đỉnh cao, sự tin cậy tuyệt đối trong hợp tác kinh doanh và tính thẩm mỹ cao nhất trong từng hệ thống bếp.”', 'saigonhoreca'); ?>
        </blockquote>
      </div>

      <!-- Cột phải: Lưới 3 ảnh so le lệch trục nghệ thuật -->
      <div class="sgh-related-media">
        <div class="sgh-related-mosaic">
          
          <!-- Ảnh 1 (Main): Cận cảnh lắp đặt thiết bị -->
          <div class="sgh-related-mosaic__item sgh-related-mosaic__item--main">
            <img src="<?php echo sgh_img('2024/06/thi-cong-02.jpg'); ?>" alt="<?php echo esc_attr__('Quy trình lắp đặt thiết bị bếp inox công nghiệp chuyên nghiệp - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          </div>
          
          <!-- Ảnh 2 (Sub 1): Thợ thi công chi tiết -->
          <div class="sgh-related-mosaic__item sgh-related-mosaic__item--sub1">
            <img src="<?php echo sgh_img('2024/06/thi-cong-04.jpg'); ?>" alt="<?php echo esc_attr__('Thi công lắp đặt thiết bị bếp tại công trường - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          </div>
          
          <!-- Ảnh 3 (Sub 2): Thiết bị bếp inox hoàn thiện -->
          <div class="sgh-related-mosaic__item sgh-related-mosaic__item--sub2">
            <img src="<?php echo sgh_img('2024/06/thi-cong-03.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống thiết bị inox bếp công nghiệp sau lắp đặt - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          </div>
          
        </div>
      </div>

    </div>
  </div>
</section>

