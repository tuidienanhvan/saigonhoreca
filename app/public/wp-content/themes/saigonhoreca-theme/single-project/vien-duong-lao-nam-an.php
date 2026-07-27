<?php
/**
 * Single Project Template — Viện dưỡng lão Nam An
 * Thumbnail: vien-duong-lao-nam-an/nam-an-mat-ngoai-toa-nha-anh-bia-du-an.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/vien-duong-lao-nam-an/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--nan" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/vien-duong-lao-nam-an/hero');
    get_template_part('template-parts/project-pillar/vien-duong-lao-nam-an/intro');
    get_template_part('template-parts/project-pillar/vien-duong-lao-nam-an/concept');
    get_template_part('template-parts/project-pillar/vien-duong-lao-nam-an/partnership');
    get_template_part('template-parts/project-pillar/vien-duong-lao-nam-an/specs');
    get_template_part('template-parts/project-pillar/vien-duong-lao-nam-an/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
