<?php
/**
 * Project Pillar — grand-marble-thuong-hieu-banh-cao-cap-nhat-ban
 * Section #3: concept — editorial layout, Danish Marble craft + signature flavours.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-gmarb pp-section-gmarb--alt pp-concept-gmarb scroll-reveal">
  <div class="pp-gmarb-glow pp-gmarb-glow--bl" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-concept-gmarb__layout">
      <div class="pp-concept-gmarb__visual scroll-reveal reveal-left">
        <div class="pp-image-container-shared pp-frame-gmarb pp-frame-gmarb--editorial">
          <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-danish-marble.jpg'); ?>" alt="<?php echo esc_attr__('Bánh Danish Marble Grand Marble với hoa văn vân đá đặc trưng', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="800" height="600">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Chiếc Danish Marble đầu tiên ra đời trên phố Gionmachi-minamigawa, kết tinh hương vị quê hương và biến thể Tây phương.', 'saigonhoreca'); ?></div>
        </div>
      </div>
      <div class="pp-concept-gmarb__glass-card scroll-reveal reveal-right delay-150">
        <span class="pp-concept-gmarb__tag"><?php echo esc_html__('Signature flavours', 'saigonhoreca'); ?></span>
        <div class="pp-gmarb-ornament pp-gmarb-ornament--right" aria-hidden="true"></div>
        <div class="pp-text-gmarb__divider pp-text-gmarb__divider--dots" aria-hidden="true"></div>
        <h2 class="pp-text-gmarb__title"><?php echo esc_html__('Kiệt Tác Danish Marble Của Kyoto', 'saigonhoreca'); ?></h2>
        <div class="pp-text-gmarb__body pp-text-gmarb__body--dropcap">
          <p><?php echo esc_html__('Trên con phố Gionmachi-minamigawa, Higashiyama-ku, Kyoto, giữa lòng thành phố cổ nghìn năm, bằng đôi bàn tay tài hoa và óc sáng tạo của những nghệ nhân bánh ngọt đã tạo nên một kiệt tác: chiếc bánh Danish Marble đầu tiên. Họ khéo léo kết hợp hương vị đậm chất quê hương với các biến thể Tây phương xa xôi, mở ra một chương mới trong nghệ thuật làm bánh Kyoto.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Từ mùi thơm đến hương vị, chiếc bánh ngọt này được đánh giá cao, đặc biệt là bởi các họa tiết hoa văn trông giống như đá marble được tạo ra bởi tài năng và tận tụy của những nghệ nhân làm bánh.', 'saigonhoreca'); ?></p>
        </div>
        <div class="pp-concept-gmarb__signals" aria-label="<?php echo esc_attr__('Các hương vị tiêu biểu của Grand Marble', 'saigonhoreca'); ?>">
          <span><?php echo esc_html__('Kyoto Sansyoku', 'saigonhoreca'); ?></span>
          <span><?php echo esc_html__('Maple Caramel', 'saigonhoreca'); ?></span>
          <span><?php echo esc_html__('Gion Tsujiri Matcha An', 'saigonhoreca'); ?></span>
        </div>
      </div>
    </div>
  </div>
</section>
