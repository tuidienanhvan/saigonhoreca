<?php
define('ABSPATH', __DIR__ . '/');
$META = []; $OPT = []; $POSTS = []; $SLEEP = 0.0; $THOI = 0.0;
foreach (range(1, 10) as $i) $POSTS[$i] = ['type' => 'sh_lead', 'title' => "Lead $i"];
$POSTS[11] = ['type' => 'post', 'title' => 'khong phai lead'];   // ID la: phai vao errors, khong crash

class WP_REST_Request { private $p; function __construct($m='', $r='', $p=[]) { $this->p = $p; } function get_json_params() { return $this->p; } function get_param($k) { return $this->p[$k] ?? null; } }
class WP_REST_Response { public $data; public $status; function __construct($d, $s = 200) { $this->data = $d; $this->status = $s; } }
class FakeWpdb {
    public $posts = 'wp_posts';
    function prepare($q, ...$a) { foreach ($a as $v) $q = preg_replace('/%[sd]/', is_int($v) ? $v : "'$v'", $q, 1); return $q; }
    function get_col($sql) {
        global $POSTS; preg_match('/ID > (\d+)/', $sql, $m); $after = (int) $m[1];
        $ids = array_keys(array_filter($POSTS, fn($p) => true)); sort($ids);
        return array_values(array_filter($ids, fn($id) => $id > $after));
    }
}
$wpdb = new FakeWpdb();
function get_option($k, $d = []) { global $OPT; return $OPT[$k] ?? $d; }
function update_option($k, $v, $a = null) { global $OPT; $OPT[$k] = $v; }
function get_post($id) { global $POSTS; global $SLEEP; if ($SLEEP) usleep((int)($SLEEP * 1e6)); $p = $POSTS[$id] ?? null; return $p ? (object)['post_title' => $p['title'], 'post_content' => '', 'post_date' => ''] : null; }
function get_post_type($id) { global $POSTS; return $POSTS[$id]['type'] ?? false; }
function get_post_meta($id, $k, $s = false) { global $META; return $META[$id][$k] ?? ''; }
function update_post_meta($id, $k, $v) { global $META; $META[$id][$k] = $v; }
function wp_json_encode($v) { return json_encode($v); }
function gmdate_($f) { return gmdate($f); }
function add_action(...$a) {}

require getenv('F');
$cls = 'Pi_Api_Leads';
if (!class_exists($cls)) { foreach (get_declared_classes() as $c) if (method_exists($c, 'rescore_leads')) $cls = $c; }
$ok = 0; $sai = 0;
function kiem($d, $m) { global $ok, $sai; echo ($d ? "  [OK]  " : "  [SAI] ") . $m . "\n"; $d ? $ok++ : $sai++; }
$req = fn($p) => new WP_REST_Request('POST', '', $p);

// 1. body {} (nut Rescore cua dashboard) — truoc day 400
$r = $cls::rescore_leads($req([]));
kiem($r->status === 200 && $r->data['success'] === true, "body {} khong con tra 400 (status {$r->status})");
kiem($r->data['processed'] === 11 && $r->data['partial'] === false && $r->data['next_after_id'] === null, "chay xong 11 ID, khong partial");
kiem($r->data['scored'] === 10 && $r->data['errors'] === [11], "10 lead duoc cham, ID la (11) vao errors thay vi lam chet request");
kiem(isset($OPT['pi_leads_scoring_last_rescore']), "ghi moc last_rescore khi chay XONG");

// 2. lead_ids cu the van chay dung, khong bi coi la 'all'
$OPT = [];
$r = $cls::rescore_leads($req(['lead_ids' => [2, 4]]));
kiem($r->data['total'] === 2 && $r->data['scored'] === 2, "lead_ids [2,4] chi cham 2 lead (total {$r->data['total']})");

// 3. ngan sach thoi gian
// 60 lead x 0.5s = 30s > ngan sach 20s
foreach (range(12, 70) as $i) $POSTS[$i] = ['type' => 'sh_lead', 'title' => "Lead $i"];
unset($POSTS[11]);
$SLEEP = 0.5; $OPT = [];
$t = microtime(true);
$r = $cls::rescore_leads($req(['all' => true]));
$dt = microtime(true) - $t;
kiem($r->data['partial'] === true, "60 lead x 0.5s vuot ngan sach -> partial=true");
kiem($dt < 22.5, sprintf("request dung ~ngan sach 20s, khong chay het 30s+ (mat %.1fs)", $dt));
kiem($r->data['remaining'] > 0 && $r->data['next_after_id'] !== null, "tra remaining={$r->data['remaining']} va next_after_id={$r->data['next_after_id']}");
kiem(!isset($OPT['pi_leads_scoring_last_rescore']), "KHONG ghi last_rescore khi moi la trang do dang");
// 4. tiep tuc theo con tro cho toi het
$tong = $r->data['processed']; $vong = 1;
while ($r->data['partial'] && $vong < 5) {
    $r = $cls::rescore_leads($req(['all' => true, 'after_id' => $r->data['next_after_id']]));
    $tong += $r->data['processed']; $vong++;
}
kiem($tong === 69 || $tong === 60 || $tong >= 59, "theo con tro het danh sach, tong da xu ly = $tong sau $vong request");
kiem($r->data['partial'] === false && isset($OPT['pi_leads_scoring_last_rescore']), "trang cuoi partial=false va ghi last_rescore");
// 5. con tro qua cuoi -> khong 400
$r = $cls::rescore_leads($req(['all' => true, 'after_id' => 99999]));
kiem($r->status === 200 && $r->data['remaining'] === 0, "after_id qua cuoi -> 200, remaining 0 (khong 400)");

echo $sai ? "KET QUA: $sai SAI\n" : "KET QUA: DAT ($ok)\n"; exit($sai ? 1 : 0);
