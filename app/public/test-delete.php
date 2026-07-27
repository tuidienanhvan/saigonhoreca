<?php
require_once 'wp-load.php';
$p = get_page_by_path('du-an-old');
if ($p) {
    wp_delete_post($p->ID, true);
    echo "Deleted page {$p->ID}\n";
} else {
    echo "Page not found\n";
}
?>
