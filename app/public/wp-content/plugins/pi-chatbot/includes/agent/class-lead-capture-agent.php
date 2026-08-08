<?php
/**
 * Lead Capture Agent
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/interface-agent.php';
require_once __DIR__ . '/class-handoff-state.php';

class Pi_Chatbot_Lead_Capture_Agent implements Pi_Chatbot_Agent_Interface {
    public function get_id(): string { return 'lead_capture'; }
    public function get_name(): string { return 'Lead Capture Specialist'; }
    public function get_description(): string { return 'Collects customer name, phone, email, and restaurant requirements for hotline callback.'; }
    public function get_system_prompt(array $context): string {
        return 'You are Saigon Horeca Customer Service Coordinator. Politely request contact details (name and phone number) for official quotation dispatch.';
    }
    public function get_tools(): array { return []; }
    public function execute(Pi_Chatbot_Handoff_State $state): array {
        return [
            'content'  => 'Dạ Quý khách vui lòng để lại Tên và Số điện thoại (hoặc Email) để bộ phận tư vấn Saigon Horeca liên hệ hỗ trợ báo giá trực tiếp!',
            'agent_id' => 'lead_capture',
        ];
    }
}
