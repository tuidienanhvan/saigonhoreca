# Rollback Plan — T-20260513-001

## Trigger conditions
- Customer reports overcharge / under-charge after deploy
- `/v1/ai/complete` returns 5xx >1% rate
- Auto-allocate fills wrong customer's quota
- Stripe billing webhook breaks

## Immediate (under 5 min) — Feature flag toggle

```bash
# Production env
export PI_AI_NEW_ROUTING_ENABLED=false
# Restart workers (uvicorn / gunicorn)
systemctl restart pi-backend
```

Legacy `keys_for_license()` path resumes instantly. Migration columns harmless when not consulted.

## Schema rollback (only if necessary)

```bash
cd pi-backend
alembic downgrade -1   # → 009 → eabfff3ba783
```

Removes: routing_mode, allowed_tiers, priority_boost, dedicated_key_count from ai_packages.
Safe because no other table FKs to these columns.

## Frontend revert

```bash
# pi-store-webapp
git revert <commit-of-AdminPackagesPage-changes>
cd pi-store-webapp && npx vite build && deploy

# pi-dashboard-webapp (only UsageBreakdown.jsx touched)
git revert <commit-of-UsageBreakdown-changes>
```

## Verification after rollback

```bash
# 1. compile + tests
cd pi-backend && .venv/Scripts/python -m compileall app
.venv/Scripts/python -m pytest tests/ --ignore=tests/test_sqladmin_auth.py

# 2. smoke /v1/ai/complete
curl -X POST http://localhost:8000/v1/ai/complete \
  -H "Authorization: Bearer <license-jwt>" \
  -d '{"messages":[{"role":"user","content":"test"}]}'
# Expect 200 (license must have allocated keys per legacy contract)

# 3. AiUsage rows still being logged
psql -c "SELECT count(*) FROM ai_usage WHERE created_at > now() - interval '1 hour';"
```

## Escalation
If rollback itself fails: page on-call backend engineer. Switch DNS to maintenance page if blast radius reaches multiple customers.
