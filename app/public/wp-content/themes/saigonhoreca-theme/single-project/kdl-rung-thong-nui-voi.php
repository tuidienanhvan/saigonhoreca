<?php
/**
 * Single Project Template — KDL RỪNG THÔNG NÚI VOI
 * Thumbnail: du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca/du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca-thumbnail-project-cover.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/kdl-rung-thong-nui-voi/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--kdl" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/kdl-rung-thong-nui-voi/hero');
    get_template_part('template-parts/project-pillar/kdl-rung-thong-nui-voi/intro');
    get_template_part('template-parts/project-pillar/kdl-rung-thong-nui-voi/concept');
    get_template_part('template-parts/project-pillar/kdl-rung-thong-nui-voi/partnership');
    get_template_part('template-parts/project-pillar/kdl-rung-thong-nui-voi/specs');
    get_template_part('template-parts/project-pillar/kdl-rung-thong-nui-voi/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
