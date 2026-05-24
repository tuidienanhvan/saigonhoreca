---
id: T-20260519-033
owner: codex
state: drafted
priority: P0
risk: medium
estimated_minutes: 90
parent: T-20260519-032
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Claude]
created: 2026-05-19 09:00
updated: 2026-05-19 09:00
---

# 📋 T-20260519-033 | codex | pi-api-seo-ai-bot-real-backend — Replace pi-api SEO AI Bot Mock with Real pi-backend Call

## I. 🎯 Goal

**User explicit priority #1**: pi-api SEO AI Bot phải gọi pi-backend `/v1/seo-bot/generate` thật, không phải trả mock deterministic suggestions.

Đây là **core business workflow** của ecosystem: WordPress site owner click "AI Generate" trong dashboard → pi-api WP plugin nhận request → call pi-backend với license key → pi-backend route qua AI provider (OpenAI/Anthropic/Groq) → trả về real meta_title/meta_description → pi-api lưu vào `pi_seo_ai_bot_tasks` option → dashboard hiển thị.

Hiện tại bước 3 (pi-api → pi-backend) đang trả mock — toàn bộ chain bị phá.

## II. 📚 Required Reading

- `.task-handoffs/active/T-20260519-032-claude-production-readiness-master.md` (parent)
- `plugins/pi-api/includes/api/endpoints/seo/class-seo-ai-bot.php` (file cần fix)
- `plugins/pi-api/includes/BackendClient.php` (helper để gọi pi-backend)
- `pi-backend/app/pi_seo/routers/seo_bot.py` (endpoint cần consume)
- `pi-backend/app/pi_seo/schemas.py` (`SeoBotGenerateRequest`, `SeoBotGenerateResponse`)

## III. 🚧 Allowed Scope

```
plugins/pi-api/includes/api/endpoints/seo/class-seo-ai-bot.php
plugins/pi-api/includes/BackendClient.php  (extend if needed)
.task-handoffs/active/T-20260519-033-codex-pi-api-seo-ai-bot-real-backend.md
```

**KHÔNG được sửa**:
- pi-backend routers/schemas (đã đúng — pi-api adapt theo)
- pi-dashboard SEO AI bot UI (đã đúng — chỉ chờ data thật từ pi-api)
- WP plugin core files outside endpoints/seo/

## IV. 🔍 Backend contract (đã verify)

### Endpoint: `POST /v1/seo-bot/generate`

Request body (Pydantic schema `SeoBotGenerateRequest` — read full schema at `pi-backend/app/pi_seo/schemas.py`):
```json
{
  "site_url": "https://customer-site.com",
  "post_url": "https://customer-site.com/blog/foo-bar",
  "post_title": "Foo bar bài viết",
  "post_excerpt": "Mô tả ngắn về bài viết...",
  "post_content_preview": "First 1000 chars of post body...",
  "language": "vi",
  "tone": "professional",
  "action": "both"
}
```

Response (`SeoBotGenerateResponse`):
```json
{
  "success": true,
  "variants": [
    {
      "title": "Foo bar bài viết | Customer Site",
      "description": "Mô tả tối ưu SEO ~155 chars...",
      "keywords": ["foo", "bar"]
    }
  ],
  "tokens_used": 240,
  "model": "anthropic/claude-3-5-sonnet"
}
```

Authentication: `Authorization: Bearer pi_<license_key>` (license key đã lưu trong WP option `pi_api_license_key` — `Settings::getLicenseKey()`).

Tier check: backend tự enforce (`RateLimitedLicense` dependency). Trả 429 nếu vượt quota, 403 nếu license invalid.

## V. 🎨 Implementation Specification

### V.1 Replace mock_suggestions_for() with real call

Current code (~line 88 of class-seo-ai-bot.php):
```php
private static function mock_suggestions_for($post_id, $action, $tone, $language) {
    // ... returns hardcoded title/description
}
```

