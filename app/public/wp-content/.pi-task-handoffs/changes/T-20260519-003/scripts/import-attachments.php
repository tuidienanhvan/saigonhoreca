<?php
/**
 * T-20260519-003 Phase C-2 — wp_posts attachment rows from manifest.
 *
 * Reads out/image-manifest.json (produced by import-images.py) and creates
 * a `post_type=attachment` row in wp_posts for each canonical image, plus
 * the standard meta keys WP uses for srcset:
 *   _wp_attached_file      = "<year>/<month>/<filename>"
 *   _wp_attachment_metadata = serialized array with width/height + sizes[]
 *
 * Run via:
 *   wp --path=<site> eval-file scripts/import-attachments.php
 *
 * Re-runnable: looks up existing attachment by _wp_attached_file and reuses
 * the post ID if found; otherwise inserts a new row.
 *
 * Output:
 *   - prints per-batch progress to stdout
 *   - writes out/attachment-map.json: { src_url => attachment_id } so the
 *     Phase D post importer can resolve image URLs to WP IDs.
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "FATAL: must be run via wp-cli `wp eval-file`\n");
    exit(1);
}

$root = dirname(__DIR__);  // .../changes/T-20260519-003
$manifest_path = $root . '/out/image-manifest.json';
$map_path      = $root . '/out/attachment-map.json';

if (!is_readable($manifest_path)) {
    fwrite(STDERR, "FATAL: missing manifest at $manifest_path\n");
    exit(1);
}

$manifest = json_decode(file_get_contents($manifest_path), true);
if (!is_array($manifest)) {
    fwrite(STDERR, "FATAL: manifest unparseable\n");
    exit(1);
}

echo "[import-attachments] manifest entries: " . count($manifest) . "\n";

$wp_upload_dir = wp_upload_dir();          // ['basedir' => ..., 'baseurl' => ...]
$base_dir = trailingslashit($wp_upload_dir['basedir']);
$base_url = trailingslashit($wp_upload_dir['baseurl']);

// Existing attachments keyed by _wp_attached_file → ID
global $wpdb;
$existing = $wpdb->get_results(
    "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file'",
    OBJECT_K
);
$by_file = [];
foreach ($existing as $row) {
    $by_file[$row->meta_value] = (int) $row->post_id;
}
echo "[import-attachments] existing attachments by _wp_attached_file: " . count($by_file) . "\n";

$created = 0;
$reused  = 0;
$failed  = 0;
$map     = [];

foreach ($manifest as $i => $entry) {
    $year   = $entry['year'];
    $month  = $entry['month'];
    $canon  = $entry['canonical_filename'];
    $rel    = "$year/$month/$canon";
    $abs    = $base_dir . $rel;
    $src    = $entry['src_url'];

    if (!is_file($abs)) {
        fwrite(STDERR, "  MISSING ON DISK: $rel\n");
        $failed++;
        continue;
    }

    if (isset($by_file[$rel])) {
        $att_id = $by_file[$rel];
        $reused++;
    } else {
        $title = pathinfo($canon, PATHINFO_FILENAME);
        $title = str_replace(['-', '_'], ' ', $title);

        $att_id = wp_insert_post([
            'post_title'     => $title,
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => wp_check_filetype($canon)['type'] ?: 'image/jpeg',
            'guid'           => $base_url . $rel,
        ], true);

        if (is_wp_error($att_id) || !$att_id) {
            fwrite(STDERR, "  INSERT FAIL $rel: "
                . (is_wp_error($att_id) ? $att_id->get_error_message() : '?') . "\n");
            $failed++;
            continue;
        }

        update_post_meta($att_id, '_wp_attached_file', $rel);
        $created++;
    }

    // Build metadata: discover dimensions of the canonical image.
    $size = @getimagesize($abs);
    $width  = $size ? (int) $size[0] : 0;
    $height = $size ? (int) $size[1] : 0;

    $sizes = [];
    foreach ($entry['size_variants'] as $v) {
        // WP "size name" is anything unique. We use "WxH" as key for clarity.
        $key = $v['width'] . 'x' . $v['height'];
        $sizes[$key] = [
            'file'      => $v['filename'],
            'width'     => $v['width'],
            'height'    => $v['height'],
            'mime-type' => wp_check_filetype($v['filename'])['type'] ?: 'image/jpeg',
            'filesize'  => @filesize(dirname($abs) . '/' . $v['filename']) ?: 0,
        ];
    }

    $metadata = [
        'width'    => $width,
        'height'   => $height,
        'file'     => $rel,
        'filesize' => @filesize($abs) ?: 0,
        'sizes'    => $sizes,
        'image_meta' => [],
    ];
    wp_update_attachment_metadata($att_id, $metadata);

    $map[$src] = (int) $att_id;

    if (($i + 1) % 50 === 0 || $i + 1 === count($manifest)) {
        printf("  [%4d/%4d] created=%4d reused=%4d failed=%4d\n",
            $i + 1, count($manifest), $created, $reused, $failed);
    }
}

file_put_contents($map_path, json_encode($map, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo "[import-attachments] DONE — created=$created reused=$reused failed=$failed\n";
echo "[import-attachments] map written -> " . str_replace('\\', '/', $map_path) . "\n";
