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
                    <a class="sh-contact-form__social-btn" href="<?php echo esc_url($contact['facebook']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($contact['zalo'])): ?>
                    <a class="sh-contact-form__social-btn sh-contact-form__social-btn--zalo" href="<?php echo esc_url($contact['zalo']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo ($lang === 'ja') ? 'Zaloでチャット' : (($lang === 'en') ? 'Chat via Zalo' : 'Chat qua Zalo'); ?>" title="Zalo">
                        <img src="<?php echo esc_url($uri . '/assets/images/zalo-icon.webp'); ?>" alt="Zalo" width="22" height="22" loading="lazy" decoding="async">
                    </a>
                    <?php endif; ?>
                    <a class="sh-contact-form__social-btn" href="https://www.instagram.com/saigonhoreca/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <?php if (!empty($contact['youtube'])): ?>
                    <a class="sh-contact-form__social-btn" href="<?php echo esc_url($contact['youtube']); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75,15.02 15.5,12 9.75,8.98 9.75,15.02" fill="#0a0a0a"/></svg>
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