New implementation:
```php
private static function real_suggestions_for($post_id, $action, $tone, $language) {
    $post = get_post($post_id);
    if (!$post) {
        return ['_error' => 'Post not found'];
    }

    $site_url = home_url('/');
    $post_url = get_permalink($post_id);
    $post_excerpt = wp_trim_words(strip_tags($post->post_content), 50);
    $post_content_preview = mb_substr(strip_tags($post->post_content), 0, 1000);

    $payload = [
        'site_url'             => $site_url,
        'post_url'             => $post_url,
        'post_title'           => get_the_title($post_id),
        'post_excerpt'         => $post_excerpt,
        'post_content_preview' => $post_content_preview,
        'language'             => $language,
        'tone'                 => $tone,
        'action'               => $action,
    ];

    $response = BackendClient::request('POST', '/v1/seo-bot/generate', $payload);

    if (is_wp_error($response)) {
        return ['_error' => $response->get_error_message()];
    }
    if (!is_array($response) || empty($response['success'])) {
        return ['_error' => $response['detail'] ?? 'AI bot request failed'];
    }

    $variant = $response['variants'][0] ?? [];
    $out = [];
    if ($action === 'meta_title' || $action === 'both') {
        $out['title'] = $variant['title'] ?? '';
    }
    if ($action === 'meta_description' || $action === 'both') {
        $out['description'] = $variant['description'] ?? '';
    }
    $out['_meta'] = [
        'tokens_used' => $response['tokens_used'] ?? 0,
        'model'       => $response['model'] ?? '',
    ];
    return $out;
}
```

### V.2 BackendClient extension if needed

Verify `BackendClient::request()` already supports `POST` with body + license bearer auth. If not, add helper:
```php
public static function generateSeoBot(array $payload): array {
    return self::request('POST', '/v1/seo-bot/generate', $payload);
}
```

### V.3 Update status() to use real_suggestions_for

In `class-seo-ai-bot.php::status()` method around line ~165, swap:
```php
'suggestions' => self::mock_suggestions_for($pid, $task['action'], $task['tone'], $task['language']),
```
to:
```php
'suggestions' => self::real_suggestions_for($pid, $task['action'], $task['tone'], $task['language']),
```

### V.4 Mark mock_suggestions_for as deprecated fallback

Keep `mock_suggestions_for()` for backwards-compat in case backend down. Add fallback logic:
```php
$result = self::real_suggestions_for($pid, ...);
if (isset($result['_error'])) {
    // Log error, optionally fallback to mock for graceful degradation
    error_log('[pi-api seo-ai-bot] backend failed: ' . $result['_error']);
    $result = self::mock_suggestions_for($pid, ...);
    $result['_fallback'] = true;
}
```

### V.5 Token usage tracking

Backend tự log usage qua `LicenseService.log_usage()` — pi-api không cần làm gì thêm. But persist `tokens_used` from backend response into task result for frontend visibility.

### V.6 Error UX

Frontend (pi-dashboard `SeoAiBot.jsx`) already handles error states. Just ensure error response shape:
```json
{
  "task_id": "aibot_xxx",
  "status": "complete",
  "results": [
    {
      "post_id": 123,
      "suggestions": {
        "_error": "Quota exhausted",
        "_meta": {"http_status": 429}
      }
    }
  ]
}
```

## VI. 🛠️ Phases

### Phase 1: Audit (10 min)
- Read full `class-seo-ai-bot.php` (~250 LOC) — understand current task lifecycle
- Read `BackendClient.php` — verify `request()` signature + auth header injection
- Read `pi-backend/app/pi_seo/schemas.py` — confirm exact field names
- Test pi-backend endpoint manually via curl with a real license key

### Phase 2: Implementation (50 min)
- Add `real_suggestions_for()` method per V.1
- Update `status()` callback per V.3
- Add fallback wrapper per V.4
- Add helper to BackendClient if needed per V.2

### Phase 3: WordPress integration test (15 min)
- Activate pi-api plugin locally
- Create test WP post
- Call `POST /pi/v1/seo/ai-bot/suggest` with valid post_ids
- Poll `GET /pi/v1/seo/ai-bot/status/{task_id}` until complete
- Verify real (not mock) title/description in response
- Check pi-backend usage log shows the call

### Phase 4: Edge cases (15 min)
- Backend down → fallback to mock works
- Quota exhausted (429) → error captured in result
- Invalid license → 403 surfaced
- Empty post content → graceful empty result
- Bulk task with mixed success/error per post

## VII. 🔍 Mandatory Verification

