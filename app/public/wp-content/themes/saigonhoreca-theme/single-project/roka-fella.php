<?php
/**
 * Single Project Template — Roka Fella
 * Thumbnail: roka-fella/roka-fella-thumbnail-project-cover.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/roka-fella-tinh-hoa-am-thuc-nhat-ban/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--rkf pp--rf" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/roka-fella/hero');
    get_template_part('template-parts/project-pillar/roka-fella/intro');
    get_template_part('template-parts/project-pillar/roka-fella/concept');
    get_template_part('template-parts/project-pillar/roka-fella/partnership');
    get_template_part('template-parts/project-pillar/roka-fella/specs');
    get_template_part('template-parts/project-pillar/roka-fella/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
