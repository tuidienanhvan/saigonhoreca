<?php
/**
 * Single Project Template — Dự Nam An An
 * Thumbnail: 2025/04/du-an-sheh-fung.jpg
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/du-nam-an-an/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--nan" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/du-nam-an-an/hero');
    get_template_part('template-parts/project-pillar/du-nam-an-an/intro');
    get_template_part('template-parts/project-pillar/du-nam-an-an/concept');
    get_template_part('template-parts/project-pillar/du-nam-an-an/partnership');
    get_template_part('template-parts/project-pillar/du-nam-an-an/specs');
    get_template_part('template-parts/project-pillar/du-nam-an-an/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
