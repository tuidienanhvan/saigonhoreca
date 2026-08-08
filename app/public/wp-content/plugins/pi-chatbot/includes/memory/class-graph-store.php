<?php
/**
 * Knowledge Graph Store & Persona Profiling Manager for Pi Chatbot
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

class Pi_Chatbot_Graph_Store {

    const DB_VERSION = 1;

    public static function maybe_upgrade(): void {
        $installed = (int) get_option('pi_chatbot_graph_db_version', 0);
        if ($installed < self::DB_VERSION) {
            self::create_tables();
            update_option('pi_chatbot_graph_db_version', self::DB_VERSION);
        }
    }

    /**
     * DB Table Names
     */
    public static function nodes_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'pi_chatbot_nodes';
    }

    public static function edges_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'pi_chatbot_edges';
    }

    public static function persona_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'pi_chatbot_persona';
    }

    /**
     * Create graph tables via dbDelta
     *
     * @return void
     */
    public static function create_tables(): void {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $t_nodes = self::nodes_table();
        $t_edges = self::edges_table();
        $t_persona = self::persona_table();

        $sql1 = "CREATE TABLE {$t_nodes} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            entity_name varchar(191) NOT NULL,
            entity_norm varchar(191) NOT NULL,
            entity_type varchar(50) NOT NULL,
            properties json DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY idx_norm_type (entity_norm, entity_type),
            KEY idx_type (entity_type)
        ) {$charset_collate};";

        $sql2 = "CREATE TABLE {$t_edges} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            source_node_id bigint(20) unsigned NOT NULL,
            target_node_id bigint(20) unsigned NOT NULL,
            relation_type varchar(50) NOT NULL,
            weight float DEFAULT 1.0,
            properties json DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY idx_edge (source_node_id, target_node_id, relation_type),
            KEY idx_target (target_node_id, relation_type)
        ) {$charset_collate};";

        $sql3 = "CREATE TABLE {$t_persona} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_id varchar(191) NOT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            persona_node_id bigint(20) unsigned NOT NULL,
            demographics json DEFAULT NULL,
            behavior_summary text DEFAULT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY idx_session (session_id),
            KEY idx_user (user_id)
        ) {$charset_collate};";

        dbDelta($sql1);
        dbDelta($sql2);
        dbDelta($sql3);
    }

    /**
     * Upsert a Node into Graph
     *
     * @param string $name
     * @param string $type E.g. 'user', 'product', 'brand', 'preference'
     * @param array $props
     * @return int Node ID
     */
    public static function upsert_node(string $name, string $type, array $props = []): int {
        global $wpdb;
        $table = self::nodes_table();
        $name_trimmed = trim($name);
        $norm = mb_strtolower($name_trimmed, 'UTF-8');

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE entity_norm = %s AND entity_type = %s LIMIT 1",
            $norm,
            $type
        ));

        $json_props = wp_json_encode(!empty($props) ? $props : (object)[]);

        if ($existing) {
            $wpdb->update(
                $table,
                ['properties' => $json_props, 'updated_at' => current_time('mysql')],
                ['id' => $existing]
            );
            return (int)$existing;
        }

        $wpdb->insert($table, [
            'entity_name' => $name_trimmed,
            'entity_norm' => $norm,
            'entity_type' => $type,
            'properties'  => $json_props,
            'created_at'  => current_time('mysql'),
            'updated_at'  => current_time('mysql'),
        ]);

        return (int)$wpdb->insert_id;
    }

    /**
     * Upsert an Edge into Graph
     *
     * @param int $src_id
     * @param int $target_id
     * @param string $relation E.g. 'INTERESTED_IN', 'PREFERRED_BRAND'
     * @param float $weight
     * @param array $props
     * @return int Edge ID
     */
    public static function upsert_edge(int $src_id, int $target_id, string $relation, float $weight = 1.0, array $props = []): int {
        global $wpdb;
        $table = self::edges_table();
        $json_props = wp_json_encode(!empty($props) ? $props : (object)[]);

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE source_node_id = %d AND target_node_id = %d AND relation_type = %s LIMIT 1",
            $src_id,
            $target_id,
            $relation
        ));

        if ($existing) {
            $wpdb->query($wpdb->prepare(
                "UPDATE {$table} SET weight = weight + %f, properties = %s, updated_at = %s WHERE id = %d",
                $weight,
                $json_props,
                current_time('mysql'),
                $existing
            ));
            return (int)$existing;
        }

        $wpdb->insert($table, [
            'source_node_id' => $src_id,
            'target_node_id' => $target_id,
            'relation_type'  => $relation,
            'weight'         => $weight,
            'properties'     => $json_props,
            'created_at'     => current_time('mysql'),
            'updated_at'     => current_time('mysql'),
        ]);

        return (int)$wpdb->insert_id;
    }

    /**
     * 1-Hop BFS Neighborhood Retrieval for Persona / Entity Prompt Injection
     *
     * @param int $node_id
     * @return array
     */
    public static function get_neighborhood(int $node_id): array {
        global $wpdb;
        $t_edges = self::edges_table();
        $t_nodes = self::nodes_table();

        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT e.relation_type, e.weight, n.entity_name, n.entity_type, n.properties
             FROM {$t_edges} e
             JOIN {$t_nodes} n ON e.target_node_id = n.id
             WHERE e.source_node_id = %d
             ORDER BY e.weight DESC LIMIT 10",
            $node_id
        ), ARRAY_A);

        return $rows ?: [];
    }

    /**
     * Build system prompt persona section from graph memory
     *
     * @param string $session_id
     * @return string
     */
    public static function build_persona_prompt(string $session_id): string {
        global $wpdb;
        $t_persona = self::persona_table();
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT persona_node_id, behavior_summary FROM {$t_persona} WHERE session_id = %s LIMIT 1",
            $session_id
        ));

        if (!$row || !$row->persona_node_id) {
            return '';
        }

        $neighborhood = self::get_neighborhood((int)$row->persona_node_id);
        if (empty($neighborhood) && empty($row->behavior_summary)) {
            return '';
        }

        $prompt = "\n<<<USER_GRAPH_PERSONA>>>\n";
        if (!empty($row->behavior_summary)) {
            $prompt .= "Customer Profile: " . esc_html($row->behavior_summary) . "\n";
        }
        if (!empty($neighborhood)) {
            $prompt .= "Known Preferences & Interests:\n";
            foreach ($neighborhood as $item) {
                $prompt .= sprintf("- %s -> %s (%s)\n", $item['relation_type'], $item['entity_name'], $item['entity_type']);
            }
        }
        $prompt .= "<<<END_USER_GRAPH_PERSONA>>>\n";

        return $prompt;
    }
}
