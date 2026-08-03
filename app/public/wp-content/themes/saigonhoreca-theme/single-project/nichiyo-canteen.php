<?php
/**
 * Single Project Template — Dự án bếp ăn công nghiệp bếp căng tin công ty Nhật Nichiyo
 * Thumbnail: du-an-bep-cang-tin-cong-ty-nhat-nichiyo/du-an-bep-cang-tin-cong-ty-nhat-nichiyo-thumbnail-project-cover.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/nichiyo-canteen/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--nichiyo" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/nichiyo-canteen/hero');
    get_template_part('template-parts/project-pillar/nichiyo-canteen/intro');
    get_template_part('template-parts/project-pillar/nichiyo-canteen/concept');
    get_template_part('template-parts/project-pillar/nichiyo-canteen/partnership');
    get_template_part('template-parts/project-pillar/nichiyo-canteen/specs');
    get_template_part('template-parts/project-pillar/nichiyo-canteen/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
