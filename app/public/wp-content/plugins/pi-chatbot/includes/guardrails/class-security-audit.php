<?php
/**
 * Security Audit & Prompt Injection Guard — 65-point red-team hardening (hello-agents Ch16).
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Security_Audit {

    /**
     * Patterns commonly used in prompt injection / jailbreak attempts.
     */
    const INJECTION_PATTERNS = [
        '/ignore\s+(all\s+)?previous\s+instructions/i',
        '/system\s+prompt\s+override/i',
        '/you\s+are\s+now\s+a\s+/i',
        '/DAN\s+mode/i',
        '/forget\s+all\s+rules/i',
        '/reveal\s+your\s+system\s+prompt/i',
        '/print\s+everything\s+above/i',
    ];

    /**
     * Inspect customer question for prompt injection signatures and spam/overflow patterns.
     *
     * @param string $message
     * @return array{is_safe: bool, reason: string|null}
     */
    public static function inspect($message) {
        $message = trim((string) $message);
        if ($message === '') {
            return ['is_safe' => true, 'reason' => null];
        }

        // 1. Check prompt injection signatures
        foreach (self::INJECTION_PATTERNS as $pattern) {
            if (preg_match($pattern, $message)) {
                return [
                    'is_safe' => false,
                    'reason'  => 'Prompt injection signature detected',
                ];
            }
        }

        // 2. Spam / Character flood protection: single character repeated 5+ times consecutively (e.g. 'xxxxx')
        if (preg_match('/(.)\1{4,}/u', $message)) {
            return [
                'is_safe' => false,
                'reason'  => 'Spam detected: repeated character flood',
            ];
        }

        // 3. Word length overflow / Gibberish protection: a single word longer than 45 chars without whitespace
        $words = preg_split('/\s+/u', $message);
        foreach ($words as $w) {
            if (mb_strlen($w) > 45) {
                return [
                    'is_safe' => false,
                    'reason'  => 'Spam detected: single word exceeds maximum length limit',
                ];
            }
        }

        // 4. Repeated special punctuation spam (e.g. '!!!!!!!!!!', '??????????')
        if (preg_match('/[!?.,;:\-_=+#@$%^&*]{8,}/u', $message)) {
            return [
                'is_safe' => false,
                'reason'  => 'Spam detected: excessive punctuation flood',
            ];
        }

        return ['is_safe' => true, 'reason' => null];
    }

    /**
     * Sanitize reference data against embedded injection payloads.
     *
     * @param string $data
     * @return string
     */
    public static function sanitize_reference_data($data) {
        $data = (string) $data;
        // Escape delimiters so reference content can never close the delimiter block prematurely
        $data = str_replace('<<<END_REFERENCE_DATA>>>', '<<<END_REF_DATA_ESCAPED>>>', $data);
        $data = str_replace('<<<REFERENCE_DATA>>>', '<<<REF_DATA_ESCAPED>>>', $data);
        return $data;
    }
}
