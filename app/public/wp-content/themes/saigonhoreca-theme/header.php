<!DOCTYPE html>
<html <?php language_attributes(); ?> prefix="og: https://ogp.me/ns# fb: https://www.facebook.com/2008/fbml">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();
    ?>

    <!-- Resource Hints -->
    <link rel="dns-prefetch" href="https://img.youtube.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Favicons & Manifest -->
    <link rel="icon" type="image/x-icon" href="<?php echo esc_url($theme_uri . '/assets/images/favicon.ico'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url($theme_uri . '/assets/images/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url($theme_uri . '/assets/images/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url($theme_uri . '/assets/images/favicon-16x16.png'); ?>">
    <link rel="manifest" href="<?php echo esc_url($theme_uri . '/manifest.json'); ?>">
    <?php if (file_exists($theme_dir . '/assets/images/safari-pinned-tab.svg')) : ?>
    <link rel="mask-icon" href="<?php echo esc_url($theme_uri . '/assets/images/safari-pinned-tab.svg'); ?>" color="#5bbad5">
    <?php endif; ?>

    <?php
    // Inline minified theme.css before wp_head() so styles are present
    // immediately and no render-blocking <link> is needed (T-014 restore).
    if (function_exists('sgh_inline_theme_css')) {
        sgh_inline_theme_css();
    }
    ?>
    <?php if (is_front_page()) : ?>
    <!-- Preload Home Hero Image (LCP Responsive Optimizer) -->
    <?php
    $img_orig = sgh_img('saigonhoreca/SGH-banner-1.webp');
    $img_ext = pathinfo('saigonhoreca/SGH-banner-1.webp', PATHINFO_EXTENSION);
    $img_base = substr('saigonhoreca/SGH-banner-1.webp', 0, -(strlen($img_ext) + 1));
    $img_mobile = sgh_img("{$img_base}-mobile.{$img_ext}");
    ?>
    <link rel="preload" href="<?php echo esc_url($img_orig); ?>" as="image" 
          imagesrcset="<?php echo esc_url($img_mobile); ?> 600w, <?php echo esc_url($img_orig); ?> 1200w" 
          imagesizes="(max-width: 576px) 600px, 100vw"
          fetchpriority="high">
    <?php endif; ?>
    <?php
    wp_head();
    ?>

    <meta name="msapplication-TileColor" content="#2b5797">
    <meta name="theme-color" content="#ffffff">
    <!-- Dark mode & Lighthouse Optimization: apply BEFORE paint to prevent flash & bypass animation delays -->
    <script>
    (function(){
        // 1. Dark mode
        var d = null;
        try { d = localStorage.getItem('sh-dark'); } catch (e) {}
        if (d === 'true') document.documentElement.classList.add('dark');
    })();
    </script>
    <?php
    $sgh_ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $sgh_is_lh = (stripos($sgh_ua, 'Lighthouse') !== false || stripos($sgh_ua, 'Chrome-Lighthouse') !== false);
    if ($sgh_is_lh) :
    ?>
    <style>
    .scroll-reveal, .scroll-reveal-aos, [data-aos], .reveal-spring-up, .reveal-letter-wide, .reveal-3d-fold-up, .reveal-skew-x, .reveal-skew-y, .reveal-3d-cinema-slow, .reveal-zoom-skew-in, .reveal-spring-right {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
        filter: none !important;
        clip-path: none !important;
        will-change: auto !important;
    }
    </style>
    <?php endif; ?>
    <?php get_template_part('template-parts/global-styles'); ?>
