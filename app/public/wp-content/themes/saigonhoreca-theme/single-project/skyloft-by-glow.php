<?php
/**
 * Single Project Template — Skyloft by Glow
 * Thumbnail: skyloft-by-glow/skyloft-by-glow-quay-bar-rooftop-dem.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/skyloft-by-glow/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--sky" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/skyloft-by-glow/hero');
    get_template_part('template-parts/project-pillar/skyloft-by-glow/intro');
    get_template_part('template-parts/project-pillar/skyloft-by-glow/concept');
    get_template_part('template-parts/project-pillar/skyloft-by-glow/specs');
    get_template_part('template-parts/project-pillar/skyloft-by-glow/partnership');
    get_template_part('template-parts/project-pillar/skyloft-by-glow/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
