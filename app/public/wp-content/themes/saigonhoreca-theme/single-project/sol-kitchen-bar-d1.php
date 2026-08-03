<?php
/**
 * Single Project Template — SOL KITCHEN & BAR
 * Thumbnail: sol-kitchen-bar-saigon-horeca/sol-kitchen-bar-saigon-horeca-thumbnail-project-cover.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/sol-kitchen-bar-d1/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-d1/hero');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-d1/intro');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-d1/concept');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-d1/specs');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-d1/partnership');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-d1/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
