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
        return 'You are the Chief Supervisor Agent for Saigon Horeca. Inspect intent and delegate to specialized subagents.';
    }

    public function get_tools(): array {
        return [];
    }

    public function route_intent(string $message, string $intent): string {
        switch ($intent) {
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
