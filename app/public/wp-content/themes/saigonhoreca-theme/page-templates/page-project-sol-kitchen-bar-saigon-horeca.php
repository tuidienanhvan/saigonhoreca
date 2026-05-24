<?php
/**
 * Template Name: Project — SOL KITCHEN & BAR
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/sol-kitchen-bar-saigon-horeca/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-saigon-horeca/hero');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-saigon-horeca/intro');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-saigon-horeca/concept');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-saigon-horeca/partnership');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-saigon-horeca/specs');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-saigon-horeca/gallery');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-saigon-horeca/related');
    get_template_part('template-parts/project-pillar/sol-kitchen-bar-saigon-horeca/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
