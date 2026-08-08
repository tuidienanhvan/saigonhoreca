<?php
/**
 * Retriever — text normalization: diacritic folding, keyword extraction,
 * word-boundary matching, excerpt windowing. Split out of class-retriever.php
 * (2026-07-29, file had grown past 1000 lines) — pure code motion, no logic
 * changed; see class-retriever.php for the class this trait belongs to.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

trait Pi_Chatbot_Retriever_Tokenizer {

    /**
     * Strips Vietnamese tone marks and đ/Đ, lowercasing as it goes.
     *
     * An explicit character map rather than iconv()/Normalizer: iconv's
     * //TRANSLIT output depends on the server's locale and libc build (it can
     * emit "d" or "?" or drop the character entirely for đ), and intl is not
     * guaranteed on cPanel shared hosting. A hardcoded table gives the same
     * answer on every host, which matters because retrieval results would
     * otherwise differ between the dev machine and production.
     */
    public static function fold_diacritics($text) {
        static $map = null;
        if ($map === null) {
            $map = [
                'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a',
                'â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a',
                'ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
                'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e',
                'ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
                'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
                'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o',
                'ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o',
                'ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
                'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u',
                'ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
                'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y',
                'đ'=>'d',
            ];
        }
        return strtr(mb_strtolower((string) $text), $map);
    }

    /** Single place the table name is resolved for search paths — keeps the new
     *  fallback from drifting away from the rest of the file if that ever changes. */
    private static function table_name_for_search() {
        return Pi_Chatbot_Indexer::table_name();
    }

    /**
     * True when the question contains no Vietnamese tone marks — the signal that
     * the customer typed without diacritics and a folded match is worth trying.
     *
     * This gate is what makes Tier 3 safe, and it was added because of a measured
     * regression, not as a precaution: without it, "cho hỏi giờ mở cửa" ("what
     * time do you open") folded to match "1 Cửa" ("1 door") in a display-fridge
     * title, and "bạn tên gì" ("what's your name") pulled in four products.
     * Both cost the small-talk group 0.100 on the golden set.
     *
     * The reasoning is simple: those questions were typed WITH full diacritics,
     * so there is no transliteration problem to solve. Folding them away only
     * destroys the information that was keeping short Vietnamese function words
     * ("cửa", "mở", "tên") from colliding with real product vocabulary. A
     * customer who typed accents correctly and matched nothing genuinely has no
     * answer in the catalog, and saying so is the right response.
     */
    private static function looks_unaccented($question) {
        return !preg_match(
            '/[àáạảãăằắẳẵặâầấẩẫậèéẹẻẽêềếểễệđìíịỉĩòóọỏõôồốổỗộơờớởỡợùúụủũưừứửữựỳýỵỷỹ]/iu',
            (string) $question
        );
    }

    /**
     * Does $word occur in $haystack?
     *
     * Substring matching for normal words — deliberately kept, because Vietnamese
     * compounds and the catalog's inconsistent spacing mean a strict word-boundary
     * rule LOSES real matches. That was measured, not assumed: making boundary
     * matching global cost 0.062 hit@1 on the short-noun group and fixed nothing,
     * so it was reverted.
     *
     * Purely NUMERIC tokens are the one justified exception, and only because
     * substring matching is actively wrong for them: "2" appears inside "RT-126MA-S",
     * "1260", "FGTC60-45" and most other model codes in this catalog, so as a
     * substring it matches nearly everything and carries no information. As a whole
     * word it means exactly what the customer said — 2 doors, 2 burners.
     *
     * The haystack is normalized the same way extract_keyword_list() normalizes the
     * question (every non-alphanumeric run becomes a space), so "2 Cửa", "2-cửa" and
     * "(2 cửa)" all match, while "126MA" does not.
     */
    private static function word_matches($haystack_l, $word_l) {
        if (!ctype_digit($word_l)) {
            return mb_strpos($haystack_l, $word_l) !== false;
        }
        $normalized = ' ' . preg_replace('/[^\p{L}\p{N}]+/u', ' ', $haystack_l) . ' ';
        return mb_strpos($normalized, ' ' . $word_l . ' ') !== false;
    }

    /**
     * SQL counterpart of word_matches(): the LIKE pattern for one lowercased
     * keyword. Numeric tokens get space-padded so they behave as whole words
     * ('%2%' would match every model code containing a 2 — see word_matches()
     * for why that is wrong). Slightly narrower than the PHP check on purpose:
     * SQL is the recall filter, PHP is the authoritative precision filter, so
     * SQL erring narrow on a token PHP will re-check is safe, while erring wide
     * is what caused the bug.
     */
    private static function like_pattern($word_l) {
        global $wpdb;
        return ctype_digit($word_l)
            ? '% ' . $wpdb->esc_like($word_l) . ' %'
            : '%' . $wpdb->esc_like($word_l) . '%';
    }

    /**
     * Strips Vietnamese stopwords, returns a RAW keyword LIST (not yet suffixed
     * with +/* — search_fulltext() builds both the AND/OR versions itself from
     * this list). Splits words along the same boundaries MySQL's default
     * FULLTEXT parser uses (every non-alphanumeric character is a separator —
     * including hyphens), so a product code like "FT-126MA-S" matches the
     * exact tokens that were indexed ("ft", "126ma", "s") instead of being
     * glued into one wrong word.
     *
     * Filters out tokens < 3 characters: MySQL (default
     * innodb_ft_min_token_size = 3) doesn't index tokens shorter than this
     * threshold — if "ft" (2 characters) were still used as a mandatory
     * condition, that token could never match anything and the whole AND
     * clause would always return empty, even though the remaining keywords
     * (e.g. "126ma") actually match a real product.
     */
    private static function extract_keyword_list($question) {
        $normalized = preg_replace('/[^\p{L}\p{N}]+/u', ' ', mb_strtolower($question));
        $words = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        $long = []; $short = [];
        foreach ($words as $word) {
            if (in_array($word, self::$stopwords, true)) continue;
            // Single characters are noise EXCEPT bare digits. In this catalog a
            // lone number is very often the whole point of the question —
            // "bàn mát 2 cửa", "bếp gas 4 họng", "tủ 6 cánh" — and dropping it
            // made every variant of a family score identically, so which one
            // came first was effectively arbitrary. Digits are matched on word
            // boundaries (see word_matches()), never as substrings, or "2"
            // would hit every model code containing a 2 ("RT-126MA-S").
            if (mb_strlen($word) < 2 && !ctype_digit($word)) continue;
            if (mb_strlen($word) < self::MIN_TOKEN_LENGTH) {
                $short[] = $word;
            } else {
                $long[] = $word;
            }
        }
        return [$long, $short];
    }

    /**
     * Extracts the excerpt AROUND the position of the first keyword match
     * found, instead of always taking the first $len characters of
     * content_text (bug found during audit: the passage that actually
     * addresses the question — specs, power rating, price — is often deep in
     * the body, while the start of the string is usually just a generic
     * intro/marketing blurb; the LLM would receive the useless part, and the
     * product would rank correctly but still answer weakly or say "no
     * information available"). When there are no $words (e.g. the brand
     * fallback with no keyword filtering), keeps the old behavior — take the
     * start of the string.
     */
    /**
     * Sentence-level Document Compression (Hello-Agents / RAGAs standard).
     * Splits text into individual sentences, scores each sentence by keyword overlap,
     * and joins the top-scoring sentences with ellipsis markers for truncated text.
     */
    private static function make_excerpt($text, $len, $words = []) {
        $text = trim(preg_replace('/\s+/', ' ', $text));
        if (mb_strlen($text) <= $len) return $text;

        $match_pos = null;
        if ($words) {
            $lower = mb_strtolower($text);
            foreach ($words as $w) {
                $pos = mb_strpos($lower, mb_strtolower($w));
                if ($pos !== false && ($match_pos === null || $pos < $match_pos)) {
                    $match_pos = $pos;
                }
            }
        }

        if ($match_pos === null) {
            return mb_substr($text, 0, $len) . '…';
        }

        $before = (int) floor($len * 0.25);
        $start = max(0, $match_pos - $before);
        $snippet = mb_substr($text, $start, $len);
        $prefix = $start > 0 ? '…' : '';
        $suffix = ($start + $len) < mb_strlen($text) ? '…' : '';
        return $prefix . trim($snippet) . $suffix;
    }
}
