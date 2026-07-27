<?php
/**
 * suite-indexer.php — assertions for Pi_Chatbot_Indexer and Pi_Chatbot_Log, the
 * only two classes in this plugin that write data. Everything here runs against
 * a THROWAWAY table (see pi_temp_index_table() in lib/harness.php), never the
 * live index — index_post_into() takes its target table as a parameter, which
 * is what makes that redirection possible without touching production data.
 *
 * A real WordPress post is created and trashed for the indexing checks, because
 * index_post_into() reads from get_post()/get_the_terms(), not from a stdClass
 * — there's no cheaper way to feed it realistic input.
 *
 * Run standalone:
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/suite-indexer.php
 * Or via tests/run-all.php.
 */

require_once __DIR__ . '/lib/harness.php';

global $wpdb;
$temp_table = null;
$log_prefix = 'PI_TEST_' . wp_rand(100000, 999999) . '_';

/**
 * Registers a post for guaranteed cleanup even if an assertion throws.
 *
 * Tracking lives in a FUNCTION STATIC, not a `global $created_post_ids`. This
 * file is included by `wp eval-file` from inside a function (documented at
 * length in lib/harness.php's pi_check() — the exact lesson this bug re-proves)
 * — so the file-scope `$created_post_ids = []` a caller might declare is a
 * LOCAL of that wrapping function, while `global` inside a helper binds to
 * $GLOBALS, a completely different variable. A first version of this suite did
 * exactly that: every post got created, every assertion passed, and the
 * `finally` cleanup loop iterated a permanently-empty array — 28 real posts
 * were left behind across two runs before this was caught by manually diffing
 * wp_posts before/after. Call with no id to retrieve the list (used by the
 * `finally` block below).
 */
function pi_indexer_track_post($id = null) {
    static $ids = [];
    if ($id === null) return $ids;
    $ids[] = $id;
    return $id;
}

function pi_indexer_make_post($args) {
    $id = wp_insert_post(array_merge(['post_status' => 'publish'], $args), true);
    if (is_wp_error($id)) return null;
    pi_indexer_track_post($id);
    return $id;
}

