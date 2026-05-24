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
$uri = get_template_directory_uri();
?>
<section class="sh-contact-map" aria-label="<?php esc_attr_e('Địa chỉ và bản đồ', 'saigonhoreca'); ?>">
    <div class="sh-contact-map__inner">

        <!-- Left: Info & Image -->
        <div class="sh-contact-map__info">
            <div class="sh-contact-map__info-text">
                <span class="sh-contact-map__eyebrow"><?php esc_html_e('Bản đồ đường đi', 'saigonhoreca'); ?></span>
                <h2 class="sh-contact-map__title"><?php esc_html_e('Công ty TNHH Sài Gòn Horeca', 'saigonhoreca'); ?></h2>
                <p class="sh-contact-map__body">
                    Saigon Horeca tự hào là đơn vị hàng đầu chuyên cung cấp và phân phối
                    <a href="<?php echo esc_url(home_url('/danh-muc-san-pham/thiet-bi-bep-cong-nghiep-sgh/')); ?>">thiết bị bếp công nghiệp</a>
                    chuyên nghiệp cùng các sản phẩm phục vụ ngành ẩm thực, quầy bar và nhà hàng — cam kết giải pháp toàn diện từ tư vấn đến triển khai thi công.
                </p>
            </div>
            <div class="sh-contact-map__img-wrap">
                <!-- Corner technical markers -->
                <span class="sh-contact-map__marker sh-contact-map__marker--tl">+</span>
                <span class="sh-contact-map__marker sh-contact-map__marker--tr">+</span>
                <span class="sh-contact-map__marker sh-contact-map__marker--bl">+</span>
                <span class="sh-contact-map__marker sh-contact-map__marker--br">+</span>
                
                <img
                    src="<?php echo esc_url(sgh_img('2023/12/bia-bep-cong-nghiep-inox.jpg')); ?>"
                    alt="Bếp công nghiệp inox Saigon Horeca"
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
                title="Bản đồ Công ty TNHH Sài Gòn Horeca"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

    </div>
</section>
