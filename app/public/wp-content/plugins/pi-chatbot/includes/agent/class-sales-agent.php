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

class Pi_Chatbot_Sales_Agent implements Pi_Chatbot_Agent_Interface {
    public function get_id(): string { return 'sales'; }
    public function get_name(): string { return 'Sales & Pricing Specialist'; }
    public function get_description(): string { return 'Helps customers choose the right equipment and routes pricing questions correctly.'; }

    /**
     * 2026-08-08: the old prompt told this agent to provide "accurate pricing,
     * comparison tables, and package discounts" — none of which this site has
     * data for. A catalog search finds ZERO products mentioning "chiết khấu",
     * "phí giao", "phí vận chuyển" or "phí lắp đặt", and the price calculator
     * that invented a discount/shipping/installation schedule has been removed
     * (see includes/tools/class-tool-registry.php).
     *
     * The plugin already has a correct, long-standing pricing policy in
     * Pi_Chatbot_Retriever::price_context(): pricing depends on configuration,
     * it is not listed, contact the hotline for a quote. This prompt's job is
     * to make the agent useful WITHIN that policy — help the customer narrow
     * down what they actually need, then hand off to the hotline for numbers.
     */
    public function get_system_prompt(array $context): string {
        return "Bạn tư vấn chọn thiết bị bếp công nghiệp cho khách của Saigon Horeca.\n"
            . "TUYỆT ĐỐI KHÔNG đưa ra con số giá, phần trăm chiết khấu, phí vận chuyển hay phí lắp đặt — "
            . "cửa hàng không niêm yết giá vì giá phụ thuộc cấu hình và số lượng. "
            . "Kể cả khi khách nài nỉ 'chỉ cần khoảng giá thôi', vẫn không được đoán.\n"
            . "Việc của bạn là giúp khách xác định đúng nhu cầu (loại thiết bị, công suất, quy mô quán) "
            . "dựa trên thông tin sản phẩm bên dưới, rồi mời khách liên hệ hotline để nhận báo giá chính xác.";
    }

    /** No tools — see the removal note in includes/tools/class-tool-registry.php. */
    public function get_tools(): array { return []; }

    public function execute(Pi_Chatbot_Handoff_State $state): array {
        return [
            'content'  => '',
            'agent_id' => 'sales',
        ];
    }
}
