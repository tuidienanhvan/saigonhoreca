<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #6: gallery + bàn giao hoàn thiện (merged related, T-034)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

/* Lưới đồng nhất: mọi tile cùng tỉ lệ 4/3 (đặt trong gallery.css) → lát phẳng, không khoảng trống đen. */
$swh_gallery = array(
  array('img' => 'spice-world-hotpot/Spice-World-Hot-Pot-01.jpg', 'cap' => __('Phòng VIP với bàn lẩu tròn âm bếp từ tích hợp', 'saigonhoreca')),
  array('img' => 'spice-world-hotpot/Spice-World-Hot-Pot-03.jpg', 'cap' => __('Khu dùng bữa với tường tranh dân gian rực rỡ', 'saigonhoreca')),
  array('img' => 'spice-world-hotpot/Spice-World-Hot-Pot-04.jpg', 'cap' => __('Hệ thống kệ inox công nghiệp tại khu lưu trữ', 'saigonhoreca')),
  array('img' => 'spice-world-hotpot/Spice-World-Hot-Pot-08.jpg', 'cap' => __('Máy thái thịt và tủ đông tại khu sơ chế', 'saigonhoreca')),
  array('img' => 'spice-world-hotpot/swh-thao-qua.jpeg', 'cap' => __('Thảo quả – gia vị tạo hương cho nước lẩu Tứ Xuyên', 'saigonhoreca')),
  array('img' => 'spice-world-hotpot/swh-tieu.jpeg', 'cap' => __('Tiêu Tứ Xuyên – linh hồn vị cay tê đặc trưng', 'saigonhoreca')),
);
?>
<section class="pp-swh-gallery">
  <div class="pp-swh-gallery__glow" aria-hidden="true"></div>
  <div class="pp-swh-gallery__watermark" aria-hidden="true">06</div>

  <div class="pp-container-shared">
    <header class="pp-swh-gallery__head">
      <div class="pp-swh-badge"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/></svg><?php echo esc_html__('Thư viện hình ảnh', 'saigonhoreca'); ?></div>
      <h2 class="pp-swh-gallery__title">
        <?php echo esc_html__('Không gian & Hệ thống bếp', 'saigonhoreca'); ?>
        <span class="pp-swh-gallery__title-gold">Spice World Hot Pot</span>
      </h2>
      <div class="pp-swh-divider pp-swh-divider--center" aria-hidden="true"></div>
      <p class="pp-swh-gallery__intro"><?php echo esc_html__('Khám phá không gian ẩm thực đẳng cấp và hệ thống bếp công nghiệp hiện đại tại Spice World Hotpot.', 'saigonhoreca'); ?></p>
    </header>

    <div class="pp-swh-gallery__mosaic">
      <?php foreach ($swh_gallery as $g) : ?>
        <figure class="pp-swh-media pp-image-container-shared pp-swh-gallery__item">
          <span class="pp-swh-gallery__corner" aria-hidden="true"></span>
          <img src="<?php echo esc_url(sgh_img($g['img'])); ?>" alt="<?php echo esc_attr($g['cap']); ?>" loading="lazy" decoding="async">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html($g['cap']); ?></figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php /* T-034: merged from related.php (handover / commitment) */ ?>
<section class="pp-swh-handover">
  <div class="pp-swh-handover__glow" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-swh-handover__grid">

      <div class="pp-swh-handover__main">
        <div class="pp-swh-glass-card pp-swh-handover__card">
          <header class="pp-swh-handover__header">
            <div class="pp-swh-badge"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/></svg><?php echo esc_html__('Hoàn thiện bàn giao', 'saigonhoreca'); ?></div>
            <h2 class="pp-swh-handover__title"><?php echo esc_html__('Cam kết chất lượng vững bền', 'saigonhoreca'); ?></h2>
            <div class="pp-swh-divider" aria-hidden="true"></div>
          </header>

          <div class="pp-swh-handover__body">
            <p><?php echo esc_html__('Để tạo ra những lát thịt cừu mảnh và những lát thịt bò được cuộn đẹp mắt, khu vực cắt thịt được trang bị một máy cắt thịt thương hiệu Berjaya và một tủ đông Hoshizaki, đảm bảo sự tươi mới của các miếng thịt được chọn lựa.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Được sự tin tưởng của Spice World Hot Pot Việt Nam, Saigon Horeca đã nỗ lực hết mình để hiện thực hóa từng chi tiết nhỏ nhất. Sự hài lòng tuyệt đối của đối tác chính là minh chứng rõ nét nhất cho hành trình 7 năm xây dựng và phát triển uy tín của chúng tôi.', 'saigonhoreca'); ?></p>
          </div>

          <blockquote class="pp-swh-handover__quote">
            <span class="pp-swh-handover__quote-line" aria-hidden="true"></span>
            <p><?php echo esc_html__('Khi đến với Saigon Horeca, khách hàng sẽ thực sự hài lòng với những gì chúng tôi nói và làm — bởi sự đáng tin cậy và chất lượng sản phẩm luôn là mục tiêu chúng tôi theo đuổi.', 'saigonhoreca'); ?></p>
          </blockquote>
        </div>
      </div>

      <div class="pp-swh-handover__side">
        <figure class="pp-swh-media pp-image-container-shared pp-swh-handover__figure">
          <span class="pp-swh-tag">EXECUTION</span>
          <img src="<?php echo esc_url(sgh_img('spice-world-hotpot/spice-world-hotpot-mat-tien-den-long-buoi-toi.jpg')); ?>" alt="<?php echo esc_attr__('Mặt tiền nhà hàng Spice World Hotpot hoàn thiện, sáng đèn về đêm', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Mặt tiền nhà hàng Spice World Hotpot hoàn thiện, sáng đèn đón khách trong ngày khai trương', 'saigonhoreca'); ?></figcaption>
        </figure>
      </div>

    </div>
  </div>
</section>
