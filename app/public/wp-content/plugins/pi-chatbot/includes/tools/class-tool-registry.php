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
require_once __DIR__ . '/class-tool-price-calculator.php';
require_once __DIR__ . '/class-tool-product-comparer.php';
require_once __DIR__ . '/class-tool-warranty-checker.php';
require_once __DIR__ . '/class-tool-spec-evaluator.php';

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
        $this->register(new Pi_Chatbot_Tool_Price_Calculator());
        $this->register(new Pi_Chatbot_Tool_Product_Comparer());
        $this->register(new Pi_Chatbot_Tool_Warranty_Checker());
        $this->register(new Pi_Chatbot_Tool_Spec_Evaluator());

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
     * Get intent-filtered tool instances to avoid context inflation
     *
     * @param string $intent
     * @return Pi_Chatbot_Tool_Base[]
     */
    public function get_tools_for_intent(string $intent): array {
        switch ($intent) {
            case 'price':
                return array_filter($this->tools, fn($name) => in_array($name, ['price_calculator', 'product_comparer']), ARRAY_FILTER_USE_KEY);
            case 'product':
                return array_filter($this->tools, fn($name) => in_array($name, ['product_comparer', 'price_calculator']), ARRAY_FILTER_USE_KEY);
            case 'service':
                return array_filter($this->tools, fn($name) => in_array($name, ['warranty_checker']), ARRAY_FILTER_USE_KEY);
            case 'project':
                return array_filter($this->tools, fn($name) => in_array($name, ['spec_evaluator', 'price_calculator']), ARRAY_FILTER_USE_KEY);
            default:
                return $this->tools;
        }
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
