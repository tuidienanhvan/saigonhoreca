<?php
if (!defined('ABSPATH')) exit;

class Pi_Multilang_Translation {
    const CRON_HOOK = 'pi_multilang_do_auto_translate';

    public static function init() {
        add_action('save_post', [__CLASS__, 'on_save_post'], 10, 3);
        add_action(self::CRON_HOOK, [__CLASS__, 'run_scheduled_translate'], 10, 2);
        // Manual "translate now" button in the post-edit metabox (class-meta.php) —
        // a normal logged-in browser AJAX call, unrelated to the automatic path below.
        add_action('wp_ajax_pi_multilang_translate', [__CLASS__, 'handle_manual_translate']);
    }

    /**
     * Schedules the translation to run via WP-Cron instead of firing an
     * immediate self-HTTP-loopback (wp_remote_post with blocking=false,
     * timeout=0.01s). The loopback approach was tested directly against the
     * real web server (nginx+php-fpm, LocalWP on Windows) and never actually
     * completed within 60s — the request apparently never gets dispatched
     * before this process/connection tears down in this environment. A real
     * WP-Cron event doesn't have that failure mode: it's picked up on the
     * next visitor request (or a real system cron hitting wp-cron.php) via
     * WordPress's own scheduler, which every WP site already relies on for
     * dozens of other things.
     */
    public static function on_save_post($post_id, $post, $update) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (wp_is_post_revision($post_id)) return;
        if ($post->post_type === 'attachment' || $post->post_type === 'nav_menu_item') return;

        // Only translate if the CURRENT post is Vietnamese (or untagged, which
        // defaults to Vietnamese — the site's existing content).
        $lang = get_post_meta($post_id, 'pi_language', true);
        if ($lang === 'en') return;

        $group = get_post_meta($post_id, 'pi_translation_group', true);
        if (!$group) {
            $group = wp_generate_uuid4();
            update_post_meta($post_id, 'pi_translation_group', $group);
        } else {
            $existing_en = get_posts([
                'post_type'      => $post->post_type,
                'post_status'    => 'any',
                'meta_query'     => [
                    'relation' => 'AND',
                    ['key' => 'pi_translation_group', 'value' => $group],
                    ['key' => 'pi_language', 'value' => 'en'],
                ],
                'fields'         => 'ids',
                'posts_per_page' => 1,
            ]);
            if (!empty($existing_en)) return; // already has an EN counterpart, don't re-translate/re-spend API cost
        }

        // Guard against scheduling the same post twice (rapid double-save,
        // e.g. autosave immediately followed by a real save).
        if (!wp_next_scheduled(self::CRON_HOOK, [$post_id, $group])) {
            wp_schedule_single_event(time() + 5, self::CRON_HOOK, [$post_id, $group]);
        }
    }

    /** Cron callback — runs the real translation off the request that saved the post. */
    public static function run_scheduled_translate($post_id, $group) {
        self::do_translate($post_id, $group);
    }

    /** Manual "translate now" button (class-meta.php) — logged-in AJAX, checked via nonce + capability. */
    public static function handle_manual_translate() {
        check_ajax_referer('pi_translate', '_ajax_nonce');
        if (!current_user_can('edit_posts')) wp_send_json_error('No permission');

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $post = get_post($post_id);
        if (!$post) wp_send_json_error('Post not found');

        $group = get_post_meta($post_id, 'pi_translation_group', true);
        if (!$group) {
            $group = wp_generate_uuid4();
            update_post_meta($post_id, 'pi_translation_group', $group);
        }

        $result = self::do_translate($post_id, $group);
        if ($result === false) {
            wp_send_json_error('Translation failed — check the LLM7 API key in Pi Chatbot settings.');
        }
        wp_send_json_success('Translated draft created, ID: ' . $result);
    }

    /**
     * Shared translation logic — used by both the automatic (cron) and
     * manual (AJAX button) paths, so there is exactly one place that calls
     * LLM7 and creates the linked draft.
     *
     * @return int|false New post ID on success, false on failure.
     */
    private static function do_translate($post_id, $group) {
        $post = get_post($post_id);
        if (!$post) return false;

        $chatbot_settings = get_option('pi_chatbot_settings', []);
        $api_key = '';
        if (!empty($chatbot_settings['llm7_keys']) && is_array($chatbot_settings['llm7_keys'])) {
            $api_key = $chatbot_settings['llm7_keys'][0];
        }
        if (empty($api_key)) return false;

        $system_prompt = 'You are a professional translator. Translate the given text to English. Output only the translated text, no extra remarks. Keep HTML tags intact if any.';

        $translated_title = self::call_llm7($api_key, $system_prompt, $post->post_title);
        if (!$translated_title) return false;

        $translated_content = '';
        if (!empty(trim($post->post_content))) {
            $translated_content = self::call_llm7($api_key, $system_prompt, $post->post_content);
            if (!$translated_content) return false;
        }

        $new_post_id = wp_insert_post([
            'post_title'   => $translated_title,
            'post_content' => $translated_content,
            'post_status'  => 'draft', // always a draft — never auto-published
            'post_type'    => $post->post_type,
        ]);
        if (is_wp_error($new_post_id)) return false;

        update_post_meta($new_post_id, 'pi_language', 'en');
        update_post_meta($new_post_id, 'pi_translation_group', $group);

        return $new_post_id;
    }

    private static function call_llm7($key, $system, $user) {
        if (empty(trim($user))) return '';
        $url = 'https://api.llm7.io/v1/chat/completions';

        $body = [
            'model' => 'gpt-oss:20b',
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ],
            'temperature' => 0.1,
        ];

        $args = [
            'body'    => wp_json_encode($body),
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $key,
            ],
            'timeout' => 60,
        ];

        $response = wp_remote_post($url, $args);
        if (is_wp_error($response)) return false;

        $code = wp_remote_retrieve_response_code($response);
        if ($code !== 200) return false;

        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($data['choices'][0]['message']['content'])) {
            return trim($data['choices'][0]['message']['content']);
        }
        return false;
    }
}
