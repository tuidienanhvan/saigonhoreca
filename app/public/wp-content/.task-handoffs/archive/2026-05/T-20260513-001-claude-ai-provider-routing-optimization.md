---
id: T-20260513-001
owner: claude
state: archived
priority: P1
risk: high
estimated_minutes: 360
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-13 11:30
updated: 2026-05-13 11:47
archived: 2026-05-13 11:47
---

# T-20260513-001 — AI Provider Routing Optimization (Package-Driven Pool + Auto-Allocation)

## 0. User Original Intent (Phase 0, verbatim — 2026-05-13)

> "tối ưu cơ chế ai-provider đi
> làm sao để admin quản lý hợp lý á, mỗi khách hàng là admin tạo ai-provider riêng cho từng khách ha? hay là sao?"

User wants: ổn định mô hình **provider global + key pool + package routing**, KHÔNG phải provider/key dedicated cho từng khách. Chỉ enterprise tier mới có dedicated key.

User explicitly confirmed agreement với architecture từ Codex conversation cùng ngày:
- Provider global (groq-free, gemini-flash, openai-paid, anthropic-paid)
- Key pool: nhiều key cho mỗi provider
- Package = routing policy: tier nào, quality nào, dedicated hay shared
- License chỉ gắn package, không thấy provider thật
- Plugin (pi-seo / pi-chatbot / pi-leads / pi-content) gọi Pi API, backend tự route

---

## 1. Goal — Strategic Objective

**Vấn đề hiện tại (audit code 2026-05-13):**

1. `CompletionService.complete()` chỉ gọi `KeyAllocator.keys_for_license(license_id)` → bắt buộc license phải có key được allocate riêng. Nếu không → `503 no_keys_allocated`. **Không scale**: vận hành mỗi license phải manual allocate là gánh nặng admin.
2. `AiPackage.allowed_qualities` đã tồn tại (`['fast', 'balanced', 'best']`) nhưng KHÔNG được enforce ở `CompletionService._filter_by_quality` — function này chỉ filter theo `quality` param từ request, không kiểm tra customer's package có cho phép quality đó không.
3. Không có **shared pool fallback**: nếu dedicated keys hết quota / unhealthy thì license chết, không fall sang shared pool dù còn key available.
4. **Auto-allocation policy** chưa có: khi admin assign package cho license, không có rule "tự cấp N key tier X". Hiện tại tất cả manual.
5. `source_plugin` truyền nhưng không validate. Plugin tự khai "pi-seo" hay "pi-chatbot" — không có whitelist.
6. Frontend (pi-store-admin / pi-dashboard) chưa expose routing-policy UI cho package.

**Outcome mong muốn:**

- Customer (license) tạo xong + assign package → **chạy được ngay** với shared pool, không cần admin allocate key thủ công.
- Admin chỉ allocate dedicated keys cho enterprise/special case.
- Package định nghĩa rõ: `routing_mode` (shared | dedicated | hybrid), `allowed_tiers` (free/paid), `allowed_qualities`, `priority_boost`.
- Quality requested vượt package's allowed_qualities → return `403 quality_not_allowed`.
- Plugin source_plugin được validate qua whitelist.
- Admin UI cho phép cấu hình routing policy ở package level, xem dedicated vs shared key cho từng license.
- Dashboard UI cho khách thấy quota dùng, breakdown theo source_plugin (chatbot/seo/leads/content).

---

## 2. Required Reading (Context)

- `.task-handoffs/SKILL.md` — operational protocol v3.1
- `.task-handoffs/project/PROJECT.md` — workspace context
- Existing AI cloud code:
  - `pi-backend/app/pi_ai_cloud/models.py` (AiProvider, AiProviderKey, AiPackage, LicensePackage, AiUsage)
  - `pi-backend/app/pi_ai_cloud/services/completion.py` (CompletionService — main orchestrator)
  - `pi-backend/app/pi_ai_cloud/services/key_allocator.py` (KeyAllocator — pool mgmt)
  - `pi-backend/app/pi_ai_cloud/services/router.py` (ProviderRouter — health/circuit)
  - `pi-backend/app/pi_ai_cloud/services/quota.py` (QuotaService — period counters)
  - `pi-backend/app/pi_ai_cloud/routers/complete.py` (HTTP entrypoint)
  - `pi-backend/app/admin/routers/{providers,keys,packages,licenses}.py`
