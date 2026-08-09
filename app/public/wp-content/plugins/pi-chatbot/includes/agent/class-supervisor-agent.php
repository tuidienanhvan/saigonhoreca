<?php
/**
 * Supervisor Agent for Multi-Agent Orchestration
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/interface-agent.php';
require_once __DIR__ . '/class-handoff-state.php';
require_once __DIR__ . '/class-agent-registry.php';

/**
 * Supervisor Agent (Router Orchestrator)
 */
class Pi_Chatbot_Supervisor_Agent implements Pi_Chatbot_Agent_Interface {

    public function get_id(): string {
        return 'supervisor';
    }

    public function get_name(): string {
        return 'Supervisor Agent';
    }

    public function get_description(): string {
        return 'Orchestrates incoming customer requests and routes them to specialized domain agents.';
    }

    public function get_system_prompt(array $context): string {
        return 'You are the Chief Supervisor Agent for ' . pi_chatbot_brand()['name'] . '. Inspect intent and delegate to specialized subagents.';
    }

    public function get_tools(): array {
        return [];
    }

    /**
     * Maps a router intent to the agent that should answer it.
     *
     * This is now the SINGLE source of truth for that mapping — until
     * 2026-08-08 the same table also existed, hand-written, inside
     * Pi_Chatbot_Rest::resolve_context()'s intent switch, so the two could
     * (and did) disagree.
     *
     * 2026-08-08 fix: 'none' (greeting / small talk / off-topic) had no case
     * and fell through `default` to 'sales', so "chào bạn" was answered under
     * a sales-specialist persona — while the REST layer separately labelled
     * the same turn agent_id='general'. Both now agree, and 'general' has a
     * real agent class behind it.
     *
     * `default` still returns 'sales' because the remaining unlisted intent is
     * 'product', which genuinely belongs to sales; an unknown/garbage intent
     * landing there is also the safest of the five (product advice, grounded
     * in retrieved catalog rows).
     */
    public function route_intent(string $message, string $intent): string {
        switch ($intent) {
            case 'none':
                return 'general';
            case 'price':
            case 'product':
                return 'sales';
            case 'project':
                return 'tech_spec';
            case 'contact':
                return 'lead_capture';
            case 'service':
                return 'warranty';
            default:
                return 'sales';
        }
    }

    public function execute(Pi_Chatbot_Handoff_State $state): array {
        $msg = $state->user_message ?? '';
        $intent = $state->metadata['intent'] ?? 'product';
        $target = $this->route_intent($msg, $intent);

        $state->handoff_to($target, ['routed_by' => 'supervisor', 'intent' => $intent]);

        return [
            'handoff_to'    => $target,
            'transfer_data' => ['intent' => $intent],
            'content'       => sprintf('Routing to %s agent.', $target),
        ];
    }
}
