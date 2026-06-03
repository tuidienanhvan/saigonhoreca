---
id: T-20260508-001
owner: antigravity
state: archived
priority: P1
risk: high
estimated_minutes: 30
created: 2026-05-08T14:48:00+07:00
updated: 2026-05-08T14:51:30+07:00
---

# T-20260508-001: Backend System Wipe & Re-seed

## Allowed Scope
- `pi-backend/scripts/`
- `pi_backend` database schema/data
- `.env` (read-only for DB credentials)

## Out Of Scope
- Modifying `app/` core logic
- Deleting essential seeding logic

## Phases

### 1. Audit & Cleanup
- [ ] Identify and remove redundant scripts: `seed-test-tenants.py`, `cleanup_legacy_users.py`.
- [ ] Fix placeholder in `seed_ai_providers.py`.

### 2. Database Reset
- [ ] Run `alembic downgrade base`.
- [ ] Run `alembic upgrade head`.

### 3. Re-seeding
- [ ] Execute `scripts.create_admin`.
- [ ] Execute `scripts.seed_ai_providers`.
- [ ] Execute `scripts.seed_pool_keys`.
- [ ] Execute `scripts.seed_pi_users`.
- [ ] Execute `scripts.seed_pro_licenses`.
- [ ] Execute `scripts.seed_test_tenants`.

### 4. Verification
- [ ] Verify table counts.
- [ ] Verify admin user existence.

## Evidence
### Database Reset
```
INFO  [alembic.runtime.migration] Running upgrade 008 -> d8acec78d2d3, add app_pass to site model
```

### Seeding Counts
- Users: 4
- AI Providers: 24
- Licenses: 10
- Tenants: 4
