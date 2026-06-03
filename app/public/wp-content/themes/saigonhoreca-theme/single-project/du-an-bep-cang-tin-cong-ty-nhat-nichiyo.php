<?php
/**
 * Single Project Template — Dự án bếp ăn công nghiệp bếp căng tin công ty Nhật Nichiyo
 * Thumbnail: du-an-bep-cang-tin-cong-ty-nhat-nichiyo/nichiyo-du-an-1-1024x576.webp
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp pp--nichiyo" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/hero');
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/intro');
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/concept');
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/partnership');
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/specs');
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/gallery');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