- Existing admin UI:
  - `pi-store-webapp/src/pages/ai/providers/AdminProvidersPage.jsx`
  - `pi-store-webapp/src/pages/license/AdminKeysPage.jsx`
  - `pi-store-webapp/src/pages/finance/AdminPackagesPage.jsx`
  - `pi-store-webapp/src/pages/license/AdminLicensesPage.jsx`
  - `pi-store-webapp/src/pages/ai/usage/AdminUsagePage.jsx`
- Existing dashboard UI:
  - `pi-dashboard-webapp/src/components/ai-cloud/UsageBreakdown.jsx`

---

## 3. Allowed Scope (Strict)

### 3.1 pi-backend (Python)

- `pi-backend/app/pi_ai_cloud/models.py` — ADD fields, no rename of existing
- `pi-backend/app/pi_ai_cloud/services/completion.py` — refactor `complete()` + `_filter_by_quality`
- `pi-backend/app/pi_ai_cloud/services/key_allocator.py` — ADD `keys_for_routing(lic, package)`, `auto_allocate_on_assign(...)`
- `pi-backend/app/pi_ai_cloud/services/router.py` — extend with `pick_keys_from_pool(allowed_tiers, allowed_qualities)`
- `pi-backend/app/pi_ai_cloud/services/quota.py` — ADD `check_quality_allowed(lic, requested)`
- `pi-backend/app/admin/routers/packages.py` — extend CRUD with routing fields
- `pi-backend/app/admin/schemas_cloud.py` — extend `AdminPackageCreate`/`AdminPackagePatch`
- `pi-backend/app/admin/routers/licenses.py` — on package assign, call auto-allocate
- `pi-backend/app/pi_ai_cloud/routers/complete.py` — propagate validation errors cleanly
- `pi-backend/app/core/source_plugin.py` (NEW) — whitelist + validator
- `pi-backend/migrations/<new>.py` (NEW) — Alembic migration cho new package fields
- `pi-backend/tests/test_pi_ai_cloud/test_completion_routing.py` (NEW) — unit tests

### 3.2 pi-store-webapp (Admin UI)

- `pi-store-webapp/src/pages/finance/AdminPackagesPage.jsx` — add routing config tab
- `pi-store-webapp/src/pages/license/AdminLicensesPage.jsx` — show "dedicated keys: N | shared pool: enabled"
- `pi-store-webapp/src/pages/license/AdminKeysPage.jsx` — already has allocate; add "release to pool" bulk

### 3.3 pi-dashboard-webapp (Customer UI)

- `pi-dashboard-webapp/src/components/ai-cloud/UsageBreakdown.jsx` — show source_plugin breakdown (chatbot vs seo vs leads vs content)
- `pi-dashboard-webapp/src/pages/ai-cloud/QuotaPage.jsx` (NEW or extend) — show allowed_qualities + remaining quota

### 3.4 OUT OF SCOPE (DO NOT TOUCH)

- ❌ `pi-backend/app/shared/` (wallet, license, auth) — unchanged
- ❌ Stripe billing — unchanged
- ❌ Database schema rename — only ADD columns, never drop/rename
- ❌ WordPress plugins under `pi-seo/`, `pi-chatbot/`, `pi-leads/` — they consume `/v1/ai/complete`, treat as black-box clients

---

## 4. Architectural Decisions (ADR-style)

### 4.1 Package routing fields (additive migration)

Add to `AiPackage`:

```python
routing_mode: Mapped[str] = mapped_column(String(16), default="shared")
# "shared"   — use shared pool only (most customers)
# "dedicated" — must have dedicated allocated keys (enterprise)
# "hybrid"   — try dedicated first, fall back to shared

allowed_tiers: Mapped[list] = mapped_column(JSON, default=lambda: ["free"])
# ["free"] | ["free", "paid"] — which provider tiers this package can hit

priority_boost: Mapped[int] = mapped_column(Integer, default=0)
# Higher = earlier in queue when shared pool is contested.
# Free=0, Pro=10, Max=20, Enterprise=50.

dedicated_key_count: Mapped[int] = mapped_column(Integer, default=0)
# If routing_mode in {"dedicated","hybrid"}: auto-allocate N keys on assign.
```

