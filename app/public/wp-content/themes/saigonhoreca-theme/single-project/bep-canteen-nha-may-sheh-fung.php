<?php
/**
 * Single Project Template — SHEH FUNG
 * Thumbnail: 2025/04/du-an-sheh-fung.jpg
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/bep-canteen-nha-may-sheh-fung/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/hero');
    get_template_part('template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/intro');
    get_template_part('template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/concept');
    get_template_part('template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/partnership');
    get_template_part('template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/specs');
    get_template_part('template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
