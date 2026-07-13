<?php
/**
 * Media Routing — route NEW media uploads into per-type folders instead of YYYY/MM,
 * with a runtime-editable config exposed over the WP REST API so the Pi backend /
 * Pi dashboard can read & change it without touching code.
 *
 *   product  -> uploads/products/<product-slug>/
 *   post     -> uploads/blog/<post-slug>/
 *   (every other post type keeps the WordPress default year/month folders)
 *
 * NEW uploads only — never moves or rewrites existing files/DB. Lives in the theme
 * (no plugin needed); loaded from functions.php.
 *
 * DETECTS THE TARGET (priority): explicit context -> $_REQUEST['post_id'] (editor)
 *   -> $_REQUEST['post'] (WP REST media endpoint, used by the Pi backend).
 *
 * REST (auth: manage_options):
 *   GET  /wp-json/sgh/v1/media-routing            -> current config + defaults
 *   POST /wp-json/sgh/v1/media-routing            -> update {enabled, flat, map}
 *   GET  /wp-json/sgh/v1/media-routing/preview?post_id=ID -> folder a post would use
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

if (!defined('SGH_MEDIA_ROUTING_OPTION')) define('SGH_MEDIA_ROUTING_OPTION', 'sgh_media_routing');

/** Built-in defaults (used when no option saved / as merge base). */
function sgh_media_routing_defaults() {
    return [
        'enabled' => true,
        'flat'    => false, // false = per-slug subfolder; true = flat products/ & blog/
        'map'     => ['product' => 'products', 'post' => 'blog'],
    ];
}

/** Effective config = defaults merged with the saved option (+ optional constant override). */
function sgh_media_routing_config() {
    $saved = get_option(SGH_MEDIA_ROUTING_OPTION, []);
    if (!is_array($saved)) $saved = [];
    $cfg = array_merge(sgh_media_routing_defaults(), $saved);
    if (empty($cfg['map']) || !is_array($cfg['map'])) $cfg['map'] = sgh_media_routing_defaults()['map'];
    if (defined('SGH_MEDIA_ROUTING_FLAT')) $cfg['flat'] = (bool) SGH_MEDIA_ROUTING_FLAT;
    $cfg['enabled'] = (bool) $cfg['enabled'];
    $cfg['flat']    = (bool) $cfg['flat'];
    return $cfg;
}

/** Post-type => base folder map (filterable). */
function sgh_media_routing_map() {
    return apply_filters('sgh_media_routing_map', sgh_media_routing_config()['map']);
}

/** Importer/crawler helpers: set context right before sideloading media. */
function sgh_set_upload_context($post_id, $post_type = '') {
    $GLOBALS['sgh_upload_post_id']   = (int) $post_id;
    $GLOBALS['sgh_upload_post_type'] = $post_type !== '' ? $post_type : (get_post_type($post_id) ?: '');
}
function sgh_clear_upload_context() {
    unset($GLOBALS['sgh_upload_post_id'], $GLOBALS['sgh_upload_post_type']);
}

/** Resolve the upload subdir ('/products/<slug>' etc.) for a post, or '' to keep WP default. */
function sgh_media_routing_subdir_for($post_id, $post_type = '') {
    $cfg = sgh_media_routing_config();
    if (empty($cfg['enabled'])) return '';
    if ($post_type === '' && $post_id) $post_type = (string) get_post_type($post_id);
    $map = sgh_media_routing_map();
    if ($post_type === '' || !isset($map[$post_type])) return '';

    $base = trim($map[$post_type], '/');
    $slug = '';
    if (empty($cfg['flat']) && $post_id) {
        $slug = get_post_field('post_name', $post_id);
        $slug = $slug ? sanitize_title($slug) : '';
    }
    return '/' . $base . ($slug !== '' ? '/' . $slug : '');
}

