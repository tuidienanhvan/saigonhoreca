<?php
/**
 * Warranty Checker Tool for Pi Chatbot ReAct Engine
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/class-tool-base.php';

class Pi_Chatbot_Tool_Warranty_Checker extends Pi_Chatbot_Tool_Base {

    public function get_name(): string {
        return 'warranty_checker';
    }

    public function get_description(): string {
        return 'Check warranty duration, covered components, service SLA, hotline number, and warranty terms by product model or brand.';
    }

    public function get_schema(): array {
        return [
            'type' => 'object',
            'properties' => [
                'product_name_or_brand' => ['type' => 'string'],
            ],
            'required' => ['product_name_or_brand'],
        ];
    }

    public function execute(array $args): array {
        $val = $this->validate_args($args);
        if (is_wp_error($val)) {
            return ['success' => false, 'error' => $val->get_error_message()];
        }

        $input = strtolower(trim(sanitize_text_field($args['product_name_or_brand'])));

        $default_hotline = apply_filters('sgh_company_contact_phone', '0901 304 365 (Hotline Saigon Horeca)');

        $policies = [
            'hoshizaki' => [
                'brand' => 'Hoshizaki (Nhật Bản)',
                'months' => 24,
                'covered_parts' => ['Lốc máy (Compressor)', 'Bo mạch điều khiển', 'Bơm nước', 'Hệ thống làm lạnh'],
                'exclusions' => ['Quả lọc nước bị tắc', 'Lỗi nguồn điện nhà hàng không ổn định', 'Rơi vỡ va đập cơ học'],
                'sla' => 'Nội thành TP.HCM / Hà Nội: Hỗ trợ kỹ thuật trong 4-8h; Tỉnh: 24h',
                'hotline' => $default_hotline,
            ],
            'berjaya' => [
                'brand' => 'Berjaya (Malaysia)',
                'months' => 12,
                'covered_parts' => ['Máy nén', 'Quạt tản nhiệt', 'Đồng hồ nhiệt độ', 'Hệ thống sưởi kính'],
                'exclusions' => ['Gioăng cao su hao mòn', 'Báng cửa vỡ', 'Dùng sai điện áp 220V/380V'],
                'sla' => 'Xử lý sự cố trong vòng 24h làm việc',
                'hotline' => $default_hotline,
            ],
            'rational' => [
                'brand' => 'Rational (Đức)',
                'months' => 24,
                'covered_parts' => ['Hệ thống gia nhiệt', 'Cảm biến nhiệt độ thịt', 'Hệ thống rửa tự động iCareSystem', 'Bo mạch chính'],
                'exclusions' => ['Không dùng hóa chất rửa chính hãng Rational', 'Nguồn nước chưa qua hệ thống lọc softened water'],
                'sla' => 'Cam kết phản hồi dưới 4h cho thiết bị bếp trung tâm',
                'hotline' => $default_hotline,
            ],
            'winterhalter' => [
                'brand' => 'Winterhalter (Đức/Thụy Sĩ)',
                'months' => 24,
                'covered_parts' => ['Bơm rửa lực cao', 'Thanh điện trở gia nhiệt', 'Hệ thống thu hồi nhiệt'],
                'exclusions' => ['Tắc vòi phun do cáu cặn nước cứng', 'Dùng sai hóa chất rửa chén'],
                'sla' => 'Kỹ thuật có mặt trong 6h',
                'hotline' => $default_hotline,
            ],
        ];

        $matched_policy = null;
        foreach ($policies as $b_key => $p) {
            if (strpos($input, $b_key) !== false) {
                $matched_policy = $p;
                break;
            }
        }

        if (!$matched_policy) {
            // Standard HORECA warranty policy fallback
            $matched_policy = [
                'brand' => 'Thiết bị HORECA Saigon Horeca cung cấp',
                'months' => 12,
                'covered_parts' => ['Lỗi nhà sản xuất đối với động cơ', 'Lốc máy', 'Bo mạch điện tử'],
                'exclusions' => ['Hao mòn tự nhiên', 'Biến dạng do va đập', 'Thiên tai ngập nước'],
                'sla' => 'Hỗ trợ kỹ thuật tận nơi trong 24h',
                'hotline' => $default_hotline,
            ];
        }

        return [
            'success' => true,
            'data'    => [
                'query'                => $args['product_name_or_brand'],
                'brand'                => $matched_policy['brand'],
                'warranty_months'      => $matched_policy['months'],
                'warranty_formatted'   => $matched_policy['months'] . ' Tháng',
                'covered_components'   => $matched_policy['covered_parts'],
                'exclusions'           => $matched_policy['exclusions'],
                'service_sla'          => $matched_policy['sla'],
                'hotline'              => $matched_policy['hotline'],
                'summary_text'         => sprintf(
                    'Chính sách bảo hành %s: Thời hạn %d tháng. Phủ sóng các linh kiện: %s. SLA hỗ trợ: %s. Hotline: %s.',
                    $matched_policy['brand'],
                    $matched_policy['months'],
                    implode(', ', $matched_policy['covered_parts']),
                    $matched_policy['sla'],
                    $matched_policy['hotline']
                ),
            ],
        ];
    }
}
