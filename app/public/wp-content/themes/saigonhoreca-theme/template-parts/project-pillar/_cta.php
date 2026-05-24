<?php
/**
 * CTA trio — Phone / Address / Email, shared cho mọi pillar page.
 * Data từ inc/config/contact-info.php (`saigonhouse_get_contact_info`).
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$c = function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : [];
if (empty($c)) return;

$cards = [
    [
        'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
        'title' => $c['hotline']     ?? '',
        'desc'  => 'Thứ 2 - Thứ 6, 8h00 - 18h00',
        'btn'   => 'Gọi ngay',
        'href'  => !empty($c['hotline_raw']) ? 'tel:' . $c['hotline_raw'] : '',
    ],
    [
        'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'title' => 'TP Hồ Chí Minh',
        'desc'  => $c['address_short'] ?? ($c['address'] ?? ''),
        'btn'   => 'Xem bản đồ',
        'href'  => 'https://maps.app.goo.gl/SmWvPSEW2ozAq5dA6',
    ],
    [
        'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
        'title' => $c['email_primary'] ?? '',
        'desc'  => 'Liên hệ mọi lúc',
        'btn'   => 'Gửi email',
        'href'  => !empty($c['email_primary']) ? 'mailto:' . $c['email_primary'] : '',
    ],
];
?>
<section class="pp__section pp__section--alt">
    <div class="pp__container">
        <div class="pp-text pp-text--center" style="margin-bottom: 3rem;">
            <span class="pp-text__divider pp-text__divider--center" aria-hidden="true"></span>
            <h2 class="pp-text__title">Thông tin liên hệ</h2>
        </div>
        <div class="pp-cta">
            <?php foreach ($cards as $card) : ?>
                <div class="pp-cta__card">
                    <span class="pp-cta__icon" aria-hidden="true"><?php echo $card['icon']; ?></span>
                    <?php if ($card['title']) : ?>
                        <h3 class="pp-cta__title"><?php echo esc_html($card['title']); ?></h3>
                    <?php endif; ?>
                    <?php if ($card['desc']) : ?>
                        <p class="pp-cta__desc"><?php echo esc_html($card['desc']); ?></p>
                    <?php endif; ?>
                    <?php if ($card['href']) : ?>
                        <a href="<?php echo esc_url($card['href']); ?>"
                           class="pp-cta__btn"<?php echo strpos($card['href'], 'http') === 0 ? ' target="_blank" rel="noopener"' : ''; ?>>
                            <?php echo esc_html($card['btn']); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
