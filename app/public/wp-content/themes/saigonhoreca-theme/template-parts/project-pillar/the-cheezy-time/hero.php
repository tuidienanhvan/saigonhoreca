<?php
/**
 * Project Pillar — the-cheezy-time
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-tct">
    <div class="pp-hero-tct__media" style="background-image:url('<?php echo sgh_img('2026/03/du-an-thecheezytime.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero-tct__overlay" aria-hidden="true"></div>
    <div class="pp-hero-tct__content">
        <h1 class="pp-hero-tct__title"><?php echo esc_html__('The Cheezy Time', 'saigonhoreca'); ?></h1>
        <p class="pp-hero-tct__subhead"><?php echo esc_html__('Khi cảm hứng chợ nổi trở thành linh hồn của một công trình', 'saigonhoreca'); ?></p>
        <p class="pp-hero-tct__subtitle"><?php echo esc_html__('Giữa lòng Cần Thơ – vùng đất đang chuyển mình mạnh mẽ – The Cheezy Time chọn một con đường rất riêng. Không chạy theo sự hào nhoáng, nhà hàng lấy cảm hứng từ chợ nổi miền Tây – nơi mọi giao thương, câu chuyện và kết nối đều bắt đầu từ dòng nước.', 'saigonhoreca'); ?></p>
    </div>
</section>
