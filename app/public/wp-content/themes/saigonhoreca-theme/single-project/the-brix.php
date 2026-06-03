<?php
/**
 * Single Project Template — The Brix
 * Thumbnail: the-brix/the-brix-khong-gian-am-thuc-len-den-dem.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/the-brix/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--brix" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/the-brix/hero');
    get_template_part('template-parts/project-pillar/the-brix/intro');
    get_template_part('template-parts/project-pillar/the-brix/concept');
    get_template_part('template-parts/project-pillar/the-brix/partnership');
    get_template_part('template-parts/project-pillar/the-brix/specs');
    get_template_part('template-parts/project-pillar/the-brix/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
