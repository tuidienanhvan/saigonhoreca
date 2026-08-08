<?php
/**
 * Tool Registry & ReAct Executor Manager
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/class-tool-base.php';

// 2026-08-08: the four domain tools that used to be required here were REMOVED
// (moved to the Recycle Bin) after an audit found every one of them serving
// invented business data to real customers as trusted context:
//   - class-tool-warranty-checker.php  : invented warranty durations, covered
//     parts and SLA response times per brand. Verified against the real
//     catalog: of ~3500 products only THREE mention "bảo hành" at all, two of
//     which are a Toprinse spray valve with "bảo hành một năm" — nothing
//     anywhere corroborates Hoshizaki 24 / Berjaya 12 / Rational 24 /
//     Winterhalter 24, nor any covered-parts list or response-time promise.
//   - class-tool-price-calculator.php  : invented a volume-discount schedule
//     (5/8/12%), regional shipping fees (0 / 500.000đ / 1.500.000đ) and a flat
//     1.000.000đ installation fee. Catalog search for "chiết khấu", "phí giao",
//     "phí vận chuyển", "phí lắp đặt": ZERO products mention any of them.
//     This also contradicts the plugin's own long-standing, deliberately
//     tested pricing policy (see Retriever::price_context()): pricing is not
//     listed, it depends on configuration, contact the hotline.
//   - class-tool-product-comparer.php  : filled missing product fields with
//     invented defaults ('Nhật Bản', 'Tiêu chuẩn HORECA', '24 Tháng'...) —
//     its own comment said "// Return synthetic specs".
//   - class-tool-spec-evaluator.php    : the models it recommended were REAL,
//     but their hardcoded capacities were wrong and always OVERSTATED
//     (IM-100CNE: claimed 100 kg/ngày, listing says 95 Kg/24h; IM-240ANE:
//     claimed 240, listing says 205). Overstating capacity makes a customer
//     under-buy. Its electrical branch was sound engineering and can be
//     revived separately if wanted — see the note left in class-rest.php.
//
// Why none of this was caught by the existing safety net: tool output was
// injected as trusted REFERENCE INFORMATION at score => 999, outranking every
// real catalog row, and Generator::verify() only checks that an answer MATCHES
// its context — never that the context is TRUE.
//
// The registry itself is kept: it is real, sane infrastructure (validation,
// exception isolation, a `pi_chatbot_registered_tools` filter) and is worth
// having when a tool backed by REAL data is written.

class Pi_Chatbot_Tool_Registry {

    private static ?Pi_Chatbot_Tool_Registry $instance = null;

    /**
     * Registered tool instances map: tool_name => instance
     *
     * @var Pi_Chatbot_Tool_Base[]
     */
    private array $tools = [];

    private function __construct() {
        $this->register_default_tools();
    }

    public static function get_instance(): Pi_Chatbot_Tool_Registry {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function register_default_tools(): void {
        // No built-in tools ship by default — see the removal note at the top
        // of this file. A tool may only be registered here once its data comes
        // from a verifiable source (the product DB, or a policy the business
        // owner has actually confirmed), never from constants typed into PHP.

        /**
         * Filter to register custom tools via WordPress ecosystem plugins
         */
        $this->tools = apply_filters('pi_chatbot_registered_tools', $this->tools);
    }

    public function register(Pi_Chatbot_Tool_Base $tool): void {
        $this->tools[$tool->get_name()] = $tool;
    }

    public function get_tool(string $name): ?Pi_Chatbot_Tool_Base {
        return $this->tools[$name] ?? null;
    }

    public function get_all_tools(): array {
        return $this->tools;
    }

    /**
     * Get intent-filtered tool instances to avoid context inflation.
     *
     * 2026-08-08: the hardcoded per-intent allowlists that used to live here
     * named the four removed tools, so they filtered against names that can no
     * longer exist. Rather than keep a stale map, filtering is now delegated to
     * the tools themselves — a registered tool declares which intents it serves
     * via the `pi_chatbot_tool_intents` filter, defaulting to "all intents".
     * With no built-in tools registered this returns [] for every intent, which
     * is the honest current state.
     *
     * @param string $intent
     * @return Pi_Chatbot_Tool_Base[]
     */
    public function get_tools_for_intent(string $intent): array {
        return array_filter($this->tools, function ($tool) use ($intent) {
            $intents = apply_filters('pi_chatbot_tool_intents', null, $tool->get_name());
            return $intents === null || in_array($intent, (array) $intents, true);
        });
    }

    /**
     * Export tools as OpenAI function definitions
     *
     * @param string|null $intent
     * @return array
     */
    public function export_openai_tools(?string $intent = null): array {
        $tools = $intent ? $this->get_tools_for_intent($intent) : $this->get_all_tools();
        $exported = [];

        foreach ($tools as $tool) {
            $exported[] = [
                'type' => 'function',
                'function' => [
                    'name'        => $tool->get_name(),
                    'description' => $tool->get_description(),
                    'parameters'  => $tool->get_schema(),
                ],
            ];
        }

        return $exported;
    }

    /**
     * Safely execute a tool call with atomic error handling
     *
     * @param string $tool_name
     * @param array $args
     * @return array
     */
    public function execute_tool(string $tool_name, array $args): array {
        $tool = $this->get_tool($tool_name);
        if (!$tool) {
            return [
                'success' => false,
                'error'   => sprintf('Tool "%s" is not registered in Pi_Chatbot_Tool_Registry.', $tool_name),
            ];
        }

        try {
            $start = microtime(true);
            $result = $tool->execute($args);
            $duration_ms = round((microtime(true) - $start) * 1000, 2);

            $result['duration_ms'] = $duration_ms;
            $result['tool_name'] = $tool_name;

            return $result;
        } catch (\Throwable $e) {
            return [
                'success'   => false,
                'error'     => sprintf('Tool "%s" execution exception: %s', $tool_name, $e->getMessage()),
                'tool_name' => $tool_name,
            ];
        }
    }
}
