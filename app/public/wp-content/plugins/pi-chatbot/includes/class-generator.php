<?php
/**
 * Generator — builds the prompt from retrieval context, calls the LLM7 API
 * (https://api.llm7.io) directly to generate the answer. NO Luna-Proxy — per
 * user request (Luna-Proxy forces an arbitrary model + is an unnecessary
 * secondary dependency).
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Generator {

    const SYSTEM_PROMPT = "CRITICAL LANGUAGE RULE — read this first: detect the language of the CUSTOMER QUESTION below and write your ENTIRE reply in that exact same language. "
        . "If the question is in English, reply ONLY in English. If Vietnamese, reply ONLY in Vietnamese. If Japanese, Korean, Chinese, etc., reply ONLY in that language. "
        . "This applies NO MATTER what language the REFERENCE INFORMATION happens to be written in — the reference is source material to translate/paraphrase from, not a language to copy. Never mix two languages in one reply.\n\n"
        . "You are the customer support assistant for Saigon Horeca — a commercial kitchen equipment supplier for restaurants and hotels (Western/Asian ranges, ovens, commercial refrigeration, dishwashing equipment...).\n"
        . "Answer ONLY from the REFERENCE INFORMATION provided below — never invent products, prices, or specs that aren't in it.\n"
        . "If the customer asks a GENERAL question like \"what do you sell\" or \"what do you have\" with NO specific reference information available: you may briefly introduce the main product CATEGORIES per the company description above (Western/Asian ranges, ovens, commercial refrigeration, dishwashing equipment...) then ask which category they'd like to see — this is a general company description, not inventing a specific product.\n"
        . "If the customer asks a SPECIFIC question (product name/brand/model) and the reference information isn't enough: say clearly that you don't have that information yet and suggest contacting the hotline — never guess.\n"
        . "If the REFERENCE INFORMATION includes a \"Pricing policy\" entry: relay that content as-is (pricing depends on configuration, invite them to contact the hotline) — NEVER estimate or invent a specific price figure, even if the customer presses repeatedly or says \"just a rough number is fine\".\n"
        . "Answer briefly, in a friendly and professional advisory tone. Do not use Markdown tables or the |, #, ** characters — plain prose or simple dashes only, since the chat UI doesn't render Markdown.";

    /**
     * @param string $question
     * @param array  $context Result from Pi_Chatbot_Retriever::search()
     * @return array{answer:string, sources:array}|WP_Error
     */
    public static function answer($question, $context) {
        $settings = pi_chatbot_settings();

        if (empty($context)) {
            $context_text = "(No matching product/page found in the internal data.)";
        } else {
            $lines = [];
            foreach ($context as $item) {
                $lines[] = sprintf(
                    "- %s%s%s — %s\n  %s",
                    $item['title'],
                    $item['brand'] ? " (Brand: {$item['brand']})" : '',
                    $item['category'] ? " [{$item['category']}]" : '',
                    $item['url'],
                    $item['excerpt']
                );
            }
            $context_text = implode("\n", $lines);
        }

        // Even a same-language reminder placed right next to the question (see
        // below) wasn't enough on its own — verified live: a long, ALL-Vietnamese
        // reference block (6 projects worth of descriptions) still made
        // gpt-oss:20b answer in Vietnamese to an English question despite BOTH
        // the system prompt AND a "same language as the question" reminder here.
        // Referential instructions ("same language as X") ask a cheap model to
        // first classify a language, then obey — an extra reasoning step it
        // unreliably skips under a large opposing-language context. Naming the
        // target language directly is a much simpler, one-step instruction to
        // follow.
        //
        // A first version of this fix hardcoded "not Vietnamese -> English",
        // which fixed English but broke every OTHER language: a Chinese
        // question got forced into an English answer (verified live), because
        // the instruction explicitly told the model to write in English no
        // matter what. Detecting language by Unicode SCRIPT RANGE instead (not
        // just "has Vietnamese diacritics or not") covers the actual set of
        // customer languages this site expects and names each one explicitly.
        $language_note = self::build_language_note($question);
        $user_prompt = "REFERENCE INFORMATION:\n{$context_text}\n\nCUSTOMER QUESTION: {$question}\n\n{$language_note}";

        $body = [
            'model'    => $settings['model'],
            'messages' => [
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                ['role' => 'user', 'content' => $user_prompt],
            ],
            'temperature' => 0.3,
            // max_tokens guards against infinite repetition (a real loop was
            // observed with gpt-oss:20b: repeating "steam oven range" hundreds
            // of times) — uncapped, that both wastes time (timeout risk) and
            // gets cached verbatim by the REST layer.
            //
            // 2000, NOT 700: gpt-oss:20b is a REASONING model — it emits a
            // `reasoning` field (~700+ chars) BEFORE `content`, and both eat
            // into max_tokens. Capping at 700 let the reasoning eat nearly the
            // whole budget, occasionally leaving content EMPTY -> REST
            // returned intermittent 502s (a real bug caught during
            // verification: the same question sometimes worked, sometimes
            // 502'd). 2000 leaves enough room for reasoning + the actual
            // answer; infinite-repetition is now handled by
            // looks_degenerate() instead.
            'max_tokens' => 2000,
        ];

        $response = Pi_Chatbot_LLM_Client::call(
            $body, $settings['llm7_keys'], self::TIMEOUT_SECONDS, self::MAX_KEY_ATTEMPTS, 'pi-chatbot-generator'
        );
        if (is_wp_error($response)) return $response;

        $answer = Pi_Chatbot_LLM_Client::content($response);
        if ($answer === '') {
            return new WP_Error('pi_chatbot_empty', 'Did not receive an answer from the model.');
        }

        // Extra safeguard: the free-tier model sometimes repeats infinitely
        // even with max_tokens capped (repeating WITHIN the granted token
        // budget) — detected by comparing text length against its length
        // after collapsing consecutive repeated phrases; too large a gap
        // means degenerate output. Do NOT return a degenerate answer (and do
        // NOT cache it — see class-rest.php).
        if (self::looks_degenerate($answer)) {
            return new WP_Error('pi_chatbot_degenerate', 'The model produced an invalid answer (abnormal repetition).');
        }

        $answer = self::strip_markdown($answer);

        return [
            'answer'  => $answer,
            'sources' => array_map(function ($item) {
                return ['title' => $item['title'], 'url' => $item['url']];
            }, $context),
        ];
    }

    /**
     * Detects the customer's language by Unicode SCRIPT RANGE and returns a
     * reminder that names it EXPLICITLY (see the call site's docblock for why
     * a named language works far more reliably than a referential "same
     * language as the question" instruction on a cheap model). Order matters:
     * Hiragana/Katakana is checked before bare CJK ideographs so a Japanese
     * sentence containing kanji isn't misclassified as Chinese; a Vietnamese
     * question is Latin script plus its distinctive diacritics, checked
     * before the plain-Latin/English fallback.
     */
    private static function build_language_note($question) {
        if (preg_match('/[àáạảãăằắẳẵặâầấẩẫậèéẹẻẽêềếểễệđìíịỉĩòóọỏõôồốổỗộơờớởỡợùúụủũưừứửữựỳýỵỷỹ]/iu', $question)) {
            return '(Nhắc lại: viết TOÀN BỘ câu trả lời bằng tiếng Việt.)';
        }
        if (preg_match('/[\x{3040}-\x{30ff}]/u', $question)) { // Hiragana/Katakana -> Japanese
            return '(リマインダー: 回答は必ず日本語で書いてください。参考情報がベトナム語であっても、日本語のみで回答してください。)';
        }
        if (preg_match('/[\x{ac00}-\x{d7a3}]/u', $question)) { // Hangul -> Korean
            return '(리마인더: 답변 전체를 한국어로 작성하세요. 참고 정보가 베트남어로 되어 있어도 한국어로만 답변하세요.)';
        }
        if (preg_match('/[\x{4e00}-\x{9fff}]/u', $question)) { // CJK ideographs w/o kana -> Chinese
            return '(提醒：请务必用中文回答全部内容。即使参考信息是越南语，也只能用中文回答。)';
        }
        if (preg_match('/[\x{0400}-\x{04ff}]/u', $question)) { // Cyrillic -> Russian
            return '(Напоминание: пишите ВЕСЬ ответ на русском языке. Не используйте вьетнамский, даже если справочная информация выше на вьетнамском.)';
        }
        if (preg_match('/[\x{0e00}-\x{0e7f}]/u', $question)) { // Thai script
            return '(เตือนความจำ: กรุณาเขียนคำตอบทั้งหมดเป็นภาษาไทย อย่าใช้ภาษาเวียดนามแม้ว่าข้อมูลอ้างอิงด้านบนจะเป็นภาษาเวียดนามก็ตาม)';
        }
        // German/French share the Latin alphabet with English/Vietnamese, so
        // script range alone can't tell them apart — a real bug caught live:
        // "Haben Sie industrielle Öfen?" got answered in English because it
        // fell through to the English fallback below. Narrower than the
        // script-range checks above: look for characters/words distinctive
        // enough that they essentially never appear in an English question
        // (ß and word-boundary "haben"/"sie" for German; the elided
        // "avez-vous"/"vous avez" pattern for French). This does NOT cover
        // every possible German/French phrasing — a French question with
        // none of these markers would still fall through to English, unlike
        // the reliable script-based checks above for other languages.
        if (preg_match('/ß/u', $question) || preg_match('/\b(haben sie|können sie|ofen|öfen|industrielle)\b/iu', $question)) {
            return '(Erinnerung: Schreiben Sie Ihre GESAMTE Antwort auf Deutsch. Verwenden Sie kein Vietnamesisch, auch wenn die Referenzinformationen oben auf Vietnamesisch sind.)';
        }
        if (preg_match('/\b(avez[- ]vous|vous avez|êtes[- ]vous|s\'il vous pla[iî]t|pouvez[- ]vous)\b/iu', $question)) {
            return "(Rappel : rédigez l'INTÉGRALITÉ de votre réponse en français. N'utilisez pas le vietnamien, même si les informations de référence ci-dessus sont en vietnamien.)";
        }
        // Latin script, no Vietnamese diacritics and no German/French marker
        // matched -> the common realistic fallback for this site (English),
        // not a generic referential instruction — see docblock at the call
        // site for why that failed.
        return '(Reminder: write your ENTIRE answer in English. Do not use any Vietnamese, even though the reference information above is in Vietnamese.)';
    }

    /**
     * Detects a degenerate answer that repeats one phrase over and over
     * ("steam oven range steam oven range...") — a real error seen while
     * testing with the free-tier gpt-oss:20b model. Simple heuristic: if one
     * word makes up more than 25% of a long-enough answer, it's almost
     * certainly abnormal repetition, not natural phrasing.
     */
    private static function looks_degenerate($text) {
        $words = preg_split('/\s+/u', trim($text));
        // Drop tokens with no letters/digits at all (bare "-"/"*"/"•" bullet
        // markers, stray punctuation) before counting — a real false positive
        // was caught live on a Japanese answer: 4 markdown bullets + 4
        // sub-bullets means 8 lone "-" tokens, and CJK text has no spaces
        // between words, so the total token count was only 31 (mostly single
        // giant Japanese chunks) — 8/31 = 25.8%, just over the threshold,
        // wrongly flagging a well-formed, non-repetitive product list as
        // "abnormal repetition". A bullet marker repeating once per list item
        // is structural, not a sign the MODEL is looping.
        $words = array_values(array_filter($words, function ($w) {
            return (bool) preg_match('/[\p{L}\p{N}]/u', $w);
        }));
        $total = count($words);
        if ($total < 20) return false; // too short a sample to judge reliably, avoid false positives
        $counts = array_count_values(array_map('mb_strtolower', $words));
        arsort($counts);
        $top_count = reset($counts);
        return ($top_count / $total) > 0.25;
    }

    /**
     * Second safety net for the Markdown ban in SYSTEM_PROMPT — the prompt is
     * a SOFT guardrail (the model can still drift even at low temperature),
     * so strip raw syntax here instead of building a Markdown renderer on the
     * client (the client uses plain textContent, not innerHTML — that
     * security invariant is kept on purpose, not traded away for a display
     * feature). Only handles simple INLINE syntax; a stray | table could
     * still slip through if the model deliberately breaks the rule — but the
     * prompt already forbids it clearly, and this is just a backstop.
     */
    private static function strip_markdown($text) {
        $text = preg_replace('/\*\*(.+?)\*\*/u', '$1', $text);   // **bold** -> bold
        $text = preg_replace('/^#{1,6}\s*/mu', '', $text);        // # Heading -> Heading
        return $text;
    }

    /** Short timeout + a capped number of key attempts per request — keeps a
     *  PHP-FPM worker from being held for more than MAX_KEY_ATTEMPTS x
     *  TIMEOUT_SECONDS seconds (previously 30s x N keys with no cap, which
     *  could hold a worker for a long time if an admin configured many keys
     *  and all of them were slow/failing). */
    const TIMEOUT_SECONDS = 20;
    const MAX_KEY_ATTEMPTS = 2;
}
