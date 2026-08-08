<?php
/**
 * Warranty & Maintenance Agent
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/interface-agent.php';
require_once __DIR__ . '/class-handoff-state.php';
require_once __DIR__ . '/../tools/class-tool-registry.php';

class Pi_Chatbot_Warranty_Agent implements Pi_Chatbot_Agent_Interface {
    public function get_id(): string { return 'warranty'; }
    public function get_name(): string { return 'Warranty & Service Specialist'; }
    public function get_description(): string { return 'Handles warranty claims, covered components, technician dispatch SLA, and maintenance hotlines.'; }
    public function get_system_prompt(array $context): string {
        return 'You are Saigon Horeca Maintenance Specialist. Provide clear warranty terms, covered parts, and service SLAs.';
    }
    public function get_tools(): array { return ['warranty_checker']; }
    public function execute(Pi_Chatbot_Handoff_State $state): array {
        $registry = Pi_Chatbot_Tool_Registry::get_instance();
        $tool_result = null;

        $target = $state->metadata['tool_args']['warranty_checker']['product_name_or_brand'] ?? $state->user_message ?? 'saigonhoreca';
        $tool_result = $registry->execute_tool('warranty_checker', ['product_name_or_brand' => $target]);

        $content = 'Warranty & Service Specialist ready.';
        if ($tool_result && !empty($tool_result['success']) && !empty($tool_result['data']['summary_text'])) {
            $content = $tool_result['data']['summary_text'];
        }

        return [
            'content'     => $content,
            'agent_id'    => 'warranty',
            'tool_result' => $tool_result,
        ];
    }
}
