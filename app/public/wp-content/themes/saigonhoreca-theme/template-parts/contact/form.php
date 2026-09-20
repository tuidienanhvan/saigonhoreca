<?php
/**
 * Contact Page Section — Form & Details
 *
 * Dark luxe: left col = thông tin liên hệ + social links (Facebook, Zalo, Instagram, YouTube).
 * Right col = WPForms shortcode hoặc fallback native form.
 * BEM: sh-contact-form
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri     = get_template_directory_uri();
$contact = function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : [];
$lang    = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';

$form_subtitles = [
    'vi' => 'Nếu bạn có bất cứ câu hỏi hay cần tư vấn thiết kế bếp nhà hàng, bếp công nghiệp — hãy điền form dưới đây, chúng tôi sẽ phản hồi trong vòng 24h.',
    'en' => 'If you have any questions or require consultation on commercial kitchen design, please complete the form below. Our specialists will respond within 24 hours.',
    'ja' => '業務用厨房機器やレストラン厨房設計のご相談・お見積もりなど、お気軽にお問い合わせください。専門スタッフが24時間以内にご案内いたします。',
];

$name_placeholders = [
    'vi' => 'Nguyễn Văn A',
    'en' => 'John Doe',
    'ja' => '山田 太郎',
];

$email_placeholders = [
    'vi' => 'email@domain.vn',
    'en' => 'contact@example.com',
    'ja' => 'contact@example.com',
];

$message_placeholders = [
    'vi' => 'Tôi cần tư vấn thiết kế bếp nhà hàng...',
    'en' => 'I would like consultation for commercial kitchen design...',
    'ja' => '飲食店厨房設計の相談を希望します...',
];

$hotline_raw = $contact['hotline_raw'] ?? '0901304365';
$hotline_display = $contact['hotline'] ?? '0901 304 365';
$hotline_alt_raw = $contact['hotline_alt_raw'] ?? '0909040920';
$hotline_alt_display = $contact['hotline_alt'] ?? '0909 040 920';
?>
<section class="sh-contact-form" aria-label="<?php echo ($lang === 'ja') ? 'お問い合わせフォームと企業情報' : (($lang === 'en') ? 'Contact Form & Information' : 'Form liên hệ và thông tin'); ?>">
    <div class="sh-contact-form__inner">

        <!-- Left: Contact Info -->
        <aside class="sh-contact-form__info">
            <div class="sh-contact-form__brand">
                <h2 class="sh-contact-form__brand-name"><?php echo esc_html($contact['company_full'] ?? 'SAIGON HORECA'); ?></h2>
                <div class="sh-contact-form__rule"></div>
            </div>

            <ul class="sh-contact-form__details">
                <li class="sh-contact-form__detail-item">
                    <span class="sh-contact-form__detail-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </span>
                    <span><?php echo esc_html($contact['address'] ?? 'Số 40, đường số 6, KDC Melosa Khang Điền, Long Trường, TP.HCM'); ?></span>
                </li>
                <li class="sh-contact-form__detail-item">
                    <span class="sh-contact-form__detail-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.06 6.06l.94-.93a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </span>
                    <span class="sh-contact-form__detail-lines">
                        <a href="tel:<?php echo esc_attr($hotline_raw); ?>">+84 <?php echo esc_html(ltrim($hotline_display, '0')); ?></a>
                        <?php if (!empty($hotline_alt_display)): ?>
                        <a href="tel:<?php echo esc_attr($hotline_alt_raw); ?>" class="sh-contact-form__detail-secondary">+84 <?php echo esc_html(ltrim($hotline_alt_display, '0')); ?></a>
                        <?php endif; ?>
                    </span>
                </li>
                <li class="sh-contact-form__detail-item">
                    <span class="sh-contact-form__detail-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </span>
                    <span class="sh-contact-form__detail-lines">
                        <a href="mailto:<?php echo esc_attr($contact['email_primary'] ?? 'contact@saigonhoreca.com'); ?>"><?php echo esc_html($contact['email_primary'] ?? 'contact@saigonhoreca.com'); ?></a>
                        <?php if (!empty($contact['email_secondary'])): ?>
                        <a href="mailto:<?php echo esc_attr($contact['email_secondary']); ?>" class="sh-contact-form__detail-secondary"><?php echo esc_html($contact['email_secondary']); ?></a>
                        <?php endif; ?>
                    </span>
                </li>
            </ul>

            <div class="sh-contact-form__social">
                <h3 class="sh-contact-form__social-label"><?php echo ($lang === 'ja') ? '公式SNS・お問い合わせ' : (($lang === 'en') ? 'Connect With Us' : 'Kết nối với chúng tôi'); ?></h3>
                <div class="sh-contact-form__social-links">
                    <?php if (!empty($contact['facebook'])): ?>
                    <a class="sh-contact-form__social-btn sh-contact-form__social-btn--facebook" href="<?php echo esc_url($contact['facebook']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Facebook" data-gtm-click="click_facebook" data-gtm-location="contact_social">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="#ffffff" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['zalo'])): ?>
                    <a class="sh-contact-form__social-btn sh-contact-form__social-btn--zalo" href="<?php echo esc_url($contact['zalo']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo ($lang === 'ja') ? 'Zaloでチャット' : (($lang === 'en') ? 'Chat via Zalo' : 'Chat qua Zalo'); ?>" title="Zalo" data-gtm-click="click_zalo" data-gtm-location="contact_social">
                        <svg viewBox="0 0 50 50" width="24" height="24" fill="none" aria-hidden="true">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.779 43.5892C10.1019 43.846 13.0061 43.1836 15.0682 42.1825C24.0225 47.1318 38.0197 46.8954 46.4923 41.4732C46.8209 40.9803 47.1279 40.4677 47.4128 39.9363C49.1062 36.7779 50.0004 33.22 50.0004 27.1316V22.7175C50.0004 16.629 49.1062 13.0711 47.4128 9.91273C45.7385 6.75436 43.2461 4.28093 40.0877 2.58758C36.9293 0.894239 33.3714 0 27.283 0H22.8499C17.6644 0 14.2982 0.652754 11.4699 1.89893C11.3153 2.03737 11.1636 2.17818 11.0151 2.32135C2.71734 10.3203 2.08658 27.6593 9.12279 37.0782C9.13064 37.0921 9.13933 37.1061 9.14889 37.1203C10.2334 38.7185 9.18694 41.5154 7.55068 43.1516C7.28431 43.399 7.37944 43.5512 7.779 43.5892Z" fill="#ffffff"/>
                            <path d="M20.5632 17H10.8382V19.0853H17.5869L10.9329 27.3317C10.7244 27.635 10.5728 27.9194 10.5728 28.5639V29.0947H19.748C20.203 29.0947 20.5822 28.7156 20.5822 28.2606V27.1421H13.4922L19.748 19.2938C19.8428 19.1801 20.0134 18.9716 20.0893 18.8768L20.1272 18.8199C20.4874 18.2891 20.5632 17.8341 20.5632 17.2844V17Z" fill="#0068FF"/>
                            <path d="M32.9416 29.0947H34.3255V17H32.2402V28.3933C32.2402 28.7725 32.5435 29.0947 32.9416 29.0947Z" fill="#0068FF"/>
                            <path d="M25.814 19.6924C23.1979 19.6924 21.0747 21.8156 21.0747 24.4317C21.0747 27.0478 23.1979 29.171 25.814 29.171C28.4301 29.171 30.5533 27.0478 30.5533 24.4317C30.5723 21.8156 28.4491 19.6924 25.814 19.6924ZM25.814 27.2184C24.2785 27.2184 23.0273 25.9672 23.0273 24.4317C23.0273 22.8962 24.2785 21.645 25.814 21.645C27.3495 21.645 28.6007 22.8962 28.6007 24.4317C28.6007 25.9672 27.3685 27.2184 25.814 27.2184Z" fill="#0068FF"/>
                            <path d="M40.4867 19.6162C37.8516 19.6162 35.7095 21.7584 35.7095 24.3934C35.7095 27.0285 37.8516 29.1707 40.4867 29.1707C43.1217 29.1707 45.2639 27.0285 45.2639 24.3934C45.2639 21.7584 43.1217 19.6162 40.4867 19.6162ZM40.4867 27.2181C38.9322 27.2181 37.681 25.9669 37.681 24.4124C37.681 22.8579 38.9322 21.6067 40.4867 21.6067C42.0412 21.6067 43.2924 22.8579 43.2924 24.4124C43.2924 25.9669 42.0412 27.2181 40.4867 27.2181Z" fill="#0068FF"/>
                            <path d="M29.4562 29.0944H30.5747V19.957H28.6221V28.2793C28.6221 28.7153 29.0012 29.0944 29.4562 29.0944Z" fill="#0068FF"/>
                        </svg>
                    </a>
                    <?php endif; ?>
                    <a class="sh-contact-form__social-btn sh-contact-form__social-btn--instagram" href="<?php echo esc_url($contact['instagram'] ?? 'https://www.instagram.com/saigonhoreca/'); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram" data-gtm-click="click_instagram" data-gtm-location="contact_social">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                        </svg>
                    </a>
                    <?php if (!empty($contact['youtube'])): ?>
                    <a class="sh-contact-form__social-btn sh-contact-form__social-btn--youtube" href="<?php echo esc_url($contact['youtube']); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube" title="YouTube" data-gtm-click="click_youtube" data-gtm-location="contact_social">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="#ffffff" aria-hidden="true">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </aside>

        <!-- Right: Form -->
        <div class="sh-contact-form__form-col">
            <div class="sh-contact-form__form-header">
                <h2 class="sh-contact-form__form-title"><?php echo ($lang === 'ja') ? 'お問い合わせください' : (($lang === 'en') ? 'Get in Touch with Us' : 'Hãy liên hệ với chúng tôi'); ?></h2>
                <p class="sh-contact-form__form-sub">
                    <?php echo esc_html($form_subtitles[$lang] ?? $form_subtitles['vi']); ?>
                </p>
            </div>

            <div class="sh-contact-form__wpforms-wrap">
                <?php
                if (shortcode_exists('wpforms')) {
                    echo do_shortcode('[wpforms id="10964"]');
                } else {
                    // Fallback native form — styled dark luxe
                    ?>
                    <form id="saigonhouse-contact-form" action="<?php echo esc_url(function_exists('sgh_url') ? sgh_url('contact') : home_url('/contact/')); ?>" method="post" class="sh-contact-form__native">
                        <div class="sh-contact-form__field">
                            <label class="sh-contact-form__label" for="cf-name"><?php echo ($lang === 'ja') ? '氏名' : (($lang === 'en') ? 'Full Name' : 'Họ và tên'); ?> <span aria-hidden="true">*</span></label>
                            <input class="sh-contact-form__input" type="text" id="cf-name" name="contact_name" required placeholder="<?php echo esc_attr($name_placeholders[$lang] ?? $name_placeholders['vi']); ?>">
                        </div>
                        <div class="sh-contact-form__field">
                            <label class="sh-contact-form__label" for="cf-phone"><?php echo ($lang === 'ja') ? '電話番号' : (($lang === 'en') ? 'Phone Number' : 'Số điện thoại'); ?></label>
                            <input class="sh-contact-form__input" type="tel" id="cf-phone" name="contact_phone" placeholder="0901 304 365">
                        </div>
                        <div class="sh-contact-form__field">
                            <label class="sh-contact-form__label" for="cf-email"><?php echo ($lang === 'ja') ? 'メールアドレス' : (($lang === 'en') ? 'Email Address' : 'Email'); ?> <span aria-hidden="true">*</span></label>
                            <input class="sh-contact-form__input" type="email" id="cf-email" name="contact_email" required placeholder="<?php echo esc_attr($email_placeholders[$lang] ?? $email_placeholders['vi']); ?>">
                        </div>
                        <div class="sh-contact-form__field sh-contact-form__field--full">
                            <label class="sh-contact-form__label" for="cf-message"><?php echo ($lang === 'ja') ? '内容' : (($lang === 'en') ? 'Message' : 'Nội dung'); ?></label>
                            <textarea class="sh-contact-form__input sh-contact-form__input--textarea" id="cf-message" name="contact_message" rows="5" placeholder="<?php echo esc_attr($message_placeholders[$lang] ?? $message_placeholders['vi']); ?>"></textarea>
                        </div>
                        <div class="sh-contact-form__field sh-contact-form__field--full">
                            <button class="sh-contact-form__submit" type="submit"><span><?php echo ($lang === 'ja') ? '送信する' : (($lang === 'en') ? 'Send Inquiry' : 'Gửi liên hệ'); ?></span></button>
                        </div>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>

    </div>
</section>
