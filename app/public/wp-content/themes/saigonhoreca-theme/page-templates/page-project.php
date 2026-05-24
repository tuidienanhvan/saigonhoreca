<?php
/**
 * Template Name: Project Pillar Page
 *
 * Page template for premium project case studies (e.g., slug `du-an/bambino`).
 * Orchestrates template-parts/project/* — sections extracted and refactored from
 * the production saigonhoreca.vn Bambino project scrape.
 *
 * Apply via wp-admin → Page → Page Attributes → Template → "Project Pillar Page",
 * or programmatically by setting post_meta `_wp_page_template` to
 * `page-templates/page-project.php`.
 *
 * Saigonhouse-theme architectural parity: same orchestrator-of-template-parts
 * pattern, per-route Tailwind v4 bundle loaded via theme-project.css.
 *
 * @package SaigonHoreca
 */

get_header(); ?>

<main id="primary" class="sh-project-pillar" tabindex="-1">
    <div class="elementor elementor-12856" data-elementor-type="wp-page" data-elementor-id="12856">
        <?php
        // 1. Hero banner with YouTube background
        get_template_part('template-parts/project/hero');

        // 2. Intro grid, swipe carousel, blockquote
        get_template_part('template-parts/project/intro');

        // 3. Design Concept section
        get_template_part('template-parts/project/concept');

        // 4. Partnership timeline / cooperation details
        get_template_part('template-parts/project/partnership');

        // 5. Specs / Kitchen layout section
        get_template_part('template-parts/project/specs');

        // 6. Gallery / Bar design section
        get_template_part('template-parts/project/gallery');

        // 7. Related projects grid
        get_template_part('template-parts/project/related');

        // 8. Contact CTA with WPForms
        get_template_part('template-parts/project/cta');
        ?>
    </div>
</main>

<?php get_footer();
