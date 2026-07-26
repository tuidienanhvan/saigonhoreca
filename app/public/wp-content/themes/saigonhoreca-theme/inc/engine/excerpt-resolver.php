<?php
/**
 * Saigon Horeca Core Smart Excerpt Resolver Module
 *
 * Provides a robust 4-tier fallback cascade for post excerpts/sapo:
 *   1. Manual post_excerpt in WP DB
 *   2. Pi SEO / Meta Description override
 *   3. Smart auto-extraction from post_content (stripping HTML, shortcodes & Gutenberg blocks)
 *   4. Safe fallback default
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('sgh_get_the_excerpt')) {
    /**
     * Retrieve a clean, smart excerpt/sapo for any post.
     *
     * @param int|WP_Post|null $post_id Post ID or WP_Post object.
     * @param int $word_count Maximum word count for auto-extracted fallback.
     * @param string $more Suffix appended to trimmed text.
     * @return string Clean excerpt text.
     */
    function sgh_get_the_excerpt($post_id = null, $word_count = 30, $more = '...') {
        $post = get_post($post_id);
        if (!$post) return '';

        // 1. Explicit post_excerpt set in WP DB
        $raw_excerpt = trim((string) $post->post_excerpt);
        if (!empty($raw_excerpt)) {
            return wp_strip_all_tags($raw_excerpt);
        }

        // 2. Custom SEO description override (if Pi SEO Resolver is active)
        if (function_exists('sgh_pi_get_post_seo')) {
            $seo_data = sgh_pi_get_post_seo($post->ID);
            if (!empty($seo_data['seo_description'])) {
                return wp_strip_all_tags($seo_data['seo_description']);
            }
            if (!empty($seo_data['og_description'])) {
                return wp_strip_all_tags($seo_data['og_description']);
            }
        }

        // 3. Smart Auto-Extraction from post_content
        $content = $post->post_content;
        if (!empty($content)) {
            // Clean shortcodes & Gutenberg block annotations
            $content = strip_shortcodes($content);
            $content = preg_replace('/<!--\s*array\s*.*?-->/s', '', $content);
            $content = preg_replace('/<!--\s*\/?wp:[^>]+-->/s', '', $content);
            
            // Remove non-body elements: scripts, styles, TOC, tables, forms, figcaption
            $clean_html = preg_replace('/<(script|style|figcaption|table|form|nav|aside|header|footer)\b[^>]*>.*?<\/\1>/is', '', $content);
            $clean_html = preg_replace('/<div[^>]*id=[\'"]toc[^\'"]*[\'"][^>]*>.*?<\/div>/is', '', $clean_html);

            // Strip all heading tags (<h1>...</h1> through <h6>...</h6>) so title headers are not mixed into Sapo!
            $body_html = preg_replace('/<h[1-6]\b[^>]*>.*?<\/h[1-6]>/is', '', $clean_html);

            // Try to extract text from the FIRST valid <p> paragraph tag
            $extracted_text = '';
            if (preg_match_all('/<p\b[^>]*>(.*?)<\/p>/is', $body_html, $matches)) {
                foreach ($matches[1] as $p_content) {
                    $candidate = wp_strip_all_tags($p_content);
                    $candidate = preg_replace('/\s+/u', ' ', trim($candidate));
                    if (mb_strlen($candidate) > 15) {
                        $extracted_text = $candidate;
                        break;
                    }
                }
            }

            // Fallback: If no valid <p> paragraph found, strip all tags from $body_html
            if (empty($extracted_text)) {
                $extracted_text = wp_strip_all_tags($body_html);
                $extracted_text = preg_replace('/\s+/u', ' ', trim($extracted_text));
            }

            // Deduplicate if extracted text starts with the Post Title
            $title = trim((string) get_the_title($post));
            if (!empty($title) && !empty($extracted_text)) {
                if (mb_strpos(mb_strtolower($extracted_text), mb_strtolower($title)) === 0) {
                    $extracted_text = trim(mb_substr($extracted_text, mb_strlen($title)));
                    // Remove leading colons, dashes, or punctuation if left over
                    $extracted_text = preg_replace('/^[\:\–\—\-\|\.\s]+/u', '', $extracted_text);
                }
            }

            if (!empty($extracted_text)) {
                return wp_trim_words($extracted_text, $word_count, $more);
            }
        }

        // 4. Fallback Default
        return sprintf(
            __('%s — Chuyên tư vấn thiết bị bếp công nghiệp, quầy bar và giải pháp F&B chuyên nghiệp.', 'saigonhoreca'),
            get_the_title($post)
        );
    }
}

if (!function_exists('sgh_the_excerpt')) {
    /**
     * Render the smart excerpt/sapo HTML wrapper for post header (single.php).
     *
     * @param int|WP_Post|null $post_id Post ID or WP_Post object.
     * @param int $word_count Maximum word count.
     * @param string $wrapper_class CSS class for wrapper element.
     */
    function sgh_the_excerpt($post_id = null, $word_count = 35, $wrapper_class = 'sh-single__excerpt') {
        $post = get_post($post_id);
        if (!$post) return;

        $is_product = ($post->post_type === 'product');
        $excerpt    = sgh_get_the_excerpt($post, $word_count);

        if (empty($excerpt)) return;

        $itemprop = $is_product ? ' itemprop="description"' : '';
        printf(
            '<div class="%s"%s><p>%s</p></div>',
            esc_attr($wrapper_class),
            $itemprop,
            esc_html($excerpt)
        );
    }
}

/**
 * Filter WordPress core get_the_excerpt to ensure theme & plugin calls
 * automatically receive the Smart Fallback excerpt.
 */
add_filter('get_the_excerpt', function ($excerpt, $post) {
    if (is_admin()) return $excerpt;
    if (!empty($excerpt)) return $excerpt;

    return sgh_get_the_excerpt($post, 30);
}, 10, 2);
