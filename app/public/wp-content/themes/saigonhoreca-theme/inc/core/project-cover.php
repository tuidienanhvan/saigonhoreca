<?php
/**
 * Project cover resolver — production-optimal, rename-safe, never-black.
 *
 * Replaces the per-card get_file_data() header read + the per-pageload
 * auto-setup iteration. A slug->cover map is built ONCE (verified with
 * file_exists via a deterministic fallback chain) and cached in a transient;
 * render does a single transient read + O(1) array lookup.
 *
 * Phase 1: serves a verified cover URL (never empty). Phase 2 layers real
 * media-library attachments for responsive srcset (see sgh_project_cover_attachment).
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

if (!defined('SGH_DEFAULT_COVER')) {
    // Relative to uploads/, served via sgh_img(). Always-valid last resort.
    define('SGH_DEFAULT_COVER', 'saigonhoreca/SGH-banner-02.webp');
}

if (!defined('SGH_COVER_MAP_KEY')) {
    define('SGH_COVER_MAP_KEY', 'sgh_cover_map_v1');
}

/**
 * A handful of project posts have a slug that diverges from their
 * single-project/<file>.php template name. Map post_name -> route basename.
 */
function sgh_cover_slug_alias($slug) {
    static $alias = [
        'grand-marble' => 'grand-marble-thuong-hieu-banh-cao-cap-nhat-ban',
    ];
    return isset($alias[$slug]) ? $alias[$slug] : $slug;
}

/**
 * Resolve a verified relative cover path via a deterministic fallback chain.
 * Never returns a missing file: ends at SGH_DEFAULT_COVER.
 */
function sgh_cover_resolve_rel($header, $folder, $uploads_dir) {
    $candidates = [];
    if ($header) {
        $candidates[] = $header;                                                  // 1. route Thumbnail: header target
    }
    if ($folder) {
        $candidates[] = $folder . '/' . $folder . '-thumbnail-project-cover.webp'; // 2. GOLD convention cover file
        $folder_abs = wp_normalize_path($uploads_dir . '/' . $folder);
        if (is_dir($folder_abs)) {
            $webps = glob($folder_abs . '/*.webp');                               // 3. first webp actually in the folder
            if ($webps) {
                sort($webps);
                $candidates[] = $folder . '/' . basename($webps[0]);
            }
        }
    }
    foreach ($candidates as $rel) {
        $abs = wp_normalize_path($uploads_dir . '/' . $rel);
        if (file_exists($abs)) return $rel;
        // header may be .jpg/.png with a .webp sibling (sgh_img swaps it)
        $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $rel);
        if ($webp !== $rel && file_exists(wp_normalize_path($uploads_dir . '/' . $webp))) return $webp;
    }
    return SGH_DEFAULT_COVER;                                                       // 4. global default — never black
}

/**
 * Build (and cache) the slug -> verified-cover-relative-path map.
 * Keyed by single-project/<route-slug>. Built once; invalidated on save/deploy.
 */
function sgh_cover_map() {
    $cached = get_transient(SGH_COVER_MAP_KEY);
    if (is_array($cached)) return $cached;

    $map = [];
    $dir = get_template_directory() . '/single-project/';
    $uploads_dir = function_exists('sgh_uploads_base_dir') ? sgh_uploads_base_dir() : wp_get_upload_dir()['basedir'];

    foreach ((array) glob($dir . '*.php') as $file) {
        $route_slug = basename($file, '.php');
        $data   = get_file_data($file, ['thumbnail' => 'Thumbnail']);
        $header = !empty($data['thumbnail']) ? trim($data['thumbnail']) : '';
        $folder = $header ? explode('/', $header)[0] : $route_slug;
        $map[$route_slug] = sgh_cover_resolve_rel($header, $folder, $uploads_dir);
    }

    set_transient(SGH_COVER_MAP_KEY, $map, 0); // no expiry — invalidated by hooks
    return $map;
}

/**
 * Public: a never-empty cover URL for a project slug (post_name).
 */
function sgh_project_cover_url($slug) {
    if (!$slug) return function_exists('sgh_img') ? sgh_img(SGH_DEFAULT_COVER) : SGH_DEFAULT_COVER;
    $map = sgh_cover_map();
    $key = isset($map[$slug]) ? $slug : sgh_cover_slug_alias($slug);
    $rel = isset($map[$key]) ? $map[$key] : SGH_DEFAULT_COVER;
    return function_exists('sgh_img') ? sgh_img($rel) : $rel;
}

/* ---- Cache invalidation (replaces the per-pageload iteration) ---- */
add_action('save_post_project', 'sgh_cover_flush_map');
add_action('switch_theme', 'sgh_cover_flush_map');
add_action('after_switch_theme', 'sgh_cover_flush_map');
function sgh_cover_flush_map() {
    delete_transient(SGH_COVER_MAP_KEY);
}

/* ---- Card image size (responsive attachments) ---- */
add_action('after_setup_theme', function () {
    add_image_size('sgh_project_card', 1024, 576, true);
});

/**
 * Ensure a project's featured image is a real attachment of its resolved cover,
 * with generated responsive sizes. Idempotent. Used by the one-off migration
 * (wp eval-file scratch/migrate_covers.php) AND the save_post hook below, so a
 * newly added / re-covered project self-heals without a manual resync.
 *
 * @return int attachment id (0 on failure)
 */
function sgh_project_cover_ensure($post_id) {
    $p = get_post($post_id);
    if (!$p || $p->post_type !== 'project') return 0;
    if (!function_exists('sgh_uploads_base_dir')) return 0;

    $uploads_dir = sgh_uploads_base_dir();
    $uploads_url = sgh_uploads_base_url();
    $url = sgh_project_cover_url($p->post_name);
    $rel = str_replace($uploads_url . '/', '', $url);
    $abs = wp_normalize_path($uploads_dir . '/' . $rel);
    if (!file_exists($abs)) return 0;

    $att_id = attachment_url_to_postid($url);
    if (!$att_id) {
        if (!function_exists('wp_generate_attachment_metadata')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }
        $ft = wp_check_filetype(basename($abs), null);
        $att_id = wp_insert_attachment([
            'guid'           => $url,
            'post_mime_type' => $ft['type'],
            'post_title'     => sanitize_file_name(pathinfo($abs, PATHINFO_FILENAME)),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ], $abs, $p->ID);
    }
    if (is_wp_error($att_id) || !$att_id) return 0;

    $meta = wp_get_attachment_metadata($att_id);
    if (empty($meta['sizes']['sgh_project_card'])) {
        if (!function_exists('wp_generate_attachment_metadata')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }
        wp_update_attachment_metadata($att_id, wp_generate_attachment_metadata($att_id, $abs));
    }

    if ((int) get_post_thumbnail_id($p->ID) !== (int) $att_id) {
        set_post_thumbnail($p->ID, $att_id);
    }
    return (int) $att_id;
}

/* ---- Self-heal on project save (admin only; guarded) ---- */
add_action('save_post_project', function ($post_id) {
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) return;
    if (!is_admin()) return;
    sgh_cover_flush_map();          // rebuild slug->cover map (covers URL resolver)
    try {
        sgh_project_cover_ensure($post_id); // refresh the responsive attachment + featured image
    } catch (\Throwable $e) {
        // never block a save on cover sync
    }
}, 20);
