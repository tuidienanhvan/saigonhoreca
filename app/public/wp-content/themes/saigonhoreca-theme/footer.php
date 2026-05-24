<?php
/**
 * Footer Template
 *
 * Two-mode layout:
 *   • Widget mode (preferred) — 4 columns powered by `saigonhouse-footer-1..4`
 *     widget areas. Active when any of the 4 areas has a widget.
 *   • Legacy mode (fallback) — hardcoded 3-column grid rendered only when ALL
 *     4 widget areas are empty. Keeps new installs visible out-of-the-box.
 *
 * @package SaigonHoreca
 */
?>
<footer class="sh-footer" role="contentinfo" itemscope itemtype="https://schema.org/WPFooter">
    <?php get_template_part('template-parts/header/mobile-menu'); ?>
    <div class="sh-footer__container">

        <!-- Legacy mode: 3-column hardcoded grid -->
        <div class="sh-footer__grid">
            <?php get_template_part('template-parts/footer/company-info'); ?>
            <?php get_template_part('template-parts/footer/quick-links'); ?>
            <?php get_template_part('template-parts/footer/contact-info'); ?>
        </div>

        <?php get_template_part('template-parts/footer/copyright'); ?>
    </div>
</footer>

<?php get_template_part('template-parts/footer/mobile-nav'); ?>
<?php get_template_part('template-parts/footer/floating-buttons'); ?>
<?php // Chatbot widget: rendered by Pi Chatbot plugin via wp_footer hook ?>

<?php wp_footer(); ?>
</body>
</html>
