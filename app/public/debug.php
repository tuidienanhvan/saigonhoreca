<?php
require_once __DIR__ . '/wp-load.php';
header('Content-Type: text/plain');

$_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';

$upload_dir = wp_get_upload_dir();

echo "Simulated sgh_img outputs with image/webp support:\n";
$img_rel_paths = [
    'bling-bling-club/bling-bling-club-quay-dj-bieu-dien-live.jpg',
    'bling-bling-club/bling-bling-club-mon-an-ruou-vang-bottega-rose.jpg',
    'bambino/bambino-he-thong-bep-nong-ata-y.jpg',
    'bambino/bambino-mon-an-khai-vi-mortadella-ham.png',
];

foreach ($img_rel_paths as $rel_path) {
    $full_path = $upload_dir['basedir'] . '/' . $rel_path;
    $exists = file_exists($full_path) ? 'YES' : 'NO';
    $url = sgh_img($rel_path);
    echo "Rel: $rel_path\n  Exists on disk: $exists\n  URL: $url\n\n";
}
?>
