<?php
/**
 * Template Name: Project — BLING BLING CLUB
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/bling-bling-club/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--bling-bling-club" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/bling-bling-club/hero');
    get_template_part('template-parts/project-pillar/bling-bling-club/intro');
    get_template_part('template-parts/project-pillar/bling-bling-club/concept');
    get_template_part('template-parts/project-pillar/bling-bling-club/partnership');
    get_template_part('template-parts/project-pillar/bling-bling-club/specs');
    get_template_part('template-parts/project-pillar/bling-bling-club/gallery');
    get_template_part('template-parts/project-pillar/bling-bling-club/related');
    get_template_part('template-parts/project-pillar/bling-bling-club/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
