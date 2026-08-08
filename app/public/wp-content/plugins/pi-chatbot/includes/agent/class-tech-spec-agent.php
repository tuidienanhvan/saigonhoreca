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

class Pi_Chatbot_Tech_Spec_Agent implements Pi_Chatbot_Agent_Interface {
    public function get_id(): string { return 'tech_spec'; }
    public function get_name(): string { return 'Technical Spec Specialist'; }
    public function get_description(): string { return 'Handles product specifications, dimensions, capacity and installation questions.'; }

    /**
     * 2026-08-08: the old prompt told this agent to focus on "sizing formulas",
     * backed by a spec-evaluator tool whose ice-machine ratios and duct-diameter
     * rule were uncited inventions, and whose hardcoded model capacities were
     * OVERSTATED against the real listings (IM-100CNE: claimed 100 kg/ngày,
     * listing says 95 Kg/24h; IM-240ANE: claimed 240, listing says 205).
     * The tool has been removed — see includes/tools/class-tool-registry.php.
     *
     * Specs the catalog genuinely carries (dimensions, điện áp, công suất W,
     * năng suất kg/24h, môi chất lạnh, xuất xứ) are real and quotable; this
     * prompt keeps the agent on those and off invented sizing maths.
     */
    public function get_system_prompt(array $context): string {
        return "Bạn tư vấn thông số kỹ thuật thiết bị bếp công nghiệp cho Saigon Horeca.\n"
            . "Chỉ nêu thông số CÓ TRONG thông tin tham khảo bên dưới (kích thước, điện áp, công suất, "
            . "năng suất, môi chất lạnh, xuất xứ...) và trích đúng con số ghi trong đó, không làm tròn, "
            . "không suy diễn sang model khác.\n"
            . "TUYỆT ĐỐI KHÔNG tự tính toán công suất cần thiết theo số ghế/diện tích, không tự suy ra "
            . "kích thước ống gió hay dòng điện aptomat — cửa hàng chưa có bộ công thức được duyệt cho "
            . "việc này. Nếu khách cần tư vấn thiết kế bếp, mời liên hệ hotline để kỹ thuật khảo sát.";
    }

    /** No tools — see the removal note in includes/tools/class-tool-registry.php. */
    public function get_tools(): array { return []; }

    public function execute(Pi_Chatbot_Handoff_State $state): array {
        return [
            'content'  => '',
            'agent_id' => 'tech_spec',
        ];
    }
}
