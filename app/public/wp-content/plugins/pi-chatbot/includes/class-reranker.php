<?php
/**
 * Reranker — reorders retrieved candidates by asking an LLM to judge relevance,
 * on the max-accuracy path only.
 *
 * Why this stage exists at all: measured against the 68-question golden set, the
 * lexical pipeline's misses are overwhelmingly ORDERING failures, not recall
 * failures — "bếp gas 2 họng" returned the right products at ranks 4 and 5 with
 * three near-identical siblings above them; "máy rửa ly" put the correct pair at
 * ranks 4-5. Retrieval had already found the answer; nothing in a bag-of-words
 * score could tell "2 họng" ("2 burners") apart from "4 họng" when both share
 * every other token. A model reading the titles can. That is the whole job here:
 * REORDER what retrieval found.
 *
 * Listwise (one call ranking all candidates together), not pointwise (one call
 * scoring each): pointwise would mean 12 LLM7 calls per question on a free tier,
 * and it also throws away the comparison that makes this work — judging "2 họng"
 * against "4 họng" requires seeing both at once.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Reranker {
    /** Same class of fast, non-reasoning model as the router — a reasoning model
     *  spends its whole token budget on a hidden field and returns empty content
     *  (measured with gpt-oss:20b while building the router). */
    const MODEL_DEFAULT = 'codestral-latest';
    const TIMEOUT_SECONDS = 15;
    const MAX_KEY_ATTEMPTS = 1;

    /** Candidates sent to the model. Beyond this the prompt gets long enough to
     *  hurt both latency and the model's own attention over the list, and
     *  fusion has already pushed anything plausible into the first dozen. */
    const MAX_CANDIDATES = 12;

    /** Characters of excerpt per candidate — enough to tell near-identical
     *  product variants apart (capacity, burner count, dimensions usually sit in
     *  the first line of the spec) without turning the prompt into the whole
     *  catalog. */
    const SNIPPET_LENGTH = 220;

    /**
     * How much of the top of the reranked list the model marked as genuinely
     * relevant. Used by the caller to decide whether to answer at all — see
     * class-rest.php.
     */
    const CONFIDENCE_SAMPLE = 3;

    /**
     * @param string $question Raw customer message (NOT the keyword query — the
     *   model judges against what the customer actually asked).
     * @param array  $candidates Formatted rows from Retriever::search_hybrid().
     * @param int    $top_k How many to keep.
     * @return array{results:array,used:bool,confidence:?float}
     *   used=false means the ranking is untouched fusion order (model disabled,
     *   unavailable, or its answer failed validation) — never an error, always a
     *   usable list.
     */
    public static function rerank($question, $candidates, $top_k = 6) {
        $candidates = array_values((array) $candidates);
        $fallback = [
            'results'    => array_slice($candidates, 0, max(1, (int) $top_k)),
            'used'       => false,
            'confidence' => null,
        ];
        if (count($candidates) < 2) return $fallback;

        $settings = pi_chatbot_settings();
        if (empty($settings['rerank_enabled'])) return $fallback;

        $keys = !empty($settings['router_keys']) ? $settings['router_keys'] : $settings['llm7_keys'];
        if (empty($keys)) return $fallback;

        $pool = array_slice($candidates, 0, self::MAX_CANDIDATES);
        $model = !empty($settings['rerank_model']) ? $settings['rerank_model'] : self::MODEL_DEFAULT;

        $response = Pi_Chatbot_LLM_Client::call(
            [
                'model'       => $model,
                'messages'    => self::build_messages($question, $pool),
                'temperature' => 0,
                'max_tokens'  => 300,
            ],
            $keys,
            self::TIMEOUT_SECONDS,
            self::MAX_KEY_ATTEMPTS,
            'pi-chatbot-reranker'
        );
        if (is_wp_error($response)) return $fallback;

        $parsed = Pi_Chatbot_LLM_Client::extract_json(Pi_Chatbot_LLM_Client::content($response));
        if (!is_array($parsed)) return $fallback;

        $ordered = self::apply($parsed, $pool);
        if ($ordered === null) return $fallback;

        // Anything the model dropped off the end still trails the reranked head,
        // so a model that returns a short list narrows the ORDER without
        // silently deleting candidates retrieval had found.
        $tail = array_slice($candidates, count($pool));
        $results = array_slice(array_merge($ordered['results'], $tail), 0, max(1, (int) $top_k));

        return [
            'results'    => $results,
            'used'       => true,
            'confidence' => $ordered['confidence'],
        ];
    }

    private static function build_messages($question, array $pool) {
        $lines = [];
        foreach ($pool as $i => $item) {
            $excerpt = trim(preg_replace('/\s+/u', ' ', (string) ($item['excerpt'] ?? '')));
            $lines[] = sprintf(
                "%d. %s%s%s\n   %s",
                $i + 1,
                $item['title'],
                !empty($item['brand']) ? " (Brand: {$item['brand']})" : '',
                !empty($item['category']) ? " [{$item['category']}]" : '',
                mb_substr($excerpt, 0, self::SNIPPET_LENGTH)
            );
        }

        $system = "You rank commercial kitchen equipment search results by how well each one answers a customer's question.\n"
            . "The customer may write in Vietnamese, English or another language; the product titles are in English.\n\n"
            . "Reply with ONLY this JSON, no other text:\n"
            . "{\"ranking\":[3,1,7],\"relevant\":[3,1]}\n"
            . "ranking: the candidate NUMBERS, best answer first. Include every number you consider usable; "
            . "omit ones that clearly do not answer the question.\n"
            . "relevant: the subset of those numbers that genuinely answer the question — not merely the same "
            . "category of equipment.\n\n"
            . "How to judge:\n"
            . "- Match the SPECIFIC detail the customer asked for. If they named a size, a burner count, a capacity, "
            . "a door count or a model code, a product with a DIFFERENT value for it is WRONG, however similar the rest "
            . "of the title looks. This is the single most common mistake — rank exact-spec matches above near misses.\n"
            . "- If they named a brand, that brand's products rank first.\n"
            . "- If the question describes a use case rather than a product, prefer equipment that fits that use.\n"
            . "- Use ONLY the numbered candidates below. Never invent a number that is not in the list.\n"
            . "- If none of them answer the question, reply {\"ranking\":[],\"relevant\":[]}.";

        $user = "CUSTOMER QUESTION: {$question}\n\nCANDIDATES:\n" . implode("\n", $lines);

        return [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $user],
        ];
    }

    /**
     * Turn the model's numbers back into rows.
     *
     * Reranking is allowed to REORDER and to DEMOTE, never to invent: every
     * returned number must index a candidate that retrieval actually produced.
     * A reply that is more than half hallucinated numbers is treated as a failed
     * call (return null -> caller keeps fusion order) rather than partially
     * honoured — at that point the model has demonstrated it was not reading the
     * list, so the fraction that happens to be valid is not evidence of
     * anything either.
     *
     * @return array{results:array,confidence:float}|null
     */
    private static function apply(array $parsed, array $pool) {
        $raw_ranking = isset($parsed['ranking']) && is_array($parsed['ranking']) ? $parsed['ranking'] : null;
        if ($raw_ranking === null) return null;

        $count = count($pool);
        $seen = [];
        $ranking = [];
        $invalid = 0;
        foreach ($raw_ranking as $n) {
            if (!is_numeric($n)) { $invalid++; continue; }
            $idx = (int) $n - 1; // prompt numbers the list from 1
            if ($idx < 0 || $idx >= $count) { $invalid++; continue; }
            if (isset($seen[$idx])) continue;
            $seen[$idx] = true;
            $ranking[] = $idx;
        }

        if ($raw_ranking && $invalid > count($raw_ranking) / 2) return null;

        // An explicitly empty ranking is a real verdict ("none of these answer
        // the question"), not a parse failure — honour it, and let the caller's
        // low-confidence path handle what to say. Distinguished from a
        // hallucinated reply by $invalid being 0.
        if (!$ranking && $invalid > 0) return null;

        $relevant = [];
        if (isset($parsed['relevant']) && is_array($parsed['relevant'])) {
            foreach ($parsed['relevant'] as $n) {
                if (!is_numeric($n)) continue;
                $idx = (int) $n - 1;
                if ($idx >= 0 && $idx < $count) $relevant[$idx] = true;
            }
        }

        $results = [];
        foreach ($ranking as $idx) {
            $row = $pool[$idx];
            $row['relevant'] = isset($relevant[$idx]);
            $results[] = $row;
        }

        // Confidence = share of the top few results the model itself called
        // genuinely relevant. Measured over the head of the list rather than the
        // whole of it because that is what actually reaches the answer prompt.
        $sample = array_slice($results, 0, self::CONFIDENCE_SAMPLE);
        $confidence = $sample
            ? count(array_filter($sample, function ($r) { return !empty($r['relevant']); })) / count($sample)
            : 0.0;

        return ['results' => $results, 'confidence' => (float) $confidence];
    }
}
