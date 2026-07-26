<?php
/**
 * Optional external web search.
 *
 * WHY THIS IS OPT-IN AND NARROW
 *
 * The catalog is the source of truth for what this company sells. If a product
 * is not in the catalog, the honest answer is "we don't carry that" plus the
 * hotline — NOT a web result dressed up as our own stock. Confusing the two is
 * the single most damaging thing this feature could do to a sales bot, so the
 * design keeps them physically separate: web results travel in their own field
 * (never merged into $context), are labelled as external in the prompt, and are
 * only ever fetched when BOTH the site owner enabled the feature AND the
 * customer switched the toggle on for that message.
 *
 * WHY A KEY IS REQUIRED (this was measured, not assumed)
 *
 * The first build targeted DuckDuckGo precisely because it needs no key. That
 * does not work, verified against the live endpoints on 2026-07-26:
 *
 *   - html.duckduckgo.com/html/ answers HTTP 202 with an anti-bot challenge
 *     page containing zero results. Getting past that would mean impersonating
 *     a browser to defeat a bot check the operator deliberately put there,
 *     which is not something this plugin will do.
 *   - api.duckduckgo.com (the official, documented, key-free Instant Answer
 *     API) answers HTTP 200 with an EMPTY abstract and zero related topics for
 *     every query this bot would actually make — "Hoshizaki", "Josper charcoal
 *     oven", "commercial ice machine", "máy làm đá công nghiệp". It covers
 *     encyclopedic topics, not commercial or product queries.
 *
 * So a keyed provider it is. The provider is pluggable and the key is entered
 * by the site owner in wp-admin. With no key configured the feature stays
 * dormant and the toggle never appears — deliberately visible as "not set up"
 * rather than quietly returning nothing, because a search feature that silently
 * finds nothing is indistinguishable from a broken one.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_WebSearch {

    const TIMEOUT_SECONDS = 8;
    const MAX_RESULTS = 4;
    /** Snippets are context for a model, not a web page — long ones crowd out
     *  the catalog data that matters more, and cost tokens for no gain. */
    const MAX_SNIPPET_CHARS = 300;
    const CACHE_TTL = 6 * HOUR_IN_SECONDS;

    const PROVIDER_BRAVE   = 'brave';
    const PROVIDER_SERPER  = 'serper';
    const PROVIDER_SEARXNG = 'searxng';

    /** Providers that authenticate with a key. SearXNG is self-hosted and
     *  authenticates with nothing — it needs a base URL instead, which is why
     *  is_configured() cannot just test for a non-empty key. */
    const KEYED_PROVIDERS = [self::PROVIDER_BRAVE, self::PROVIDER_SERPER];

    /**
     * Circuit breaker, same pattern and reasoning as the router's: once the
     * endpoint is failing, every further request pays the full timeout before
     * failing too. Without a breaker, one provider outage turns into 8 extra
     * seconds on EVERY question with the toggle on.
     */
    const BREAKER_KEY = 'pi_chatbot_web_breaker';
    const BREAKER_THRESHOLD = 3;
    const BREAKER_COOLDOWN = 10 * MINUTE_IN_SECONDS;

    /** True when a provider and key are configured. The widget toggle and the
     *  admin status badge both read this, so "enabled but no key" is shown as
     *  what it is rather than silently behaving like "no results". */
    public static function is_configured() {
        $s = pi_chatbot_settings();
        if (empty($s['web_search_enabled'])) return false;
        if (self::provider() === self::PROVIDER_SEARXNG) {
            return self::searxng_base_url() !== '';
        }
        return trim((string) $s['web_search_key']) !== '';
    }

    /** Resolved provider — anything unrecognised falls back to Brave, matching
     *  the whitelist the admin form enforces on save. */
    public static function provider() {
        $p = (string) pi_chatbot_settings()['web_search_provider'];
        return in_array($p, [self::PROVIDER_BRAVE, self::PROVIDER_SERPER, self::PROVIDER_SEARXNG], true)
            ? $p : self::PROVIDER_BRAVE;
    }

    /**
     * Base URL of the self-hosted SearXNG instance, validated here rather than
     * trusted from the option. Only http/https survive esc_url_raw, and the
     * trailing slash is normalised so building '/search' below can't produce a
     * double slash against some instances and not others.
     */
    private static function searxng_base_url() {
        $url = trim((string) pi_chatbot_settings()['web_search_url']);
        if ($url === '') return '';
        $url = esc_url_raw($url);
        if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) return '';
        return rtrim($url, '/');
    }

    /**
     * @return array<int,array{title:string,url:string,snippet:string}> Empty on
     *         ANY failure — callers treat empty as "no web results", never as an
     *         error to surface.
     */
    public static function search($query, $limit = self::MAX_RESULTS) {
        $query = trim(wp_strip_all_tags((string) $query));
        if ($query === '') return [];
        if (!self::is_configured()) return [];
        if (self::breaker_open()) return [];

        $settings = pi_chatbot_settings();
        $provider = self::provider();
        $key = trim((string) $settings['web_search_key']);

        // Provider is part of the cache identity: switching provider, key or
        // instance URL must not keep serving the previous one's results.
        $cache_key = 'pi_chatbot_web_' . md5(
            $provider . '|' . md5($key . '|' . self::searxng_base_url())
            . '|' . mb_strtolower($query) . '|' . (int) $limit
        );
        $cached = get_transient($cache_key);
        if (is_array($cached)) return $cached;

        if ($provider === self::PROVIDER_SEARXNG) {
            $results = self::fetch_searxng($query, $limit);
        } elseif ($provider === self::PROVIDER_SERPER) {
            $results = self::fetch_serper($query, $limit, $key);
        } else {
            $results = self::fetch_brave($query, $limit, $key);
        }

        if ($results === null) {   // transport/auth failure
            self::bump_breaker();
            return [];
        }

        self::reset_breaker();
        // An empty-but-valid response IS cached: "the web has nothing for this
        // query" is a real answer, and re-asking the provider every time would
        // burn quota to re-learn it.
        set_transient($cache_key, $results, self::CACHE_TTL);
        return $results;
    }

    /**
     * Brave Search API. Free tier ~2000 queries/month.
     * Docs: https://api.search.brave.com/app/documentation
     *
     * @return array|null null = request failed (breaker counts it);
     *                    array (possibly empty) = provider answered.
     */
    private static function fetch_brave($query, $limit, $key) {
        // country/search_lang bias the result set toward Vietnamese commercial
        // sources — an unscoped query for industry equipment terms returns
        // mostly generic English retail pages.
        $url = 'https://api.search.brave.com/res/v1/web/search?' . http_build_query([
            'q'           => $query,
            'count'       => max(1, min(20, (int) $limit)),
            'country'     => 'vn',
            'search_lang' => 'vi',
        ]);
        $response = wp_remote_get($url, [
            'timeout' => self::TIMEOUT_SECONDS,
            'headers' => [
                'Accept'               => 'application/json',
                'Accept-Encoding'      => 'gzip',
                'X-Subscription-Token' => $key,
            ],
        ]);
        if (is_wp_error($response)) return null;
        if ((int) wp_remote_retrieve_response_code($response) !== 200) return null;

        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($data)) return null;

        $out = [];
        foreach ((array) ($data['web']['results'] ?? []) as $r) {
            if (count($out) >= $limit) break;
            $row = self::normalize_row($r['title'] ?? '', $r['url'] ?? '', $r['description'] ?? '');
            if ($row) $out[] = $row;
        }
        return $out;
    }

    /**
     * Serper.dev — Google results as JSON. Free tier ~2500 queries.
     * Docs: https://serper.dev/playground
     */
    private static function fetch_serper($query, $limit, $key) {
        $response = wp_remote_post('https://google.serper.dev/search', [
            'timeout' => self::TIMEOUT_SECONDS,
            'headers' => [
                'X-API-KEY'    => $key,
                'Content-Type' => 'application/json',
            ],
            'body' => wp_json_encode([
                'q'   => $query,
                'gl'  => 'vn',
                'hl'  => 'vi',
                'num' => max(1, min(20, (int) $limit)),
            ]),
        ]);
        if (is_wp_error($response)) return null;
        if ((int) wp_remote_retrieve_response_code($response) !== 200) return null;

        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($data)) return null;

        $out = [];
        foreach ((array) ($data['organic'] ?? []) as $r) {
            if (count($out) >= $limit) break;
            $row = self::normalize_row($r['title'] ?? '', $r['link'] ?? '', $r['snippet'] ?? '');
            if ($row) $out[] = $row;
        }
        return $out;
    }

    /**
     * SearXNG — self-hosted open-source metasearch. The only option here that
     * is genuinely free with no quota and no card, because you run it yourself;
     * the cost is a container to maintain rather than a bill.
     *
     * Point this at YOUR OWN instance. Public instances are deliberately not
     * suggested anywhere in the UI: most disable the JSON format outright, and
     * pointing a production chatbot at a stranger's free server is both
     * unreliable and rude.
     *
     * The instance must have `json` in its `search.formats` setting
     * (settings.yml) — it is off by default, and without it SearXNG answers 403
     * and this returns null, which the breaker then counts as a failure.
     */
    private static function fetch_searxng($query, $limit) {
        $base = self::searxng_base_url();
        if ($base === '') return null;

        $url = $base . '/search?' . http_build_query([
            'q'        => $query,
            'format'   => 'json',
            'language' => 'vi',
            'safesearch' => 1,
        ]);
        $response = wp_remote_get($url, [
            'timeout' => self::TIMEOUT_SECONDS,
            'headers' => ['Accept' => 'application/json'],
        ]);
        if (is_wp_error($response)) return null;
        if ((int) wp_remote_retrieve_response_code($response) !== 200) return null;

        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($data)) return null;

        $out = [];
        foreach ((array) ($data['results'] ?? []) as $r) {
            if (count($out) >= $limit) break;
            $row = self::normalize_row($r['title'] ?? '', $r['url'] ?? '', $r['content'] ?? '');
            if ($row) $out[] = $row;
        }
        return $out;
    }

    /** One shape for every provider, so nothing downstream has to know which
     *  one answered. Also where all sanitising happens exactly once. */
    private static function normalize_row($title, $url, $snippet) {
        $title = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags((string) $title)));
        $url   = self::clean_url($url);
        if ($title === '' || $url === '') return null;

        $snippet = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags((string) $snippet)));
        if (mb_strlen($snippet) > self::MAX_SNIPPET_CHARS) {
            $snippet = mb_substr($snippet, 0, self::MAX_SNIPPET_CHARS) . '...';
        }
        return ['title' => $title, 'url' => $url, 'snippet' => $snippet];
    }

    /**
     * Normalise and harden a result URL. esc_url_raw() enforces an allowed
     * protocol, and the explicit http/https recheck afterwards means a
     * javascript: or data: URL from a compromised or malicious provider
     * response can never reach the prompt or the client.
     */
    private static function clean_url($href) {
        $href = (string) $href;
        if ($href === '') return '';
        if (strpos($href, '//') === 0) $href = 'https:' . $href;
        $href = esc_url_raw($href);
        return (strpos($href, 'http://') === 0 || strpos($href, 'https://') === 0) ? $href : '';
    }

    /** Renders results for the prompt. Deliberately verbose about what these
     *  are: the model must never present them as this company's own stock. */
    public static function to_prompt_block($results) {
        if (!$results) return '';
        $lines = [];
        foreach ($results as $i => $r) {
            $lines[] = sprintf('[Web %d] %s (%s)', $i + 1, $r['title'], $r['url']);
            if ($r['snippet'] !== '') $lines[] = '    ' . $r['snippet'];
        }
        return implode("\n", $lines);
    }

    private static function breaker_open() {
        return (int) get_transient(self::BREAKER_KEY) >= self::BREAKER_THRESHOLD;
    }

    private static function bump_breaker() {
        $n = (int) get_transient(self::BREAKER_KEY);
        set_transient(self::BREAKER_KEY, $n + 1, self::BREAKER_COOLDOWN);
    }

    private static function reset_breaker() {
        delete_transient(self::BREAKER_KEY);
    }
}
