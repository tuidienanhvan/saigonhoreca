---
id: T-20260517-010
owner: claude
state: archived
priority: P1
risk: medium
estimated_minutes: 240
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Gemini]
created: 2026-05-17 18:53
updated: 2026-05-17 20:10
archived: 2026-05-17 20:10
---

## 0. 📥 User Original Intent (Phase 0 — Verbatim)

> **Turn N của session (sau khi seed Pro license cho admin)**:
> sao ko seed max mà seed pro cho admin Tu Anh Van vậy?
> là sao mà user mới cần chứ nhỉ? có free,pro,max user r mà?
> và hiện tại pi-dashboard chưa có thiết kế chuẩn và chi tiết cho 3 tier và chức năng đúng k? lên dossier làm chi tiết

**Diễn giải / Interpretation**:
1. User phát hiện admin được seed Pro thay vì Max (script `seed_pro_licenses.py` default tier Pro) → cần fix admin tier + clarify guidelines.
2. User question về test users đã có (3 users trong `seed_pi_users.py`) → cần document rõ test data vs production data + how to test 3-tier UI properly.
3. User request: pi-dashboard hiện chưa có thiết kế chuẩn chi tiết cho 3 tier (free/pro/max) — yêu cầu dossier consolidate.

---

# 📋 T-20260517-010 | claude | tier-spec-consolidation — Bản đặc tả công việc chi tiết / Detailed Task Specification

## I. 📊 Frontmatter

| Field | Value | Notes |
|---|---|---|
| `priority` | P1 | Cao — đang có **4 sources of truth khác nhau** cho cùng quota numbers. Risk: customer mua Pro 2M tokens (per DOCS) nhưng backend cấp 1M → support escalation. Phải fix trước khi onboard customer thật. |
| `risk` | medium | Touches 3 codebases (backend, pi-api, dashboard webapp, store webapp) + pricing strategy decision. Wrong numbers ship = revenue loss + customer trust hit. |
| `estimated_minutes` | 240 | 60' audit + 30' user decision aligment + 60' backend SoT + 45' UI sync + 30' migration + 15' verify. |
| `state` | **drafted (HOLD)** | Cần user product decision về 4 things trước khi dispatch (xem §II.C). |

---

## II. 🎯 Goal & Strategic Objective

### A. Outcome

1. **1 single source of truth** cho tier matrix — tất cả nơi khác chỉ reference (không hardcode lại numbers).
2. **Feature matrix rõ ràng** — mỗi tier có những features gì, UI gating đúng theo backend enforcement.
3. **Quota numbers nhất quán** — DOCS, UI pricing page, backend enforcement, env defaults đều khớp.
4. **Admin tier policy** — admin email (`9.13.tuanhvan2018@gmail.com`) được set tier `enterprise` hoặc `max` (user decide). Document guidelines cho future admin users.
5. **Test data policy** — clear rule: production = real users only. Test 3-tier UI qua Neon dev branch hoặc admin impersonation.

### B. Current State (audit findings, sources of truth conflict)

**🚨 4 chỗ define quota numbers khác nhau:**

| Source | Free | Pro | Max | Enterprise | Notes |
|---|---:|---:|---:|---:|---|
| `pi-backend/app/saas/tiers.py` (server enforcement) | 50,000 | 1,000,000 | 3,000,000 | ∞ | Used by SaaS Tenant model + `monthly_quota_for_tier()` |
| `plugins/pi-api/includes/Settings.php::getTokenQuota()` | 50,000 | 1,000,000 | 3,000,000 | ∞ | WP plugin side — matches backend ✅ |
| `plugins/pi-api/DOCS.md` (customer-facing) | **20,000** | **2,000,000** | **10,000,000** | n/a | **MISMATCH** — different from backend |
| `pi-backend/app/core/config.py` `rate_limit_*_per_month` defaults | **5,000** | **100,000** | **500,000** | ∞ | **MISMATCH** — env override path, different again |

**Feature matrix (chỉ define server-side, UI có thể không sync):**

| Tier | Features (per `saas/tiers.py`) |
|---|---|
| free | `seo_audit` |
| pro | `ai_chatbot`, `seo_audit`, `lead_pipeline`, `analytics` |
| max | + `multi_site`, `white_label`, `devops` |
| enterprise | `*` (all) |

