<?php
/**
 * Episodic Memory Engine — persistent interaction history and customer preferences.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Episodic_Memory {
    const OPTION_PREFIX = 'pi_chatbot_ep_mem_';

    /**
     * Store customer preference/topic interaction in option + Graph Store.
     *
     * @param string $session_id
     * @param array  $fact Array key-value pairs (e.g. ['preferred_brand' => 'Hoshizaki'])
     */
    public static function remember($session_id, array $fact) {
        if (!is_scalar($session_id) || empty($session_id) || empty($fact)) return;
        $session_id = (string) $session_id;
        $key = self::OPTION_PREFIX . md5($session_id);
        $current = get_option($key, []);
        if (!is_array($current)) $current = [];

        $updated = array_merge($current, $fact, ['updated_at' => time()]);
        update_option($key, $updated, false);

        // Graph Store ER Extraction & Persistence
        if (class_exists('Pi_Chatbot_Graph_Store')) {
            $user_node_id = Pi_Chatbot_Graph_Store::upsert_node('User_' . substr(md5($session_id), 0, 8), 'user', ['session_id' => $session_id]);
            
            foreach ($fact as $k => $v) {
                if ($k === 'updated_at' || !is_scalar($v) || empty($v)) continue;
                $v_str = (string)$v;
                $target_node_id = Pi_Chatbot_Graph_Store::upsert_node($v_str, 'preference', ['attribute' => $k]);
                
                $rel_type = strtoupper(sanitize_key($k));
                if (empty($rel_type)) $rel_type = 'HAS_PREFERENCE';
                
                Pi_Chatbot_Graph_Store::upsert_edge($user_node_id, $target_node_id, $rel_type, 1.0, ['session_id' => $session_id]);
            }
        }
    }

    /**
     * Recall persistent memory for a session.
     *
     * @param string $session_id
     * @return array
     */
    public static function recall($session_id) {
        if (!is_scalar($session_id) || empty($session_id)) return [];
        $key = self::OPTION_PREFIX . md5((string) $session_id);
        $mem = get_option($key, []);
        return is_array($mem) ? $mem : [];
    }
}
