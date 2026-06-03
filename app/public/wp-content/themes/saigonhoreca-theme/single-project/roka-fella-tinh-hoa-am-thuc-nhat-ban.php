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

<main id="primary" class="pp pp--rkf" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/roka-fella-tinh-hoa-am-thuc-nhat-ban/hero');
    get_template_part('template-parts/project-pillar/roka-fella-tinh-hoa-am-thuc-nhat-ban/intro');
    get_template_part('template-parts/project-pillar/roka-fella-tinh-hoa-am-thuc-nhat-ban/concept');
    get_template_part('template-parts/project-pillar/roka-fella-tinh-hoa-am-thuc-nhat-ban/partnership');
    get_template_part('template-parts/project-pillar/roka-fella-tinh-hoa-am-thuc-nhat-ban/specs');
    get_template_part('template-parts/project-pillar/roka-fella-tinh-hoa-am-thuc-nhat-ban/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
