<!DOCTYPE html>
<html <?php language_attributes(); ?> prefix="og: https://ogp.me/ns# fb: https://www.facebook.com/2008/fbml">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();
    $gtm_id = get_option('sgh_gtm_id', 'GTM-PVSPDW5B');
    ?>

    <!-- Resource Hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://img.youtube.com">

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
    wp_head();
    ?>

    <!-- SEO Fallback: Robust Meta & OpenGraph if not provided by an SEO plugin -->
    <?php
    $has_seo_plugin = class_exists('WPSEO_Frontend') || class_exists('RankMath');
    if (!$has_seo_plugin) :
        $seo_desc = is_singular() && has_excerpt() ? strip_tags(get_the_excerpt()) : get_bloginfo('description');
        $seo_title = is_singular() ? get_the_title() : get_bloginfo('name');
        $seo_url = is_singular() ? get_permalink() : home_url('/');
        $seo_type = is_singular('post') ? 'article' : 'website';
        $seo_img = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'large') : esc_url($theme_uri . '/assets/images/og-default.jpg');
    ?>
        <meta name="description" content="<?php echo esc_attr($seo_desc); ?>">
        <meta property="og:title" content="<?php echo esc_attr($seo_title); ?>">
        <meta property="og:description" content="<?php echo esc_attr($seo_desc); ?>">
        <meta property="og:url" content="<?php echo esc_url($seo_url); ?>">
        <meta property="og:type" content="<?php echo esc_attr($seo_type); ?>">
        <meta property="og:image" content="<?php echo esc_url($seo_img); ?>">
        <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <?php endif; ?>

    <meta name="msapplication-TileColor" content="#2b5797">
    <meta name="theme-color" content="#ffffff">
    <!-- Dark mode: apply BEFORE paint to prevent flash -->
    <script>
    (function(){
        var d = null;
        try { d = localStorage.getItem('sh-dark'); } catch (e) {}
        if (d === 'true') document.documentElement.classList.add('dark');
    })();
    </script>
    <?php get_template_part('template-parts/global-styles'); ?>
</head>
<body <?php body_class(); ?>>
<a href="#primary" class="sh-skip-link">Chuyển đến nội dung chính</a>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo rawurlencode($gtm_id); ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<!-- Main Header -->
<header id="main-header" class="sh-header <?php if (is_admin_bar_showing()) echo 'sh-header--admin-bar'; ?>" role="banner" aria-label="Header chính của website">
    <div class="sh-header__bar <?php echo is_front_page() ? 'sh-header__bar--home' : ''; ?>">
        <div class="sh-header__arch" aria-hidden="true">
            <svg class="sh-header__arch-svg" viewBox="0 0 1920 80" preserveAspectRatio="none" focusable="false">
                <!-- Blueprint grid (T-015: kept; only 3 lines, minor cost) -->
                <g class="sh-header__arch-grid">
                    <line x1="0" y1="20" x2="1920" y2="20"/>
                    <line x1="0" y1="40" x2="1920" y2="40"/>
                    <line x1="0" y1="60" x2="1920" y2="60"/>
                </g>

                <!-- Skyline silhouette (T-015: single path, ~50 child nodes removed
                     to drop Lighthouse DOM size out of the warning band) -->
                <path class="sh-header__arch-skyline" d="M0,78 L40,78 L40,62 L55,48 L70,62 L70,78 L110,78 L110,56 L130,40 L150,56 L150,78 L260,78 L260,52 L288,52 L288,78 L292,78 L292,42 L316,42 L316,78 L320,78 L320,34 L352,34 L352,78 L480,78 L480,28 L580,30 L540,30 L540,52 L540,78 L680,78 L680,50 L710,32 L740,50 L740,78 L880,78 L880,44 L916,44 L916,78 L920,78 L920,38 L956,38 L956,78 L1200,78 L1200,28 L1224,28 L1224,78 L1228,78 L1228,38 L1248,38 L1248,78 L1400,78 L1400,54 L1415,42 L1430,54 L1430,78 L1440,78 L1440,50 L1455,38 L1470,50 L1470,78 L1480,78 L1480,54 L1495,42 L1510,54 L1510,78 L1650,78 L1650,48 L1690,48 L1690,78 L1700,78 L1700,36 L1740,36 L1740,78 L1750,78 L1750,52 L1780,52 L1780,78 L1920,78 Z"/>

                <!-- Ground line -->
                <line class="sh-header__arch-ground" x1="0" y1="78" x2="1920" y2="78"/>

                <!-- Accent line -->
                <line class="sh-header__arch-accent" x1="0" y1="79.5" x2="1920" y2="79.5"/>
            </svg>
        </div>

        <!-- Logo -->
        <?php get_template_part('template-parts/header/logo'); ?>

        <!-- Desktop Navigation -->
        <?php get_template_part('template-parts/header/navigation'); ?>

        <!-- Right Actions -->
        <div class="sh-header__actions">
            <!-- Dark Mode Toggle -->
            <button id="sh-dark-toggle" class="sh-header__dark-toggle" aria-label="Chuyển chế độ sáng/tối" title="Dark mode">
                <span id="sh-icon-sun" style="display:none;"><?php echo sh_icon('sun', 'sh-header__icon'); ?></span>
                <span id="sh-icon-moon"><?php echo sh_icon('moon', 'sh-header__icon'); ?></span>
            </button>
            <!-- Mobile Menu Toggle -->
            <button id="mobile-menu-toggle" class="sh-header__mobile-toggle" aria-label="Mở menu điều hướng" aria-expanded="false" aria-controls="mobile-sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sh-header__hamburger-icon">
                    <line x1="4" x2="20" y1="12" y2="12"/>
                    <line x1="4" x2="20" y1="6" y2="6"/>
                    <line x1="4" x2="20" y1="18" y2="18"/>
                </svg>
            </button>
        </div>
    </div>
    <?php get_template_part('template-parts/header/top-bar'); ?>
</header>
<?php
// Note: Mobile menu moved to footer.php for better layout stability
?>
