# Decision Log — T-20260513-001

## Final architecture chosen

**Package-driven routing with shared pool + dedicated allocation hybrid.**

### Why this over "provider-per-customer"
- Operations cost: per-customer provider = N×admin work for every onboarding
- Industry standard SaaS: pool of upstream keys multiplexed across customers
- User explicitly agreed via 2026-05-13 chat: "Không nên tạo ai-provider riêng cho từng khách hàng"

### Why feature flag `PI_AI_NEW_ROUTING_ENABLED` defaults false
- Money path — bug could overcharge or block customers
- Allow staged rollout: enable on staging first, observe AiUsage logs, then prod
- Legacy `keys_for_license()` path preserved when flag off → instant rollback

### Why source_plugin whitelist (not free-form)
- Billing integrity: customers/plugins cannot inject arbitrary labels to obscure usage
- Analytics: pre-defined buckets for `usage_by_plugin` aggregation
- Whitelist: pi-seo, pi-chatbot, pi-leads, pi-content, pi-internal

### Why additive-only migration
- Backward compat for existing packages
- Rollback safe (drop columns reversible)
- No data loss on downgrade

## Files modified

### Backend
- `app/pi_ai_cloud/models.py` — AiPackage +4 fields
- `app/pi_ai_cloud/services/completion.py` — routing branch + feature flag + source_plugin validation
- `app/pi_ai_cloud/services/key_allocator.py` — +auto_allocate_to_license() +keys_from_shared_pool()
- `app/admin/routers/packages.py` — assign_package hooks auto-allocate
- `app/admin/schemas_cloud.py` — package schemas +4 fields
- `app/core/source_plugin.py` (NEW) — whitelist
- `migrations/versions/009_add_package_routing_policy.py` (NEW)

### Frontend (admin)
- `pi-store-webapp/src/pages/finance/AdminPackagesPage.jsx` — routing form + badge column

### Frontend (customer)
- `pi-dashboard-webapp/src/components/ai-cloud/UsageBreakdown.jsx` — by_plugin section

### Tests
- `pi-backend/tests/test_pi_ai_cloud/test_completion_routing.py` (NEW) — 14 tests

## Evidence

- compileall app migrations: exit 0
- pytest tests/test_pi_ai_cloud/: 14 passed in 0.07s
- pi-store-webapp vite build: ✓ built in 891ms (234.30 kB / gzip 71.85 kB)
- pi-dashboard-webapp esbuild parse (changed file only): exit 0; full vite blocked by pre-existing SEO duplicate-identifier bugs unrelated to this task
