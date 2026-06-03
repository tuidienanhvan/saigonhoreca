<?php
/**
 * Single Project Template — Casa Maria
 * Thumbnail: casa-maria/casa-maria-thumbnail-project-cover.webp
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/casa-maria/hero');
    get_template_part('template-parts/project-pillar/casa-maria/intro');
    get_template_part('template-parts/project-pillar/casa-maria/concept');
    get_template_part('template-parts/project-pillar/casa-maria/partnership');
    get_template_part('template-parts/project-pillar/casa-maria/specs');
    get_template_part('template-parts/project-pillar/casa-maria/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
