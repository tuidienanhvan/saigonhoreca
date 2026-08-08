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

        // 2. Spam / character-flood protection.
        //
        // 2026-08-08 fix — this was blocking real customers. The old pattern
        // was /(.)\1{4,}/u, i.e. ANY character five times in a row, DIGITS
        // INCLUDED. Vietnamese mobile numbers routinely contain runs like
        // that, so these were all rejected with HTTP 400 "Your request
        // contains unsafe pattern":
        //     "so cua toi la 0999999999"     -> BLOCKED
        //     "toi can bao gia 0911111111"   -> BLOCKED
        // Verified against the live guard before the fix. Doubly wrong given
        // the plugin's whole lead-capture feature exists to collect phone
        // numbers — the guard was rejecting the single most valuable message
        // a visitor can send.
        //
        // The rule this check is actually meant to enforce is "a wall of one
        // repeated character is not a real question" (aaaaaaaa, ééééééé).
        // Digits repeat legitimately — phone numbers, model codes (FGTC60-45,
        // IM-100), quantities, years — so they are excluded from the run
        // check entirely rather than special-cased per format. A genuine
        // digit flood is still caught by the length limits in checks 3/4 and
        // by MAX_MESSAGE_LENGTH upstream.
        //
        // The threshold moves 5 -> 8 for the remaining characters as well:
        // "okeeee", "khôngggg", "hmmmmm" are ordinary informal Vietnamese
        // typing, not spam.
        if (preg_match('/([^\d\s])\1{7,}/u', $message)) {
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
