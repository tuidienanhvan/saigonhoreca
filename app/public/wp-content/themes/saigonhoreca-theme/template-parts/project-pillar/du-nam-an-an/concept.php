<?php
/**
 * Project Pillar — du-nam-an-an
 * Section #3: concept — Thiết bị bếp (core equipment chosen for stability).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$nan_equipment = [
  [
    'icon' => '<path d="M5 3h14v4H5zM7 7v3a3 3 0 0 0 6 0V7m4 0v3a2 2 0 0 1-2 2M7 21h10M9 12v9m6-9v9" stroke-linecap="round" stroke-linejoin="round"/>',
    'title' => __('Hệ bếp Âu 6 họng điện', 'saigonhoreca'),
    'desc'  => __('Được lựa chọn nhằm đảm bảo khả năng kiểm soát nhiệt chính xác, giảm thiểu rủi ro cháy nổ so với bếp gas, đồng thời phù hợp với môi trường vận hành liên tục.', 'saigonhoreca'),
  ],
  [
    'icon' => '<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M9 2v20M9 7h.01M9 13h.01" stroke-linecap="round"/>',
    'title' => __('Hệ 4 tủ lạnh đứng 4 cánh', 'saigonhoreca'),
    'desc'  => __('Đóng vai trò trung tâm trong việc bảo quản và lưu trữ thực phẩm, nguyên liệu theo từng nhóm riêng biệt, giúp kiểm soát chất lượng và hạn sử dụng chặt chẽ hơn.', 'saigonhoreca'),
  ],
  [
    'icon' => '<path d="M4 7h16M6 7l1 13a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-13M9 7V5a3 3 0 0 1 6 0v2M10 12v5m4-5v5" stroke-linecap="round" stroke-linejoin="round"/>',
    'title' => __('Máy rửa chén dạng âm quầy', 'saigonhoreca'),
    'desc'  => __('Được tích hợp tinh gọn trong layout, giúp tối ưu diện tích nhưng vẫn đảm bảo tiêu chuẩn làm sạch, tiết kiệm thời gian cho đội ngũ nhân viên chăm sóc.', 'saigonhoreca'),
  ],
];
?>
<section class="pp-section-nan pp-concept-nan scroll-reveal">
  <div class="pp-ambient-glow-nan pp-ambient-glow-nan--bottom-left" aria-hidden="true"></div>
  <div class="pp-concept-nan__watermark" aria-hidden="true">02</div>

  <div class="pp-container-shared">
    <div class="pp-split-nan">

      <div class="pp-split-nan__media">
        <div class="pp-concept-nan__mask pp-image-container-shared">
          <div class="pp-concept-nan__frame" aria-hidden="true"></div>
          <img src="<?php echo esc_url(sgh_img('du-nam-an-an/nam-an-bep-tu-cong-nghiep-mat-kinh-inox.webp')); ?>" alt="<?php echo esc_attr__('Bếp từ công nghiệp mặt kính đen khung inox nhiều vùng nấu — thiết bị nấu cốt lõi bếp Nam An do Saigon Horeca thi công', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Bếp từ công nghiệp mặt kính – thiết bị nấu cốt lõi, vận hành ổn định', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <div class="pp-split-nan__body">
        <div class="pp-concept-nan__card">
          <header class="pp-concept-nan__header">
            <span class="pp-badge-nan">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                <path d="M12 2 4 6v6c0 5 3.5 8 8 10 4.5-2 8-5 8-10V6l-8-4Z" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <?php echo esc_html__('Thiết bị bếp', 'saigonhoreca'); ?>
            </span>
            <h2 class="pp-text-nan__title"><?php echo esc_html__('Lựa chọn vì sự ổn định lâu dài', 'saigonhoreca'); ?></h2>
            <span class="pp-text-nan__divider" aria-hidden="true"></span>
            <p class="pp-concept-nan__lead"><?php echo esc_html__('Thay vì chạy theo số lượng, Saigon Horeca tập trung vào những thiết bị cốt lõi, vận hành ổn định và phù hợp với đặc thù Nursing Home Kitchen.', 'saigonhoreca'); ?></p>
          </header>

          <ul class="pp-concept-nan__list">
            <?php foreach ($nan_equipment as $item): ?>
              <li class="pp-concept-nan__item">
                <span class="pp-concept-nan__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><?php echo $item['icon']; ?></svg>
                </span>
                <div class="pp-concept-nan__item-body">
                  <h3><?php echo esc_html($item['title']); ?></h3>
                  <p><?php echo esc_html($item['desc']); ?></p>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

    </div>
  </div>
</section>
