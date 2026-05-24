<?php
/**
 * Contact Page Section — Form & Details
 *
 * Dark luxe: left col = thông tin liên hệ + social links.
 * Right col = WPForms shortcode hoặc fallback native form.
 * BEM: sh-contact-form
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sh-contact-form" aria-label="<?php esc_attr_e('Form liên hệ và thông tin', 'saigonhoreca'); ?>">
    <div class="sh-contact-form__inner">

        <!-- Left: Contact Info -->
        <aside class="sh-contact-form__info">
            <div class="sh-contact-form__brand">
                <h2 class="sh-contact-form__brand-name">Saigon Horeca</h2>
                <div class="sh-contact-form__rule"></div>
            </div>

            <ul class="sh-contact-form__details">
                <li class="sh-contact-form__detail-item">
                    <span class="sh-contact-form__detail-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </span>
                    <span>Số 40, đường số 6, KDS Melosa Khang Điền, Phú Hữu, TPHCM</span>
                </li>
                <li class="sh-contact-form__detail-item">
                    <span class="sh-contact-form__detail-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.06 6.06l.94-.93a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </span>
                    <a href="tel:+84901304365">+84 901 304 365</a>
                </li>
                <li class="sh-contact-form__detail-item">
                    <span class="sh-contact-form__detail-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </span>
                    <a href="mailto:contact@saigonhoreca.com">contact@saigonhoreca.com</a>
                </li>
            </ul>

            <div class="sh-contact-form__founder">
                <h3 class="sh-contact-form__founder-label"><?php esc_html_e('Nhà sáng lập', 'saigonhoreca'); ?></h3>
                <p class="sh-contact-form__founder-name">Dương Văn Giáp</p>
                <ul class="sh-contact-form__founder-contacts">
                    <li><a href="mailto:admin@saigonhoreca.com">admin@saigonhoreca.com</a></li>
                </ul>
            </div>

            <div class="sh-contact-form__social">
                <h4 class="sh-contact-form__social-label"><?php esc_html_e('Kết nối với chúng tôi', 'saigonhoreca'); ?></h4>
                <div class="sh-contact-form__social-links">
                    <a class="sh-contact-form__social-btn" href="https://www.facebook.com/saigonhoreca" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a class="sh-contact-form__social-btn" href="https://www.instagram.com/saigonhoreca/" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <a class="sh-contact-form__social-btn" href="https://www.youtube.com/@saigonhoreca7167" target="_blank" rel="noopener" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75,15.02 15.5,12 9.75,8.98 9.75,15.02" fill="#0a0a0a"/></svg>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Right: Form -->
        <div class="sh-contact-form__form-col">
            <div class="sh-contact-form__form-header">
                <h2 class="sh-contact-form__form-title"><?php esc_html_e('Hãy liên hệ với chúng tôi', 'saigonhoreca'); ?></h2>
                <p class="sh-contact-form__form-sub">
                    Nếu bạn có bất cứ câu hỏi hay cần tư vấn thiết kế bếp nhà hàng, bếp công nghiệp — hãy điền form dưới đây, chúng tôi sẽ phản hồi trong vòng 24h.
                </p>
            </div>

            <div class="sh-contact-form__wpforms-wrap">
                <?php
                if (shortcode_exists('wpforms')) {
                    echo do_shortcode('[wpforms id="10964"]');
                } else {
                    // Fallback native form — styled dark luxe
                    ?>
                    <form action="<?php echo esc_url(sgh_url('contact')); ?>" method="post" class="sh-contact-form__native">
                        <div class="sh-contact-form__field">
                            <label class="sh-contact-form__label" for="cf-name"><?php esc_html_e('Họ và tên', 'saigonhoreca'); ?> <span aria-hidden="true">*</span></label>
                            <input class="sh-contact-form__input" type="text" id="cf-name" name="contact_name" required placeholder="Nguyễn Văn A">
                        </div>
                        <div class="sh-contact-form__field">
                            <label class="sh-contact-form__label" for="cf-phone"><?php esc_html_e('Số điện thoại', 'saigonhoreca'); ?></label>
                            <input class="sh-contact-form__input" type="tel" id="cf-phone" name="contact_phone" placeholder="0901 304 365">
                        </div>
                        <div class="sh-contact-form__field">
                            <label class="sh-contact-form__label" for="cf-email"><?php esc_html_e('Email', 'saigonhoreca'); ?> <span aria-hidden="true">*</span></label>
                            <input class="sh-contact-form__input" type="email" id="cf-email" name="contact_email" required placeholder="email@domain.vn">
                        </div>
                        <div class="sh-contact-form__field sh-contact-form__field--full">
                            <label class="sh-contact-form__label" for="cf-message"><?php esc_html_e('Nội dung', 'saigonhoreca'); ?></label>
                            <textarea class="sh-contact-form__input sh-contact-form__input--textarea" id="cf-message" name="contact_message" rows="5" placeholder="<?php esc_attr_e('Tôi cần tư vấn thiết kế bếp nhà hàng...', 'saigonhoreca'); ?>"></textarea>
                        </div>
                        <div class="sh-contact-form__field sh-contact-form__field--full">
                            <button class="sh-contact-form__submit" type="submit"><?php esc_html_e('Gửi liên hệ', 'saigonhoreca'); ?></button>
                        </div>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>

    </div>
</section>
