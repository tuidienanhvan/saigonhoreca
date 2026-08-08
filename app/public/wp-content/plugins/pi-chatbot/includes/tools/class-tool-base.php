<?php
/**
 * Abstract Tool Base for Pi Chatbot ReAct Engine
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

abstract class Pi_Chatbot_Tool_Base {

    /**
     * Get tool name (unique identifier)
     *
     * @return string
     */
    abstract public function get_name(): string;

    /**
     * Get tool description for LLM function calling / system prompt injection
     *
     * @return string
     */
    abstract public function get_description(): string;

    /**
     * Get JSON Schema definition for arguments
     *
     * @return array
     */
    abstract public function get_schema(): array;

    /**
     * Execute tool logic atomically
     *
     * @param array $args
     * @return array Result payload with success boolean and data/error
     */
    abstract public function execute(array $args): array;

    /**
     * Validate input arguments against schema required parameters
     *
     * @param array $args
     * @return true|WP_Error
     */
    public function validate_args(array $args) {
        $schema = $this->get_schema();
        $required = $schema['required'] ?? [];
        $properties = $schema['properties'] ?? [];

        foreach ($required as $param) {
            if (!array_key_exists($param, $args) || $args[$param] === null || $args[$param] === '') {
                return new WP_Error(
                    'invalid_tool_arg',
                    sprintf(__('Missing required parameter "%s" for tool "%s".', 'pi-chatbot'), $param, $this->get_name())
                );
            }
        }

        foreach ($args as $key => $val) {
            if (isset($properties[$key]['type'])) {
                $expected_type = $properties[$key]['type'];
                $is_valid = true;

                switch ($expected_type) {
                    case 'array':
                        $is_valid = is_array($val);
                        break;
                    case 'object':
                        $is_valid = is_array($val);
                        break;
                    case 'string':
                        $is_valid = is_string($val) || is_numeric($val);
                        break;
                    case 'integer':
                    case 'number':
                        $is_valid = is_numeric($val);
                        break;
                    case 'boolean':
                        $is_valid = is_bool($val) || $val === 'true' || $val === 'false' || $val === 1 || $val === 0;
                        break;
                }

                if (!$is_valid) {
                    return new WP_Error(
                        'invalid_tool_arg_type',
                        sprintf(__('Parameter "%s" for tool "%s" must be of type %s.', 'pi-chatbot'), $key, $this->get_name(), $expected_type)
                    );
                }
            }

            // 2026-08-08: enum / minItems / maxItems are now ENFORCED.
            // Schemas already declared them (the removed spec_evaluator, for
            // instance, declared an enum of calculation types and the removed
            // product_comparer declared minItems 2 / maxItems 3) but
            // validate_args() only ever checked `required` and `type` — so a
            // tool could be handed a value its own schema forbade and would
            // run anyway, on whatever branch its switch/default landed on.
            // A schema that is documented but not enforced is worse than no
            // schema: it reads like a guarantee while providing none.
            if (isset($properties[$key]['enum']) && is_array($properties[$key]['enum'])) {
                if (!in_array($val, $properties[$key]['enum'], true)) {
                    return new WP_Error(
                        'invalid_tool_arg_enum',
                        sprintf(
                            __('Parameter "%s" for tool "%s" must be one of: %s.', 'pi-chatbot'),
                            $key,
                            $this->get_name(),
                            implode(', ', array_map('strval', $properties[$key]['enum']))
                        )
                    );
                }
            }

            if (is_array($val)) {
                if (isset($properties[$key]['minItems']) && count($val) < (int) $properties[$key]['minItems']) {
                    return new WP_Error(
                        'invalid_tool_arg_min_items',
                        sprintf(
                            __('Parameter "%s" for tool "%s" needs at least %d item(s).', 'pi-chatbot'),
                            $key, $this->get_name(), (int) $properties[$key]['minItems']
                        )
                    );
                }
                if (isset($properties[$key]['maxItems']) && count($val) > (int) $properties[$key]['maxItems']) {
                    return new WP_Error(
                        'invalid_tool_arg_max_items',
                        sprintf(
                            __('Parameter "%s" for tool "%s" accepts at most %d item(s).', 'pi-chatbot'),
                            $key, $this->get_name(), (int) $properties[$key]['maxItems']
                        )
                    );
                }
            }
        }

        return true;
    }
}
