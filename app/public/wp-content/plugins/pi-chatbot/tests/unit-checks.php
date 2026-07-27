<?php
/**
 * unit-checks.php — RETIRED, superseded by the full suite set.
 *
 * Every assertion that used to live here (fold_diacritics, language detection,
 * product-code short-circuit, Router::validate incl. the 'none' verdict fix,
 * Reranker::apply/rerank, LLM_Client::extract_json) now lives in
 * tests/suite-pure.php and tests/suite-retrieval.php, verified assertion-for-
 * assertion against this file before it was retired — nothing was dropped.
 *
 * This file is kept only as a pointer (not deleted) so a bookmark, a script,
 * or muscle memory pointing at `wp eval-file .../unit-checks.php` still finds
 * something explaining where things went, rather than a confusing "file not
 * found".
 *
 * Run the real suite instead:
 *   wp eval-file wp-content/plugins/pi-chatbot/tests/run-all.php
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Must be run via `wp eval-file`, not directly with the PHP CLI.\n");
    exit(1);
}

echo "\nunit-checks.php has been retired — its checks now live in suite-pure.php\n"
    . "and suite-retrieval.php (verified assertion-for-assertion before removal).\n\n"
    . "Run instead:\n"
    . "  wp eval-file wp-content/plugins/pi-chatbot/tests/run-all.php\n\n";
exit(0);
