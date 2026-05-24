<?php
/**
 * Template Name: Project — LITTLE BEAR
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/little-bear-thao-dien/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/hero');
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/intro');
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/concept');
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/partnership');
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/specs');
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/gallery');
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/related');
    get_template_part('template-parts/project-pillar/little-bear-thao-dien/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
