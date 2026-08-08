<?php
/**
 * Price Calculator Tool for Pi Chatbot ReAct Engine
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/class-tool-base.php';

class Pi_Chatbot_Tool_Price_Calculator extends Pi_Chatbot_Tool_Base {

    public function get_name(): string {
        return 'price_calculator';
    }

    public function get_description(): string {
        return 'Calculate dynamic quotation for kitchen equipment package including subtotal, volume discounts, VAT, shipping estimate, and installation fee.';
    }

    public function get_schema(): array {
        return [
            'type' => 'object',
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'product_name' => ['type' => 'string'],
                            'quantity' => ['type' => 'integer', 'minimum' => 1],
                            'unit_price' => ['type' => 'number', 'minimum' => 0],
                        ],
                        'required' => ['product_name', 'quantity', 'unit_price'],
                    ],
                ],
                'destination' => ['type' => 'string'],
                'include_installation' => ['type' => 'boolean', 'default' => true],
                'vat_rate' => ['type' => 'number', 'enum' => [0.08, 0.10], 'default' => 0.08],
            ],
            'required' => ['items'],
        ];
    }

    public function execute(array $args): array {
        $val = $this->validate_args($args);
        if (is_wp_error($val)) {
            return [
                'success' => false,
                'error'   => $val->get_error_message(),
            ];
        }

        $items = $args['items'] ?? [];
        if (empty($items)) {
            return [
                'success' => false,
                'error'   => 'Items array cannot be empty.',
            ];
        }

        $subtotal = 0.0;
        $total_qty = 0;
        $item_details = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $p_name = sanitize_text_field($item['product_name'] ?? 'Product');
            $qty = max(1, (int)($item['quantity'] ?? 1));
            $price = max(0.0, (float)($item['unit_price'] ?? 0));
            $line_total = $qty * $price;

            $subtotal += $line_total;
            $total_qty += $qty;

            $item_details[] = [
                'product_name' => $p_name,
                'quantity'     => $qty,
                'unit_price'   => $price,
                'line_total'   => $line_total,
                'formatted'    => sprintf('%s x%d = %s VNĐ', $p_name, $qty, number_format($line_total)),
            ];
        }

        // Volume discount determination
        $discount_pct = 0;
        if ($total_qty >= 11) {
            $discount_pct = 12;
        } elseif ($total_qty >= 6) {
            $discount_pct = 8;
        } elseif ($total_qty >= 3) {
            $discount_pct = 5;
        }

        $discount_amount = ($subtotal * $discount_pct) / 100.0;
        $net_subtotal = $subtotal - $discount_amount;

        $vat_rate = isset($args['vat_rate']) ? (float)$args['vat_rate'] : 0.08;
        $vat_amount = $net_subtotal * $vat_rate;

        // Shipping estimate
        $dest_raw = !empty($args['destination']) ? sanitize_text_field($args['destination']) : 'HCM';
        $dest = mb_strtolower($dest_raw, 'UTF-8');
        $shipping_fee = 0;
        if (strpos($dest, 'hcm') !== false || strpos($dest, 'sài gòn') !== false || strpos($dest, 'hồ chí minh') !== false) {
            $shipping_fee = 0; // Free inner-city HCM
        } elseif (strpos($dest, 'bình dương') !== false || strpos($dest, 'đồng nai') !== false || strpos($dest, 'long an') !== false) {
            $shipping_fee = 500000;
        } else {
            $shipping_fee = 1500000;
        }

        $inc_install = isset($args['include_installation']) ? (bool)$args['include_installation'] : true;
        $install_fee = $inc_install ? 1000000 : 0;

        $grand_total = $net_subtotal + $vat_amount + $shipping_fee + $install_fee;

        return [
            'success' => true,
            'data'    => [
                'subtotal'            => $subtotal,
                'subtotal_formatted'  => number_format($subtotal) . ' VNĐ',
                'total_quantity'      => $total_qty,
                'discount_percentage' => $discount_pct,
                'discount_amount'     => $discount_amount,
                'discount_formatted'  => number_format($discount_amount) . ' VNĐ',
                'net_subtotal'        => $net_subtotal,
                'vat_rate_pct'        => ($vat_rate * 100) . '%',
                'vat_amount'          => $vat_amount,
                'shipping_fee'        => $shipping_fee,
                'installation_fee'    => $install_fee,
                'grand_total'         => $grand_total,
                'grand_total_formatted' => number_format($grand_total) . ' VNĐ',
                'items'               => $item_details,
                'summary_text'        => sprintf(
                    'Tạm tính: %s | Chiết khấu (%d%%): -%s | VAT (%s): %s | Phí giao hàng (%s): %s | Phí lắp đặt: %s | TỔNG CỘNG: %s VNĐ',
                    number_format($subtotal),
                    $discount_pct,
                    number_format($discount_amount),
                    ($vat_rate * 100) . '%',
                    number_format($vat_amount),
                    $dest_raw,
                    number_format($shipping_fee),
                    number_format($install_fee),
                    number_format($grand_total)
                ),
            ],
        ];
    }
}
