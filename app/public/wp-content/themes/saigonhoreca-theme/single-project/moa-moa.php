<?php
/**
 * Single Project Template — Casa Maria
 * Thumbnail: 2025/04/du-an-sheh-fung.jpg
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/moa-moa/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--moa" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/moa-moa/hero');
    get_template_part('template-parts/project-pillar/moa-moa/intro');
    get_template_part('template-parts/project-pillar/moa-moa/concept');
    get_template_part('template-parts/project-pillar/moa-moa/partnership');
    get_template_part('template-parts/project-pillar/moa-moa/specs');
    get_template_part('template-parts/project-pillar/moa-moa/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
