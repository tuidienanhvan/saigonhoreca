<?php
/**
 * Single Project Template — BAMBINO
 * Thumbnail: bambino/bambino-thumbnail-project-cover.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/bambino-saigonhoreca/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--bam" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/bambino-saigonhoreca/hero');
    get_template_part('template-parts/project-pillar/bambino-saigonhoreca/intro');
    get_template_part('template-parts/project-pillar/bambino-saigonhoreca/concept');
    get_template_part('template-parts/project-pillar/bambino-saigonhoreca/partnership');
    get_template_part('template-parts/project-pillar/bambino-saigonhoreca/specs');
    get_template_part('template-parts/project-pillar/bambino-saigonhoreca/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
