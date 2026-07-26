<?php
/**
 * Indexer — internal FULLTEXT table for retrieval (no embeddings/vector DB).
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Indexer {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    /**
     * Schema version — bump this number whenever the table structure changes so
     * maybe_upgrade() runs dbDelta on sites that are already installed. Previously
     * create_table() ONLY ran on activate, so every schema change required a
     * manual deactivate/activate (this was the root cause of SAG's entities table
     * never being created, which in turn made the RENAME TABLE fail outright and
     * silently broke reindexing).
     *
     * 3 — added a wp_pi_chatbot_vectors table for semantic embeddings.
     * 4 — removed it again: the embedding leg was dropped (a second external API
     * for a capability the router's query rewrites and the reranker already
     * cover — see pi_chatbot_settings()). The table is dropped rather than left
     * behind, since nothing reads it any more and an orphaned MEDIUMBLOB table
     * is exactly the kind of leftover the uninstall audit flagged before.
     */
    // v5 (2026-07-26): added a real KEY on post_type. Every query filtering
    // WHERE post_type = 'product' (total_product_count(), compute_idf(),
    // search_unaccented(), search_by_product_code(), search_projects(),
    // Router::few_shot_examples()) previously had NOTHING to use but a full
    // table scan — the schema had only PRIMARY KEY(post_id) and the FULLTEXT
    // index, neither of which helps a plain equality filter. dbDelta() adds
    // the key to the LIVE table via ALTER TABLE, not a reindex — no data loss,
    // no downtime, no shadow-table swap needed for this change.
    const DB_VERSION = 5;

    /** Minimum length threshold to index — only applies to post types other than 'product'. */
    const MIN_INDEXABLE_LENGTH = 100;

    /** Post types that get indexed — matches the theme's real CPTs (product/project) plus regular pages. */
    public static function indexable_post_types() {
        return ['product', 'project', 'page'];
    }

    /**
     * Hook 'plugins_loaded' — syncs the schema when the stored option version
     * doesn't match DB_VERSION. DELIBERATELY does not call reindex_all() here:
     * reindexing is heavy work and must be triggered explicitly by the user
     * clicking a button in wp-admin (a lesson learned from SAG — stuffing a heavy
     * job into an auto-run hook is the fastest way to hang the whole site).
     */
    public static function maybe_upgrade() {
        if ((int) get_option('pi_chatbot_db_version') === self::DB_VERSION) return;
        self::create_table();
        self::drop_removed_tables();
        update_option('pi_chatbot_db_version', self::DB_VERSION, false);
    }

    /**
     * Cleans up tables from features that no longer exist. Currently just the
     * embeddings vectors table (schema 3, removed in 4). Guarded by the schema
     * version so it runs once, not on every request.
     */
    private static function drop_removed_tables() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pi_chatbot_vectors");
        wp_clear_scheduled_hook('pi_chatbot_embed_backfill');
    }

    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . PI_CHATBOT_TABLE;
    }

    public static function on_activate() {
        self::create_table();
        update_option('pi_chatbot_db_version', self::DB_VERSION, false);
        $result = self::reindex_all();
        // Activation does not hard-fail if reindex errors out (the site should
        // still be able to activate; the admin can trigger a manual Reindex
        // later) — but it must be logged, not silently swallowed the way
        // reindex_all() used to be (see its docblock).
        if (is_wp_error($result)) {
            error_log('[pi-chatbot] on_activate reindex failed: ' . $result->get_error_message());
        }
    }

    public static function create_table() {
        self::create_table_at(self::table_name());
    }

    private static function create_table_at($table) {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $sql = "CREATE TABLE {$table} (
            post_id BIGINT UNSIGNED NOT NULL,
            post_type VARCHAR(20) NOT NULL,
            title TEXT NOT NULL,
            brand VARCHAR(190) DEFAULT '',
            category VARCHAR(190) DEFAULT '',
            url VARCHAR(500) NOT NULL,
            content_text LONGTEXT NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (post_id),
            KEY post_type (post_type),
            FULLTEXT KEY content_ft (title, brand, category, content_text)
        ) {$charset_collate} ENGINE=InnoDB;";
        dbDelta($sql);
    }

    /**
     * Re-scans all published content. Returns the number of records indexed.
     *
     * Loads into a SHADOW table then does an atomic RENAME TABLE, instead of
     * TRUNCATE-ing the live table and reloading it gradually (a real bug found
     * during audit: TRUNCATE opens a window where the table is COMPLETELY EMPTY
     * for the entire ~419-record reload — a customer chatting during exactly that
     * window would get empty context even though the real data was still intact,
     * just being reloaded). RENAME TABLE across multiple tables in a single
     * statement is atomic in MySQL — the main table only "disappears" for the
     * instant it takes to rename the metadata, with no empty gap a customer could
     * ever hit.
     */
    /**
     * @return int|WP_Error Number of records indexed, or WP_Error if the swap failed.
     *
     * Previously NOTHING here checked for errors — if RENAME TABLE failed (exactly
     * the kind of failure that broke the plugin last time: renaming referenced a
     * table that didn't exist, atomic failure of the whole statement), execution
     * would continue anyway, write a fresh last_reindex/index_count, and report
     * "reindex succeeded" even though NOTHING had actually been swapped. Now every
     * raw query is checked, plus 2 guard gates:
     *   - Gate 1: confirm the shadow table REALLY exists after create_table_at().
     *   - Gate 2: if $count === 0, ABORT the swap and keep the old index — 419+
     *     published posts existing but scanning to 0 can only be an upstream bug,
     *     never a valid outcome.
     * Deliberately NOT adding a "dropped by more than X%" gate: bulk-unpublishing
     * is a legitimate operation, and any gate that fires on a legitimate action
     * would just get disabled by the admin the first time it happens.
     */
    public static function reindex_all() {
        global $wpdb;
        $table = self::table_name();
        $shadow = $table . '_shadow';
        $old = $table . '_old';

        if ($wpdb->query("DROP TABLE IF EXISTS {$shadow}") === false) {
            return new WP_Error('pi_chatbot_reindex_failed', 'Failed to drop old shadow table: ' . $wpdb->last_error);
        }
        self::create_table_at($shadow);
        if ((string) $wpdb->get_var("SHOW TABLES LIKE '{$shadow}'") !== $shadow) {
            return new WP_Error('pi_chatbot_reindex_failed', 'Shadow table was not created successfully.');
        }

        $count = 0;
        foreach (self::indexable_post_types() as $post_type) {
            $ids = get_posts([
                'post_type'      => $post_type,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            ]);
            foreach ($ids as $post_id) {
                if (self::index_post_into($shadow, $post_id)) $count++;
            }
        }

        if ($count === 0) {
            $wpdb->query("DROP TABLE IF EXISTS {$shadow}");
            return new WP_Error('pi_chatbot_reindex_empty', 'Reindex produced 0 records — aborting swap, keeping the existing index.');
        }

        $wpdb->query("DROP TABLE IF EXISTS {$old}");
        if ($wpdb->query("RENAME TABLE {$table} TO {$old}, {$shadow} TO {$table}") === false) {
            $err = $wpdb->last_error;
            $wpdb->query("DROP TABLE IF EXISTS {$shadow}");
            return new WP_Error('pi_chatbot_reindex_failed', 'RENAME TABLE failed: ' . $err);
        }
        $wpdb->query("DROP TABLE IF EXISTS {$old}");

        // Only write bookkeeping AFTER the swap has been confirmed successful.
        update_option('pi_chatbot_last_reindex', current_time('mysql'));
        update_option('pi_chatbot_index_count', $count);
        self::bump_index_version();
        // Pi_Chatbot_Entities is always require_once'd before any hook runs (fixed
        // load order in pi-chatbot.php) — no need for a defensive class_exists().
        Pi_Chatbot_Entities::flush_cache();
        Pi_Chatbot_Router::flush_cache();
        return $count;
    }

    /**
     * Global counter used as part of the answer-cache key (see class-rest.php).
     * Previously the cache only expired when reindex_all() ran — save_post
     * (editing a single post) never bumped last_reindex, so editing one product's
     * price/specs would still leave the customer getting the old answer + old
     * sources for up to an hour. Autoload=true because this option is read on
     * every chat request (unlike options holding an API key, which are
     * deliberately autoload=false).
     */
    private static function bump_index_version() {
        $v = (int) get_option('pi_chatbot_index_version', 0);
        update_option('pi_chatbot_index_version', $v + 1, true);
    }

    /**
     * Invalidates ALL currently stored answer caches (transients keyed by
     * question hash + index_version — see class-rest.php) WITHOUT touching the
     * actual index data. Bumping the version is far cheaper than a bulk DELETE
     * with a LIKE pattern: old transients simply no longer match the new key, and
     * WP cleans them up naturally once they expire.
     *
     * Hooked into the theme's 'sgh_after_flush_cache' action (the /flush-cache
     * endpoint) — one single trigger point clears both the theme's static cache
     * and the chatbot's answer cache, instead of having to remember two separate
     * places when testing data changes.
     */
    public static function flush_answer_cache() {
        self::bump_index_version();
    }

    /** Index/refresh a single post into the MAIN table — used by the save_post hook (incremental update). */
    public static function index_post($post_id) {
        return self::index_post_into(self::table_name(), $post_id);
    }

    /** Builds the data for a single post and writes it (REPLACE) into the given
     *  $table — shared by both the incremental update (index_post, main table)
     *  and the full reindex (shadow table). */
    private static function index_post_into($table, $post_id) {
        $post = get_post($post_id);
        if (!$post || $post->post_status !== 'publish') return false;
        if (!in_array($post->post_type, self::indexable_post_types(), true)) return false;

        $brand = '';
        $category = '';
        if ($post->post_type === 'product') {
            $brand    = self::term_names($post_id, 'product_brand');
            $category = self::term_names($post_id, 'product_category');
        } elseif ($post->post_type === 'project') {
            $category = self::term_names($post_id, 'project_category');
        }

        $body = wp_strip_all_tags(strip_shortcodes($post->post_content));
        $excerpt = wp_strip_all_tags($post->post_excerpt);
        // title/brand/category are NO LONGER appended into content_text (audit
        // 2026-07-18 flag: those 3 columns already exist separately and are all
        // part of the FULLTEXT KEY content_ft — appending them into content_text
        // as well caused their tokens to be counted twice, slightly skewing the
        // relevance score compared to fields that only appear once).
        $content_text = trim($excerpt . ' ' . $body);
        // MySQL FULLTEXT ignores words shorter than the default min_word_length
        // (4) — nothing is trimmed here; the full text is kept as-is so the
        // retriever can MATCH it in natural language mode on its own.

        // Pages/projects that are nearly empty just add noise to FULLTEXT — they
        // carry no real information but still match tokens in the title and take
        // up a top-K slot. Measured on real data: 3/8 pages = 0 characters,
        // another 3/8 (cart page etc.) = 79 characters — a threshold of 100
        // correctly cuts exactly the 6 useless pages while keeping the 2 pages
        // with real content. NOT applied to 'product': a short spec (e.g. 89
        // characters: dimensions/weight/capacity) is still a valid answer, not
        // noise.
        if ($post->post_type !== 'product' && mb_strlen(trim($content_text)) < self::MIN_INDEXABLE_LENGTH) {
            return false;
        }

        // If the taxonomy field is empty, extract the brand from the title
        // (e.g. "Bàn Đông FT-158MA-S - Hoshizaki"). This ONLY fills a gap —
        // taxonomy always wins, so this step never overwrites data the user set
        // manually. Measured on real data: 271/280 products with an empty brand
        // field have the brand name present in the title, and all 271 match
        // exactly one brand (no ambiguity) — brand coverage goes from 26% to 98%
        // without using any LLM.
        if ($brand === '' && $post->post_type === 'product') {
            $detected = Pi_Chatbot_Entities::detect_brand_from_title($post->post_title);
            if ($detected) $brand = $detected;
        }

        global $wpdb;
        $wpdb->replace($table, [
            'post_id'      => $post_id,
            'post_type'    => $post->post_type,
            'title'        => $post->post_title,
            'brand'        => $brand,
            'category'     => $category,
            'url'          => get_permalink($post_id),
            'content_text' => $content_text,
            'updated_at'   => current_time('mysql'),
        ], ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);

        // Only bump the version when writing to the MAIN table (incremental
        // save_post) — reindex_all() bumps it once itself after the swap
        // completes, no need to bump it 419 times while building the shadow
        // table.
        if ($table === self::table_name()) self::bump_index_version();

        return true;
    }

    public static function remove_post($post_id) {
        global $wpdb;
        $wpdb->delete(self::table_name(), ['post_id' => $post_id], ['%d']);
        self::bump_index_version();
    }

    private static function term_names($post_id, $taxonomy) {
        if (!taxonomy_exists($taxonomy)) return '';
        $terms = get_the_terms($post_id, $taxonomy);
        if (!$terms || is_wp_error($terms)) return '';
        return implode(', ', wp_list_pluck($terms, 'name'));
    }

    /** Hook save_post — updates exactly one row instead of reindexing everything. */
    public static function on_save_post($post_id, $post, $update) {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) return;
        if (!in_array($post->post_type, self::indexable_post_types(), true)) return;

        if ($post->post_status === 'publish') {
            self::index_post($post_id);
        } else {
            self::remove_post($post_id);
        }
        Pi_Chatbot_Entities::flush_cache();
        Pi_Chatbot_Router::flush_cache();
    }

    public static function on_delete_post($post_id) {
        self::remove_post($post_id);
    }
}
