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
    /**
     * Rewritten 2026-08-09. The previous one line was a stub that survived
     * into production when the agent layer was wired up, and it pulled the
     * opposite way from the request it serves.
     *
     * Supervisor routes intent='contact' here, and section 3 of the system
     * prompt already tells the model: "Customer wants CONTACT / HOTLINE
     * information. Provide Saigon Horeca hotline and contact details
     * clearly." The stub then said "Politely request contact details (name
     * and phone number) for official quotation dispatch" — asking the visitor
     * for THEIR number in reply to a request for OURS. Since the agent prompt
     * lands at section 4b, AFTER section 3, it is also the later instruction.
     *
     * Measured live before rewriting rather than assumed: the model gave the
     * hotline first and then appended "Để gửi báo giá chính thức, vui lòng
     * cung cấp tên và số điện thoại của bạn." Nobody had asked for a quote.
     * So the real defect is a pushy trailing ask bolted onto an answer that
     * was otherwise correct — smaller than "refuses to answer", but it turns
     * a simple "what's your number" into a sales capture, which is the fastest
     * way to make a helpful bot feel like a form.
     *
     * The rule below therefore inverts the default: give first, completely,
     * and only offer to take details when the visitor has actually signalled
     * they want a quote or a callback. The widget already has a dedicated
     * lead form that appears on intent='price' (chatbot.js), so the prompt
     * does not need to do that job in prose as well.
     *
     * Written in Vietnamese to match sales / tech_spec / warranty — the
     * customers are Vietnamese and a lone English prompt drifts in tone.
     */
    public function get_system_prompt(array $context): string {
        return "Bạn phụ trách câu hỏi về thông tin liên hệ của Saigon Horeca.\n"
            . "Nhiệm vụ chính: ĐƯA cho khách thông tin liên hệ của cửa hàng "
            . "(hotline, email, địa chỉ, trang liên hệ) một cách đầy đủ và rõ ràng, "
            . "đúng như phần thông tin tham chiếu cung cấp.\n"
            . "KHÔNG đòi tên hay số điện thoại của khách khi họ chỉ hỏi thông tin "
            . "liên hệ của cửa hàng — họ đang hỏi số của mình, không phải xin báo giá.\n"
            . "Chỉ mời khách để lại thông tin khi chính khách đã tỏ ý muốn được báo giá "
            . "hoặc muốn nhân viên gọi lại; khi đó nói như một lời mời, không phải điều kiện.\n"
            . "TUYỆT ĐỐI KHÔNG bịa số điện thoại, email hay địa chỉ không có trong "
            . "thông tin tham chiếu.";
    }
    public function get_tools(): array { return []; }
    public function execute(Pi_Chatbot_Handoff_State $state): array {
        return [
            'content'  => 'Dạ Quý khách vui lòng để lại Tên và Số điện thoại (hoặc Email) để bộ phận tư vấn Saigon Horeca liên hệ hỗ trợ báo giá trực tiếp!',
            'agent_id' => 'lead_capture',
        ];
    }
}