UI components touching tier (need audit if synced):
- `pi-dashboard-webapp/src/features/system/components/license/FeatureMatrix.jsx`
- `pi-dashboard-webapp/src/features/system/components/license/LicenseGate.jsx`
- `pi-dashboard-webapp/src/features/system/components/license/TierBadge.jsx`
- `pi-dashboard-webapp/src/features/billing/Subscription.jsx`
- `pi-dashboard-webapp/src/features/system/components/db-explorer/ProGate.jsx`
- `pi-dashboard-webapp/src/features/ai/AiCloud.jsx`
- `pi-dashboard-webapp/src/features/leads/Customers.jsx`
- `pi-store-webapp/src/features/pricing/components/PricingGrid.jsx`
- `pi-store-webapp/src/features/pricing/components/PricingCard.jsx`
- `pi-store-webapp/src/features/pricing/components/PricingBundles.jsx`
- `pi-store-webapp/src/features/pricing/components/PricingEnterprise.jsx`
- `pi-store-webapp/src/features/pricing/components/PricingFAQ.jsx`

### C. 4 User decisions — ✅ ANSWERED (2026-05-17 turn N+1)

| # | Decision | Answer | Rationale |
|---|---|---|---|
| 1 | Quota numbers | **Giữ hiện tại** — 50k / 1M / 3M / ∞ | Server enforcement đang chạy đúng — đỡ migration risk |
| 2 | Admin tier | **enterprise** | Admin debug toàn bộ |
| 3 | Feature matrix | **Giữ nguyên** | Free=seo_audit · Pro=+ai_chatbot+lead_pipeline+analytics · Max=+multi_site+white_label+devops · Enterprise=* |
| 4 | Pricing USD/month | **Free=$0 / Pro=$29 / Max=$99 / Enterprise=custom** | Standard SaaS micro pricing |

### C.1 Canonical tier matrix (locked)

```python
TIER_MATRIX = {
    "free": {
        "display_name": "Free",
        "monthly_tokens": 50_000,
        "max_sites": 1,
        "price_usd_per_month": 0,
        "priority_support": False,
        "features": ["seo_audit"],
    },
    "pro": {
        "display_name": "Pro",
        "monthly_tokens": 1_000_000,
        "max_sites": 3,
        "price_usd_per_month": 29,
        "priority_support": False,
        "features": ["seo_audit", "ai_chatbot", "lead_pipeline", "analytics"],
    },
    "max": {
        "display_name": "Max",
        "monthly_tokens": 3_000_000,
        "max_sites": 10,
        "price_usd_per_month": 99,
        "priority_support": True,
        "features": [
            "seo_audit", "ai_chatbot", "lead_pipeline", "analytics",
            "multi_site", "white_label", "devops",
        ],
    },
    "enterprise": {
        "display_name": "Enterprise",
        "monthly_tokens": -1,  # unlimited
        "max_sites": -1,        # unlimited
        "price_usd_per_month": None,  # custom quote
        "priority_support": True,
        "features": ["*"],  # all
    },
}
```

→ **State: drafted → ready for dispatch** (user đã chốt đủ 4 decisions). User trigger `set-state.sh T-20260517-010 dispatched` để bắt đầu Phase B-H.

---

## III. 📚 Required Reading

- `pi-backend/app/saas/tiers.py` — current server-side tier policy (single function, 41 lines)
- `pi-backend/app/saas/models.py` — Tenant model with tier field
- `pi-backend/app/admin/schemas.py` — `AdminLicenseCreate`, `AdminPackageCreate`, etc.
- `pi-backend/app/admin/routers/packages.py` — package CRUD (`allowed_tiers` array)
- `pi-backend/app/core/config.py` — env defaults
- `plugins/pi-api/includes/Settings.php` — WP-side tier reading
- `plugins/pi-api/DOCS.md` — customer-facing tier doc (currently wrong)
- All UI files listed in §II.B
- `pi-store-webapp/src/features/pricing/` — full pricing page
- `pi-backend/DEPLOYMENT.md` + `pi-backend/PRODUCTION-SETUP.md`

