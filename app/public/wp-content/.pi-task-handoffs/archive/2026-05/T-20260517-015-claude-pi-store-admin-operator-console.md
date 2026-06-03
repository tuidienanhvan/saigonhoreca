---
id: T-20260517-015
owner: claude
state: archived
priority: P1
risk: medium
estimated_minutes: 2400
parent: null
children: []
depends_on: [T-20260517-010]
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Gemini]
created: 2026-05-17 22:00
updated: 2026-05-18 09:32
archived: 2026-05-18 09:32
---

## 0. 📥 User Original Intent (Phase 0 — Verbatim)

> **User audit (full text)**:
> Mình đã rà cấu trúc chính của `pi-backend`, `pi-dashboard-webapp`, `pi-store-webapp`, và `plugins/pi-api`.
>
> Hiện tại **pi-store admin đã có**: overview, usage, revenue, packages, licenses, providers, provider keys, releases, users, cron, audit log, settings.
>
> Nhưng để đúng nghiệp vụ "admin quản lý AI provider/API/token cho khách hàng", admin pi-store còn thiếu các phần lớn này:
>
> 1. **Quản lý khách hàng / tenant SaaS** — Backend đã có `/v1/admin/saas/tenants` nhưng pi-store admin chưa có UI/nav/client cho: tạo khách hàng/tenant, gắn domain/site/license/tier, nạp token, xem trạng thái tenant, quản lý API link riêng cho khách. Đây là thiếu lớn nhất.
> 2. **Đồng bộ tier/package từ backend** — pi-api, pi-dashboard, pi-store pricing hardcode tier/quota/pricing. Cần dùng canonical endpoint `/v1/tiers/spec`, để admin/packages là nguồn quản lý chính.
> 3. **Token ledger chi tiết** — Admin adjust token được, thiếu xem lịch sử cộng/trừ, lý do, refund/bonus/admin adjust, lọc theo license/customer/provider.
> 4. **AI usage log cấp platform** — Backend có `AiUsage` nhưng admin chưa có explorer: request nào gọi model nào, khách dùng bao nhiêu, SEO/chat/post tiêu token ra sao, lỗi/latency/upstream cost, margin theo request/provider/customer.
> 5. **Provider/key operations chưa đủ sâu** — Provider/key CRUD có, thiếu health dashboard, cảnh báo key gần hết quota, bulk rotate, banned/exhausted workflow, routing policy shared/dedicated/hybrid, kiểm tra provider ngoài `openai_compat`.
> 6. **Billing/subscription lifecycle** — Revenue là tổng quan. Thiếu subscription detail, invoice/payment history, failed payment/webhook issue, cancel/reactivate/renewal, liên kết Stripe với license/package rõ ràng.
> 7. **Customer 360** — Trang khách tổng hợp: user info, licenses, sites, package, token wallet, AI usage, invoices, audit/support notes. Hiện rời rạc.
> 8. **Release management còn mỏng** — Có upload/list, thiếu promote stable, rollback, yank, rollout theo tier/channel, stats, changelog admin.
> 9. **Cron/health chưa hoàn chỉnh** — Một số cron placeholder (health check, usage rollup). Trang cron có nhưng chưa đủ value vận hành.
>
> Kết luận: pi-store admin mới đủ phần quản trị cơ bản, còn thiếu "operator console" cho SaaS/AI business: tenant/customer, tier spec, token ledger, AI usage drilldown, billing lifecycle, provider health/routing.
>
> Ưu tiên: **Tenant/Customer API management → Token ledger → AI usage logs → Tier spec sync → Billing/subscription**.

---

# 📋 T-20260517-015 | claude | pi-store-admin-operator-console — Master Epic Dossier

## I. Frontmatter

| Field | Value |
|---|---|
| `priority` | P1 — operator console is business-critical (SaaS revenue model depends on it) |
| `risk` | medium — large surface area but mostly NEW UI consuming existing backend endpoints; minimal breaking-change risk |
| `estimated_minutes` | 2400 (40 hours) total across 9 sub-tasks |
| `state` | **drafted (HOLD — EPIC)** — too large to dispatch as single task. User dispatches sub-tasks T-016 → T-024 individually per priority order |
| `depends_on` | T-010 (tier spec endpoint already done) |
| `children` | T-016 (tenant), T-017 (token ledger), T-018 (AI usage), T-019 (provider deep), T-020 (billing), T-021 (customer 360), T-022 (releases), T-023 (cron health), T-024 (audit follow-ups). T-011/T-012/T-013 cover Gap #2 (tier sync) — already drafted |

