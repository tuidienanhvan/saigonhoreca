<?php
/**
 * Template Name: Project — Dự án bếp ăn công nghiệp bếp căng tin công ty Nhật Nichiyo
 *
 * Auto-routed via inc/core/pillar-routes.php for /du-an/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/.
 *
 * @package SaigonHoreca
 */
get_header(); ?>

<main id="primary" class="pp" tabindex="-1">
    <?php
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/hero');
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/intro');
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/concept');
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/partnership');
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/specs');
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/gallery');
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/related');
    get_template_part('template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/cta');

    get_template_part('template-parts/project-pillar/_related');
    get_template_part('template-parts/project-pillar/_cta');
    ?>
</main>

<?php get_footer();
