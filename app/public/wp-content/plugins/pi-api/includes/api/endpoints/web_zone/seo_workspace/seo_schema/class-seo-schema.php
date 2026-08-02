<?php
/**
 * Pi API SEO Schema Endpoints
 * Manages JSON-LD schema rules stored in the `sgh_seo_schema_rules` option.
 *
 * Routes:
 *   GET    /pi/v1/seo/schema/rules
 *   POST   /pi/v1/seo/schema/rules
 *   DELETE /pi/v1/seo/schema/rules/{id}
 *   POST   /pi/v1/seo/schema/import
 *   GET    /pi/v1/seo/schema/preview?post_id=123
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_SeoSchema {

    const OPTION_KEY = 'sgh_seo_schema_rules';

    /** Schema types supported by the dynamic form. */
    const ALLOWED_TYPES = [
        'Organization',
        'LocalBusiness',
        'Article',
        'Product',
        'Event',
        'FAQ',
        'HowTo',
        'BreadcrumbList',
    ];

    /** Scope values. */
    const ALLOWED_SCOPES = ['site', 'post_type', 'single'];

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/seo/schema/rules', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_rules'],   'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'upsert_rule'],  'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/seo/schema/rules/(?P<id>[a-zA-Z0-9_\-]+)', [
            ['methods' => 'DELETE', 'callback' => [self::class, 'delete_rule'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/seo/schema/import', [
            ['methods' => 'POST', 'callback' => [self::class, 'import_jsonld'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/seo/schema/preview', [
            ['methods' => 'GET', 'callback' => [self::class, 'preview_for_post'], 'permission_callback' => $admin],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // LIST
    // ──────────────────────────────────────────────────────────────────────
    public static function list_rules(\WP_REST_Request $request) {
        $rules = self::get_rules();
        return new \WP_REST_Response([
            'rules' => array_values($rules),
            'total' => count($rules),
            'types' => self::ALLOWED_TYPES,
            'scopes' => self::ALLOWED_SCOPES,
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // UPSERT
    // ──────────────────────────────────────────────────────────────────────
    public static function upsert_rule(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Body phải là JSON object'], 400);
        }

        $type = isset($body['type']) ? (string) $body['type'] : '';
        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Loại schema không hợp lệ. Cho phép: ' . implode(', ', self::ALLOWED_TYPES),
            ], 400);
        }

        $scope = isset($body['scope']) ? (string) $body['scope'] : 'site';
        if (!in_array($scope, self::ALLOWED_SCOPES, true)) {
            $scope = 'site';
        }

        $id = !empty($body['id']) ? sanitize_key((string) $body['id']) : ('rule_' . uniqid());

        $rule = [
            'id'         => $id,
            'type'       => $type,
            'scope'      => $scope,
            'conditions' => self::sanitize_conditions($body['conditions'] ?? []),
            'data'       => self::sanitize_data($body['data'] ?? [], $type),
            'enabled'    => array_key_exists('enabled', $body) ? (bool) $body['enabled'] : true,
            'label'      => isset($body['label']) ? sanitize_text_field((string) $body['label']) : '',
            'updated_at' => current_time('mysql'),
        ];

        // Build JSON-LD once, store alongside.
        $rule['json_ld'] = self::build_json_ld($rule);

        $rules = self::get_rules();
        $rules[$id] = $rule;
        update_option(self::OPTION_KEY, $rules, false);

        return new \WP_REST_Response(['success' => true, 'rule' => $rule], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // DELETE
    // ──────────────────────────────────────────────────────────────────────
    public static function delete_rule(\WP_REST_Request $request) {
        $id = sanitize_key((string) $request->get_param('id'));
        if ($id === '') {
            return new \WP_REST_Response(['success' => false, 'message' => 'ID không hợp lệ'], 400);
        }
        $rules = self::get_rules();
        if (!isset($rules[$id])) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không tìm thấy rule'], 404);
        }
        unset($rules[$id]);
        update_option(self::OPTION_KEY, $rules, false);
        return new \WP_REST_Response(['success' => true, 'deleted' => $id], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // IMPORT JSON-LD
    // ──────────────────────────────────────────────────────────────────────
    public static function import_jsonld(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        $raw = isset($body['json_ld']) ? trim((string) $body['json_ld']) : '';
        if ($raw === '') {
            return new \WP_REST_Response(['success' => false, 'message' => 'Cần truyền json_ld'], 400);
        }

        // Strip wrapping <script> tags if pasted from HTML source.
        $raw = preg_replace('#<script[^>]*>|</script>#i', '', $raw);
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'JSON-LD không parse được'], 400);
        }

        $imported = [];
        $rules = self::get_rules();

        // Accept either a single schema object OR a @graph array.
        $items = [];
        if (isset($decoded['@graph']) && is_array($decoded['@graph'])) {
            $items = $decoded['@graph'];
        } elseif (isset($decoded[0]) && is_array($decoded[0])) {
            $items = $decoded;
        } else {
            $items = [$decoded];
        }

        foreach ($items as $item) {
            if (!is_array($item)) continue;
            $type = $item['@type'] ?? '';
            if (is_array($type)) $type = reset($type);
            if (!in_array($type, self::ALLOWED_TYPES, true)) continue;

            $id = 'import_' . substr(md5(wp_json_encode($item)), 0, 10);
            $rule = [
                'id'         => $id,
                'type'       => $type,
                'scope'      => 'site',
                'conditions' => [],
                'data'       => self::extract_data_from_schema($item, $type),
                'enabled'    => true,
                'label'      => 'Nhập từ JSON-LD',
                'updated_at' => current_time('mysql'),
            ];
            $rule['json_ld'] = self::build_json_ld($rule);
            $rules[$id] = $rule;
            $imported[] = $rule;
        }

        if (!$imported) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Không có schema hợp lệ (cần @type thuộc danh sách: ' . implode(', ', self::ALLOWED_TYPES) . ')',
            ], 400);
        }

        update_option(self::OPTION_KEY, $rules, false);
        return new \WP_REST_Response(['success' => true, 'imported' => $imported, 'count' => count($imported)], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // PREVIEW
    // ──────────────────────────────────────────────────────────────────────
    public static function preview_for_post(\WP_REST_Request $request) {
        $post_id = (int) $request->get_param('post_id');
        $post = null;

        if ($post_id > 0) {
            $post = get_post($post_id);
        }

        // Prefer plugin's schema builder if available.
        if (class_exists('\\Pi_Seo_Schema') && method_exists('\\Pi_Seo_Schema', 'build_for_post') && $post) {
            try {
                $built = \Pi_Seo_Schema::build_for_post($post_id);
                if (is_array($built)) {
                    return new \WP_REST_Response([
                        'post_id' => $post_id,
                        'schemas' => $built,
                        'source'  => 'Pi_Seo_Schema::build_for_post',
                    ], 200);
                }
            } catch (\Throwable $e) {
                // fall through to local rendering
            }
        }

        // Fallback: render all matching rules for this post.
        $rules = self::get_rules();
        $output = [];
        foreach ($rules as $rule) {
            if (empty($rule['enabled'])) continue;
            if (!self::rule_applies_to_post($rule, $post)) continue;
            $output[] = self::resolve_placeholders($rule['json_ld'] ?? [], $post);
        }

        return new \WP_REST_Response([
            'post_id' => $post_id,
            'schemas' => $output,
            'source'  => 'local_rules',
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────

    protected static function get_rules(): array {
        $rules = get_option(self::OPTION_KEY, []);
        if (!is_array($rules)) return [];
        $out = [];
        foreach ($rules as $key => $r) {
            if (!is_array($r)) continue;
            $id = $r['id'] ?? (is_string($key) ? $key : null);
            if (!$id) continue;
            $r['id'] = $id;
            $out[$id] = $r;
        }
        return $out;
    }

    protected static function sanitize_conditions($input): array {
        if (!is_array($input)) return [];
        $clean = [];
        foreach ($input as $k => $v) {
            $k = sanitize_key((string) $k);
            if ($k === '') continue;
            if (is_array($v)) {
                $clean[$k] = array_map('sanitize_text_field', array_map('strval', $v));
            } else {
                $clean[$k] = sanitize_text_field((string) $v);
            }
        }
        return $clean;
    }

    protected static function sanitize_data($input, string $type): array {
        if (!is_array($input)) return [];
        $clean = [];
        foreach ($input as $k => $v) {
            $k = sanitize_key((string) $k);
            if ($k === '') continue;
            if (is_array($v)) {
                $clean[$k] = array_values(array_filter(array_map(function ($item) {
                    if (is_array($item)) return array_map('sanitize_text_field', array_map('strval', $item));
                    return sanitize_text_field((string) $item);
                }, $v)));
            } else {
                // Preserve URLs but strip tags
                $clean[$k] = wp_kses_post((string) $v);
            }
        }
        return $clean;
    }

    /** Convert stored rule `data` into a proper schema.org JSON-LD array. */
    protected static function build_json_ld(array $rule): array {
        $type = $rule['type'] ?? 'Organization';
        $data = $rule['data'] ?? [];

        $base = [
            '@context' => 'https://schema.org',
            '@type'    => $type,
        ];

        switch ($type) {
            case 'Organization':
            case 'LocalBusiness':
                return array_merge($base, array_filter([
                    'name'          => $data['name']          ?? '',
                    'url'           => $data['url']           ?? '',
                    'logo'          => $data['logo']          ?? '',
                    'description'   => $data['description']   ?? '',
                    'telephone'     => $data['telephone']     ?? '',
                    'email'         => $data['email']         ?? '',
                    'address'       => $data['address']       ?? '',
                    'sameAs'        => isset($data['sameAs']) && is_array($data['sameAs']) ? array_values(array_filter($data['sameAs'])) : [],
                    'priceRange'    => $data['priceRange']    ?? '',
                    'openingHours'  => $data['openingHours']  ?? '',
                ], fn($v) => $v !== '' && $v !== []));

            case 'Article':
                return array_merge($base, array_filter([
                    'headline'      => $data['headline']      ?? '%%post_title%%',
                    'description'   => $data['description']   ?? '%%post_excerpt%%',
                    'image'         => $data['image']         ?? '%%featured_image%%',
                    'author'        => isset($data['author'])  ? ['@type' => 'Person', 'name' => (string) $data['author']] : null,
                    'datePublished' => $data['datePublished'] ?? '%%post_date%%',
                    'dateModified'  => $data['dateModified']  ?? '%%post_modified%%',
                    'mainEntityOfPage' => $data['mainEntityOfPage'] ?? '%%post_url%%',
                    'publisher'     => isset($data['publisher']) ? ['@type' => 'Organization', 'name' => (string) $data['publisher']] : null,
                ], fn($v) => $v !== '' && $v !== null));

            case 'Product':
                return array_merge($base, array_filter([
                    'name'          => $data['name']          ?? '',
                    'image'         => $data['image']         ?? '',
                    'description'   => $data['description']   ?? '',
                    'brand'         => isset($data['brand']) ? ['@type' => 'Brand', 'name' => (string) $data['brand']] : null,
                    'sku'           => $data['sku']           ?? '',
                    'offers'        => (isset($data['price']) || isset($data['priceCurrency'])) ? [
                        '@type'         => 'Offer',
                        'price'         => $data['price']         ?? '',
                        'priceCurrency' => $data['priceCurrency'] ?? 'VND',
                        'availability'  => $data['availability']  ?? 'https://schema.org/InStock',
                        'url'           => $data['offerUrl']       ?? '',
                    ] : null,
                ], fn($v) => $v !== '' && $v !== null));

            case 'Event':
                return array_merge($base, array_filter([
                    'name'         => $data['name']         ?? '',
                    'startDate'    => $data['startDate']    ?? '',
                    'endDate'      => $data['endDate']      ?? '',
                    'location'     => isset($data['location']) ? ['@type' => 'Place', 'name' => (string) $data['location']] : null,
                    'image'        => $data['image']        ?? '',
                    'description'  => $data['description']  ?? '',
                    'organizer'    => isset($data['organizer']) ? ['@type' => 'Organization', 'name' => (string) $data['organizer']] : null,
                    'eventStatus'  => $data['eventStatus']  ?? 'https://schema.org/EventScheduled',
                ], fn($v) => $v !== '' && $v !== null));

            case 'FAQ':
                $base['@type'] = 'FAQPage';
                $qas = isset($data['qa']) && is_array($data['qa']) ? $data['qa'] : [];
                $mainEntity = [];
                foreach ($qas as $qa) {
                    if (!is_array($qa)) continue;
                    $q = trim((string) ($qa['question'] ?? ''));
                    $a = trim((string) ($qa['answer'] ?? ''));
                    if ($q === '' || $a === '') continue;
                    $mainEntity[] = [
                        '@type'          => 'Question',
                        'name'           => $q,
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a],
                    ];
                }
                if ($mainEntity) $base['mainEntity'] = $mainEntity;
                return $base;

            case 'HowTo':
                $steps = isset($data['steps']) && is_array($data['steps']) ? $data['steps'] : [];
                $stepList = [];
                foreach ($steps as $i => $step) {
                    if (!is_array($step)) continue;
                    $text = trim((string) ($step['text'] ?? ''));
                    if ($text === '') continue;
                    $stepList[] = [
                        '@type' => 'HowToStep',
                        'position' => $i + 1,
                        'name'  => $step['name'] ?? ('Bước ' . ($i + 1)),
                        'text'  => $text,
                    ];
                }
                return array_merge($base, array_filter([
                    'name'         => $data['name']        ?? '',
                    'description'  => $data['description'] ?? '',
                    'totalTime'    => $data['totalTime']   ?? '',
                    'step'         => $stepList,
                ], fn($v) => $v !== '' && $v !== []));

            case 'BreadcrumbList':
                $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];
                $listItem = [];
                foreach ($items as $i => $item) {
                    if (!is_array($item)) continue;
                    $listItem[] = [
                        '@type'    => 'ListItem',
                        'position' => $i + 1,
                        'name'     => $item['name'] ?? '',
                        'item'     => $item['url']  ?? '',
                    ];
                }
                $base['itemListElement'] = $listItem;
                return $base;
        }

        return $base;
    }

    /** Reverse-engineer data fields from an imported JSON-LD node. */
    protected static function extract_data_from_schema(array $node, string $type): array {
        $out = [];
        switch ($type) {
            case 'Organization':
            case 'LocalBusiness':
                foreach (['name', 'url', 'description', 'telephone', 'email', 'priceRange'] as $k) {
                    if (isset($node[$k])) $out[$k] = (string) $node[$k];
                }
                if (isset($node['logo'])) {
                    $out['logo'] = is_array($node['logo']) ? ($node['logo']['url'] ?? '') : (string) $node['logo'];
                }
                if (isset($node['address'])) {
                    $out['address'] = is_array($node['address']) ? wp_json_encode($node['address']) : (string) $node['address'];
                }
                if (isset($node['sameAs']) && is_array($node['sameAs'])) {
                    $out['sameAs'] = array_values(array_map('strval', $node['sameAs']));
                }
                break;
            case 'Article':
                foreach (['headline', 'description', 'datePublished', 'dateModified', 'mainEntityOfPage'] as $k) {
                    if (isset($node[$k])) $out[$k] = (string) $node[$k];
                }
                if (isset($node['author']['name'])) $out['author'] = (string) $node['author']['name'];
                if (isset($node['publisher']['name'])) $out['publisher'] = (string) $node['publisher']['name'];
                if (isset($node['image'])) $out['image'] = is_array($node['image']) ? ($node['image']['url'] ?? '') : (string) $node['image'];
                break;
            case 'Product':
                foreach (['name', 'description', 'sku', 'image'] as $k) {
                    if (isset($node[$k])) $out[$k] = is_array($node[$k]) ? wp_json_encode($node[$k]) : (string) $node[$k];
                }
                if (isset($node['brand']['name'])) $out['brand'] = (string) $node['brand']['name'];
                if (isset($node['offers']) && is_array($node['offers'])) {
                    $offers = $node['offers'];
                    $out['price']         = (string) ($offers['price']         ?? '');
                    $out['priceCurrency'] = (string) ($offers['priceCurrency'] ?? 'VND');
                    $out['availability']  = (string) ($offers['availability']  ?? '');
                    $out['offerUrl']      = (string) ($offers['url']           ?? '');
                }
                break;
            case 'FAQ':
                $qa = [];
                foreach (($node['mainEntity'] ?? []) as $q) {
                    if (!is_array($q)) continue;
                    $qa[] = [
                        'question' => (string) ($q['name'] ?? ''),
                        'answer'   => (string) ($q['acceptedAnswer']['text'] ?? ''),
                    ];
                }
                $out['qa'] = $qa;
                break;
        }
        return $out;
    }

    protected static function rule_applies_to_post(array $rule, $post): bool {
        $scope = $rule['scope'] ?? 'site';
        if ($scope === 'site') return true;
        if (!$post) return false;

        $cond = $rule['conditions'] ?? [];
        if ($scope === 'post_type') {
            $target = (string) ($cond['post_type'] ?? '');
            return $target === '' || $target === $post->post_type;
        }
        if ($scope === 'single') {
            $target = (int) ($cond['post_id'] ?? 0);
            return $target === 0 || $target === (int) $post->ID;
        }
        return false;
    }

    /** Replace placeholders like %%post_title%% inside the schema values. */
    protected static function resolve_placeholders($schema, $post) {
        if (!$post) return $schema;
        $replacements = [
            '%%post_title%%'     => get_the_title($post),
            '%%post_url%%'       => get_permalink($post),
            '%%post_excerpt%%'   => wp_strip_all_tags(get_the_excerpt($post)),
            '%%post_author%%'    => get_the_author_meta('display_name', $post->post_author),
            '%%post_date%%'      => mysql2date('c', $post->post_date),
            '%%post_modified%%'  => mysql2date('c', $post->post_modified),
            '%%featured_image%%' => get_the_post_thumbnail_url($post, 'full') ?: '',
            '%%site_name%%'      => get_bloginfo('name'),
            '%%site_url%%'       => home_url('/'),
        ];

        if (is_string($schema)) {
            return strtr($schema, $replacements);
        }
        if (is_array($schema)) {
            $out = [];
            foreach ($schema as $k => $v) {
                $out[$k] = self::resolve_placeholders($v, $post);
            }
            return $out;
        }
        return $schema;
    }
}