---

## II. Goal

Turn `pi-store-webapp/admin/` từ "basic CRUD admin" thành **operator console** đủ vận hành SaaS AI business. After this epic completes, admin có đủ tool để: onboard customer mới, theo dõi token economy real-time, debug provider issues, manage billing lifecycle, generate customer-360 view, ship releases an toàn.

### Strategic context

pi-store admin hôm nay = CRUD wrapper quanh 12 admin/* endpoints. Đủ cho dev testing nhưng KHÔNG đủ cho production operator (DevOps + customer success + finance ops). 9 gaps user identified = exact deltas between "dev tool" và "ops tool".

### Architecture

```
                    ┌─────────────────────────────────────┐
                    │   pi-store-webapp/admin/            │
                    │   (Vite SPA — /admin route)         │
                    └──────────────┬──────────────────────┘
                                   │ REST + admin JWT
                                   ▼
                  ┌────────────────────────────────────────┐
                  │   pi-backend FastAPI (Railway)         │
                  │   /v1/admin/*        (12 routers)      │
                  │   /v1/admin/saas/*   (tenants — NEW UI)│
                  │   /v1/billing/*      (subscription)    │
                  │   /v1/tiers/spec     (canonical, T-010)│
                  └────────────────────────────────────────┘
```

Backend endpoints (mostly) already exist — frontend is the gap.

### Status of 12 existing admin sections + 1 missing

| pi-store-webapp/admin/features/ | Backend endpoint | Status today | Gap closure target |
|---|---|---|---|
| `overview/` | `/v1/admin/overview` | ✅ Basic dashboard | + Customer 360 links (T-021) |
| `usage/` | `/v1/admin/usage` | 🟡 Aggregate only | Drilldown per request/provider/customer (T-018) |
| `revenue/` | `/v1/admin/revenue` | 🟡 Aggregate only | Full billing lifecycle (T-020) |
| `packages/` | `/v1/admin/packages` | ✅ CRUD | + tier sync from `/v1/tiers/spec` (T-013 covers) |
| `licenses/` | `/v1/admin/licenses` | ✅ CRUD | + link to billing + sites + 360 (T-020 + T-021) |
| `providers/` | `/v1/admin/providers` | ✅ CRUD + test | + health dashboard + routing policy (T-019) |
| `keys/` | `/v1/admin/keys` | ✅ CRUD | + bulk ops + warnings + banned workflow (T-019) |
| `releases/` | `/v1/admin/releases` | 🟡 Upload/list | + promote/rollback/yank/stats (T-022) |
| `users/` | `/v1/admin/users` | ✅ CRUD | + 360 link (T-021) |
| `cron/` | `/v1/admin/cron` | 🟡 Listing | + run-now + history + alerts + fix placeholders (T-023) |
| `audit/` | `/v1/admin/audit` | ✅ List | + filter/search + retention policy (T-024) |
| `settings/` | `/v1/admin/settings` | ✅ CRUD | + tier sync sources from `/v1/tiers/spec` (T-013) |
| **`tenants/`** | `/v1/admin/saas/tenants` | ❌ MISSING (no folder, no nav) | **T-016 NEW** |

---

## III. Required Reading

- This dossier (the master plan)
- **`pi-backend/app/saas/admin_router.py`** — tenant endpoints already exist (list/create/patch/recharge)
- **`pi-backend/app/billing/router.py`** — subscription lifecycle (checkout/change-tier/cancel/status/webhook)
- **`pi-backend/app/admin/routers/usage.py`** — aggregate + raw event endpoints (audit shape for T-018)
- **`pi-backend/app/admin/routers/cron.py`** — see what's placeholder vs real (T-023 prep)
- **`pi-backend/app/shared/usage.py`** — `UsageLog` model (token ledger source)
- **`pi-store-webapp/admin/layout/`** — `AdminLayout.jsx` + `AdminSidebar.jsx` — where to register new nav entries
- **`pi-store-webapp/admin/_shared/`** — existing components, API client pattern
- **`pi-backend/TIER-MATRIX.md`** — tier spec already canonical (T-010)
- **`pi-backend/PRODUCTION-SETUP.md`** — current ops state

---

## IV. Allowed Scope (Strict — by sub-task)

### T-016 — Tenant/Customer SaaS management 🔴 PRIORITY 1
Est: 6 hours. Risk: low (NEW UI, backend exists).

**Backend**: `/v1/admin/saas/tenants` GET/POST/PATCH + `/tokens/recharge` POST already implemented.

**Files to create**:
- `pi-store-webapp/admin/features/tenants/TenantsListPage.jsx` — table: id, name, domain, license_key, tier, status, last_seen, tokens_balance, actions
- `pi-store-webapp/admin/features/tenants/TenantDetailPage.jsx` — single tenant view with sub-tabs: info, sites, tokens, usage, audit
- `pi-store-webapp/admin/features/tenants/TenantCreateModal.jsx` — name, domain, site_url, license_key, tier, status, features
- `pi-store-webapp/admin/features/tenants/TenantRechargeModal.jsx` — amount, reason
- `pi-store-webapp/admin/features/tenants/api.js` — TanStack Query hooks (listTenants, createTenant, patchTenant, rechargeTokens)
- Add nav entry in `AdminSidebar.jsx`: "Tenants" between "Users" and "Licenses"

**Backend touch**: Possibly add `GET /v1/admin/saas/tenants/{id}` (single fetch — verify exists; if not, add)

### T-017 — Token ledger detail 🔴 PRIORITY 2
Est: 4 hours. Risk: low.

**Backend gap**: Need `GET /v1/admin/tokens/ledger` endpoint returning paginated rows of `{ timestamp, tenant_id, license_id, amount, type (charge/refund/bonus/admin_adjust), reason, actor, balance_after, provider_id (if AI), source (seo|chat|post|admin) }`.

**Schema source**: derive from `app/saas/models.Token` + `app/shared/usage.UsageLog` joined; may need new `app/admin/routers/tokens.py` router.

**Files to create**:
- `pi-backend/app/admin/routers/tokens.py` (NEW) — `GET /ledger` + filters (license_id, tenant_id, type, date range)
- `pi-backend/app/admin/schemas.py` — `TokenLedgerRow`, `TokenLedgerResponse`
- `pi-store-webapp/admin/features/usage/TokenLedger.jsx` — table with filters
- Or extend existing `usage/` feature with new tab

### T-018 — AI usage drilldown 🔴 PRIORITY 3
Est: 6 hours. Risk: low.

**Backend**: `UsageLog` model + `/v1/admin/usage` exists. Audit if it returns enough fields. If not:
- Add `GET /v1/admin/usage/events` returning paginated event rows: `{ id, timestamp, license_key, tenant_id, source, provider_slug, model_id, tokens_input, tokens_output, latency_ms, status, error, pi_tokens_charged, upstream_cost_usd_cents }`
- Add filters: license, tenant, provider, source, status, date range
- Add aggregation endpoint `/v1/admin/usage/aggregate` returning per-provider/per-customer/per-source rollups

**Files**:
- `pi-backend/app/admin/routers/usage.py` — extend with event drilldown
- `pi-store-webapp/admin/features/usage/UsageExplorer.jsx` — events table + filter sidebar
- `pi-store-webapp/admin/features/usage/UsageAggregates.jsx` — pivot view (rows: customer/provider/source)
- `pi-store-webapp/admin/features/usage/components/UsageRowDetail.jsx` — drawer with full request/response/error

### Phase Gap #2 — Tier spec sync (T-011/T-012/T-013 already drafted)
No new sub-task. References parent T-010 + existing children.

### T-019 — Provider/key deep operations 🟡 PRIORITY 5
Est: 5 hours. Risk: medium (touches AI routing — production-critical).

**Files**:
- `pi-backend/app/admin/routers/keys.py` — add `POST /keys/bulk-rotate`, `POST /keys/{id}/ban`, `POST /keys/{id}/unban`, `GET /keys/health` (quota warnings)
- `pi-backend/app/admin/routers/providers.py` — add `GET /providers/{id}/health` (recent test results, latency, error rate)
- `pi-backend/app/pi_ai_cloud/services/routing_policy.py` (NEW) — encode shared/dedicated/hybrid routing rules
- `pi-store-webapp/admin/features/providers/ProviderHealthPage.jsx` — dashboard with charts (request count, latency p50/p99, error rate, key quota usage)
- `pi-store-webapp/admin/features/keys/BulkRotateModal.jsx` — select keys + replace en masse
- `pi-store-webapp/admin/features/keys/KeyAlertBanner.jsx` — surface keys near quota cap (>80% used)
- `pi-store-webapp/admin/features/providers/RoutingPolicyEditor.jsx` — per-tier routing config

### T-020 — Billing/subscription lifecycle 🟡 PRIORITY 4
Est: 5 hours. Risk: medium (Stripe integration sensitive).

**Backend**: `/v1/billing/*` exists for customer-side. Need admin-facing:
- `GET /v1/admin/billing/subscriptions` — all subscriptions paginated, filters by status
- `GET /v1/admin/billing/subscriptions/{license_id}` — single subscription detail
- `GET /v1/admin/billing/invoices` — all invoices
- `POST /v1/admin/billing/subscriptions/{id}/cancel` — admin cancel
- `POST /v1/admin/billing/subscriptions/{id}/reactivate` — admin reactivate
- `GET /v1/admin/billing/webhook-events` — Stripe webhook event log + retry failed
- `POST /v1/admin/billing/webhook-events/{id}/retry`

**Files**:
- `pi-backend/app/admin/routers/billing.py` (NEW)
- `pi-store-webapp/admin/features/revenue/SubscriptionsTab.jsx` — list + filters
- `pi-store-webapp/admin/features/revenue/SubscriptionDetailPage.jsx`
- `pi-store-webapp/admin/features/revenue/InvoicesTab.jsx`
- `pi-store-webapp/admin/features/revenue/WebhookEventsTab.jsx`

### T-021 — Customer 360 page 🟢 PRIORITY 6
Est: 4 hours. Risk: low.

Aggregation page — composes data from existing endpoints. No new backend.

**Files**:
- `pi-store-webapp/admin/features/customer-360/CustomerProfilePage.jsx` — accepts `user_id` route param, queries 6 endpoints in parallel
- `pi-store-webapp/admin/features/customer-360/components/UserInfoCard.jsx`
- `pi-store-webapp/admin/features/customer-360/components/LicensesCard.jsx`
- `pi-store-webapp/admin/features/customer-360/components/SitesCard.jsx`
- `pi-store-webapp/admin/features/customer-360/components/WalletCard.jsx`
- `pi-store-webapp/admin/features/customer-360/components/UsageCard.jsx`
- `pi-store-webapp/admin/features/customer-360/components/InvoicesCard.jsx`
- `pi-store-webapp/admin/features/customer-360/components/AuditNotesCard.jsx`
- Linked from `users/UsersListPage.jsx` → "View 360" button per row
- Linked from `tenants/TenantDetailPage.jsx` → user_id pivot

### T-022 — Release management extension 🟢 PRIORITY 7
Est: 4 hours. Risk: medium (release misroute breaks customer auto-updates).

**Backend**:
- `pi-backend/app/admin/routers/releases.py` — add `POST /releases/{id}/promote` (mark stable channel), `POST /releases/{id}/yank`, `GET /releases/{id}/stats` (download counts per tier), `POST /releases/{id}/rollout` (gradual % to tier/channel)
- Add `release_channel` + `rollout_percent` fields to `plugin_releases` table (alembic migration)

**Files**:
- `pi-backend/migrations/versions/011_release_channel_rollout.py` (NEW)
- `pi-backend/app/shared/updates/models.py` — extend PluginRelease
- `pi-store-webapp/admin/features/releases/ReleasePromoteModal.jsx`
- `pi-store-webapp/admin/features/releases/ReleaseYankModal.jsx`
- `pi-store-webapp/admin/features/releases/ReleaseStatsTab.jsx` (downloads chart)
- `pi-store-webapp/admin/features/releases/ChangelogEditor.jsx` (admin-only changelog editing)

### T-023 — Cron/health real ops 🟢 PRIORITY 8
Est: 3 hours. Risk: low.

**Backend**: Replace placeholder cron handlers with real:
- `health_check_all_providers` — actually call each provider's `/models` endpoint, update `ai_providers.last_success_at + consecutive_failures`
- `usage_rollup_daily` — aggregate `usage_logs` → `usage_daily` table (new), enables fast `/v1/admin/usage` queries
- `key_quota_warning_scan` — flag keys >80% quota → push to admin notifications

**Files**:
- `pi-backend/app/celery_tasks/health.py` (extend if exists, or NEW)
- `pi-backend/app/celery_tasks/rollup.py` (NEW)
- `pi-backend/app/celery_tasks/quota_scan.py` (NEW)
- `pi-backend/app/admin/routers/cron.py` — add `POST /cron/{job_name}/run-now`, `GET /cron/{job_name}/history`
- `pi-store-webapp/admin/features/cron/CronJobDetailPage.jsx` — run-now button + history list

### T-024 — Audit log follow-ups 🟢 PRIORITY 9
Est: 3 hours. Risk: low.

**Files**:
- `pi-backend/app/admin/audit.py` — add retention policy (auto-delete > 90 days)
- `pi-backend/app/admin/routers/audit.py` — add search by actor/target/action + date range filter
- `pi-store-webapp/admin/features/audit/AuditExplorer.jsx` — refactor list to use filters + search bar
- `pi-store-webapp/admin/features/audit/components/AuditRowDetail.jsx` — drawer with before/after JSON diff

---

## V. Out Of Scope (Strictly Forbidden)

- ❌ pi-dashboard-webapp changes (separate webapp, different audience — customer-facing dashboard, not operator console)
- ❌ pi-api plugin changes (already T-005, T-008, T-014, T-006 work — stable surface)
- ❌ Adding NEW backend domains (no new ai_*, pi_seo, pi_chatbot work)
- ❌ Migrating off Stripe to another billing platform
- ❌ Rewriting existing admin sections (only extend)
- ❌ Frontend redesign (use existing AdminLayout + components)
- ❌ Adding role granularity beyond `is_admin` (no admin roles like "support", "billing-only" — defer)
- ❌ Touching saigonhouse-theme

---

## VI. Phases of Execution (per sub-task)

Each sub-task T-016 → T-024 follows standard pattern:
1. Phase A — Snapshot + audit existing backend endpoint shape
2. Phase B — Backend changes (if needed; many sub-tasks are UI-only)
3. Phase C — Frontend implementation (pi-store-webapp/admin/)
4. Phase D — Verification (PHP -l, npm build+lint, manual smoke)
5. Phase E — Dossier finalization + archive

The MASTER (T-015) itself does NOT execute. It's a planning artifact. User dispatches sub-tasks individually.

### Recommended dispatch order (user-specified priority)

```
T-016 (Tenant)         → 6h   ┐
T-017 (Token ledger)   → 4h   │
T-018 (AI usage)       → 6h   │  Critical path — operator's daily work
T-011 (pi-api tier)    → 1h   │
T-012 (dashboard tier) → 1h   │
T-013 (store tier)     → 1h   ┘
T-020 (Billing)        → 5h   ┐
T-021 (Customer 360)   → 4h   │  Revenue + customer success
                              ┘
T-019 (Provider deep)  → 5h   ┐  Ops maturity
T-022 (Releases)       → 4h   │
T-023 (Cron health)    → 3h   │
T-024 (Audit follow)   → 3h   ┘
```

Parallelization: Backend changes serialize (single DB schema). Frontend within a sub-task can be parallelized across files. Some sub-tasks parallelizable if no shared files (T-019 + T-022 + T-024 e.g.).

---

## VII. Verification Commands (per sub-task)

```bash
cd "C:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content"

# Backend
cd pi-backend
railway run ".venv\Scripts\python.exe" -X utf8 -m pytest tests/ -k "admin" -v
railway run ".venv\Scripts\python.exe" -c "from app.main import app; print('boot OK')"

# Store admin webapp build
cd ../pi-store-webapp
npm run lint
npm run build  # outputs to build/

# Manual smoke (per sub-task)
# - cd pi-store-webapp/admin && npm run dev (separate Vite entry?)
# - open localhost:5174/admin
# - login as admin → navigate to new section
# - perform CRUD ops, verify network requests hit correct admin endpoint
```

---

## VIII. Acceptance Criteria (Epic-level)

After all 9 sub-tasks complete:
- [ ] Admin can onboard new customer end-to-end (T-016): create tenant + license + tier + initial token balance
- [ ] Admin can debug "where did my tokens go?" question (T-017 + T-018)
- [ ] Admin can answer "is provider X healthy?" (T-019)
- [ ] Admin can manage subscription lifecycle (T-020): see active subs, cancel, retry failed webhooks
- [ ] Admin has 1-screen Customer 360 view (T-021)
- [ ] Admin can ship plugin updates safely (T-022): canary rollout, yank bad releases
- [ ] Cron jobs run real work + are observable (T-023)
- [ ] Audit log is searchable + has retention (T-024)
- [ ] All tier numbers consume `/v1/tiers/spec` (T-011/T-012/T-013)
- [ ] Each sub-task passes 4 quality gates (build/lint/scope/logic)
- [ ] Each sub-task documented in `changes/T-NNN-*/` with snapshots + decision.md + rollback-plan.md
- [ ] No regression in existing 12 admin sections
- [ ] `lint-handoffs.sh --strict` exits 0 throughout

---

## IX. Worker Prompt (Per Sub-Task)

Master epic does NOT have a worker prompt — it doesn't execute as single task. Each sub-task (T-016+) will be created with new-task.sh + filled with its own focused prompt referencing this dossier.

Template for sub-task prompt:
```text
You are claude. Execute T-NNN per dossier:
  .task-handoffs/active/T-NNN-claude-<slug>.md