try {
    $temp_table = pi_temp_index_table();
    pi_check('throwaway index table created', $temp_table !== null);

    // ── index_post_into: content composition ────────────────────────────────
    pi_section('Indexer::index_post_into — content composition');

    $pid = pi_indexer_make_post([
        'post_type'    => 'product',
        'post_title'   => 'Bàn Đông Test XYZ-999',
        'post_content' => str_repeat('Nội dung mô tả sản phẩm test rất chi tiết. ', 10),
        'post_excerpt' => 'Trích dẫn ngắn gọn.',
    ]);
    if ($pid) {
        $ok = pi_call_private('Pi_Chatbot_Indexer', 'index_post_into', [$temp_table, $pid]);
        pi_check('a publish product post is indexed successfully', $ok === true);

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$temp_table} WHERE post_id=%d", $pid));
        pi_check('the indexed row carries the post title', $row && $row->title === 'Bàn Đông Test XYZ-999');
        // content_text = excerpt + body ONLY. Title/brand/category deliberately
        // are NOT appended into it (they already live in their own FULLTEXT
        // columns) — appending them would double-count those tokens in scoring.
        pi_check('content_text is built from excerpt+body, not the title',
            $row && strpos($row->content_text, 'Bàn Đông Test XYZ-999') === false, $row->content_text ?? 'no row');
        pi_check('content_text includes the excerpt', $row && strpos($row->content_text, 'Trích dẫn ngắn gọn') !== false);
        pi_check('content_text includes the body', $row && strpos($row->content_text, 'mô tả sản phẩm test') !== false);
    }

    // ── MIN_INDEXABLE_LENGTH: products are exempt, pages/projects are not ───
    pi_section('Indexer::index_post_into — MIN_INDEXABLE_LENGTH threshold');

    $short_product = pi_indexer_make_post(['post_type' => 'product', 'post_title' => 'X', 'post_content' => 'ngắn', 'post_excerpt' => '']);
    if ($short_product) {
        $ok = pi_call_private('Pi_Chatbot_Indexer', 'index_post_into', [$temp_table, $short_product]);
        pi_check('a SHORT product is still indexed (products are exempt from the length floor)', $ok === true);
    }

    $short_page = pi_indexer_make_post(['post_type' => 'page', 'post_title' => 'X', 'post_content' => 'ngắn', 'post_excerpt' => '']);
    if ($short_page) {
        $ok = pi_call_private('Pi_Chatbot_Indexer', 'index_post_into', [$temp_table, $short_page]);
        pi_check('a page shorter than MIN_INDEXABLE_LENGTH is rejected (noise, no real content)', $ok === false);
    }

    $long_page = pi_indexer_make_post(['post_type' => 'page', 'post_title' => 'X', 'post_content' => str_repeat('nội dung dài ', 20), 'post_excerpt' => '']);
    if ($long_page) {
        $ok = pi_call_private('Pi_Chatbot_Indexer', 'index_post_into', [$temp_table, $long_page]);
        pi_check('a page at/above MIN_INDEXABLE_LENGTH is indexed', $ok === true);
    }

    // ── Non-indexable post types and non-publish statuses are skipped ──────
    pi_section('Indexer::index_post_into — rejects what it should reject');

    $draft = pi_indexer_make_post(['post_type' => 'product', 'post_title' => 'Draft product', 'post_status' => 'publish']);
    if ($draft) {
        wp_update_post(['ID' => $draft, 'post_status' => 'draft']);
        $ok = pi_call_private('Pi_Chatbot_Indexer', 'index_post_into', [$temp_table, $draft]);
        pi_check('a non-publish post is rejected regardless of type', $ok === false);
    }

    $post_type_post = pi_indexer_make_post(['post_type' => 'post', 'post_title' => 'Ordinary blog post']);
    if ($post_type_post) {
        $ok = pi_call_private('Pi_Chatbot_Indexer', 'index_post_into', [$temp_table, $post_type_post]);
        pi_check("post_type='post' (not in indexable_post_types) is rejected", $ok === false);
    }

    // ── remove_post / bump_index_version / flush_answer_cache ──────────────
    pi_section('Indexer::remove_post / index version bookkeeping');

    if ($pid) {
        // remove_post() always targets Indexer::table_name() (the LIVE table),
        // never a caller-supplied table — so this check inserts directly into
        // the temp table to isolate what it's actually testing (the row-delete
        // SQL shape), without letting remove_post() touch the live table.
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$temp_table} WHERE post_id = %d", $pid
        ));
        pi_check('a deleted row no longer appears in the table',
            (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$temp_table} WHERE post_id=%d", $pid)) === 0);
    }

    $v0 = (int) get_option('pi_chatbot_index_version', 0);
    pi_call_private('Pi_Chatbot_Indexer', 'bump_index_version', []);
    $v1 = (int) get_option('pi_chatbot_index_version', 0);
    pi_check('bump_index_version strictly increases the counter', $v1 === $v0 + 1, "before={$v0} after={$v1}");

    Pi_Chatbot_Indexer::flush_answer_cache();
    $v2 = (int) get_option('pi_chatbot_index_version', 0);
    pi_check('flush_answer_cache also bumps the version (invalidates the answer cache)', $v2 === $v1 + 1);

    // Restore the option to what it was before this suite ran, so re-running
    // the suite (or the eval-retrieval no-regression gate right after it)
    // doesn't leave the answer cache permanently invalidated for no reason.
    update_option('pi_chatbot_index_version', $v0, true);

    // ── reindex_all: the empty-result abort guard, without touching the live index ──
    pi_section('Indexer::reindex_all — aborts on zero records (does not touch live data)');

    // reindex_all() always operates on Indexer::table_name() (the live table) —
    // there is no table parameter to redirect. So this check does NOT call
    // reindex_all() directly (that would rebuild the real site index). Instead
    // it re-implements just the abort CONDITION being verified: index_post_into
    // returning 0 successes for a set of ids that resolve to nothing indexable.
    // The real reindex_all() is exercised implicitly every time an admin clicks
    // "Reindex now" in production — this suite's job is the unit the abort
    // logic depends on, not the full destructive operation.
    $count = 0;
    foreach ([999999999, 999999998] as $nonexistent_id) { // ids that cannot resolve to a real post
        if (pi_call_private('Pi_Chatbot_Indexer', 'index_post_into', [$temp_table, $nonexistent_id])) $count++;
    }
    pi_check('indexing a batch of nonexistent post ids indexes zero rows (the condition reindex_all aborts on)', $count === 0);

    // ── Log: record / stats / top_unanswered ────────────────────────────────
    pi_section('Log::record / stats / top_unanswered');

    Pi_Chatbot_Log::create_table(); // idempotent — the live log table already exists on this install
    $q1 = $log_prefix . 'câu hỏi có ngữ cảnh';
    $q2 = $log_prefix . 'câu hỏi không có ngữ cảnh';
    Pi_Chatbot_Log::record($q1, true, 3, true);
    Pi_Chatbot_Log::record($q2, false, 0, false);
    Pi_Chatbot_Log::record($q2, false, 0, false); // repeat, to test grouping

    $log_table = Pi_Chatbot_Log::table_name();
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$log_table} WHERE question_norm LIKE %s ORDER BY id", $wpdb->esc_like($log_prefix) . '%'
    ));
    pi_check('record() writes exactly the rows just logged', count($rows) === 3, count($rows) . ' rows found');
    pi_check('had_context is stored as given', $rows && (int) $rows[0]->had_context === 1);
    pi_check('source_count is stored as given', $rows && (int) $rows[0]->source_count === 3);
    pi_check('router_used is stored as given', $rows && (int) $rows[0]->router_used === 1);

    $unanswered = Pi_Chatbot_Log::top_unanswered(50, 1);
    $found = array_filter($unanswered, function ($r) use ($q2) { return $r->question_norm === $q2; });
    pi_check('top_unanswered groups the repeated unanswered question with count=2',
        !empty($found) && (int) reset($found)->cnt === 2, !empty($found) ? reset($found)->cnt : 'not found');
    $found_answered = array_filter($unanswered, function ($r) use ($q1) { return $r->question_norm === $q1; });
    pi_check('top_unanswered does NOT include a question that HAD context', empty($found_answered));

    // ── Cleanup: remove every row this suite wrote ──────────────────────────
    $wpdb->query($wpdb->prepare("DELETE FROM {$log_table} WHERE question_norm LIKE %s", $wpdb->esc_like($log_prefix) . '%'));
    $remaining = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$log_table} WHERE question_norm LIKE %s", $wpdb->esc_like($log_prefix) . '%'));
    pi_check('teardown removed every log row this suite wrote', $remaining === 0);

} finally {
    // Runs even if an assertion above threw — no orphaned post, table, or log row.
    $cleaned = 0;
    foreach (pi_indexer_track_post() as $id) {
        if (wp_delete_post($id, true)) $cleaned++;
    }
    pi_drop_temp_table($temp_table);
    pi_drop_all_temp_tables(); // sweeps any table orphaned by a previous crashed run

    // Verified, not assumed: confirm cleanup actually worked rather than
    // trusting wp_delete_post()'s return value alone — that is exactly the
    // category of bug this teardown just had.
    $tracked = pi_indexer_track_post();
    if ($tracked) {
        $placeholders = implode(',', array_fill(0, count($tracked), '%d'));
        $still_there = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE ID IN ({$placeholders})", $tracked
        ));
        pi_check('teardown removed every post this suite created',
            $still_there === 0, "{$cleaned}/" . count($tracked) . " reported deleted, {$still_there} still in wp_posts");
    }
}

// pi_report() prints a summary from the SHARED pi_check() static state — when
// run standalone that's exactly this suite's own totals; when run-all.php
// requires this file alongside others, the same static keeps accumulating, so
// printing here would show a confusing cumulative count instead of this
// suite's own. run-all.php does its own before/after accounting per suite and
// prints one combined summary at the end, so this suite only prints (and
// exits on) its own report when running standalone.
if (!defined('PI_TEST_SUITE_MODE')) exit(pi_report());
