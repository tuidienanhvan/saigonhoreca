<?php
/**
 * General Assistant Agent — greetings, small talk, and anything the router
 * classified as intent='none'.
 *
 * Added 2026-08-08. Before this, Pi_Chatbot_Supervisor_Agent::route_intent()
 * had no 'none' case, so every greeting fell through `default` and was routed
 * to the SALES agent — which meant a customer saying "chào bạn" would have
 * been answered under a sales-specialist persona. The REST layer already
 * labelled these turns agent_id='general', so the routing table and the
 * label disagreed; this class is the missing half.
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/interface-agent.php';
require_once __DIR__ . '/class-handoff-state.php';

class Pi_Chatbot_General_Agent implements Pi_Chatbot_Agent_Interface {
    public function get_id(): string { return 'general'; }
    public function get_name(): string { return 'General Assistant'; }
    public function get_description(): string { return 'Handles greetings, small talk, and general "what do you sell" questions.'; }

    public function get_system_prompt(array $context): string {
        return "Khách đang chào hỏi hoặc hỏi chung chung, chưa hỏi về sản phẩm cụ thể nào.\n"
            . "Trả lời ngắn, thân thiện, rồi giới thiệu ngắn gọn các NHÓM thiết bị Saigon Horeca cung cấp "
            . "(bếp Á/Âu, lò nướng, tủ lạnh - tủ đông công nghiệp, máy rửa chén...) và hỏi khách quan tâm nhóm nào.\n"
            . "KHÔNG bịa tên model, thông số hay giá — ở bước này chưa có dữ liệu sản phẩm nào được truy xuất.";
    }

    public function get_tools(): array { return []; }

    public function execute(Pi_Chatbot_Handoff_State $state): array {
        return [
            'content'  => '',
            'agent_id' => 'general',
        ];
    }
}
