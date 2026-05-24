<?php
/**
 * Project Pillar — yuzu-omakase
 * Section #2: with_gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$stats = [
  ['label' => __('Xếp hạng', 'saigonhoreca'), 'value' => __('Top 50 Thái Lan', 'saigonhoreca')],
  ['label' => __('Mô hình', 'saigonhoreca'), 'value' => __('Fine dining / Omakase', 'saigonhoreca')],
  ['label' => __('Vai trò SGH', 'saigonhoreca'), 'value' => __('Thiết kế & thiết bị bếp', 'saigonhoreca')],
];

$gallery = [
  ['src' => '2024/01/Yuzu-Omakase-1.jpg', 'alt' => __('Khu vực phục vụ Yuzu Omakase', 'saigonhoreca'), 'class' => 'pp-intro-yzo__gallery-item--large'],
  ['src' => '2024/01/Yuzu-Omakase-2.jpg', 'alt' => __('Món ăn tại Yuzu Omakase', 'saigonhoreca'), 'class' => ''],
  ['src' => '2024/01/Yuzu-Omakase-3-1.jpg', 'alt' => __('Chi tiết món omakase', 'saigonhoreca'), 'class' => ''],
  ['src' => '2024/01/Yuzu-Omakase-4.jpg', 'alt' => __('Không gian trưng bày tại Yuzu Omakase', 'saigonhoreca'), 'class' => ''],
  ['src' => '2024/01/Yuzu-Omakase-5.jpg', 'alt' => __('Sản phẩm cao cấp của Yuzu Omakase', 'saigonhoreca'), 'class' => ''],
  ['src' => '2024/01/Yuzu-Omakase-6.jpg', 'alt' => __('Không gian bàn ăn Yuzu Omakase', 'saigonhoreca'), 'class' => 'pp-intro-yzo__gallery-item--wide'],
];
?>
<section class="pp__section pp__section--alt pp-intro-yzo">
  <div class="pp__container">
    <div class="pp-intro-yzo__header">
      <div class="pp-intro-yzo__title-block">
        <span class="pp-text-yzo__divider" aria-hidden="true"></span>
        <span class="pp-intro-yzo__eyebrow">Restaurant profile</span>
        <h2 class="pp-text-yzo__title">Yuzu Omakase</h2>
        <p class="pp-intro-yzo__lead"><?php echo esc_html__('Không gian fine dining nổi bật tại trung tâm TP.HCM, nơi trải nghiệm omakase đòi hỏi một hệ bếp chính xác, sạch và vận hành ổn định.', 'saigonhoreca'); ?></p>
      </div>

      <div class="pp-intro-yzo__story">
        <p><?php echo esc_html__('Yuzu Omakase Vietnam tại 34 Thủ Khoa Huân là điểm đến ẩm thực mang tinh thần Nhật hiện đại, được phát triển bởi YUZU GROUP và gắn với danh mục fine dining được Hungry Hub ghi nhận tại Thái Lan.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Saigon Horeca đồng hành trong việc tổ chức không gian bếp, lựa chọn thiết bị thép không gỉ và tinh chỉnh luồng vận hành để hỗ trợ đầu bếp phục vụ các trải nghiệm omakase có nhịp độ cao.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <div class="pp-intro-yzo__stats" aria-label="<?php echo esc_attr__('Thông tin nổi bật', 'saigonhoreca'); ?>">
      <?php foreach ($stats as $item) : ?>
        <div class="pp-intro-yzo__stat">
          <span><?php echo esc_html($item['label']); ?></span>
          <strong><?php echo esc_html($item['value']); ?></strong>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="pp-intro-yzo__gallery" aria-label="<?php echo esc_attr__('Hình ảnh Yuzu Omakase', 'saigonhoreca'); ?>">
      <?php foreach ($gallery as $image) : ?>
        <figure class="pp-intro-yzo__gallery-item <?php echo esc_attr($image['class']); ?>">
          <img src="<?php echo esc_url(sgh_img($image['src'])); ?>" alt="<?php echo esc_attr($image['alt']); ?>" loading="lazy" decoding="async">
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