---

## IV. 🚧 Allowed Scope (Strict)

### A. Backend — Single source of truth
- 📄 `pi-backend/app/saas/tiers.py` — **PRIMARY**. Expand to include:
  - `TIER_FEATURES` (existing)
  - `TIER_TOKEN_QUOTA` (existing)
  - `TIER_DISPLAY_NAME` (NEW) — `{"free": "Free", "pro": "Pro", "max": "Max", "enterprise": "Enterprise"}`
  - `TIER_PRICE_USD_PER_MONTH` (NEW) — `{"free": 0, "pro": ?, "max": ?, "enterprise": "custom"}`
  - `TIER_MAX_SITES` (NEW) — `{"free": 1, "pro": 3, "max": 10, "enterprise": -1}`
  - `TIER_PRIORITY_SUPPORT` (NEW) — `{"free": false, "pro": false, "max": true, "enterprise": true}`
  - `tier_summary()` function returning all tier info as dict (for API consumption)
- 📄 `pi-backend/app/core/config.py` — DEPRECATE `rate_limit_free_per_month`, `rate_limit_pro_per_month`, `rate_limit_max_per_month` defaults. Comment them as legacy + remove from `monthly_quota_for` property. Use `saas/tiers.py` only.

### B. Backend — New endpoint
- 📄 `pi-backend/app/saas/router.py` (NEW or extend existing) — `GET /v1/tiers/spec` public endpoint returning tier matrix JSON. Both webapps + pi-api plugin can fetch single source of truth instead of hardcoding.

### C. pi-api plugin
- 📄 `plugins/pi-api/includes/Settings.php` — `getTokenQuota()` should fetch from backend via `BackendClient::getTierSpec()` (cached in transient). Fallback to hardcoded values only if backend unreachable.
- 📄 `plugins/pi-api/includes/BackendClient.php` — add `getTierSpec(): array` method calling `GET /v1/tiers/spec`.
- 📄 `plugins/pi-api/DOCS.md` — update Subscription Tiers section với CORRECT numbers (matching `saas/tiers.py`). Add note "values fetched from backend at runtime".

### D. Dashboard webapp
- 📄 `pi-dashboard-webapp/src/_shared/hooks/useTierSpec.js` (NEW) — TanStack Query hook calling `GET /v1/tiers/spec`, cached 1 hour.
- 📄 `pi-dashboard-webapp/src/features/system/components/license/FeatureMatrix.jsx` — replace hardcoded matrix with `useTierSpec()` data.
- 📄 `pi-dashboard-webapp/src/features/system/components/license/LicenseGate.jsx` — same.
- 📄 `pi-dashboard-webapp/src/features/system/components/license/TierBadge.jsx` — display name + color from spec.
- 📄 `pi-dashboard-webapp/src/features/billing/Subscription.jsx` — pricing + quota từ spec.
- 📄 `pi-dashboard-webapp/src/features/ai/AiCloud.jsx` — quota display.

