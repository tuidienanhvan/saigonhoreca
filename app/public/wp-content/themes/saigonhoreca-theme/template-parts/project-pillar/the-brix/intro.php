<?php
/**
 * Project Pillar — the-brix
 * Section #2: intro
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-intro-brix">
  <div class="pp-container-shared">
    <div class="pp-intro-brix__grid scroll-reveal">

      <div class="pp-intro-brix__body">
        <div class="pp-intro-brix__badge">The Brix</div>
        <h2 class="pp-intro-brix__title"><?php echo esc_html__('Ốc đảo nhiệt đới giữa lòng Sài Gòn', 'saigonhoreca'); ?></h2>
        <div class="pp-intro-brix__divider" aria-hidden="true"></div>
        <div class="pp-intro-brix__text">
          <p><?php echo esc_html__('Nếu cảm thấy quá ngột ngạt trước nhịp sống xô bồ của phố thị Sài Gòn và muốn tìm một nơi mát mẻ, trong lành để thư giãn, dùng bữa hoặc trò chuyện cùng bạn bè, bạn hãy đến với The Brix.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Tọa lạc tại trung tâm Quận 2, nhà hàng The Brix được thiết kế như một hòn đảo nghỉ dưỡng nhiệt đới. Nơi đây sở hữu hồ bơi mát mẻ cùng khu rừng thu nhỏ xanh mướt tràn đầy sức sống.', 'saigonhoreca'); ?></p>
        </div>
      </div>

      <div class="pp-intro-brix__side">
        <div class="pp-image-container-shared pp-intro-brix__image-container">
          <div class="pp-intro-brix__image-tag">Resort Living</div>
          <img src="<?php echo sgh_img('the-brix/the-brix-khong-gian-am-thuc-len-den-dem.jpg'); ?>" alt="<?php echo esc_attr__('Khu lounge ngoài trời The Brix với ô dù và sofa bên hồ bơi lúc chạng vạng', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Khu lounge ngoài trời lúc chạng vạng với hệ ô dù canvas mềm mại và ghế sofa thấp bên hồ bơi, ánh đèn ấm vàng cùng rặng cây nhiệt đới rợp bóng kiến tạo trải nghiệm nghỉ dưỡng thư thái.', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
