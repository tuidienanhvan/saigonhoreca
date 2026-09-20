<?php
/**
 * Contact Page Section — Map & Company Info
 *
 * Dark luxe: 2-col split — left text + image, right Google Maps embed.
 * BEM: sh-contact-map
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri  = get_template_directory_uri();
$lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';

$map_eyebrows = [
    'vi' => 'Bản đồ đường đi',
    'en' => 'Location Map',
    'ja' => 'アクセスマップ',
];

$map_titles = [
    'vi' => 'Công ty TNHH Sài Gòn Horeca',
    'en' => 'SAIGON HORECA CO., LTD',
    'ja' => 'サイゴンホレカ株式会社',
];

$products_url = function_exists('sgh_url') ? sgh_url('products_index') : home_url('/san-pham/');

$map_bodies = [
    'vi' => 'Saigon Horeca tự hào là đơn vị hàng đầu chuyên cung cấp và phân phối <a href="' . esc_url($products_url) . '">thiết bị bếp công nghiệp</a> chuyên nghiệp cùng các sản phẩm phục vụ ngành ẩm thực, quầy bar và nhà hàng — cam kết giải pháp toàn diện từ tư vấn đến triển khai thi công.',
    'en' => 'Saigon Horeca is a leading provider of professional <a href="' . esc_url($products_url) . '">commercial kitchen equipment</a> and bar solutions for the hospitality industry — committed to turnkey delivery from concept to installation.',
    'ja' => 'Saigon Horeca（サイゴンホレカ）は、ベトナムにおける高品質な<a href="' . esc_url($products_url) . '">業務用厨房機器</a>およびバー設備のリーディングカンパニーです。設計提案から機器選定、施工まで一貫したトータルソリューションをお届けします。',
];
?>
<section class="sh-contact-map" aria-label="<?php echo esc_attr($map_eyebrows[$lang] ?? $map_eyebrows['vi']); ?>">
    <div class="sh-contact-map__inner">

        <!-- Left: Info & Image -->
        <div class="sh-contact-map__info">
            <div class="sh-contact-map__info-text">
                <span class="sh-contact-map__eyebrow"><?php echo esc_html($map_eyebrows[$lang] ?? $map_eyebrows['vi']); ?></span>
                <h2 class="sh-contact-map__title"><?php echo esc_html($map_titles[$lang] ?? $map_titles['vi']); ?></h2>
                <p class="sh-contact-map__body">
                    <?php echo wp_kses_post($map_bodies[$lang] ?? $map_bodies['vi']); ?>
                </p>
            </div>
            <div class="sh-contact-map__img-wrap">
                <!-- Corner technical markers -->
                <span class="sh-contact-map__marker sh-contact-map__marker--tl">+</span>
                <span class="sh-contact-map__marker sh-contact-map__marker--tr">+</span>
                <span class="sh-contact-map__marker sh-contact-map__marker--bl">+</span>
                <span class="sh-contact-map__marker sh-contact-map__marker--br">+</span>
                
                <img
                    src="<?php echo esc_url(sgh_img('saigonhoreca/bia-bep-cong-nghiep-inox.jpg')); ?>"
                    alt="<?php echo esc_attr($map_titles[$lang] ?? $map_titles['vi']); ?>"
                    loading="lazy"
                    width="640"
                    height="360"
                />
            </div>
        </div>

        <!-- Right: Map -->
        <div class="sh-contact-map__embed-wrap">
            <!-- Corner technical markers -->
            <span class="sh-contact-map__marker sh-contact-map__marker--tl">+</span>
            <span class="sh-contact-map__marker sh-contact-map__marker--tr">+</span>
            <span class="sh-contact-map__marker sh-contact-map__marker--bl">+</span>
            <span class="sh-contact-map__marker sh-contact-map__marker--br">+</span>
            
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.416034323343!2d106.7909441761891!3d10.779413659136281!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3175252a29e0e1dd%3A0x48d9232be13376!2zQ8OUTkcgVFkgVE5ISCBTw4BJIEfDkk4gSE9SRUNB!5e0!3m2!1sen!2s!4v1685068576551!5m2!1sen!2s"
                title="<?php echo esc_attr($map_titles[$lang] ?? $map_titles['vi']); ?>"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

    </div>
</section>

