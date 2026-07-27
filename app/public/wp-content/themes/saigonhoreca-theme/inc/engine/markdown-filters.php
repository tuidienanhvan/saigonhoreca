<?php
/**
 * Markdown Filters
 * 
 * Extracts and cleans up markdown formatting from content and excerpts.
 * Used primarily for content imported by AI/bots.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/**
 * Strip markdown syntax from excerpt (**, *, #, []() etc.)
 */
function saigonhouse_strip_markdown_excerpt($excerpt) {
    if (empty($excerpt)) return $excerpt;
    // Decode HTML entities first (&#42; → *, &amp; → &, &#8211; → –)
    $excerpt = html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8');
    // Remove **bold** and *italic* markers
    $excerpt = preg_replace('/\*\*(.+?)\*\*/', '$1', $excerpt);
    $excerpt = preg_replace('/(?<!\*)\*([^*]+?)\*(?!\*)/', '$1', $excerpt);
    // Remove # headings
    $excerpt = preg_replace('/^#{1,6}\s+/m', '', $excerpt);
    // Remove markdown links [text](url) → text
    $excerpt = preg_replace('/\[(.+?)\]\([^)]+\)/', '$1', $excerpt);
    // Clean extra whitespace
    $excerpt = preg_replace('/\s+/', ' ', trim($excerpt));
    return $excerpt;
}
add_filter('the_excerpt', 'saigonhouse_strip_markdown_excerpt');
add_filter('get_the_excerpt', 'saigonhouse_strip_markdown_excerpt');

/**
 * Parse basic markdown in post content (for imported posts with ** syntax)
 * Runs early (priority 5) before other content filters
 */
add_filter('the_content', function ($content) {
    if (empty($content) || strpos($content, '**') === false) return $content;
    // Skip pure Gutenberg block content (no markdown)
    if (strpos($content, '<!-- wp:') === 0 && strpos($content, '**') > 100) return $content;
    $content = preg_replace('/\*\*([^*<>]+?)\*\*/', '<strong>$1</strong>', $content);
    return $content;
}, 5);

/**
 * Strip <code> tags wrapping normal Vietnamese text (from markdown backtick import)
 * Real code has ASCII-only content; Vietnamese text in <code> is a conversion artifact.
 */
add_filter('the_content', function ($content) {
    if (empty($content) || strpos($content, '<code>') === false) return $content;
    // Unwrap <code> containing Vietnamese/Unicode characters (not real code)
    return preg_replace_callback('/<code>([^<]+)<\/code>/', function ($m) {
        // If content has Vietnamese diacritics or is mostly non-ASCII, strip the <code> wrapper
        if (preg_match('/[\x{00C0}-\x{024F}\x{1E00}-\x{1EFF}]/u', $m[1])) {
            return $m[1];
        }
        return $m[0];
    }, $content);
}, 6);

/**
 * Fix AI-generated Gutenberg paragraph blocks missing <p> tags
 * This ensures do_blocks correctly renders proper HTML elements, 
 * allowing CSS (like lobotomized owl * + *) to style the spacing.
 */
add_filter('the_content', function ($content) {
    if (empty($content) || strpos($content, '<!-- wp:paragraph') === false) return $content;

    // Use regex to find wp:paragraph blocks that DON'T contain <p> tags at the start
    $content = preg_replace_callback('/(<!-- wp:paragraph[^>]*-->)(.*?)(<!-- \/wp:paragraph -->)/is', function($m) {
        $inner = trim($m[2]);
        if (!empty($inner) && stripos($inner, '<p') !== 0) {
            // Wrap bare text in <p> tag
            return $m[1] . "\n<p>" . $inner . "</p>\n" . $m[3];
        }
        return $m[0];
    }, $content);

    return $content;
}, 8); // Priority 8 runs BEFORE do_blocks (priority 9)
