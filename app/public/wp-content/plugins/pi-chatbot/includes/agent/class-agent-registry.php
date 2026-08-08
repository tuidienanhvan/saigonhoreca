<?php
/**
 * Agent Registry for Pi Chatbot Multi-Agent Orchestration
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/interface-agent.php';
require_once __DIR__ . '/class-handoff-state.php';
require_once __DIR__ . '/class-supervisor-agent.php';
require_once __DIR__ . '/class-general-agent.php';
require_once __DIR__ . '/class-sales-agent.php';
require_once __DIR__ . '/class-tech-spec-agent.php';
require_once __DIR__ . '/class-lead-capture-agent.php';
require_once __DIR__ . '/class-warranty-agent.php';

class Pi_Chatbot_Agent_Registry {

    private static ?Pi_Chatbot_Agent_Registry $instance = null;

    /**
     * Registered subagents: agent_id => instance
     *
     * @var Pi_Chatbot_Agent_Interface[]
     */
    private array $agents = [];

    private function __construct() {
        $this->register_default_agents();
    }

    private function register_default_agents(): void {
        $this->register(new Pi_Chatbot_Supervisor_Agent());
        $this->register(new Pi_Chatbot_General_Agent());
        $this->register(new Pi_Chatbot_Sales_Agent());
        $this->register(new Pi_Chatbot_Tech_Spec_Agent());
        $this->register(new Pi_Chatbot_Lead_Capture_Agent());
        $this->register(new Pi_Chatbot_Warranty_Agent());
    }

    public static function get_instance(): Pi_Chatbot_Agent_Registry {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register(Pi_Chatbot_Agent_Interface $agent): void {
        $this->agents[$agent->get_id()] = $agent;
    }

    public function get_agent(string $id): ?Pi_Chatbot_Agent_Interface {
        $all = $this->get_all_agents();
        return $all[$id] ?? null;
    }

    public function get_all_agents(): array {
        /**
         * Filter to register subagents dynamically in WordPress
         */
        return apply_filters('pi_chatbot_registered_agents', $this->agents);
    }
}
