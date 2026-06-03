<?php
/**
 * Single Project Template — Renovate SOL KITCHEN & BAR quận 7
 * Thumbnail: sol-kitchen-bar-saigon-horeca/SGH-projects-01-1024x576.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/sol-kitchen-bar/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/sol-kitchen-bar/hero');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar/intro');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar/concept');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar/specs');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar/partnership');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