add_filter('upload_dir', 'sgh_media_routing_upload_dir');
function sgh_media_routing_upload_dir($dirs) {
    if (!empty($dirs['error'])) return $dirs;

    // Resolve target post id from every common upload context.
    $post_id = 0;
    if (!empty($GLOBALS['sgh_upload_post_id'])) {
        $post_id = (int) $GLOBALS['sgh_upload_post_id'];
    } elseif (isset($_REQUEST['post_id']) && (int) $_REQUEST['post_id'] > 0) {
        $post_id = (int) $_REQUEST['post_id'];
    } elseif (isset($_REQUEST['post']) && (int) $_REQUEST['post'] > 0) {
        $post_id = (int) $_REQUEST['post'];
    }

    // Resolve target post type.
    $post_type = '';
    if (!empty($GLOBALS['sgh_upload_post_type'])) {
        $post_type = (string) $GLOBALS['sgh_upload_post_type'];
    } elseif ($post_id) {
        $post_type = (string) get_post_type($post_id);
    }

    $sub = sgh_media_routing_subdir_for($post_id, $post_type);
    if ($sub === '') return $dirs; // not routed -> leave WP default untouched

    $dirs['subdir'] = $sub;
    $dirs['path']   = $dirs['basedir'] . $sub;
    $dirs['url']    = $dirs['baseurl'] . $sub;
    return $dirs;
}

/* ============================================================================
 * REST API — let the Pi backend / Pi dashboard read & manage the config.
 * ========================================================================== */

add_action('rest_api_init', 'sgh_media_routing_rest_routes');
function sgh_media_routing_rest_routes() {
    // Register under BOTH namespaces:
    //   sgh/v1 — theme-native (standalone / direct use)
    //   pi/v1  — matches the Pi dashboard's WordPress-call convention (client.js routes
    //            '/pi/v1/*' to wpSiteUrl/wp-json + Basic auth) so it consumes with zero
    //            client changes. (Safe now that functions.php guards the admin requires.)
    foreach (['sgh/v1', 'pi/v1'] as $ns) {
        register_rest_route($ns, '/media-routing', [
            [
                'methods'             => 'GET',
                'callback'            => 'sgh_media_routing_rest_get',
                'permission_callback' => 'sgh_media_routing_rest_perm',
            ],
            [
                'methods'             => 'POST',
                'callback'            => 'sgh_media_routing_rest_set',
                'permission_callback' => 'sgh_media_routing_rest_perm',
            ],
        ]);
        register_rest_route($ns, '/media-routing/preview', [
            'methods'             => 'GET',
            'callback'            => 'sgh_media_routing_rest_preview',
            'permission_callback' => 'sgh_media_routing_rest_perm',
            'args'                => ['post_id' => ['required' => true, 'sanitize_callback' => 'absint']],
        ]);
    }
}

function sgh_media_routing_rest_perm() {
    return current_user_can('manage_options');
}

function sgh_media_routing_rest_get() {
    return rest_ensure_response([
        'config'   => sgh_media_routing_config(),
        'defaults' => sgh_media_routing_defaults(),
        'option'   => SGH_MEDIA_ROUTING_OPTION,
    ]);
}

function sgh_media_routing_rest_set($req) {
    $body = $req->get_json_params();
    if (!is_array($body)) $body = $req->get_params();

    $new = sgh_media_routing_config();

    if (array_key_exists('enabled', $body)) $new['enabled'] = (bool) rest_sanitize_boolean($body['enabled']);
    if (array_key_exists('flat', $body))    $new['flat']    = (bool) rest_sanitize_boolean($body['flat']);

    if (isset($body['map']) && is_array($body['map'])) {
        $clean = [];
        foreach ($body['map'] as $pt => $folder) {
            $pt     = sanitize_key((string) $pt);
            $folder = preg_replace('~[^a-z0-9/_-]~i', '', trim((string) $folder, '/'));
            if ($pt !== '' && $folder !== '') $clean[$pt] = $folder;
        }
        if ($clean) $new['map'] = $clean;
    }

    // persist only the user-facing keys (constant override stays out of storage)
    update_option(SGH_MEDIA_ROUTING_OPTION, [
        'enabled' => $new['enabled'],
        'flat'    => $new['flat'],
        'map'     => $new['map'],
    ]);

    return rest_ensure_response(['updated' => true, 'config' => sgh_media_routing_config()]);
}

function sgh_media_routing_rest_preview($req) {
    $pid  = (int) $req->get_param('post_id');
    $type = $pid ? (string) get_post_type($pid) : '';
    $sub  = sgh_media_routing_subdir_for($pid, $type);
    $up   = wp_upload_dir(null, false);
    return rest_ensure_response([
        'post_id'   => $pid,
        'post_type' => $type ?: null,
        'routed'    => $sub !== '',
        'subdir'    => $sub !== '' ? $sub : ($up['subdir'] ?? ''),
        'url'       => $up['baseurl'] . ($sub !== '' ? $sub : ($up['subdir'] ?? '')),
    ]);
}
