<?php
/**
 * Sales Specialist Agent
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/interface-agent.php';
require_once __DIR__ . '/class-handoff-state.php';
require_once __DIR__ . '/../tools/class-tool-registry.php';

class Pi_Chatbot_Sales_Agent implements Pi_Chatbot_Agent_Interface {
    public function get_id(): string { return 'sales'; }
    public function get_name(): string { return 'Sales & Pricing Specialist'; }
    public function get_description(): string { return 'Handles product quotations, comparison tables, discounts, and order inquiries.'; }
    public function get_system_prompt(array $context): string {
        return 'You are Saigon Horeca Sales Specialist. Focus on providing accurate pricing, comparison tables, and package discounts.';
    }
    public function get_tools(): array { return ['price_calculator', 'product_comparer']; }
    public function execute(Pi_Chatbot_Handoff_State $state): array {
        $registry = Pi_Chatbot_Tool_Registry::get_instance();
        $tool_result = null;

        if (!empty($state->metadata['tool_args']['price_calculator'])) {
            $tool_result = $registry->execute_tool('price_calculator', $state->metadata['tool_args']['price_calculator']);
        } elseif (!empty($state->metadata['tool_args']['product_comparer'])) {
            $tool_result = $registry->execute_tool('product_comparer', $state->metadata['tool_args']['product_comparer']);
        }

        $content = 'Sales & Pricing Specialist ready.';
        if ($tool_result && !empty($tool_result['success']) && !empty($tool_result['data']['summary_text'])) {
            $content = $tool_result['data']['summary_text'];
        } elseif ($tool_result && !empty($tool_result['success']) && !empty($tool_result['data']['comparison_matrix_text'])) {
            $content = $tool_result['data']['comparison_matrix_text'];
        }

        return [
            'content'     => $content,
            'agent_id'    => 'sales',
            'tool_result' => $tool_result,
        ];
    }
}