This is sub-task #N of T-20260517-015 EPIC (pi-store admin operator console).
Master dossier at archive/2026-05/T-20260517-015-*.md.

Focus: <specific gap description>
Backend: <new or existing endpoints>
Frontend: pi-store-webapp/admin/features/<area>/

Risk = <low|medium>. Standard 5-phase flow + archive.
Verify no regression in existing admin sections before marking verified.
```

---

## X. Agent Result (Populated by Orchestrator)
Status: `drafted (HOLD — EPIC)` — planning artifact only. User dispatches sub-tasks individually.

## XI. Quality Gates — N/A (epic-level)
Each sub-task has its own quality gates.

## XII. Evidence — N/A (epic-level)

## XIII. Diff Summary — N/A
Aggregated across 9 sub-tasks. Expected total touch: ~50 files, ~5000 LOC across pi-backend + pi-store-webapp/admin.

## XIV. Orchestrator Review & Final Decision

**Status: drafted (HOLD — EPIC)**.

User reviews + decides dispatch cadence:
- **Option A**: Dispatch T-016 first (highest user-priority gap). Em executes Phase A-E. Archive. Then T-017, etc. Serial — ~40 hours total wall time spread across many sessions.
- **Option B**: Parallel where possible (T-019/T-022/T-024 don't share files) — em dispatches 2-3 at once if user OK. Faster but harder to review.
- **Option C**: User picks 1-2 most urgent (e.g., T-016 + T-017) — em executes only those. Other gaps deferred.

Em recommend **Option A serial** for first 3 (T-016 → T-017 → T-018) to establish patterns, then **Option B parallel** for later groups.

---

## XV. Escalation, Errors & Rollback

### Epic-level risks
- **Scope creep** — easy with 9 sub-tasks. Mitigation: strict §IV per sub-task; reject pull-in of unrelated improvements; defer to follow-up epic.
- **Inconsistent UX** — multiple sub-tasks adding new pages may diverge in style. Mitigation: T-016 establishes pattern; T-017+ reuse same component primitives.
- **Backend changes serialize** — T-017/T-018/T-019/T-020/T-022/T-023/T-024 all touch pi-backend. Conflicting migrations or model changes possible. Mitigation: only 1 backend-touching sub-task dispatched at a time.

### Per-sub-task rollback
Standard pattern (per T-005, T-010, T-014):
- Snapshot `changes/T-NNN-*/before/`
- Backend revert: restore Python files, run `alembic downgrade -1` if migration added
- Frontend revert: restore JSX/CSS files, rebuild

### Escalation path
- Codex for surgical refactors (e.g., extending existing routers)
- Gemini for cross-codebase audits (verify no regression in existing 12 admin sections)
- ChatGPT for product-strategy decisions if user needs second opinion on tier policy / billing UX

---

## XVI. CHANGE LOG
- **2026-05-17 22:00**: Master epic dossier drafted by Claude based on user audit. 9 gaps documented + sub-task IDs assigned (T-016 → T-024). State: `drafted (HOLD)` — awaits user dispatch decisions.
- **2026-05-18 09:32**: State drafted → dispatched
- **2026-05-18 09:32**: State dispatched → returned
- **2026-05-18 09:32**: State returned → verified
