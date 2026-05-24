<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-trd">
    <div class="pp-hero-trd__media" style="background-image:url('<?php echo sgh_img('2025/05/the-royal-sgh-2.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero-trd__overlay" aria-hidden="true"></div>
    <div class="pp-hero-trd__content">
        <h1 class="pp-hero-trd__title">THE ROYAL - ALL DAY DINING</h1>
        <h2 class="pp-hero-trd__subhead"><?php echo esc_html__('Sự giao thoa giữa tinh hoa phương Tây và tâm hồn Việt', 'saigonhoreca'); ?></h2>
        <p class="pp-hero-trd__subtitle"><em><?php echo esc_html__('Ngay từ ánh nhìn đầu tiên, The Royal All Day Dining toát lên khí chất sang trọng và đẳng cấp, như một biểu tượng của nghệ thuật kiến trúc tinh tế giữa lòng thành phố.', 'saigonhoreca'); ?></em></p>
    </div>
</section>
