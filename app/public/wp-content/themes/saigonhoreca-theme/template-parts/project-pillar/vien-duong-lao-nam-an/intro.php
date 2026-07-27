<?php
/**
 * Project Pillar — vien-duong-lao-nam-an
 * Section #2: intro — Layout vận hành (operational layout + gallery + stats).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$nan_intro_gallery = [
  [
    'src' => 'vien-duong-lao-nam-an/nam-an-loi-vao-vien-duong-lao.webp', 
    'alt' => __('Lối vào chính Viện dưỡng lão Nam An với khuôn viên cảnh quan sạch đẹp', 'saigonhoreca'), 
    'caption' => __('Lối vào chính xanh mát – Viện dưỡng lão Nam An Garden', 'saigonhoreca'), 
    'class' => 'pp-intro-nan__gallery-item--large'
  ],
  [
    'src' => 'vien-duong-lao-nam-an/nam-an-banner-home.webp', 
    'alt' => __('Toàn cảnh khuôn viên Viện dưỡng lão Nam An Garden nhìn từ bên ngoài', 'saigonhoreca'), 
    'caption' => __('Toàn cảnh khuôn viên xanh mát của Viện dưỡng lão Nam An Garden', 'saigonhoreca'), 
    'class' => 'pp-intro-nan__gallery-item--wide'
  ],
  [
    'src' => 'vien-duong-lao-nam-an/nam-an-toan-canh-cong-trinh-ngoai-that.webp', 
    'alt' => __('Toàn cảnh kiến trúc ngoại thất Viện dưỡng lão Nam An Garden', 'saigonhoreca'), 
    'caption' => __('Toàn cảnh kiến trúc ngoại thất của Viện dưỡng lão Nam An', 'saigonhoreca'), 
    'class' => 'pp-intro-nan__gallery-item--wide'
  ],
];
?>
<section class="pp-section-nan pp-intro-nan">
  <div class="pp-ambient-glow-nan pp-ambient-glow-nan--top-right" aria-hidden="true"></div>
  <div class="pp-ambient-glow-nan pp-ambient-glow-nan--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
  <div class="pp-intro-nan__header">

  <div class="pp-intro-nan__intro">
  <span class="pp-badge-nan">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
  <path d="M4 21V8l8-5 8 5v13M9 21v-6h6v6" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>
  <?php echo esc_html__('Layout vận hành', 'saigonhoreca'); ?>
  </span>
  <h2 class="pp-text-nan__title"><?php echo esc_html__('Khi an toàn và hiệu quả song hành', 'saigonhoreca'); ?></h2>
  <span class="pp-text-nan__divider" aria-hidden="true"></span>
  <p class="pp-intro-nan__lead"><?php echo esc_html__('Trong dự án Viện dưỡng lão Nam An – Nursing Home Kitchen, Saigon Horeca không chỉ tiếp cận bài toán ở góc độ thiết kế bếp công nghiệp, mà đi sâu hơn: kiến tạo một hệ thống bếp cao cấp phục vụ người cao tuổi – nhóm khách hàng đặc biệt nhạy cảm với thực phẩm, dinh dưỡng và môi trường vận hành.', 'saigonhoreca'); ?></p>
        
  <div class="pp-intro-nan__featured-image pp-image-container-shared">
  <img src="<?php echo esc_url(sgh_img('vien-duong-lao-nam-an/nam-an-phong-o-nguoi-cao-tuoi.webp')); ?>" alt="<?php echo esc_attr__('Phòng nghỉ dưỡng tiện nghi cho người cao tuổi tại Viện dưỡng lão Nam An', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <div class="pp-image-caption-shared"><?php echo esc_html__('Không gian phòng nghỉ dưỡng ấm cúng, thiết kế tối ưu cho người cao tuổi', 'saigonhoreca'); ?></div>
  </div>
  </div>

  <div class="pp-intro-nan__story">
  <p><?php echo esc_html__('Lấy cảm hứng từ triết lý ẩm thực và chuẩn mực chất lượng mà Nam An theo đuổi suốt nhiều năm, dự án được phát triển với mục tiêu rõ ràng: mỗi bữa ăn không chỉ đủ dinh dưỡng, mà còn an toàn, chuẩn mực và nhất quán trong từng khâu vận hành.', 'saigonhoreca'); ?></p>
  <p><?php echo esc_html__('Khác với bếp nhà hàng hay bếp dịch vụ thông thường, Nursing Home Kitchen là mô hình có mức độ yêu cầu cao hơn rất nhiều. Mọi sai sót – dù nhỏ – đều có thể ảnh hưởng trực tiếp đến sức khỏe người dùng.', 'saigonhoreca'); ?></p>
  <p><?php echo esc_html__('Saigon Horeca tiếp cận mô hình này bằng tư duy vận hành trước – thiết kế sau. Từ thói quen ăn uống của người cao tuổi, đặc thù chế biến suất ăn mềm, dễ tiêu hóa, cho đến cường độ làm việc của đội ngũ chăm sóc sức khỏe, toàn bộ được phân tích kỹ lưỡng trước khi đặt bút vẽ layout.', 'saigonhoreca'); ?></p>
  <p><?php echo esc_html__('Trọng tâm của dự án Nam An nằm ở tối ưu layout vận hành. Gian bếp được tổ chức theo luồng khép kín: từ tiếp nhận – bảo quản – sơ chế – chế biến – làm sạch, đảm bảo thực phẩm luôn đi theo một chiều logic, hạn chế tối đa nguy cơ nhiễm chéo.', 'saigonhoreca'); ?></p>
  </div>

  </div>

  <div class="pp-intro-nan__stats">
  <div class="pp-intro-nan__stat">
  <span><?php echo esc_html__('Mô hình', 'saigonhoreca'); ?></span>
  <strong><?php echo esc_html__('Nursing Home Kitchen', 'saigonhoreca'); ?></strong>
  </div>
  <div class="pp-intro-nan__stat">
  <span><?php echo esc_html__('Luồng vận hành', 'saigonhoreca'); ?></span>
  <strong><?php echo esc_html__('1 chiều khép kín', 'saigonhoreca'); ?></strong>
  </div>
  <div class="pp-intro-nan__stat">
  <span><?php echo esc_html__('Đối tượng phục vụ', 'saigonhoreca'); ?></span>
  <strong><?php echo esc_html__('Người cao tuổi', 'saigonhoreca'); ?></strong>
  </div>
  </div>

  <div class="pp-intro-nan__gallery">
  <?php foreach ($nan_intro_gallery as $img): ?>
  <figure class="pp-intro-nan__gallery-item <?php echo esc_attr($img['class']); ?>">
  <div class="pp-image-container-shared">
  <img src="<?php echo esc_url(sgh_img($img['src'])); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy" decoding="async">
  <div class="pp-image-caption-shared"><?php echo esc_html($img['caption']); ?></div>
  </div>
  </figure>
  <?php endforeach; ?>
  </div>

  <p class="pp-intro-nan__closing"><?php echo esc_html__('Không gian sơ chế được bố trí hệ bàn và kệ inox chuyên dụng, đáp ứng yêu cầu vệ sinh nghiêm ngặt trong môi trường chăm sóc sức khỏe. Mọi bề mặt tiếp xúc thực phẩm đều dễ làm sạch, bền bỉ và đạt chuẩn an toàn thực phẩm.', 'saigonhoreca'); ?></p>
  </div>
</section>
