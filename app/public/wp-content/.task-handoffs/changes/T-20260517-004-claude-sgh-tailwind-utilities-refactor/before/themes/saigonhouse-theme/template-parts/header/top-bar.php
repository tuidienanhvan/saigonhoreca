<?php
/**
 * Template Part: Top Bar (Marquee)
 *
 * @package SaigonHouse
 */

$contact = $args['contact'] ?? (function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : []);
?>
<div class="sh-topbar">
    <div class="sh-topbar__inner">
        <!-- Marquee area -->
        <div class="sh-topbar__marquee-wrap">
            <?php
            $lines = [];
            if (isset($args['marquee_lines']) && is_array($args['marquee_lines'])) {
                $lines = $args['marquee_lines'];
            } else {
                for ($i = 1; $i <= 4; $i++) {
                    $text = get_theme_mod('saigonhouse_top_bar_line_' . $i);
                    if ($text === false) {
                        $defaults = [
                            1 => 'Chào mừng kỷ niệm 15 năm thành lập Kiến Trúc Saigon House!',
                            2 => 'Giảm ngay 50% phí thiết kế khi thi công trọn gói.',
                            3 => 'Top 10 Thương Hiệu Uy Tín 2026',
                            4 => ''
                        ];
                        $text = isset($defaults[$i]) ? $defaults[$i] : '';
                    }
                    if (!empty($text)) $lines[] = $text;
                }
            }

            $icons = ['party-popper', 'gift', 'trophy', 'star'];
            ob_start();
            $index = 0;
            foreach ($lines as $line) :
                $icon = isset($icons[$index]) ? $icons[$index] : 'star';
                $index++;
            ?>
                <span class="sh-topbar__item"><?php echo function_exists('sh_icon') ? sh_icon($icon, 'sh-topbar__item-icon') : ''; ?> <?php echo esc_html($line); ?></span>
            <?php endforeach; ?>
                <span class="sh-topbar__item"><?php echo function_exists('sh_icon') ? sh_icon('phone', 'sh-topbar__item-icon') : ''; ?> Hotline: <?php echo esc_html($contact['hotline'] ?? ''); ?></span>
                <span class="sh-topbar__item"><?php echo function_exists('sh_icon') ? sh_icon('mail', 'sh-topbar__item-icon') : ''; ?> <?php echo esc_html($contact['email_primary'] ?? ''); ?></span>
            <?php $marquee_items = ob_get_clean(); ?>
            <div class="sh-marquee-track">
                <div class="sh-marquee-content"><?php echo $marquee_items; ?></div>
                <div class="sh-marquee-content" aria-hidden="true"><?php echo $marquee_items; ?></div>
            </div>
        </div>

        <!-- Right side: Search + CTA (desktop only) -->
        <div class="sh-topbar__actions">
             <form role="search" method="get" class="sh-topbar__search" action="<?php echo home_url('/'); ?>">
                 <input type="search" class="sh-topbar__search-input" placeholder="Tìm kiếm..." value="<?php echo get_search_query(); ?>" name="s" />
                 <button type="submit" class="sh-topbar__search-btn">
                     <?php echo sh_icon('search', 'sh-topbar__search-icon'); ?>
                 </button>
             </form>
             <a href="<?php echo home_url('/lien-he'); ?>" class="sh-topbar__cta">
                 <span>LIÊN HỆ NGAY</span>
                 <?php echo sh_icon('arrow-right', 'sh-topbar__cta-icon'); ?>
             </a>
        </div>
    </div>
</div>
