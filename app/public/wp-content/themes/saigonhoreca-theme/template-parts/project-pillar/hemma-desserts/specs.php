<?php
/**
 * Project Pillar — hemma-desserts
 * Section #5: specs — "Thẩm mỹ và trải nghiệm" + value cards + final pair gallery.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$hmd_values = array(
  array(
    'icon'  => '<path d="M12 2 4 6v6c0 5 3.5 8 8 10 4.5-2 8-5 8-10V6z" stroke-linecap="round" stroke-linejoin="round"/>',
    'title' => 'Nhỏ gọn – tinh tế – ấm áp',
    'desc'  => 'Saigon Horeca giữ trọn tinh thần thương hiệu Hemma Desserts trong từng đường nét thiết bị.',
  ),
  array(
    'icon'  => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 9h18M9 21V9" stroke-linecap="round"/>',
    'title' => 'Inox bo tròn mềm mại',
    'desc'  => 'Thiết bị inox vốn mang vẻ "công nghiệp" được bo tròn góc cạnh, đồng điệu với không gian quán.',
  ),
  array(
    'icon'  => '<path d="M4 20V10M12 20V4M20 20v-7" stroke-linecap="round"/>',
    'title' => 'Đi dây – thoát sàn ẩn khéo',
    'desc'  => 'Toàn bộ dây điện, ống nước và thoát sàn được ẩn khéo léo, giữ mặt sàn gọn gàng, sạch sẽ.',
  ),
);

$hmd_specs_gallery = array(
  array('img' => 'hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien/hemma-desserts-11-1.jpg', 'cap' => 'Cụm máy pha espresso La Carimali và máy xay cà phê – trái tim của quầy bar Ý trong dự án'),
  array('img' => 'hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien/hemma-desserts-12-1.jpg', 'cap' => 'Cận cảnh khay hứng lưới inox của máy pha espresso trên mặt bàn inox – đường nét gia công tinh xảo'),
);
?>
<section class="pp-section-hmd pp-hmd-specs">
  <div class="pp-hmd-watermark" aria-hidden="true">Hemma</div>

  <div class="pp-container-shared">
    <div class="pp-text-hmd pp-text-hmd--center">
      <span class="pp-text-hmd__divider pp-text-hmd__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-hmd__title"><?php echo esc_html__('Thẩm mỹ và trải nghiệm đến từ giải pháp của chuyên gia', 'saigonhoreca'); ?></h2>
      <div class="pp-text-hmd__body">
        <p><?php echo esc_html__('Một trong những điểm đáng tự hào của dự án chính là cách Saigon Horeca giữ trọn tinh thần của thương hiệu Hemma Desserts: nhỏ gọn, tinh tế và ấm áp.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Nhờ vậy, khi bước vào Hemma, khách hàng không chỉ thấy một quầy bánh đẹp – mà còn cảm nhận sự chuyên nghiệp ẩn sau từng thao tác nhỏ của đầu bếp.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <div class="pp-hmd-values">
      <?php foreach ($hmd_values as $v) : ?>
        <article class="pp-hmd-value-card">
          <span class="pp-hmd-value-card__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><?php echo $v['icon']; ?></svg>
          </span>
          <h3 class="pp-hmd-value-card__title"><?php echo esc_html__($v['title'], 'saigonhoreca'); ?></h3>
          <p class="pp-hmd-value-card__desc"><?php echo esc_html__($v['desc'], 'saigonhoreca'); ?></p>
        </article>
      <?php endforeach; ?>
    </div>

    <blockquote class="pp-hmd-quote pp-hmd-quote--solo">
      <p><?php echo esc_html__('"Không gian dù nhỏ, hiệu suất vẫn phải lớn."', 'saigonhoreca'); ?></p>
      <cite class="pp-hmd-quote__cite"><?php echo esc_html__('Triết lý Saigon Horeca', 'saigonhoreca'); ?></cite>
    </blockquote>

    <div class="pp-text-hmd pp-text-hmd--center">
      <div class="pp-text-hmd__body">
        <p><?php echo esc_html__('Bằng sự kết hợp giữa thiết kế tinh gọn – kỹ thuật thi công chuẩn xác – và hiểu biết sâu sắc về vận hành thực tế, Saigon Horeca đã giúp Hemma Desserts khai thác trọn vẹn tiềm năng của không gian, biến mọi giới hạn thành cơ hội.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <div class="pp-gallery-hmd pp-gallery-hmd--cols-2">
      <?php foreach ($hmd_specs_gallery as $g) : ?>
        <figure class="pp-gallery-hmd__item pp-image-container-shared">
          <img src="<?php echo sgh_img($g['img']); ?>" alt="<?php echo esc_attr($g['cap']); ?>" loading="lazy" decoding="async" width="1366" height="1024">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__($g['cap'], 'saigonhoreca'); ?></figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
