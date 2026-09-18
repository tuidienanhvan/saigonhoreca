<?php
/**
 * So sanh dau ra Pi_Api_Content::get_list truoc/sau khi toi uu — chay tren DB WordPress THAT.
 *
 * Toi uu N+1 rat de doi tham ket qua ma khong bao loi, nen phai so JSON tung byte:
 *   1. wp eval-file tests/compare-get-list.php cu.json      (tren ban CHUA sua)
 *   2. wp eval-file tests/compare-get-list.php moi.json     (tren ban da sua)
 *   3. diff <(python -m json.tool cu.json) <(python -m json.tool moi.json)   -> phai rong
 * Moi ca in ra so truy van + thoi gian de doi chieu. Chi DOC, khong ghi DB.
 */
global $wpdb;
$ra = $args[0] ?? 'ra.json';
wp_set_current_user(1);
delete_transient('pi_api_cpt_words_project'); // khong de cache lan chay truoc lam lech phep do

$cases = [
    ['type' => 'page', 'per_page' => 50],
    ['type' => 'post', 'per_page' => 50],
    ['type' => 'project', 'per_page' => 30],
    ['type' => 'post', 'per_page' => 50, 'status' => 'any', 'search' => 'nha'],
    ['type' => 'page', 'per_page' => 5, 'page' => 2],
];
$out = []; $tong_q = 0;
foreach ($cases as $i => $c) {
    // xoa cache doi tuong de moi case do nhu request moi
    wp_cache_flush();
    $req = new WP_REST_Request('GET', '/pi/v1/content');
    foreach ($c as $k => $v) $req->set_param($k, $v);
    $before = $wpdb->num_queries;
    $t = microtime(true);
    $res = Pi_Api_Content::get_list($req);
    $ms = (microtime(true) - $t) * 1000;
    $q = $wpdb->num_queries - $before; $tong_q += $q;
    $d = $res->get_data();
    $out["case$i"] = $d;
    fprintf(STDERR, "%-46s items=%-3d queries=%-4d %.0fms\n", json_encode($c), count($d['items']), $q, $ms);
}
fprintf(STDERR, "TONG truy van: %d\n", $tong_q);
file_put_contents($ra, json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
