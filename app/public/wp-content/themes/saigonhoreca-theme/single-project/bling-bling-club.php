<?php
/**
 * Single Project Template — BLING BLING CLUB
 * Thumbnail: bling-bling-club/bling-bling-club-thumbnail-project-cover.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/bling-bling-club/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--bbc" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/bling-bling-club/hero');
    get_template_part('template-parts/project-pillar/bling-bling-club/intro');
    get_template_part('template-parts/project-pillar/bling-bling-club/concept');
    get_template_part('template-parts/project-pillar/bling-bling-club/partnership');
    get_template_part('template-parts/project-pillar/bling-bling-club/specs');
    get_template_part('template-parts/project-pillar/bling-bling-club/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