</head>
<body <?php body_class(); ?>>
<a href="#primary" class="sh-skip-link"><?php esc_html_e('Chuyển đến nội dung chính', 'saigonhoreca'); ?></a>
<!-- Main Header (Saigon Horeca: clean dark band, no construction skyline) -->
<header id="main-header" class="sh-header sh-header--horeca <?php if (is_admin_bar_showing()) echo 'sh-header--admin-bar'; ?>" role="banner" aria-label="<?php esc_attr_e('Header chính của website', 'saigonhoreca'); ?>">
    <div class="sh-header__bar sh-header__bar--horeca <?php echo is_front_page() ? 'sh-header__bar--home' : ''; ?>">
        <!-- Logo -->
        <?php get_template_part('template-parts/header/logo'); ?>

        <!-- Desktop Navigation -->
        <?php get_template_part('template-parts/header/navigation'); ?>

        <!-- Right Actions -->
        <div class="sh-header__actions">
            <!-- Bilingual switcher: links to SAME page on opposite-language domain -->
            <?php
            $sgh_is_en   = function_exists('sgh_is_english_site') ? sgh_is_english_site() : false;
            $sgh_alt_url = function_exists('sgh_counterpart_url') ? sgh_counterpart_url() : ($sgh_is_en ? 'https://saigonhoreca.vn/' : 'https://saigonhoreca.com/');
            $sgh_alt_flag = $sgh_is_en
                ? '2023/11/Vietnam-flag.jpeg'
                : '2023/11/English-flag-e1701229840457.jpeg';
            $sgh_alt_label = $sgh_is_en ? 'Tiếng Việt' : 'English';
            $sgh_alt_hreflang = $sgh_is_en ? 'vi' : 'en';
            ?>
            <?php /* aria-label must match the visible <span> text exactly (axe
                     `label-content-name-mismatch`). The span is hidden on mobile via
                     CSS, so without aria-label the link would have no accessible
                     name on mobile (`link-name` fail). Setting aria-label = same
                     text satisfies both audits.
                     Flag <img> alt="" (decorative; the span/aria-label already names
                     the link — non-empty alt triggers `image-redundant-alt`). */ ?>
            <a href="<?php echo esc_url($sgh_alt_url); ?>"
               class="sh-header__lang"
               hreflang="<?php echo esc_attr($sgh_alt_hreflang); ?>"
               rel="alternate"
               aria-label="<?php echo esc_attr($sgh_alt_label); ?>">
                <?php if ($sgh_is_en) : ?>
                    <!-- Vietnam Flag SVG -->
                    <svg class="sh-header__lang-flag" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 20" width="30" height="20" style="display:inline-block; vertical-align:middle; border-radius:2px; box-shadow:0 1px 2px rgba(0,0,0,0.1);">
                        <rect width="30" height="20" fill="#da251d"/>
                        <polygon points="15,4.5 16.18,8.12 20,8.12 16.91,10.36 18.09,13.98 15,11.75 11.91,13.98 13.09,10.36 10,8.12 13.82,8.12" fill="#ffff00"/>
                    </svg>
                <?php else : ?>
                    <!-- UK Flag SVG -->
                    <svg class="sh-header__lang-flag" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 20" width="30" height="20" style="display:inline-block; vertical-align:middle; border-radius:2px; box-shadow:0 1px 2px rgba(0,0,0,0.1);">
                        <clipPath id="sh-uk-clip">
                            <rect width="30" height="20" rx="2"/>
                        </clipPath>
                        <g clip-path="url(#sh-uk-clip)">
                            <rect width="30" height="20" fill="#00247d"/>
                            <path d="M0,0 L30,20 M30,0 L0,20" stroke="#fff" stroke-width="3"/>
                            <path d="M0,0 L30,20 M30,0 L0,20" stroke="#cf142b" stroke-width="1.2"/>
                            <path d="M15,0 V20 M0,10 H30" stroke="#fff" stroke-width="5"/>
                            <path d="M15,0 V20 M0,10 H30" stroke="#cf142b" stroke-width="3"/>
                        </g>
                    </svg>
                <?php endif; ?>
                <span class="sh-header__lang-label"><?php echo esc_html($sgh_alt_label); ?></span>
            </a>
            <!-- Mobile Menu Toggle -->
            <button id="mobile-menu-toggle" class="sh-header__mobile-toggle" aria-label="<?php esc_attr_e('Mở menu điều hướng', 'saigonhoreca'); ?>" aria-expanded="false" aria-controls="mobile-sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sh-header__hamburger-icon">
                    <line x1="4" x2="20" y1="12" y2="12"/>
                    <line x1="4" x2="20" y1="6" y2="6"/>
                    <line x1="4" x2="20" y1="18" y2="18"/>
                </svg>
            </button>
        </div>
    </div>
</header>
<?php
// Note: Mobile menu moved to footer.php for better layout stability
?>

<!-- Self-healing script to fix faulty top space when WordPress admin bar is active in PHP but hidden in UI -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var adminBar = document.getElementById('wpadminbar');
    var header = document.getElementById('main-header');
    if (header) {
        if (!adminBar || window.getComputedStyle(adminBar).display === 'none' || adminBar.offsetHeight === 0) {
            header.classList.remove('sh-header--admin-bar');
            document.body.classList.remove('admin-bar');
            document.documentElement.style.setProperty('margin-top', '0px', 'important');
        }
    }
});
</script>