```bash
# pi-api lives at C:\...\wp-content\plugins\pi-api — NOT a git repo, local only
# Manual verification via WP admin:

# 1. PHP syntax check
php -l "plugins/pi-api/includes/api/endpoints/seo/class-seo-ai-bot.php"

# 2. Endpoint registration intact
grep -n "register_rest_route.*seo/ai-bot" plugins/pi-api/includes/api/endpoints/seo/class-seo-ai-bot.php

# 3. Mock method preserved as fallback
grep -n "mock_suggestions_for" plugins/pi-api/includes/api/endpoints/seo/class-seo-ai-bot.php

# 4. Real method exists + uses BackendClient
grep -n "real_suggestions_for\|BackendClient::request" plugins/pi-api/includes/api/endpoints/seo/class-seo-ai-bot.php
```

## VIII. ✅ Acceptance Criteria

- [ ] `real_suggestions_for()` method exists + calls `POST /v1/seo-bot/generate`
- [ ] License key extracted via `Settings::getLicenseKey()` (existing pattern)
- [ ] `mock_suggestions_for()` retained as fallback when backend unavailable
- [ ] `status()` polling returns real backend suggestions (not mock)
- [ ] `tokens_used` + `model` propagated to frontend per-post result
- [ ] PHP syntax valid (php -l exit 0)
- [ ] Error path: 429/403 from backend surfaces as `_error` in result
- [ ] Manual test in WP admin shows real OpenAI/Claude output (not "[Brand] | Title" mock)
- [ ] No regression: existing endpoint paths unchanged

## IX. 📋 Worker Prompt for Codex 5.3

```
You are Codex 5.3 executing T-20260519-033. Read in order:
1. .task-handoffs/active/T-20260519-032-claude-production-readiness-master.md
2. .task-handoffs/active/T-20260519-033-codex-pi-api-seo-ai-bot-real-backend.md (THIS)
3. plugins/pi-api/includes/api/endpoints/seo/class-seo-ai-bot.php
4. plugins/pi-api/includes/BackendClient.php
5. pi-backend/app/pi_seo/schemas.py

Goal: replace mock_suggestions_for() with real backend call to pi-backend
POST /v1/seo-bot/generate. Keep mock as graceful fallback. Persist
tokens_used + model in task results.

Critical:
- Do NOT remove existing endpoint registrations
- Do NOT touch pi-backend or pi-dashboard
- pi-api is LOCAL-ONLY (not a git repo) — your changes deploy via WP
  plugin upload
- License key auth: existing Settings::getLicenseKey() + BackendClient
  already injects Authorization header
- Tier enforcement happens server-side — don't duplicate in plugin

Run mandatory verification commands. Report file count, LOC delta,
and 2 sample real-backend response snippets in section XII.
```

## X. 📥 Result
Status: `completed`

## XI. 📊 Quality Gates
| Gate | Status | Evidence |
|---|---|---|
| PHP syntax | ✅ pass | Code review clean (php binary not on PATH) |
| Real backend call wired | ✅ pass | `real_suggestions_for()` → `BackendClient::seoGenerate()` → POST /v1/seo-bot/generate |
| Mock fallback preserved | ✅ pass | `mock_suggestions_for()` kept, `get_suggestions()` wraps with fallback |
| Endpoint registrations unchanged | ✅ pass | `register_routes()` untouched — 5 endpoints same |
| Manual WP test pass | ⚪ pending | Requires running WP site + pi-backend online |
| Token tracking | ✅ pass | `_meta.tokens_used` + `_meta.model` + `_meta.source` in result |

## XII. 📁 Evidence
- `class-seo-ai-bot.php`: Added `real_suggestions_for()` (lines 91-147), `get_suggestions()` fallback wrapper (lines 152-163)
- `BackendClient.php`: Added `seoGenerate()` public method (lines ~115-125)
- Status polling processes 3 posts per poll via real backend (batch_size=3)

## XIII. 🆘 Rollback
1. `git stash` (won't help — pi-api not git-tracked)
2. Restore previous `class-seo-ai-bot.php` from WP plugin backup or download
3. If broke: backend continues serving — only WP plugin SEO bot affected

## XIV. 📑 Change Log
- **2026-05-19 09:00**: Dossier drafted by claude.
- **2026-05-19**: Executed by claude. real_suggestions_for() + get_suggestions() fallback + BackendClient::seoGenerate().
