<?php
/**
 * Kitchen Specification Evaluator Tool (Code Interpreter Light)
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/class-tool-base.php';

class Pi_Chatbot_Tool_Spec_Evaluator extends Pi_Chatbot_Tool_Base {

    public function get_name(): string {
        return 'spec_evaluator';
    }

    public function get_description(): string {
        return 'Calculate commercial kitchen requirements: ice machine kg/day, exhaust hood CFM/airflow, or total 3-phase electrical load kW and amperage.';
    }

    public function get_schema(): array {
        return [
            'type' => 'object',
            'properties' => [
                'calculation_type' => [
                    'type' => 'string',
                    'enum' => ['ice_requirement', 'hood_airflow_cfm', 'electrical_load_kw'],
                ],
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'seats' => ['type' => 'integer'],
                        'establishment_type' => [
                            'type' => 'string',
                            'enum' => ['buffet', 'bar', 'fine_dining', 'cafe', 'canteen'],
                        ],
                        'equipment_kw_list' => [
                            'type' => 'array',
                            'items' => ['type' => 'number'],
                        ],
                        'hood_length_meters' => ['type' => 'number'],
                    ],
                ],
            ],
            'required' => ['calculation_type', 'parameters'],
        ];
    }

    public function execute(array $args): array {
        $val = $this->validate_args($args);
        if (is_wp_error($val)) {
            return ['success' => false, 'error' => $val->get_error_message()];
        }

        $type = sanitize_text_field($args['calculation_type']);
        $params = $args['parameters'] ?? [];

        switch ($type) {
            case 'ice_requirement':
                $seats = max(1, (int)($params['seats'] ?? 50));
                $est_type = strtolower(sanitize_text_field($params['establishment_type'] ?? 'buffet'));

                // kg per seat per day ratio
                $ratio = 1.0;
                if ($est_type === 'buffet' || $est_type === 'canteen') {
                    $ratio = 1.5;
                } elseif ($est_type === 'bar') {
                    $ratio = 1.2;
                } elseif ($est_type === 'cafe') {
                    $ratio = 0.8;
                } elseif ($est_type === 'fine_dining') {
                    $ratio = 0.5;
                }

                $needed_kg = $seats * $ratio;
                $rec_model = 'Hoshizaki IM-100CNE (100kg/ngày)';
                if ($needed_kg > 300) {
                    $rec_model = 'Hoshizaki FM-480AKE (450kg/ngày)';
                } elseif ($needed_kg > 180) {
                    $rec_model = 'Hoshizaki FM-300AKE (320kg/ngày)';
                } elseif ($needed_kg > 100) {
                    $rec_model = 'Hoshizaki IM-240ANE (240kg/ngày)';
                }

                return [
                    'success' => true,
                    'data'    => [
                        'type'            => 'Nhu cầu công suất máy làm đá',
                        'seats'           => $seats,
                        'type_label'      => $est_type,
                        'ice_needed_kg'   => $needed_kg,
                        'unit'            => 'kg/ngày',
                        'formula'         => sprintf('%d ghế * %.1f kg/ghế (%s)', $seats, $ratio, $est_type),
                        'recommendation'  => sprintf('Khuyên dùng máy làm đá công suất tối thiểu %d kg/ngày (Gợi ý: %s)', ceil($needed_kg), $rec_model),
                    ],
                ];

            case 'hood_airflow_cfm':
                $length_m = max(0.5, (float)($params['hood_length_meters'] ?? 2.0));
                // Standard commercial exhaust velocity: 1500 m3/h per meter of hood
                $m3h = $length_m * 1500;
                $cfm = $m3h / 1.699; // 1 CFM = 1.699 m3/h

                return [
                    'success' => true,
                    'data'    => [
                        'type'             => 'Lưu lượng gió chụp hút khói bếp',
                        'hood_length_m'    => $length_m,
                        'airflow_m3h'      => round($m3h, 2),
                        'airflow_cfm'      => round($cfm, 2),
                        'recommendation'   => sprintf('Chụp hút dài %.1fm cần quạt hút công suất tối thiểu %d m³/h (~%d CFM), đường ống Ø%d mm.', $length_m, ceil($m3h), ceil($cfm), $length_m >= 3.0 ? 350 : 250),
                        'summary_text'     => sprintf('Chụp hút dài %.1fm cần quạt hút công suất tối thiểu %d m³/h (~%d CFM), đường ống Ø%d mm.', $length_m, ceil($m3h), ceil($cfm), $length_m >= 3.0 ? 350 : 250),
                    ],
                ];

            case 'electrical_load_kw':
                $raw_kw = $params['equipment_kw_list'] ?? [5.0, 3.5, 12.0];
                $kw_list = is_array($raw_kw) ? $raw_kw : (is_numeric($raw_kw) ? [$raw_kw] : [5.0, 3.5, 12.0]);
                $total_kw = array_sum(array_map('floatval', $kw_list));
                
                // 3-Phase 380V amperage calculation: I = P / (sqrt(3) * U * cos_phi) -> I ≈ P_kW * 1.8
                $amps = $total_kw * 1.8;
                $rec_breaker = ceil($amps * 1.25); // 25% safety margin

                return [
                    'success' => true,
                    'data'    => [
                        'type'            => 'Tổng công suất điện & Aptomat bếp',
                        'total_kw'        => round($total_kw, 2),
                        'amperage_3phase' => round($amps, 1),
                        'unit_amp'        => 'Amperes (A)',
                        'recommendation'  => sprintf('Tổng công suất %.1f kW (3 pha 380V). Dòng điện ~%.1fA. Khuyên dùng Aptomat tổng MCCB 3P %dA.', $total_kw, $amps, $rec_breaker),
                        'summary_text'     => sprintf('Tổng công suất %.1f kW (3 pha 380V). Dòng điện ~%.1fA. Khuyên dùng Aptomat tổng MCCB 3P %dA.', $total_kw, $amps, $rec_breaker),
                    ],
                ];

            default:
                return ['success' => false, 'error' => 'Unknown calculation type.'];
        }
    }
}