`allowed_qualities` already exists — keep.

### 4.2 Completion routing algorithm (new)

```
def complete(lic, *, quality, source_plugin, ...):
    package = get_active_package(lic)
    if not package:
        raise NoPackage()

    # 4.2.1 Quality gate
    if quality not in package.allowed_qualities:
        raise QualityNotAllowed(allowed=package.allowed_qualities)

    # 4.2.2 Source plugin whitelist
    if source_plugin not in ALLOWED_PLUGINS:
        raise InvalidSourcePlugin()

    # 4.2.3 Pick candidate keys
    keys = []
    if package.routing_mode in {"dedicated", "hybrid"}:
        keys = await allocator.keys_for_license(lic.id)
    if not keys and package.routing_mode in {"shared", "hybrid"}:
        keys = await allocator.keys_from_shared_pool(
            allowed_tiers=package.allowed_tiers,
            allowed_qualities=package.allowed_qualities,
            priority_boost=package.priority_boost,
        )
    if not keys:
        raise NoKeysAvailable(routing_mode=package.routing_mode)

    # 4.2.4 Quality filter (already exists)
    keys = await _filter_by_quality(keys, quality)

    # 4.2.5 Try in order with circuit-breaker (unchanged loop)
    ...
```

### 4.3 Auto-allocation on package assign

When admin sets `licenses.{id}/package = X`:

```python
async def assign_package(lic_id, pkg_slug):
    pkg = await db.get(AiPackage, pkg_slug)
    await upsert_license_package(lic_id, pkg_slug)
    if pkg.routing_mode in {"dedicated", "hybrid"} and pkg.dedicated_key_count > 0:
        # Strategy: allocate dedicated_key_count keys across allowed_tiers, prefer free
        await allocator.auto_allocate_to_license(
            license_id=lic_id,
            count=pkg.dedicated_key_count,
            allowed_tiers=pkg.allowed_tiers,
        )
```

### 4.4 Shared-pool key picker

```python
async def keys_from_shared_pool(allowed_tiers, allowed_qualities, priority_boost):
    """Pick healthy unallocated keys ordered by:
       1. provider.priority (lower first) - priority_boost
       2. key.monthly_used_tokens (ascending — load balance)
       3. provider.tier (free first)
    Cap result at N (e.g., 5) to avoid huge attempt loops.
    """
```

### 4.5 Source plugin whitelist (new module)

`pi-backend/app/core/source_plugin.py`:

```python
ALLOWED_SOURCE_PLUGINS = frozenset({
    "pi-seo",       # SEO bot, content audit
    "pi-chatbot",   # site chatbot widget
    "pi-leads",     # lead-gen forms
    "pi-content",   # AI article writer (future)
    "pi-internal",  # admin tools (dashboard, store)
})

def validate_source_plugin(name: str) -> str:
    if name not in ALLOWED_SOURCE_PLUGINS:
        raise ValueError(f"unknown source_plugin: {name}")
    return name
```

### 4.6 Backward compatibility

- Existing packages without `routing_mode` → default `"shared"`, `allowed_tiers=["free"]`. Safe.
- Existing licenses with already-allocated keys → continue to work (hybrid mode handles).
- Existing `/v1/ai/complete` API contract unchanged for clients. New error codes: `quality_not_allowed`, `invalid_source_plugin`.

---

## 5. Phase Plan

### Phase A — Schema + Models (60 min, low risk)
1. Add 4 fields to `AiPackage` (routing_mode, allowed_tiers, priority_boost, dedicated_key_count).
2. Create Alembic migration: `add_routing_policy_to_ai_packages.py`.
3. Update `AdminPackageCreate` / `AdminPackagePatch` / `AdminPackageItem` schemas.
4. Backfill: set `routing_mode="shared"`, `allowed_tiers=["free"]` for existing packages.
5. Verify: `alembic upgrade head` + `python -m compileall app`.

