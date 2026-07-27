<?php
/**
 * Project Pillar — hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien
 * Section #4: partnership — "Điểm nhấn mạnh mẽ trong thiết kế" (split reverse + stats).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$hmd_stats = array(
  array('value' => '11m²',     'label' => 'Tổng diện tích khu bếp'),
  array('value' => '02',       'label' => 'Công năng trong một không gian'),
  array('value' => '100%',     'label' => 'Thiết bị inox custom theo thực tế'),
);
?>
<section class="pp-section-hmd pp-section-hmd--alt pp-hmd-partnership">
  <div class="pp-container-shared">
    <div class="pp-split-hmd pp-split-hmd--reverse">
      <div class="pp-split-hmd__body">
        <span class="pp-text-hmd__divider" aria-hidden="true"></span>
        <h2 class="pp-text-hmd__title"><?php echo esc_html__('Điểm nhấn mạnh mẽ trong thiết kế của Saigon Horeca', 'saigonhoreca'); ?></h2>
        <div class="pp-text-hmd__body">
          <p><?php echo esc_html__('Đặc biệt, Saigon Horeca đã ứng dụng hệ thống tủ ray trượt thông minh thay cho tủ mở truyền thống. Giải pháp này giúp tận dụng triệt để chiều sâu tủ, lấy đồ nhanh và giữ mọi vật dụng luôn gọn gàng trong tầm với.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Nhờ sự sắp đặt logic này, Hemma Desserts có thể vận hành hai quy trình độc lập (bánh & cà phê) trong cùng một không gian – vừa hiệu quả, vừa an toàn, vừa thẩm mỹ.', 'saigonhoreca'); ?></p>
        </div>

        <div class="pp-hmd-stats">
          <?php foreach ($hmd_stats as $s) : ?>
            <div class="pp-hmd-stats__item">
              <span class="pp-hmd-stats__value"><?php echo esc_html($s['value']); ?></span>
              <span class="pp-hmd-stats__label"><?php echo esc_html__($s['label'], 'saigonhoreca'); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="pp-split-hmd__media pp-image-container-shared">
        <img src="<?php echo sgh_img('hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien/hemma-desserts-ke-khay-nuong-inox-xep-tang.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống tủ ray trượt thông minh Saigon Horeca tại Hemma Desserts', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="1024">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống tủ ray trượt thông minh – giải pháp tối ưu cho không gian hẹp', 'saigonhoreca'); ?></div>
      </div>
    </div>
  </div>
</section>
