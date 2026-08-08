<?php
/**
 * Technical Spec Agent
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/interface-agent.php';
require_once __DIR__ . '/class-handoff-state.php';
require_once __DIR__ . '/../tools/class-tool-registry.php';

class Pi_Chatbot_Tech_Spec_Agent implements Pi_Chatbot_Agent_Interface {
    public function get_id(): string { return 'tech_spec'; }
    public function get_name(): string { return 'Technical Kitchen Engineer'; }
    public function get_description(): string { return 'Handles kitchen design sizing, electrical load calculations, exhaust airflow, and technical specs.'; }
    public function get_system_prompt(array $context): string {
        return 'You are Saigon Horeca Technical Kitchen Engineer. Focus on exact dimensions, power/gas requirements, and sizing formulas.';
    }
    public function get_tools(): array { return ['spec_evaluator', 'product_comparer']; }
    public function execute(Pi_Chatbot_Handoff_State $state): array {
        $registry = Pi_Chatbot_Tool_Registry::get_instance();
        $tool_result = null;

        if (!empty($state->metadata['tool_args']['spec_evaluator'])) {
            $tool_result = $registry->execute_tool('spec_evaluator', $state->metadata['tool_args']['spec_evaluator']);
        }

        $content = 'Technical Kitchen Engineer ready.';
        if ($tool_result && !empty($tool_result['success']) && !empty($tool_result['data']['recommendation'])) {
            $content = $tool_result['data']['recommendation'];
        }

        return [
            'content'     => $content,
            'agent_id'    => 'tech_spec',
            'tool_result' => $tool_result,
        ];
    }
}