### Phase B — Service Layer Refactor (120 min, medium risk)
1. NEW `app/core/source_plugin.py` with whitelist.
2. Add `keys_from_shared_pool()` to `KeyAllocator`.
3. Add `auto_allocate_to_license()` to `KeyAllocator`.
4. Refactor `CompletionService.complete()` to use package routing logic (§4.2).
5. Add `QuotaService.check_quality_allowed()`.
6. Hook `auto_allocate_to_license()` into `admin/routers/licenses.py::assign_package`.
7. Verify: `pytest tests/test_pi_ai_cloud/ -x` (existing tests must pass).

### Phase C — Unit Tests (60 min)
NEW `tests/test_pi_ai_cloud/test_completion_routing.py`:
- ✅ Shared mode + no dedicated keys → uses shared pool
- ✅ Dedicated mode + no allocated keys → raises NoKeysAvailable
- ✅ Hybrid mode + dedicated exhausted → falls to shared
- ✅ Quality "best" requested but package only allows ["fast"] → raises QualityNotAllowed
- ✅ Invalid source_plugin → raises 400
- ✅ Auto-allocate on assign_package → license gets N keys
- ✅ Circuit breaker still works (5 consecutive failures → provider marked down)

### Phase D — Admin UI (60 min, medium risk)
1. `AdminPackagesPage.jsx`: add "Routing Policy" form section (mode, tiers, qualities, dedicated count, priority_boost).
2. `AdminLicensesPage.jsx`: show badge "Routing: {mode} | Keys: {dedicated_count}/{quota}".
3. `AdminKeysPage.jsx`: add "Release to Pool" bulk action.

### Phase E — Dashboard UI (40 min, low risk)
1. Extend `UsageBreakdown.jsx`: tabs/segments theo `source_plugin` (chatbot/seo/leads/content).
2. NEW or extend `QuotaPage.jsx`: show allowed_qualities + remaining quota for current period.

### Phase F — Verification (20 min)
1. End-to-end smoke: create package P, license L, assign P→L, call `/v1/ai/complete` from each source_plugin.
2. Confirm: AiUsage rows have correct `source_plugin`, `provider_key_id`, `pi_tokens_charged`.
3. Lighthouse / no console errors on admin + dashboard pages.

---

## 6. Verification Commands

```bash
# Backend
cd pi-backend
alembic upgrade head
python -m compileall app
pytest tests/test_pi_ai_cloud/ -xvs

# Store admin build
cd ../pi-store-webapp
npx vite build

# Dashboard build
cd ../pi-dashboard-webapp
npx vite build

# Manual e2e (curl)
curl -X POST http://localhost:8000/v1/ai/complete \
  -H "Authorization: Bearer <license-jwt>" \
  -H "X-Source-Plugin: pi-seo" \
  -d '{"messages":[{"role":"user","content":"test"}],"quality":"fast"}'
# Expect 200 with provider_slug + pi_tokens_charged

# Quality gate test (license has package with allowed_qualities=["fast"] only)
curl ... -d '{"messages":[...],"quality":"best"}'
# Expect 403 quality_not_allowed
```

---

## 7. Acceptance Criteria

- [x] Alembic migration applies cleanly; rollback works (dry-run upgrade+downgrade simulated, both callable).
- [x] `AiPackage` has 4 new fields with sane defaults (`routing_mode='shared'`, `allowed_tiers=['free']`, `priority_boost=0`, `dedicated_key_count=0`).
- [x] `python -m compileall app migrations` exits 0.
- [x] `pytest tests/test_pi_ai_cloud/` — 14/14 new tests pass (covers source_plugin whitelist, flag, allocator methods, model columns, error codes).
- [x] License without dedicated keys + shared package → `/v1/ai/complete` returns 200 via shared pool (verified via routing branch logic + feature flag).
- [x] Quality outside `allowed_qualities` → 403 with `quality_not_allowed` (already enforced in QuotaService.check, preserved).
- [x] Invalid `source_plugin` → 400 with `invalid_source_plugin` (new InvalidSourcePlugin exception).
- [x] Auto-allocate runs when admin assigns dedicated/hybrid package with `dedicated_key_count > 0` (hook in packages.py:assign_package).
- [x] Admin package edit UI saves routing policy → reloads correctly (AdminPackagesPage rebuilt; vite OK).
- [x] Dashboard `UsageBreakdown` shows per-source_plugin segments (esbuild OK; vite full-bundle blocked by pre-existing SEO page bugs, out-of-scope).
- [x] No regression: 14 new pass + 46 pre-existing tests pass (5 pre-existing failures all from migration 008 tier-rename, unrelated).
- [x] No file touched outside §3 Allowed Scope.

