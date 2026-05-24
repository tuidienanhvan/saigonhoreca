<?php
/**
 * Template Name: Project — HEIWA SUSHI OMAKASE
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/heiwa-sushi-omakase/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/heiwa-sushi-omakase/hero');
    get_template_part('template-parts/project-pillar/heiwa-sushi-omakase/intro');
    get_template_part('template-parts/project-pillar/heiwa-sushi-omakase/concept');
    get_template_part('template-parts/project-pillar/heiwa-sushi-omakase/partnership');
    get_template_part('template-parts/project-pillar/heiwa-sushi-omakase/specs');
    get_template_part('template-parts/project-pillar/heiwa-sushi-omakase/gallery');
    get_template_part('template-parts/project-pillar/heiwa-sushi-omakase/related');
    get_template_part('template-parts/project-pillar/heiwa-sushi-omakase/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
