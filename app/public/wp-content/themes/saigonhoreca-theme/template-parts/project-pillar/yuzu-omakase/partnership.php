<?php
/**
 * Project Pillar — yuzu-omakase
 * Section #5: partnership — background section with premium brand integrations and CSS Parallax.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$brands = [
  ['name' => 'Hoshizaki', 'type' => esc_html__('Hệ thống lạnh (Nhật Bản)', 'saigonhoreca'), 'logo' => 'HOSHIZAKI'],
  ['name' => 'Winterhalter', 'type' => esc_html__('Máy rửa ly chén (Đức)', 'saigonhoreca'), 'logo' => 'WINTERHALTER'],
  ['name' => 'ATA', 'type' => esc_html__('Thiết bị bếp nóng (Ý)', 'saigonhoreca'), 'logo' => 'ATA ITALY'],
  ['name' => 'Berjaya', 'type' => esc_html__('Thiết bị bổ trợ (Malaysia)', 'saigonhoreca'), 'logo' => 'BERJAYA']
];
?>
<section class="pp-partnership-yzo">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt -->
  <div class="pp-partnership-yzo__bg" style="background-image:url('<?php echo esc_url(sgh_img('yuzu-omakase/yuzu-omakase-chan-dung-dau-bep-bieu-dien.webp')); ?>');"></div>
  <div class="pp-partnership-yzo__overlay" aria-hidden="true"></div>
  
  <div class="pp-container-shared">
    <div class="pp-partnership-yzo__wrap">
      
      <!-- Khối chữ giới thiệu dự án -->
      <div class="pp-partnership-yzo__content scroll-reveal">
        <span class="pp-intro-yzo__divider pp-text-yzo__divider--center" aria-hidden="true"></span>
        <span class="pp-partnership-yzo__eyebrow"><?php echo esc_html__('Operational Partnership', 'saigonhoreca'); ?></span>
        <h2 class="pp-partnership-yzo__title"><?php echo esc_html__('Sự Đồng Hành Của Saigon Horeca', 'saigonhoreca'); ?></h2>
        
        <div class="pp-partnership-yzo__body">
          <p><?php echo esc_html__('Giai đoạn lên ý tưởng ban đầu bao gồm các cuộc thảo luận chuyên sâu và khảo sát thực địa kỹ lưỡng để nắm vững triết lý phục vụ ẩm thực của Yuzu Omakase. Saigon Horeca đã hợp tác chặt chẽ với đội ngũ quản lý nhằm phát triển hệ bếp tối giản hiện đại.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Với kinh nghiệm và chuyên môn sâu rộng, chúng tôi đảm bảo khu bếp đáp ứng 100% tiêu chuẩn vệ sinh an toàn thực phẩm khắt khe nhất, kiến tạo một không gian truyền cảm hứng sáng tạo tối đa cho các bậc thầy đầu bếp Omakase biểu diễn.', 'saigonhoreca'); ?></p>
        </div>
      </div>

      <!-- Mảng lưới giới thiệu các thương hiệu thiết bị cao cấp tích hợp -->
      <div class="pp-partnership-yzo__brands-section scroll-reveal">
        <h3 class="pp-partnership-yzo__brands-title"><?php echo esc_html__('Hệ Thiết Bị Nhập Khẩu Đồng Bộ', 'saigonhoreca'); ?></h3>
        <div class="pp-partnership-yzo__brands-grid">
          <?php foreach ($brands as $brand): ?>
            <div class="pp-partnership-yzo__brand-card">
              <div class="pp-partnership-yzo__brand-logo" aria-hidden="true">
                <span><?php echo esc_html($brand['logo']); ?></span>
              </div>
              <h4 class="pp-partnership-yzo__brand-name"><?php echo esc_html($brand['name']); ?></h4>
              <p class="pp-partnership-yzo__brand-type"><?php echo esc_html($brand['type']); ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </div>
</section>
