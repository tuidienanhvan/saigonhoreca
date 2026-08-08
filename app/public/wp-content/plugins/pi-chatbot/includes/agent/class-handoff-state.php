<?php
/**
 * Handoff State DTO for Multi-Agent Orchestration
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

class Pi_Chatbot_Handoff_State {

    public string $session_id;
    public string $active_agent_id;
    public array $history = [];
    public array $metadata = [];
    public int $turn_count = 0;
    public ?string $user_message = null;

    public function __construct(string $session_id, string $active_agent_id = 'supervisor') {
        $this->session_id = $session_id;
        $this->active_agent_id = $active_agent_id;
    }

    /**
     * Load session state from WordPress transient
     *
     * @param string $session_id
     * @return self
     */
    public static function load(string $session_id): self {
        $key = 'pi_cb_agent_state_' . md5($session_id);
        $data = get_transient($key);

        $state = new self($session_id);
        if (is_array($data)) {
            $state->active_agent_id = $data['active_agent_id'] ?? 'supervisor';
            $state->history         = $data['history'] ?? [];
            $state->metadata        = $data['metadata'] ?? [];
            $state->turn_count      = (int)($data['turn_count'] ?? 0);
            $state->user_message    = $data['user_message'] ?? null;
        }

        return $state;
    }

    /**
     * Persist session state into WordPress transient (TTL 24 hours)
     *
     * @return bool
     */
    public function save(): bool {
        $key = 'pi_cb_agent_state_' . md5($this->session_id);
        $payload = [
            'active_agent_id' => $this->active_agent_id,
            'history'         => array_slice($this->history, -20), // Keep last 20 turns
            'metadata'        => $this->metadata,
            'turn_count'      => $this->turn_count,
            'user_message'    => $this->user_message,
            'updated_at'      => time(),
        ];

        return set_transient($key, $payload, DAY_IN_SECONDS);
    }

    /**
     * Start a new user turn, resetting per-turn handoff depth tracker
     *
     * @param string|null $message
     * @return void
     */
    public function start_turn(?string $message = null): void {
        $this->turn_count++;
        if ($message !== null) {
            $this->user_message = $message;
        }
        $this->metadata['turn_handoff_history'] = [];
    }

    /**
     * Execute dynamic handoff to target agent
     *
     * @param string $next_agent_id
     * @param array $transfer_data
     * @return void
     */
    public function handoff_to(string $next_agent_id, array $transfer_data = []): void {
        $previous_agent = $this->active_agent_id;

        // Circular loop & max handoff depth guard (max 5 handoffs per turn)
        $depth = count($this->metadata['turn_handoff_history'] ?? []);
        if ($depth >= 5 && $next_agent_id !== 'supervisor') {
            $next_agent_id = 'supervisor';
        }

        $this->active_agent_id = $next_agent_id;

        $this->metadata['last_handoff'] = [
            'from'          => $previous_agent,
            'to'            => $next_agent_id,
            'timestamp'     => time(),
            'transfer_data' => $transfer_data,
        ];

        $date_str = function_exists('wp_date') ? wp_date('Y-m-d H:i:s') : date('Y-m-d H:i:s');
        $entry = sprintf(
            '[%s] Handoff %s -> %s',
            $date_str,
            $previous_agent,
            $next_agent_id
        );
        $this->metadata['handoff_history'][] = $entry;
        $this->metadata['turn_handoff_history'][] = $entry;
    }
}
