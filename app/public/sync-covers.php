<?php
require_once 'wp-load.php';

echo "Starting sync...\n";
$args = array(
    'post_type' => 'project',
    'posts_per_page' => -1,
);
$projects = get_posts($args);

$results = [];
foreach ($projects as $p) {
    if (function_exists('sgh_project_cover_ensure')) {
        $att_id = sgh_project_cover_ensure($p->ID);
        $results[$p->post_name] = $att_id;
    }
}

echo "<pre>";
print_r($results);
echo "</pre>";
?>