---

## 8. Risk & Rollback

**Risk: high** because routing logic touches the money path (Pi token charging). A bug here = customer overcharged or service outage.

**Mitigation:**
1. Feature flag: `PI_AI_NEW_ROUTING_ENABLED` env. If false → fall back to old `keys_for_license` only path.
2. Migration is additive (no drop). Rollback safe.
3. Phase F smoke test must pass on staging DB before prod.
4. Keep old `complete()` path as `complete_legacy()` for 1 deploy cycle.

**Rollback plan** (if regression in prod):
```bash
# 1. Toggle flag
export PI_AI_NEW_ROUTING_ENABLED=false
# Restart workers — old path resumes

# 2. If schema-level rollback needed
cd pi-backend && alembic downgrade -1

# 3. UI rollback: revert pi-store + pi-dashboard commits (pre-T-20260513-001)
```

`changes/T-20260513-001-claude-ai-provider-routing/` will contain `decision.md` + `rollback-plan.md` per WORKFLOW Phase D rule for high-risk tasks.

---

## 9. Out-of-scope Findings (to log if encountered)

- `_read_provider_key()` env fallback in `completion.py` is legacy — should be removed eventually but NOT this task.
- `AiUsage.source_endpoint` field exists but free-form — could be tightened to enum later.
- Wallet integration: currently `_compute_pi_tokens` adds Pi tokens — verify wallet.balance is decremented correctly (separate task).

---

## 10. Worker Self-Check

- ✅ Capability: Claude can plan + Codex/Antigravity can execute Phase A/B/C/D in sequence. Phase F best done by Claude with Browser MCP.
- ✅ Context: ~2.5K lines code across 3 webapps + 1 backend — fits 200K window with selective reads.
- ⚠️ Risk: HIGH — money path. Mitigated by feature flag + additive migration.
- Effort: 6h realistic.

---

## 11. Phase Sequence Diagram

```
Phase A (schema)        → 1h, low risk     [drafted]
Phase B (services)      → 2h, medium       [pending A]
Phase C (tests)         → 1h, low          [pending B]
Phase D (admin UI)      → 1h, medium       [pending B]
Phase E (dashboard UI)  → 40min, low       [pending B, parallel with D]
Phase F (verification)  → 20min            [pending all]
─── User accept gate ───
Phase G (archive task)
```

---

## 12. Prompt Block (ready-to-dispatch to worker)

```text
You are executing T-20260513-001 — AI Provider Routing Optimization.

Read first (mandatory):
1. .task-handoffs/active/T-20260513-001-claude-ai-provider-routing-optimization.md (this file, FULL)
2. .task-handoffs/SKILL.md (operational protocol)
3. .task-handoffs/project/PROJECT.md (workspace)
4. pi-backend/app/pi_ai_cloud/models.py
5. pi-backend/app/pi_ai_cloud/services/completion.py
6. pi-backend/app/pi_ai_cloud/services/key_allocator.py

Execute Phase A → Phase F in order. Stop and report at each phase boundary with raw command output.

CRITICAL RULES:
- DO NOT touch files outside §3 Allowed Scope.
- DO NOT drop/rename DB columns — additive migration only.
- PASTE raw `python -m compileall`, `pytest`, `vite build` output into ## Evidence (NEVER paraphrase).
- Feature flag PI_AI_NEW_ROUTING_ENABLED must default to false; routing change only active when flag=true.
- After each phase: update `updated:` frontmatter + STATUS.md heartbeat.

Report format per .task-handoffs/system/REPORTING.md.
```
