---
id: T-20260517-011
owner: claude
state: archived
priority: P2
risk: low
estimated_minutes: 45
parent: T-20260517-010
children: []
depends_on: [T-20260517-010]
parallelization_ok: true
retry_count: 0
retry_max: 1
escalation_path: [Codex, Gemini]
created: 2026-05-17 20:11
updated: 2026-05-18 09:30
archived: 2026-05-18 09:30
---

## 0. 📥 User Original Intent (Phase 0 — Verbatim)

> **Split from T-20260517-010** per user "Option Tách nhỏ" — Phase C (pi-api consumer sync).
>
> Parent context: T-010 archived backend SoT + `GET /v1/tiers/spec` endpoint. Phase C-E split into 3 follow-up tasks so consumer rollouts can ship independently after PR #1 merges to Railway prod.

---

# 📋 T-20260517-011 | claude | tier-spec-pi-api-consumer

## I. Frontmatter

| Field | Value |
|---|---|
| `priority` | P2 — tier data drift in pi-api means customer-facing DOCS still wrong |
| `risk` | low — backend fallback path in place; cache transient protects against backend outage |
| `depends_on` | T-010 (PR #1 must merge → endpoint live on Railway before dispatch) |
| `estimated_minutes` | 45 |

---

## II. Goal

`plugins/pi-api/includes/Settings::getTokenQuota()` currently hardcodes `[free=>50k, pro=>1M, max=>3M, ent=>-1]` — matches canonical by coincidence today, but drift-prone. Replace with HTTP fetch to `/v1/tiers/spec` cached in WP transient (1h). Backend unreachable → fall back to the existing hardcoded map (graceful degradation).

Also fix `plugins/pi-api/DOCS.md` Subscription Tiers section — currently says 20k/2M/10M (wrong vs canonical).

---

## III. Required Reading

- `pi-backend/TIER-MATRIX.md` — canonical reference (already on main after PR #1 merges)
- `pi-backend/app/saas/tier_router.py` — endpoint contract
- `plugins/pi-api/includes/Settings.php::getTokenQuota()` (current implementation)
- `plugins/pi-api/includes/BackendClient.php` (HTTP client pattern)
- `plugins/pi-api/DOCS.md` lines ~47-67 (Subscription Tiers — needs rewrite)

---

## IV. Allowed Scope

- 📄 `plugins/pi-api/includes/Settings.php` — refactor `getTokenQuota()` to call `BackendClient::getTierSpec()` then memoize the result via `get_transient('pi_api_tier_spec', ...)`. Hardcoded map stays as fallback.
- 📄 `plugins/pi-api/includes/BackendClient.php` — add `getTierSpec(): array` method calling `GET /v1/tiers/spec` (no auth needed — public endpoint). Strip query params, raw URL only. 5s timeout.
- 📄 `plugins/pi-api/DOCS.md` — rewrite Subscription Tiers section. Numbers cite `/v1/tiers/spec` as source. Sample JSON shown.
- 📄 `plugins/pi-api/pi-api.php` — bump plugin version `1.0.1 → 1.0.2`.

NEW helper methods are OK as long as they live in `Settings` / `BackendClient`. No new files.

---

## V. Out Of Scope

- ❌ Touching `IframeRenderer.php`, `JwtAjax.php`, `CorsBridge.php`, `Heartbeat.php`, `AuthManager.php`, `ApiBootstrap.php`
- ❌ Adding feature-checking helpers (`Settings::hasFeature()`) — future task if needed
- ❌ pi-backend changes (already done in T-010)
- ❌ Dashboard / store webapp (those are T-012 / T-013)

---

## VI. Phases

1. **A — Snapshot** 5' — `cp Settings.php BackendClient.php DOCS.md pi-api.php → changes/T-011/before/`
2. **B — BackendClient::getTierSpec()** 10' — `wp_remote_get(PI_API_BACKEND_URL . '/v1/tiers/spec', ['timeout' => 5])`. Decode JSON, validate `tiers` array. Return shape: `['tiers' => [...], 'public_slugs' => [...]]` OR empty array on failure.
3. **C — Settings::getTokenQuota() refactor** 15' — check transient first. Miss → call BackendClient. Success → set transient 1h, return quota for current tier. Failure → fall back to hardcoded map (current behavior preserved).
4. **D — DOCS.md rewrite** 5' — Subscription Tiers section replaced with canonical numbers + JSON sample + endpoint citation.
5. **E — Version bump** 1' — pi-api.php header + `PI_API_VERSION` constant.
6. **F — Verify** 10' — `php -l` all 3 touched files. Simulate: on WP, `var_dump(\PiApi\Settings::getTokenQuota())` should match `/v1/tiers/spec` for installed license tier.

---

## VII. Verification Commands

```bash
PHP="/c/Users/Administrator/AppData/Roaming/Local/lightning-services/php-8.2.30+1/bin/win64/php.exe"

# Syntax
"$PHP" -l plugins/pi-api/includes/Settings.php
"$PHP" -l plugins/pi-api/includes/BackendClient.php
"$PHP" -l plugins/pi-api/pi-api.php

# Endpoint live check
curl -s https://pi-backend.up.railway.app/v1/tiers/spec | jq '.tiers[].slug'

# Manual smoke (on saigonhouse.local WP admin):
# 1. Visit Pi API License page → tier displayed
# 2. delete_transient('pi_api_tier_spec') via wp-cli or manually
# 3. Reload → confirm backend hit (Network tab) → transient repopulated
```

---

## VIII. Acceptance Criteria

- [ ] `BackendClient::getTierSpec()` returns valid spec on success, empty array on failure (no exception thrown).
- [ ] `Settings::getTokenQuota()` returns backend-fetched value when cache warm or backend reachable.
- [ ] Backend unreachable → fallback to hardcoded map (no error visible to admin).
- [ ] DOCS.md numbers match `/v1/tiers/spec` (50k / 1M / 3M / unlimited; $0 / $29 / $99 / custom).
- [ ] pi-api.php version `1.0.2`.
- [ ] PHP syntax all green.
- [ ] No drift outside §IV scope.

---

## IX. Worker Prompt

```text
You are claude. Execute T-20260517-011 per dossier. Depends on T-010 PR #1
merged to main + Railway deployed (curl /v1/tiers/spec returns 200).
DO NOT dispatch if endpoint is not live — check first.

Scope: 4 files in plugins/pi-api/. Risk=low, est 45 min. Standard archive flow.
```

---

## X. Agent Result
Status: `not-started` — drafted, waits on T-010 PR #1 merge.

## XI. Quality Gates — pending
## XII. Evidence — TBD
## XIII. Diff — TBD
## XIV. Verdict — pending
## XV. Rollback — standard snapshot restore from `changes/T-011-*/before/`
## XVI. Change log
- 2026-05-17 20:11: Drafted by Claude (split from T-010 Phase C).
- **2026-05-18 09:30**: State drafted → dispatched
- **2026-05-18 09:30**: State dispatched → returned
- **2026-05-18 09:30**: State returned → verified
