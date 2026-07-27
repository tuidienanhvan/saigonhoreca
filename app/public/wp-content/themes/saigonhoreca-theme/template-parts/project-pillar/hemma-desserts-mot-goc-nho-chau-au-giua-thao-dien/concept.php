<?php
/**
 * Project Pillar — hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien
 * Section #3: concept — "Giải pháp của Saigon Horeca" (split).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-hmd pp-section-hmd--alt pp-hmd-concept">
  <div class="pp-container-shared">
    <div class="pp-split-hmd">
      <div class="pp-split-hmd__body">
        <span class="pp-text-hmd__divider" aria-hidden="true"></span>
        <h2 class="pp-text-hmd__title"><?php echo esc_html__('Giải pháp của Saigon Horeca', 'saigonhoreca'); ?></h2>
        <div class="pp-text-hmd__body">
          <p><?php echo esc_html__('Ngay từ khâu khảo sát, đội ngũ kỹ sư Saigon Horeca đã nhận thấy rằng: để biến không gian 11m² thành một bếp bánh kiêm bar café hiệu quả, không thể dùng thiết bị tiêu chuẩn sẵn có, mà phải custom toàn bộ thiết bị inox theo kích thước thực tế.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Từng centimet được tính toán kỹ lưỡng:', 'saigonhoreca'); ?></p>
        </div>

        <ul class="pp-hmd-feature-list">
          <li class="pp-hmd-feature-list__item">
            <span class="pp-hmd-feature-list__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="pp-hmd-feature-list__text"><?php echo esc_html__('Khu rửa & lưu trữ dụng cụ: máy rửa chén và kệ inox 3 tầng giúp tối ưu không gian thẳng đứng, hạn chế bề mặt tiếp xúc – dễ vệ sinh, sạch sẽ tuyệt đối.', 'saigonhoreca'); ?></span>
          </li>
        </ul>
      </div>

      <div class="pp-split-hmd__media pp-image-container-shared">
        <img src="<?php echo sgh_img('hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien/hemma-desserts-lo-nuong-banh-southstar-2-tang-tu-mat.jpg'); ?>" alt="<?php echo esc_attr__('Khu bếp bánh chuyên nghiệp với thiết bị inox custom Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="1024">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Khu bếp bánh chuyên nghiệp với thiết bị inox custom theo kích thước thực tế', 'saigonhoreca'); ?></div>
      </div>
    </div>
  </div>
</section>