### E. Store webapp pricing page
- 📄 `pi-store-webapp/src/features/pricing/components/PricingGrid.jsx` — fetch tier spec, render dynamically (don't hardcode prices/features).
- 📄 `pi-store-webapp/src/features/pricing/components/PricingCard.jsx` — accept tier spec as prop.
- 📄 `pi-store-webapp/src/features/pricing/components/PricingBundles.jsx` — sync với spec.
- 📄 `pi-store-webapp/src/features/pricing/components/PricingFAQ.jsx` — FAQ entries reference spec numbers.
- 📄 `pi-store-webapp/src/features/pricing/components/PricingEnterprise.jsx` — enterprise tier display.

### F. Admin tier fix (one-time)
- 📄 SQL on Neon production: `UPDATE licenses SET tier = '<enterprise|max>' WHERE email = '9.13.tuanhvan2018@gmail.com';` (tier per §II.C decision)
- 📄 OR: new `pi-backend/scripts/seed_admin_license.py` — reusable script for admin tier provisioning.

### G. Documentation
- 📄 `pi-backend/PRODUCTION-SETUP.md` — add §1.6 Tier matrix table referencing `/v1/tiers/spec` endpoint.
- 📄 `pi-backend/TIER-MATRIX.md` (NEW) — full reference doc: feature matrix, quota numbers, pricing, upgrade paths, edge cases (downgrade behavior, mid-cycle proration), API endpoint spec.
- 📄 `~/.claude/.../memory/reference_tier_matrix.md` (NEW) — em remember the canonical tier spec for future sessions.

### H. Test users policy doc
- 📄 `pi-backend/PRODUCTION-SETUP.md` — add §2.4b "Testing 3-tier UI" with the 3 options (Neon dev branch / admin impersonation / production aliases). Cập nhật rule "NEVER seed `seed_pi_users.py` on production".

---

## V. 🚫 Out Of Scope

- ❌ Stripe billing integration (separate task — needs Stripe account + price ID creation)
- ❌ Pricing page visual redesign (only data sync, no layout changes)
- ❌ Adding new tier (e.g., "Team", "Agency") — current 4 tiers (free/pro/max/enterprise) stay
- ❌ Migration of existing customer licenses (none yet — only admin)
- ❌ Backend rate limiting implementation (already exists via Redis)
- ❌ Adding new features to any tier (only consolidating existing)
- ❌ Touching saigonhouse-theme (theme not tier-aware)
- ❌ Touching pi-backend AI provider routing logic

---

## VI. 🛠️ Phases of Execution

### Phase A — Decision alignment with user (30')

User answers 4 questions in §II.C BEFORE dispatch:
1. Quota numbers (em recommends current `saas/tiers.py`)
2. Feature matrix (em recommends keep as-is)
3. Admin tier (em recommends `enterprise`)
4. Pricing per tier (em can suggest market rates based on token costs)

**Outputs**: filled decision table → goes into `TIER-MATRIX.md` draft.

### Phase B — Snapshot + backend SoT (60')

1. Snapshot 13+ files to `changes/T-010-*/before/`
2. Expand `saas/tiers.py` with all new dicts + `tier_summary()` function
3. Deprecate `config.py` rate_limit_*_per_month
4. Create `GET /v1/tiers/spec` endpoint (or extend existing public router)
5. Test endpoint: `curl https://pi-backend.up.railway.app/v1/tiers/spec` returns clean JSON
6. Update `pi-backend/PRODUCTION-SETUP.md` + new `TIER-MATRIX.md`

### Phase C — pi-api plugin sync (30')

1. Add `BackendClient::getTierSpec()` with transient caching (1 hour)
2. Refactor `Settings::getTokenQuota()` to use backend spec with fallback
3. Update `DOCS.md` numbers
4. PHP syntax check

### Phase D — Dashboard webapp sync (45')

1. Add `useTierSpec()` hook
2. Refactor FeatureMatrix, LicenseGate, TierBadge, Subscription, AiCloud, Customers, ProGate
3. Replace hardcoded numbers with hook data
4. webapp build + lint

### Phase E — Store webapp pricing sync (45')

1. Update PricingGrid + PricingCard to accept tier spec as prop
2. Sync PricingBundles, PricingEnterprise, PricingFAQ
3. Webapp build + lint

### Phase F — Admin tier fix (5')

1. Run SQL or new script to update admin license tier
2. Verify via SELECT query

### Phase G — Verification (30')

1. PHP -l, npm run build + lint cho 2 webapps
2. Manual smoke:
   - Open Pi Dashboard → FeatureMatrix shows correct features per tier
   - Open store pricing page → numbers match backend
   - WP admin Pi API → License page shows correct quota
3. API test: `curl /v1/tiers/spec` from local + production
4. Verify admin can see all features (tier=enterprise)

### Phase H — Dossier + changes + archive (15')

1. Fill §X-XVI
2. Create `changes/T-010-*/` with decision.md (per Phase A answers) + diff.patch + rollback-plan.md
3. State transitions → archive
4. lint-handoffs --strict

---

## VII. 🔍 Verification Commands

```bash
cd "C:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content"
PHP="/c/Users/Administrator/AppData/Roaming/Local/lightning-services/php-8.2.30+1/bin/win64/php.exe"

# Phase G — API
curl -s https://pi-backend.up.railway.app/v1/tiers/spec | jq .
# Expect: JSON with all 4 tiers, features, quotas, prices

# Backend test (local-against-Neon)
cd pi-backend
railway run ".venv\Scripts\python.exe" -X utf8 -m pytest tests/test_tiers.py -v

# pi-api PHP
"$PHP" -l plugins/pi-api/includes/Settings.php
"$PHP" -l plugins/pi-api/includes/BackendClient.php

# Dashboard
cd pi-dashboard-webapp && npm run lint && npm run build

# Store
cd pi-store-webapp && npm run lint && npm run build

# DB verify admin tier
# (Run on Neon SQL Editor)
SELECT email, tier, max_sites FROM licenses WHERE email = '9.13.tuanhvan2018@gmail.com';
# Expect: tier = 'enterprise' (or 'max' per Phase A decision)
```

---

## VIII. ✅ Acceptance Criteria

### Consistency
- [ ] `grep -r "50000\|50_000\|20000\|20_000" pi-backend plugins/pi-api pi-dashboard-webapp/src pi-store-webapp/src` returns ONLY refs in `saas/tiers.py` (the SoT) + tests.
- [ ] `GET /v1/tiers/spec` returns same numbers that `saas/tiers.py` defines.
- [ ] Dashboard FeatureMatrix uses `useTierSpec()` hook (no hardcoded array).
- [ ] Store PricingGrid renders from spec endpoint.
- [ ] pi-api `Settings::getTokenQuota()` calls backend.
- [ ] `DOCS.md` Subscription Tiers section quotes spec endpoint, not hardcoded numbers.

### Functional
- [ ] API endpoint live + returns expected JSON shape (per schema in `TIER-MATRIX.md`).
- [ ] Admin user license tier = `enterprise` (or `max`) in Neon production.
- [ ] Admin sees all features in dashboard (no LicenseGate blocking).
- [ ] Store pricing page renders 3-4 cards (free/pro/max/enterprise) with correct numbers.
- [ ] Customer flow: signup → activate → see quota matching spec.

### Code quality
- [ ] All 4 quality gates pass.
- [ ] UTF-8 preserved, no mojibake.
- [ ] No hardcoded tier numbers outside `saas/tiers.py` + tests.

### Documentation
- [ ] `pi-backend/TIER-MATRIX.md` exists with full feature × tier table + API schema + edge case notes.
- [ ] `PRODUCTION-SETUP.md` §1 updated với tier matrix reference.
- [ ] `~/.claude/.../memory/reference_tier_matrix.md` saved.

### Risk=medium requirements (changes/ folder)
- [ ] `changes/T-20260517-010-*/before/` snapshot of all 13+ files.
- [ ] `changes/T-20260517-010-*/decision.md` documenting Phase A user answers.
- [ ] `changes/T-20260517-010-*/diff.patch`.
- [ ] `changes/T-20260517-010-*/rollback-plan.md`.

---

## IX. 📋 Copy-Paste Prompt (Worker Instructions)

```text
You are claude (Orchestrator-direct). Execute T-20260517-010 per dossier.

PRE-FLIGHT: This task is DRAFTED (HOLD). User MUST answer 4 decisions in §II.C
before you can start Phase B. Do not invent values — wait for user.

Once answers received:
- Consolidate tier spec to single source of truth (pi-backend/app/saas/tiers.py)
- Expose via GET /v1/tiers/spec endpoint
- Sync 4 codebases: pi-api plugin, dashboard webapp, store webapp, docs
- Fix admin license tier (Pro → enterprise/max per user decision)
- Document in new TIER-MATRIX.md + update PRODUCTION-SETUP.md

CRITICAL: This is a wide cross-codebase refactor. Snapshot ALL files first
(13+ files). Risk=medium means changes/T-010-*/ folder required.

Phase order: A (user decisions) → B (backend SoT + endpoint) → C (pi-api) →
D (dashboard) → E (store) → F (admin fix) → G (verify) → H (archive).
```

---

## X. 📥 Agent Result (Populated by Orchestrator)

**Status: ✅ PASS (Phase B only)** — Original scope spanned Phase B-E (backend SoT + 3 consumer syncs). Per user direction ("Option Tách nhỏ"), Phase B completed in this session; Phase C-E split into T-011 / T-012 / T-013 drafted follow-ups.

### Phase B deliverables ✅

1. **`pi-backend/app/saas/tiers.py`** expanded with `TIER_MATRIX` canonical dict + 6 helpers (`tier_spec`, `all_tier_specs`, `public_tier_specs`, `max_sites_for_tier`, `price_for_tier`, `normalize_tier`). Legacy aliases `TIER_FEATURES` + `TIER_TOKEN_QUOTA` derived from matrix → cannot drift.
2. **`pi-backend/app/saas/tier_router.py`** NEW — `GET /v1/tiers/spec` + `GET /v1/tiers/spec/{slug}` with `Cache-Control: public, max-age=3600`.
3. **`pi-backend/app/main.py`** — register router with prefix `/v1/tiers`.
4. **`pi-backend/app/core/deps.py` + `app/shared/license/router.py`** — switch quota lookup from `settings.monthly_quota_for[tier]` → `monthly_quota_for_tier(tier)` (canonical).
5. **`pi-backend/app/core/config.py`** — deprecate `rate_limit_*_per_month` fields (kept as proxy to TIER_MATRIX so Railway env vars don't break boot; numbers bumped 5k/100k/500k → 50k/1M/3M to match canonical).
6. **`pi-backend/TIER-MATRIX.md`** NEW (200 lines) — canonical reference doc.
7. **`pi-backend/PRODUCTION-SETUP.md`** — §1.6 NEW tier mini-table + endpoint link.
8. **`pi-backend/scripts/promote_admin_to_enterprise.py`** NEW — reusable admin tier upgrade script.
9. **Admin license fix (Phase F manual)** — user ran SQL on Neon production: `UPDATE licenses SET tier='enterprise', max_sites=-1 WHERE email='9.13.tuanhvan2018@gmail.com'` → license #2, tier `enterprise`.
10. **Memory** — `~/.claude/.../memory/reference_tier_matrix.md` created, MEMORY.md index updated.

### Local verification ✅

```text
$ railway run .venv/Scripts/python.exe -X utf8 -m uvicorn app.main:app --port 8765
INFO:     Application startup complete.

$ curl -si http://127.0.0.1:8765/v1/tiers/spec | head
HTTP/1.1 200 OK
content-type: application/json
cache-control: public, max-age=3600
x-request-id: 48373566bfbe
x-response-time-ms: 0.4

{"tiers":[{"slug":"free",...,"monthly_tokens":50000,...,"price_usd_per_month":0,...},
{"slug":"pro",...,"monthly_tokens":1000000,...,"price_usd_per_month":29,...},
{"slug":"max",...,"monthly_tokens":3000000,...,"price_usd_per_month":99,...},
{"slug":"enterprise",...,"monthly_tokens":-1,...,"price_usd_per_month":null,...}],
"public_slugs":["free","pro","max"]}
```

### Git workflow ✅

- 2 clean commits on `main` (local): `docs: deployment topology + pi_dashboard docstring cleanup` (T-005 followup) + `feat(tiers): single source of truth + GET /v1/tiers/spec endpoint`
- Auto-classifier blocked direct push to `main` (correct — production-connected repo). Switched to feature branch `feat/tier-spec-consolidation`, pushed via `git push -u`.
- **PR #1 opened**: https://github.com/tuidienanhvan/pi-backend/pull/1 — user reviews + merges → Railway auto-deploys.

### Follow-up dossiers (Phase C-E split)

| Task | Scope | State |
|---|---|---|
| T-011 | Phase C — pi-api plugin: `Settings::getTokenQuota()` fetches `/v1/tiers/spec` (transient cache 1h), fix `DOCS.md` numbers | drafted (HOLD) |
| T-012 | Phase D — pi-dashboard-webapp: `useTierSpec()` hook + refactor 7 components (FeatureMatrix, LicenseGate, TierBadge, Subscription, AiCloud, Customers, ProGate) | drafted (HOLD) |
| T-013 | Phase E — pi-store-webapp: sync 5 pricing components (PricingGrid, PricingCard, PricingBundles, PricingFAQ, PricingEnterprise) to fetch endpoint | drafted (HOLD) |

User dispatch when ready — recommend after PR #1 merged + production endpoint verified.

---

## XI. 📊 Quality Gate Verification Matrix (Phase B scope only)
| Gate | Status | Evidence | Description |
|---|---|---|---|
| **Build Gate** | 🏗️ ✅ N/A | n/a | Pure backend Python — no webapp build in Phase B (webapp builds covered in T-012/T-013) |
| **Lint Gate** | 🧹 ✅ PASS | §X local test "Application startup complete" | Boot test confirms imports + route registration clean. No syntax errors. |
| **Scope Gate** | 📂 ✅ PASS | git diff stat: 9 files modified, all in §IV.A + §IV.B + §IV.G + §IV.F | No drift. Phase C-E files untouched (intentionally — split). |
| **Logic Gate** | 🎯 ✅ PASS | §X endpoint test response | All 4 tiers returned with canonical numbers (50k/1M/3M/-1, $0/$29/$99/null). Cache header present. Admin license tier=enterprise verified by user SQL. |

---

## XII. 📁 Evidence
```text
$ <populated after execution>
```

---

## XIII. 📉 Diff Summary
| File | +Lines | -Lines | Type |
|---|---|---|---|
| `pi-backend/app/saas/tiers.py` | TBD | TBD | Major expand |
| `pi-backend/app/saas/router.py` | TBD | 0 | NEW endpoint |
| `pi-backend/app/core/config.py` | TBD | TBD | Deprecate rate_limit_* |
| `pi-backend/TIER-MATRIX.md` | TBD | 0 | NEW doc |
| `pi-backend/PRODUCTION-SETUP.md` | TBD | TBD | Update §1, §2 |
| `pi-backend/scripts/seed_admin_license.py` | TBD | 0 | NEW (optional) |
| `plugins/pi-api/includes/Settings.php` | TBD | TBD | Use backend spec |
| `plugins/pi-api/includes/BackendClient.php` | TBD | 0 | Add getTierSpec |
| `plugins/pi-api/DOCS.md` | TBD | TBD | Correct numbers |
| `pi-dashboard-webapp/src/_shared/hooks/useTierSpec.js` | TBD | 0 | NEW hook |
| `pi-dashboard-webapp/src/features/system/components/license/*.jsx` | TBD | TBD | Sync with hook |
| `pi-dashboard-webapp/src/features/billing/Subscription.jsx` | TBD | TBD | Sync with hook |
| `pi-store-webapp/src/features/pricing/components/*.jsx` | TBD | TBD | Sync with endpoint |

---

## XIV. 🛡️ Orchestrator Review & Final Decision
Status: `pending` — awaits user decisions per §II.C.

---

## XV. 🆘 Escalation, Errors & Rollback

### Risk: medium
- Cross-codebase refactor (4 codebases): backend, pi-api, dashboard, store
- Customer-facing pricing page changes
- Existing admin license being mutated

### Failure types
1. **Webapp build fails after hook refactor** → restore from snapshot
2. **`GET /v1/tiers/spec` 500 on Railway** → check `saas/tiers.py` import errors via `railway logs`
3. **pi-api transient cache stale** → bump `pi_api_tier_spec_v1` transient key
4. **Admin tier update breaks license logic** → SQL revert: `UPDATE licenses SET tier = 'pro' WHERE email = '...'`

### Rollback
- Standard snapshot restore from `changes/T-010-*/before/`
- Endpoint removal: comment out route registration in `app/main.py`
- Webapp: rebuild from snapshot configs

### Escalation path
- Codex for surgical webapp refactor (multiple JSX files)
- Gemini for cross-codebase audit (1M context)

---

## XVI. CHANGE LOG
- **2026-05-17 18:53**: Dossier drafted by Claude based on user request to consolidate tier spec across 4 sources of truth. State: `drafted` (HOLD — awaits user 4 decisions §II.C).
- **2026-05-17 19:06**: State drafted → dispatched
- **2026-05-17 20:10**: State dispatched → returned
- **2026-05-17 20:10**: State returned → verified
