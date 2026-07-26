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
     * Renders the retrieval context into the exact block the model is told to
     * answer from. Shared with verify() ON PURPOSE: a faithfulness check that
     * reads even a slightly different rendering than the one the answer was
     * written against would flag correct answers as unsupported.
     */
    public static function context_to_text($context) {
        if (empty($context)) {
            return "(No matching product/page found in the internal data.)";
        }
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
        return implode("\n", $lines);
    }

    /**
     * @param string $question
     * @param array  $context Result from Pi_Chatbot_Retriever::search()
     * @param array  $options correction: feedback from a failed faithfulness
     *   check, appended to the system prompt on the single retry.
     * @return array{answer:string, sources:array}|WP_Error
     */
    public static function answer($question, $context, $options = []) {
        $settings = pi_chatbot_settings();

        $context_text = self::context_to_text($context);

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

        // The correction is appended to the SYSTEM prompt rather than the user
        // prompt so it carries the same weight as the no-inventing rule it is
        // enforcing — a "you got this wrong" note buried after the reference
        // block reads as part of the customer's message, not as an instruction.
        $system_prompt = self::SYSTEM_PROMPT;
        if (!empty($options['correction'])) {
            $system_prompt .= "\n\nIMPORTANT — your previous draft was rejected by a fact check: "
                . trim((string) $options['correction'])
                . "\nWrite a new answer using ONLY facts present in the REFERENCE INFORMATION. "
                . "If the reference does not support a claim, leave it out entirely rather than softening it.";
        }

        $body = [
            'model'    => $settings['model'],
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
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
     * Faithfulness check — a second model reads the answer against the same
     * reference block and reports any claim the reference does not support.
     *
     * This is the last line of a defence that starts with the system prompt's
     * no-inventing rule, and it exists because that rule is a SOFT guardrail:
     * the model can still drift, and the drift that matters commercially is
     * inventing a price, a capacity or a model number that a customer will
     * quote back. Run only on the max-accuracy path — the questions routed
     * there are the ambiguous ones, which are precisely where a model is most
     * tempted to fill a gap with something plausible.
     *
     * Uses the router-class model (fast, non-reasoning), not the answer model:
     * checking "is every claim present in this text" is a comparison task, and
     * asking the same model that wrote the answer to grade it invites it to
     * defend its own output.
     *
     * @return array{faithful:bool,issue:string}|null null = check unavailable
     *   (disabled, no key, unparseable reply). A check that could not run must
     *   never be reported as a check that passed — the caller distinguishes
     *   null from ['faithful' => false].
     */
    public static function verify($question, $context, $answer) {
        $settings = pi_chatbot_settings();
        if (empty($settings['verify_enabled'])) return null;
        $answer = trim((string) $answer);
        if ($answer === '') return null;

        $keys = !empty($settings['router_keys']) ? $settings['router_keys'] : $settings['llm7_keys'];
        if (empty($keys)) return null;

        $system = "You are a strict fact checker for a commercial kitchen equipment shop's chatbot.\n"
            . "Decide whether every factual claim in ANSWER is supported by REFERENCE.\n"
            . "Claims that matter: product names, model codes, brands, prices, capacities, dimensions, "
            . "power ratings, stock availability, warranty terms.\n"
            . "NOT violations — do not flag these:\n"
            . "- polite phrasing, greetings, offers to help further, invitations to call the hotline\n"
            . "- saying that information is unavailable, or asking the customer a clarifying question\n"
            . "- naming broad product CATEGORIES the shop sells in a general introduction\n"
            . "- translating or paraphrasing the reference into the customer's language\n"
            . "A violation is a SPECIFIC fact stated as true that REFERENCE does not contain.\n\n"
            . "Reply with ONLY this JSON, no other text:\n"
            . "{\"faithful\":true}  or  {\"faithful\":false,\"issue\":\"<the unsupported claim, max 20 words>\"}";

        $user = "REFERENCE:\n" . self::context_to_text($context)
            . "\n\nCUSTOMER QUESTION: {$question}\n\nANSWER:\n{$answer}";

        $response = Pi_Chatbot_LLM_Client::call(
            [
                'model'       => !empty($settings['rerank_model']) ? $settings['rerank_model'] : Pi_Chatbot_Reranker::MODEL_DEFAULT,
                'messages'    => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user],
                ],
                'temperature' => 0,
                'max_tokens'  => 150,
            ],
            $keys,
            self::VERIFY_TIMEOUT_SECONDS,
            1,
            'pi-chatbot-verifier'
        );
        if (is_wp_error($response)) return null;

        $parsed = Pi_Chatbot_LLM_Client::extract_json(Pi_Chatbot_LLM_Client::content($response));
        if (!is_array($parsed) || !array_key_exists('faithful', $parsed)) return null;

        // Only an explicit boolean false counts as a failure. A missing or
        // non-boolean value means the checker did not actually reach a verdict,
        // and discarding a good answer on that basis would be worse than the
        // unverified claim it was meant to catch.
        $faithful = !($parsed['faithful'] === false || $parsed['faithful'] === 'false');
        $issue = '';
        if (!$faithful && !empty($parsed['issue']) && is_string($parsed['issue'])) {
            $issue = mb_substr(trim($parsed['issue']), 0, 200);
        }
        return ['faithful' => $faithful, 'issue' => $issue];
    }

    /** Shorter than the answer timeout — the check reads existing text and
     *  emits one small JSON object, so a slow reply here means something is
     *  wrong, and waiting on it just adds to a request the customer is already
     *  waiting ~30s for. */
    const VERIFY_TIMEOUT_SECONDS = 12;

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
        // script range alone can't tell them apart. A first attempt matched a
        // handful of fixed PHRASES (e.g. "avez-vous", "haben sie") — verified
        // live to be too narrow: "Je voudrais des informations sur les fours
        // industriels pour ma cuisine." contains none of those phrases and
        // still fell through to the English fallback, answering a French
        // question in English. Counting matches against a small list of
        // common FUNCTION WORDS (je/des/les/pour/vos/vous...) is far more
        // robust than a handful of fixed phrases, since ordinary sentences
        // are built from a small closed set of grammatical words regardless
        // of how the sentence is phrased — 2+ hits is a strong signal, these
        // words essentially never appear in genuine English text. Still not
        // full language detection (no accent required, so it can't
        // distinguish French from, say, Portuguese if they shared enough
        // stopwords — but French/German don't overlap with each other or with
        // English here, which is the realistic ambiguity this site faces).
        $fr_hits = preg_match_all(
            '/\b(je|voudrais|des|les|pour|vos|votre|nous|avec|dans|sur|quel|quelle|c\'est|qu\'est|où|bonjour|merci|fours?|cuisine)\b/iu',
            $question
        );
        if ($fr_hits >= 2) {
            return "(Rappel : rédigez l'INTÉGRALITÉ de votre réponse en français. N'utilisez pas le vietnamien, même si les informations de référence ci-dessus sont en vietnamien.)";
        }
        $de_hits = preg_match_all(
            '/\b(ich|möchte|die|der|das|für|ihre|wir|mit|und|auf|welche|wo|danke|hallo|haben|sie|können|öfen?|industrielle)\b/iu',
            $question
        );
        if ($de_hits >= 2 || preg_match('/ß/u', $question)) {
            return '(Erinnerung: Schreiben Sie Ihre GESAMTE Antwort auf Deutsch. Verwenden Sie kein Vietnamesisch, auch wenn die Referenzinformationen oben auf Vietnamesisch sind.)';
        }
        // UNACCENTED VIETNAMESE — the last branch before the English fallback,
        // and the one that matters most in practice on this site. Vietnamese
        // typed without diacritics is extremely common (phone keyboards, speed,
        // habit), and it is invisible to every check above: it is plain Latin
        // script with no tone marks at all.
        //
        // Left unhandled it produced a genuinely bad result once retrieval
        // learned to answer these questions (see Retriever::search_unaccented):
        // "tu dong 2 canh" now finds the right 2-door freezers and then
        // described them to a Vietnamese customer IN ENGLISH.
        //
        // Detection is the same counting approach already used for French and
        // German above, with one addition: English is scored too, and the
        // higher score wins. Scoring only Vietnamese would misfire on genuine
        // English questions, which are also plain unaccented Latin — and this
        // site really does get those (a whole golden-set group is English).
        // Ties go to English, keeping the previous behaviour as the default.
        //
        // The word list mixes function words with unaccented forms of this
        // catalog's own vocabulary, because a short product question ("tu dong 2
        // canh") often contains no function words at all — the FR/DE lists
        // above can rely on function words alone only because those questions
        // are full sentences.
        //
        // "gas" and "inox" are deliberately ABSENT despite being everyday
        // Vietnamese words here: they are spelled identically in English, and
        // including "gas" was measured sending "Fujimak gas stove FGTLS1260" —
        // a real English golden-set question — down the Vietnamese branch.
        // Dropping both costs nothing (Vietnamese detection stays at 43/47)
        // and takes English back to 7/7.
        $vi_hits = preg_match_all(
            '/\b(khong|duoc|nhieu|muon|giup|minh|loai|cai|nao|voi|cua|cho|gia|bao|thiet|quan|hang|mua|tot|gium|toi|nay|kieu|dung|tich|cong suat|bep|lo|tu|may|ban|chien|nuong|rua|da|ly|chen|dia|hong|canh|ngan|dong|mat|nong|lanh|hap|nuoc|dien|cong nghiep|nha hang|quan an)\b/iu',
            $question
        );
        $en_hits = preg_match_all(
            '/\b(the|is|are|do|does|you|your|have|has|what|which|how|for|with|need|want|looking|price|about|can|please|and|of|in|on|me|my|any|there|this|that|best|good|buy|show)\b/iu',
            $question
        );
        if ($vi_hits > $en_hits) {
            return '(Nhắc lại: viết TOÀN BỘ câu trả lời bằng tiếng Việt CÓ DẤU đầy đủ, kể cả khi khách hàng gõ không dấu.)';
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
    /** Share of the answer a single repeated TOKEN may occupy. */
    const DEGENERATE_UNIGRAM_RATIO = 0.25;
    /** Share of the answer a single repeated PHRASE may occupy. Much higher
     *  than the unigram limit on purpose — see the comment at the loop. */
    const DEGENERATE_NGRAM_COVERAGE = 0.5;
    /** Longest repeated phrase looked for. Loops observed on this free tier are
     *  short list fragments (2-5 tokens); scanning further costs time and finds
     *  nothing new. */
    const DEGENERATE_MAX_NGRAM = 6;

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
        $words = array_map('mb_strtolower', $words);

        $counts = array_count_values($words);
        arsort($counts);
        if ((reset($counts) / $total) > self::DEGENERATE_UNIGRAM_RATIO) return true;

        // Phrase-level check. The single-token ratio above CANNOT see a loop
        // that repeats a multi-word phrase, because the phrase's own length
        // divides each token's share: measured live on "giới thiệu về công ty",
        // the model emitted "lò nướng bánh quy," 241 times in a row — 93% of
        // the entire answer, which then got cut off mid-word at max_tokens —
        // yet the most frequent single token ("lò", 253/1037) came to 0.244,
        // just UNDER the 0.25 threshold. The answer shipped to the customer.
        //
        // Coverage (occurrences x phrase length / total tokens) is the honest
        // measure: it asks what share of the text this one phrase actually is,
        // which is the same question the unigram ratio asks, only not blinded
        // by phrase length. Threshold is deliberately much higher than the
        // unigram one — a product list legitimately repeats a short phrase
        // ("tủ mát", "máy rửa") once per item, and that must not be flagged,
        // whereas nothing legitimate makes one phrase HALF the answer.
        for ($n = 2; $n <= self::DEGENERATE_MAX_NGRAM; $n++) {
            if ($total < $n * 4) break; // too few tokens for a repeat to mean anything
            $ngrams = [];
            for ($i = 0; $i + $n <= $total; $i++) {
                $key = implode(' ', array_slice($words, $i, $n));
                $ngrams[$key] = ($ngrams[$key] ?? 0) + 1;
            }
            if (!$ngrams) continue;
            arsort($ngrams);
            $coverage = (reset($ngrams) * $n) / $total;
            if ($coverage > self::DEGENERATE_NGRAM_COVERAGE) return true;
        }

        return false;
    }

    /**
     * Second safety net for the Markdown ban in SYSTEM_PROMPT — the prompt is
     * a SOFT guardrail (the model can still drift even at low temperature),
     * so strip raw syntax here instead of building a Markdown renderer on the
     * client (the client uses plain textContent, not innerHTML — that
     * security invariant is kept on purpose, not traded away for a display
     * feature).
     *
     * A Markdown table WAS the one gap this function's own earlier comment
     * predicted ("a stray | table could still slip through") — confirmed live
     * during a 107-question quality audit: asked to explain the difference
     * between two ice machine types, the model answered with a full
     * `| column | column |` table despite the system prompt forbidding it. The
     * raw pipe characters render as literal garbage in the plain-text widget.
     * table_to_lines() converts each row into a plain dash-joined line instead
     * of just deleting it — the customer still gets the comparison, in the
     * prose format the UI can actually show.
     */
    private static function strip_markdown($text) {
        $text = preg_replace('/\*\*(.+?)\*\*/u', '$1', $text);   // **bold** -> bold
        $text = preg_replace('/^#{1,6}\s*/mu', '', $text);        // # Heading -> Heading
        $text = self::table_to_lines($text);
        return $text;
    }

    /**
     * Rewrites Markdown table blocks into plain dash-joined lines.
     *
     * Walks the text line by line rather than matching the whole block with one
     * regex — a table can be preceded/followed by ordinary prose, and a single
     * multiline pattern would risk swallowing text outside the table.
     * Recognizes a table by its SEPARATOR row (`|---|:--:|--|` or similar,
     * checked as the second row of a run of `|`-containing lines) — the
     * standard Markdown signal a table exists rather than a sentence that
     * happens to contain a pipe character.
     */
    private static function table_to_lines($text) {
        $lines = explode("\n", $text);
        $out = [];
        $i = 0;
        $n = count($lines);
        $is_row = function ($line) { return strpos($line, '|') !== false; };
        $is_separator = function ($line) {
            return (bool) preg_match('/^\s*\|?(\s*:?-{2,}:?\s*\|)+\s*:?-{2,}:?\s*\|?\s*$/u', $line);
        };

        while ($i < $n) {
            $line = $lines[$i];
            if ($is_row($line) && $i + 1 < $n && $is_separator($lines[$i + 1])) {
                // Header row + separator row confirmed -> this is a real table.
                // Emit the header as one dash-joined line, drop the separator,
                // then convert every subsequent row until the table ends.
                $out[] = self::table_row_to_line($line);
                $i += 2; // skip header + separator
                while ($i < $n && $is_row($lines[$i])) {
                    $converted = self::table_row_to_line($lines[$i]);
                    if ($converted !== '') $out[] = $converted;
                    $i++;
                }
                continue;
            }
            $out[] = $line;
            $i++;
        }
        return implode("\n", $out);
    }

    /** "| A | B | C |" -> "A - B - C". Empty cells (common in the outer edge
     *  columns of a `|`-delimited row) are dropped rather than kept as blanks. */
    private static function table_row_to_line($row) {
        $cells = explode('|', trim($row));
        $cells = array_map('trim', $cells);
        $cells = array_values(array_filter($cells, function ($c) { return $c !== ''; }));
        return implode(' - ', $cells);
    }

    /** Short timeout + a capped number of key attempts per request — keeps a
     *  PHP-FPM worker from being held for more than MAX_KEY_ATTEMPTS x
     *  TIMEOUT_SECONDS seconds (previously 30s x N keys with no cap, which
     *  could hold a worker for a long time if an admin configured many keys
     *  and all of them were slow/failing). */
    const TIMEOUT_SECONDS = 20;
    const MAX_KEY_ATTEMPTS = 2;
}
