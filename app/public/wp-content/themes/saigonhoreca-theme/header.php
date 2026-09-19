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
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url($theme_uri . '/assets/images/apple-touch-icon.webp'); ?>">
    <link rel="icon" type="image/webp" sizes="32x32" href="<?php echo esc_url($theme_uri . '/assets/images/favicon-32x32.webp'); ?>">
    <link rel="icon" type="image/webp" sizes="16x16" href="<?php echo esc_url($theme_uri . '/assets/images/favicon-16x16.webp'); ?>">
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
    <?php /* Home hero LCP preload is emitted dynamically in inc/core/enqueue.php
             (real hero image via _pi_hero_1_bg / latest featured). The old hardcoded
             SGH-banner-1.webp preload here was a DUPLICATE pointing at a non-existent
             file — removed so only ONE correct hero image competes for fetchpriority. */ ?>
    <?php
    wp_head();
    ?>

    <!-- Luxury Circular Flag Multilingual Switcher (VI / EN / JA) -->
    <style id="sgh-lang-switcher-style">
    .sh-header__lang-group {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        padding: 4px 6px !important;
        background: rgba(20, 22, 26, 0.65) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 9999px !important;
        backdrop-filter: blur(16px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(16px) saturate(180%) !important;
        box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.08), 0 4px 16px rgba(0, 0, 0, 0.4) !important;
    }
    .sh-header .sh-header__lang,
    .sh-mobile .sh-header__lang {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 30px !important;
        height: 30px !important;
        border-radius: 50% !important;
        background: transparent !important;
        border: 1.5px solid transparent !important;
        text-decoration: none !important;
        padding: 0 !important;
        cursor: pointer !important;
        opacity: 0.55 !important;
        box-shadow: none !important;
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                    opacity 0.2s ease,
                    background 0.25s ease,
                    border-color 0.25s ease,
                    box-shadow 0.25s ease !important;
    }
    .sh-header .sh-header__lang:hover,
    .sh-mobile .sh-header__lang:hover {
        opacity: 0.95 !important;
        transform: translateY(-1.5px) scale(1.08) !important;
        background: rgba(255, 255, 255, 0.08) !important;
    }
    .sh-header .sh-header__lang:active,
    .sh-mobile .sh-header__lang:active {
        transform: scale(0.92) !important;
        transition-duration: 0.1s !important;
    }
    .sh-header .sh-header__lang--active,
    .sh-header .sh-header__lang.active,
    .sh-header .sh-header__lang.is-active,
    .sh-mobile .sh-header__lang--active,
    .sh-mobile .sh-header__lang.active,
    .sh-mobile .sh-header__lang.is-active {
        opacity: 1 !important;
        background: linear-gradient(135deg, rgba(245, 166, 35, 0.2) 0%, rgba(209, 137, 16, 0.08) 100%) !important;
        border-color: rgba(245, 166, 35, 0.6) !important;
        box-shadow: 0 0 12px rgba(245, 166, 35, 0.3), inset 0 1px 2px rgba(255, 255, 255, 0.25) !important;
        transform: scale(1.05) !important;
    }
    .sh-header__lang-flag {
        width: 24px !important;
        height: 24px !important;
        border-radius: 50% !important;
        display: block !important;
        overflow: hidden !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.35) !important;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), filter 0.25s ease !important;
    }
    .sh-header .sh-header__lang:hover .sh-header__lang-flag,
    .sh-mobile .sh-header__lang:hover .sh-header__lang-flag {
        filter: brightness(1.06) !important;
    }
    @media (max-width: 640px) {
        .sh-header__lang-group { padding: 3px 4px !important; gap: 4px !important; }
        .sh-header .sh-header__lang,
        .sh-mobile .sh-header__lang { width: 26px !important; height: 26px !important; }
        .sh-header__lang-flag { width: 20px !important; height: 20px !important; }
    }
    </style>

    <meta name="msapplication-TileColor" content="#2b5797">
    <meta name="theme-color" content="#ffffff">
    <!-- Dark mode & Lighthouse Optimization: apply BEFORE paint to prevent flash & bypass animation delays -->
    <script nonce="<?php echo esc_attr($GLOBALS['sgh_csp_nonce'] ?? ''); ?>">
    (function(){
        // 1. Dark mode
        var d = null;
        try { d = localStorage.getItem('sh-dark'); } catch (e) {}
        if (d === 'true') document.documentElement.classList.add('dark');

        // 2. Google Fonts async swap: rel="preload" -> rel="stylesheet" on load.
        // Moved off an inline onload="..." attribute (blocked by CSP
        // script-src-elem, which has no nonce coverage for event-handler
        // attributes — see inc/engine/enqueue.php's style_loader_tag filter)
        // into this nonced <script>, which CSP does allow.
        var fontLink = document.getElementById('google-fonts-sgh-css');
        if (fontLink && fontLink.rel === 'preload') {
            fontLink.addEventListener('load', function () {
                fontLink.rel = 'stylesheet';
            });
        }
    })();
    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-16RD3DMN8W"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-16RD3DMN8W');
    </script>
    
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '861182343476463');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=861182343476463&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
</head>
<body <?php body_class(); ?>>
<a href="#primary" class="sh-skip-link"><?php esc_html_e('Chuyển đến nội dung chính', 'saigonhoreca'); ?></a>
<!-- Main Header (Saigon Horeca: clean dark band, no construction skyline) -->
<header id="main-header" class="sh-header sh-header--horeca <?php if (is_admin_bar_showing()) echo 'sh-header--admin-bar'; ?>" role="banner" aria-label="<?php esc_attr_e('Header chính của website', 'saigonhoreca'); ?>">
    <div class="sh-header__border"></div>
    <div class="sh-header__bar sh-header__bar--horeca <?php echo is_front_page() ? 'sh-header__bar--home' : ''; ?>">
        <!-- Logo -->
        <?php get_template_part('template-parts/header/logo'); ?>

        <!-- Desktop Navigation -->
        <?php get_template_part('template-parts/header/navigation'); ?>

        <!-- Right Actions -->
        <div class="sh-header__actions">
            <!-- Multilingual switcher (VI / EN / JA) -->
            <?php
            $current_lang = defined('PI_CURRENT_LANGUAGE') ? PI_CURRENT_LANGUAGE : 'vi';
            $vi_url = class_exists('Pi_Multilang_Frontend') ? (Pi_Multilang_Frontend::url_in_language('vi') ?: home_url('/')) : home_url('/');
            $en_url = class_exists('Pi_Multilang_Frontend') ? (Pi_Multilang_Frontend::url_in_language('en') ?: home_url('/en/')) : home_url('/en/');
            $ja_url = class_exists('Pi_Multilang_Frontend') ? (Pi_Multilang_Frontend::url_in_language('ja') ?: home_url('/ja/')) : home_url('/ja/');
            ?>
            <div class="sh-header__lang-group">
                <!-- Vietnam Flag -->
                <a href="<?php echo esc_url($vi_url); ?>"
                   class="sh-header__lang <?php echo $current_lang === 'vi' ? 'sh-header__lang--active active is-active' : ''; ?>"
                   hreflang="vi"
                   rel="alternate"
                   aria-label="Tiếng Việt"
                   title="Tiếng Việt"
                   <?php echo $current_lang === 'vi' ? 'aria-current="page"' : ''; ?>>
                    <svg class="sh-header__lang-flag" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="24" height="24">
                        <defs>
                            <clipPath id="sh-vn-clip-hdr">
                                <circle cx="16" cy="16" r="16"/>
                            </clipPath>
                            <radialGradient id="sh-vn-grad-hdr" cx="38%" cy="28%" r="72%">
                                <stop offset="0%" stop-color="#EA2820"/>
                                <stop offset="100%" stop-color="#A80D08"/>
                            </radialGradient>
                            <linearGradient id="sh-vn-star-hdr" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#FFF275"/>
                                <stop offset="100%" stop-color="#FFB800"/>
                            </linearGradient>
                        </defs>
                        <g clip-path="url(#sh-vn-clip-hdr)">
                            <circle cx="16" cy="16" r="16" fill="url(#sh-vn-grad-hdr)"/>
                            <polygon points="16,8.2 17.76,13.62 23.46,13.62 18.85,16.97 20.61,22.39 16,19.04 11.39,22.39 13.15,16.97 8.54,13.62 14.24,13.62" fill="url(#sh-vn-star-hdr)"/>
                            <circle cx="16" cy="16" r="15.5" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="1"/>
                        </g>
                    </svg>
                </a>
                <!-- UK Flag -->
                <a href="<?php echo esc_url($en_url); ?>"
                   class="sh-header__lang <?php echo $current_lang === 'en' ? 'sh-header__lang--active active is-active' : ''; ?>"
                   hreflang="en"
                   rel="alternate"
                   aria-label="English"
                   title="English"
                   <?php echo $current_lang === 'en' ? 'aria-current="page"' : ''; ?>>
                    <svg class="sh-header__lang-flag" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="24" height="24">
                        <defs>
                            <clipPath id="sh-uk-clip-hdr">
                                <circle cx="16" cy="16" r="16"/>
                            </clipPath>
                        </defs>
                        <g clip-path="url(#sh-uk-clip-hdr)">
                            <circle cx="16" cy="16" r="16" fill="#012169"/>
                            <path d="M-4,-4 L36,36 M36,-4 L-4,36" stroke="#FFFFFF" stroke-width="5.5" stroke-linecap="square"/>
                            <path d="M-4,-4 L16,16 M36,-4 L16,16 M36,36 L16,16 M-4,36 L16,16" stroke="#C8102E" stroke-width="2"/>
                            <path d="M16,-4 V36 M-4,16 H36" stroke="#FFFFFF" stroke-width="9"/>
                            <path d="M16,-4 V36 M-4,16 H36" stroke="#C8102E" stroke-width="5.5"/>
                            <circle cx="16" cy="16" r="15.5" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="1"/>
                        </g>
                    </svg>
                </a>
                <!-- Japan Flag -->
                <a href="<?php echo esc_url($ja_url); ?>"
                   class="sh-header__lang <?php echo $current_lang === 'ja' ? 'sh-header__lang--active active is-active' : ''; ?>"
                   hreflang="ja"
                   rel="alternate"
                   aria-label="日本語"
                   title="日本語"
                   <?php echo $current_lang === 'ja' ? 'aria-current="page"' : ''; ?>>
                    <svg class="sh-header__lang-flag" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="24" height="24">
                        <defs>
                            <clipPath id="sh-ja-clip-hdr">
                                <circle cx="16" cy="16" r="16"/>
                            </clipPath>
                            <radialGradient id="sh-ja-sun-hdr" cx="42%" cy="36%" r="65%">
                                <stop offset="0%" stop-color="#DC143C"/>
                                <stop offset="100%" stop-color="#9E001F"/>
                            </radialGradient>
                        </defs>
                        <g clip-path="url(#sh-ja-clip-hdr)">
                            <circle cx="16" cy="16" r="16" fill="#F8F9FA"/>
                            <circle cx="16" cy="16" r="9.6" fill="url(#sh-ja-sun-hdr)"/>
                            <circle cx="16" cy="16" r="15.5" fill="none" stroke="rgba(0,0,0,0.08)" stroke-width="1"/>
                            <circle cx="16" cy="16" r="15.5" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="0.75"/>
                        </g>
                    </svg>
                </a>
            </div>
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
<script nonce="<?php echo esc_attr($GLOBALS['sgh_csp_nonce'] ?? ''); ?>">
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
