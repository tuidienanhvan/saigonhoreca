<?php
/**
 * Contact Page Section — Hero
 *
 * Dark luxe compact hero với BEM sh-contact-hero.
 * Giữ cùng pattern với archive-product hero compact.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri    = get_template_directory_uri();
$bg_url = sgh_img('saigonhoreca/Saigon-Horeca-thiet-bi-bep-cong-nghiep.webp');
$lang   = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';

$hero_subs = [
    'vi' => 'Chúng tôi luôn sẵn sàng lắng nghe và tư vấn giải pháp tối ưu nhất cho gian bếp của bạn.',
    'en' => 'We are always ready to listen and provide the optimal equipment solutions for your kitchen.',
    'ja' => 'お客様の厨房に最適なソリューションと機器をご提案いたします。お気軽にご相談ください。',
];
?>
<section class="sh-contact-hero" aria-label="<?php echo ($lang === 'ja') ? 'Saigon Horecaへのお問い合わせ' : (($lang === 'en') ? 'Contact Saigon Horeca' : 'Liên hệ Saigon Horeca'); ?>">
    <div class="sh-contact-hero__bg" style="background-image:url('<?php echo esc_url($bg_url); ?>')"></div>
    <div class="sh-contact-hero__overlay"></div>
    <div class="sh-contact-hero__content">
        <span class="sh-contact-hero__badge"><?php echo ($lang === 'ja') ? 'お問い合わせ' : (($lang === 'en') ? 'Contact Us' : 'Liên hệ chúng tôi'); ?></span>
        <h1 class="sh-contact-hero__title"><?php echo ($lang === 'ja') ? 'Saigon Horecaへのお問い合わせ' : (($lang === 'en') ? 'Contact Saigon Horeca' : 'Liên hệ Saigon Horeca'); ?></h1>
        <p class="sh-contact-hero__sub">
            <?php echo esc_html($hero_subs[$lang] ?? $hero_subs['vi']); ?>
        </p>
    </div>
</section>

