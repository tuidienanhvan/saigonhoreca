<?php
/**
 * Warranty & Service Specialist Agent
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/interface-agent.php';
require_once __DIR__ . '/class-handoff-state.php';

class Pi_Chatbot_Warranty_Agent implements Pi_Chatbot_Agent_Interface {
    public function get_id(): string { return 'warranty'; }
    public function get_name(): string { return 'Warranty Specialist'; }
    public function get_description(): string { return 'Handles after-sales, maintenance and service questions.'; }

    /**
     * 2026-08-08: this prompt previously read "Provide clear warranty terms,
     * covered parts, and service SLAs" — an instruction to state facts this
     * site has NO data for. Verified against the real catalog: of ~3500
     * products only three mention "bảo hành" at all, two of them a Toprinse
     * spray valve with "bảo hành một năm"; the homepage says only a general
     * "bảo hành lâu dài". Nothing states per-brand durations, covered parts or
     * response times. The tool that used to invent them has been removed (see
     * includes/tools/class-tool-registry.php).
     *
     * So this agent's job is the opposite of what the old prompt said: answer
     * from the service description that DOES exist, and be straightforward
     * that specific warranty terms have to come from the hotline.
     */
    public function get_system_prompt(array $context): string {
        return "Bạn phụ trách câu hỏi hậu mãi, bảo trì và dịch vụ của " . pi_chatbot_brand()['name'] . ".\n"
            . "TUYỆT ĐỐI KHÔNG nêu thời hạn bảo hành, danh sách linh kiện được bảo hành, "
            . "hay thời gian cam kết xử lý sự cố — cửa hàng chưa công bố những thông tin này, "
            . "và bịa ra một con số là gây thiệt hại thật cho khách. Nếu khách hỏi những điều đó, "
            . "nói thẳng là cần liên hệ hotline để được xác nhận chính xác.\n"
            . "Chỉ trình bày những gì có trong phần thông tin tham khảo bên dưới.";
    }

    /**
     * No tools: the warranty tool this agent used to call was removed for
     * fabricating data. Kept empty rather than deleted so the interface
     * contract stays intact until the agent layer is properly wired up.
     */
    public function get_tools(): array { return []; }

    public function execute(Pi_Chatbot_Handoff_State $state): array {
        return [
            'content'  => '',
            'agent_id' => 'warranty',
        ];
    }
}
