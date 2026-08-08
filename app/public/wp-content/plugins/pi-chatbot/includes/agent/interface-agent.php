<?php
/**
 * Agent Interface for Pi Chatbot Multi-Agent Orchestration
 *
 * @package Pi_Chatbot
 */

if (!defined('ABSPATH')) {
    exit;
}

interface Pi_Chatbot_Agent_Interface {

    /**
     * Unique Agent Identifier
     *
     * @return string E.g. 'supervisor', 'sales', 'tech_spec', 'lead_capture', 'warranty'
     */
    public function get_id(): string;

    /**
     * Agent Name
     *
     * @return string
     */
    public function get_name(): string;

    /**
     * Agent Role Description
     *
     * @return string
     */
    public function get_description(): string;

    /**
     * System Prompt tailored to this Agent's specific role
     *
     * @param array $context
     * @return string
     */
    public function get_system_prompt(array $context): string;

    /**
     * Tools assigned to this agent
     *
     * @return array
     */
    public function get_tools(): array;

    /**
     * Execute agent turn on state
     *
     * @param Pi_Chatbot_Handoff_State $state
     * @return array Response payload with optional handoff_to directive
     */
    public function execute(Pi_Chatbot_Handoff_State $state): array;
}
