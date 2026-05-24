<?php
/**
 * Project Pillar - little-bear-thao-dien
 * Section #2: intro
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$meta_items = [
  [
    'label' => __('Địa điểm', 'saigonhoreca'),
    'value' => __('36 Nguyễn Bá Huân, Thảo Điền, TP.HCM', 'saigonhoreca'),
  ],
  [
    'label' => __('Khung giờ phục vụ', 'saigonhoreca'),
    'value' => __('17h30 - 21h00 | Thứ ba - Chủ nhật', 'saigonhoreca'),
  ],
  [
    'label' => __('Nhận đặt bàn tối', 'saigonhoreca'),
    'value' => __('Đến trước 17h00', 'saigonhoreca'),
  ],
];

$scope_items = [
  __('Thiết kế line bếp theo nhịp phục vụ dinner service', 'saigonhoreca'),
  __('Tích hợp thiết bị Rational, Hoshizaki và Fujimak', 'saigonhoreca'),
  __('Gia công inox riêng theo mặt bằng hiện hữu', 'saigonhoreca'),
];

$gallery = [
  [
    'src' => '2024/01/Little-bear-04.jpeg',
    'alt' => __('Đầu bếp làm việc tại Little Bear', 'saigonhoreca'),
    'class' => 'pp-intro-lbt__gallery-item--feature',
    'caption' => __('Bếp mở theo nhịp phục vụ', 'saigonhoreca'),
  ],
  [
    'src' => '2024/01/Little-bear-03.jpeg',
    'alt' => __('Không gian bar và bếp tại Little Bear', 'saigonhoreca'),
    'class' => 'pp-intro-lbt__gallery-item--compact',
    'caption' => __('Bar station nhìn thẳng line bếp', 'saigonhoreca'),
  ],
  [
    'src' => '2024/01/SGH-Little-Bear.jpg',
    'alt' => __('Mặt tiền Little Bear Thảo Điền', 'saigonhoreca'),
    'class' => 'pp-intro-lbt__gallery-item--compact pp-intro-lbt__gallery-item--entry',
    'caption' => __('Lối vào gọn và sáng', 'saigonhoreca'),
  ],
  [
    'src' => '2024/01/SGH-Little-Bear01.jpg',
    'alt' => __('Chi tiết thiết bị bếp tại Little Bear', 'saigonhoreca'),
    'class' => 'pp-intro-lbt__gallery-item--wide',
    'caption' => __('Thiết bị và bề mặt inox cho vận hành hàng đêm', 'saigonhoreca'),
  ],
];
?>
<section class="pp__section pp__section--alt pp-intro-lbt">
  <div class="pp__container">
    <div class="pp-intro-lbt__grid">
      <div class="pp-intro-lbt__content">
        <span class="pp-intro-lbt__divider" aria-hidden="true"></span>
        <span class="pp-intro-lbt__eyebrow"><?php echo esc_html__('Restaurant profile', 'saigonhoreca'); ?></span>
        <h2 class="pp-intro-lbt__title"><?php echo esc_html__('Little Bear Thảo Điền', 'saigonhoreca'); ?></h2>
        <p class="pp-intro-lbt__lead">
          <?php echo esc_html__('Một không gian F&B nhỏ nhưng có nhịp vận hành rõ, nơi bếp mở, quầy bar và trải nghiệm khách được giữ trong cùng một mạch liên tục.', 'saigonhoreca'); ?>
        </p>

        <div class="pp-intro-lbt__body">
          <p><?php echo esc_html__('Bước vào Little Bear, khách gặp ngay chất liệu gạch ấm, cây xanh mềm và nhịp phục vụ gần gũi. Không gian nhỏ nhưng không hẹp; từng bề mặt, từng line thao tác đều cần gọn, chính xác và bền trước cường độ vận hành buổi tối.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Saigon Horeca tham gia hoàn thiện căn bếp đa năng với thiết bị từ Rational, Hoshizaki, Fujimak cùng các hạng mục inox đặt riêng. Mục tiêu không chỉ là đủ công năng, mà còn là giữ cho khu bếp và quầy bar vận hành sạch, nhanh và đồng bộ với tinh thần của Little Bear.', 'saigonhoreca'); ?></p>
        </div>
      </div>

      <aside class="pp-intro-lbt__rail" aria-label="<?php echo esc_attr__('Thông tin dự án', 'saigonhoreca'); ?>">
        <div class="pp-intro-lbt__panel">
          <span class="pp-intro-lbt__panel-label"><?php echo esc_html__('Vận hành thực tế', 'saigonhoreca'); ?></span>
          <div class="pp-intro-lbt__meta">
            <?php foreach ($meta_items as $item) : ?>
              <div class="pp-intro-lbt__meta-item">
                <span><?php echo esc_html($item['label']); ?></span>
                <strong><?php echo esc_html($item['value']); ?></strong>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="pp-intro-lbt__panel pp-intro-lbt__panel--soft">
          <span class="pp-intro-lbt__panel-label"><?php echo esc_html__('Phạm vi Saigon Horeca', 'saigonhoreca'); ?></span>
          <ul class="pp-intro-lbt__scope">
            <?php foreach ($scope_items as $item) : ?>
              <li><?php echo esc_html($item); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </aside>
    </div>

    <div class="pp-intro-lbt__gallery" aria-label="<?php echo esc_attr__('Hình ảnh Little Bear Thảo Điền', 'saigonhoreca'); ?>">
      <?php foreach ($gallery as $image) : ?>
        <figure class="pp-intro-lbt__gallery-item <?php echo esc_attr($image['class']); ?>">
          <img src="<?php echo esc_url(sgh_img($image['src'])); ?>" alt="<?php echo esc_attr($image['alt']); ?>" loading="lazy" decoding="async">
          <figcaption><?php echo esc_html($image['caption']); ?></figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
