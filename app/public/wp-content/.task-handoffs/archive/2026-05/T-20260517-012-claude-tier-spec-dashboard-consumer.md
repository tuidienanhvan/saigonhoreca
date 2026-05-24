---
id: T-20260517-012
owner: claude
state: archived
priority: P2
risk: low
estimated_minutes: 60
parent: T-20260517-010
children: []
depends_on: [T-20260517-010]
parallelization_ok: true
retry_count: 0
retry_max: 1
escalation_path: [Codex, Gemini]
created: 2026-05-17 20:12
updated: 2026-05-18 09:31
archived: 2026-05-18 09:31
---

## 0. 📥 User Original Intent (Phase 0 — Verbatim)

> **Split from T-20260517-010** per user "Option Tách nhỏ" — Phase D (pi-dashboard-webapp consumer sync).
>
> Parent context: T-010 archived backend SoT + endpoint. T-011 covers pi-api consumer. This task covers dashboard webapp.

---

# 📋 T-20260517-012 | claude | tier-spec-dashboard-consumer

## I. Frontmatter

| Field | Value |
|---|---|
| `priority` | P2 — dashboard UI may show stale tier info if not synced |
| `risk` | low — read-only client refactor with TanStack Query cache layer |
| `depends_on` | T-010 PR #1 merged + endpoint live |
| `estimated_minutes` | 60 |

---

## II. Goal

Add a `useTierSpec()` TanStack Query hook that fetches `GET /v1/tiers/spec` once per hour (matches backend cache TTL) and replace hardcoded tier matrices/quotas in 7 dashboard UI components. After this task, no number in dashboard source code duplicates `TIER_MATRIX` — all derived from the hook.

---

## III. Required Reading

- `pi-backend/TIER-MATRIX.md` — full schema (especially response shape)
- `pi-dashboard-webapp/src/_shared/` — find existing query/api setup pattern
- Components to refactor (7):
  - `src/features/system/components/license/FeatureMatrix.jsx`
  - `src/features/system/components/license/LicenseGate.jsx`
  - `src/features/system/components/license/TierBadge.jsx`
  - `src/features/system/components/license/LicenseInfoCard.jsx`
  - `src/features/billing/Subscription.jsx`
  - `src/features/ai/AiCloud.jsx`
  - `src/features/system/components/db-explorer/ProGate.jsx`

---

## IV. Allowed Scope

- 📄 `pi-dashboard-webapp/src/_shared/hooks/useTierSpec.js` (NEW) — TanStack Query hook, `staleTime: 3600000` (1h), key `['tier-spec']`. Returns `{ tiers, publicSlugs, isLoading, error }`. Helper `getTierBySlug(slug)`.
- 📄 7 component files listed in §III — replace hardcoded tier data with `useTierSpec()` consumption. Loading states: skeleton; error states: fall back to free-tier display (defensive).
- 📄 `pi-dashboard-webapp/src/_shared/api/client.js` (if exists) — add `GET /v1/tiers/spec` to base path config if not auto-resolved.

NEW hook + edits only. No new component files.

---

## V. Out Of Scope

- ❌ Pricing page UI redesign (tier card layouts) — pure data swap only
- ❌ Adding feature-gating logic (e.g., new `<FeatureGate>` component) — stretch goal, separate task
- ❌ Changing license/billing flow (Stripe integration etc.)
- ❌ Touching store webapp or pi-api plugin

---

## VI. Phases

1. **A — Snapshot** 5' — 9 files to `changes/T-012-*/before/`
2. **B — Hook** 10' — `useTierSpec.js` with TanStack Query. Test: log return shape.
3. **C — Component sweep** 30' — refactor 7 components one at a time. Visual smoke after each (dev server).
4. **D — Verify** 10' — `npm run lint && npm run build` exit 0. Manual smoke on dev: open admin → FeatureMatrix renders 4 tiers correctly, Subscription page shows price/quota from endpoint.
5. **E — Dossier + archive** 5'

---

## VII. Verification Commands

```bash
cd "C:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content/pi-dashboard-webapp"

# Lint + build
npm run lint
npm run build

# Verify no hardcoded tier numbers in src/ (allow legacy fallback constants)
grep -rn "50_000\|50000\|1_000_000\|1000000\|3_000_000\|3000000" src/ \
  | grep -v "useTierSpec.js" | grep -v ".test."
# Expect: no matches in active source

# Dev smoke
npm run dev
# Browse to localhost:5173 → admin → check FeatureMatrix + Subscription pages
```

---

## VIII. Acceptance Criteria

- [ ] `useTierSpec()` hook exists, TanStack Query-based, staleTime 1h.
- [ ] All 7 components consume hook (no hardcoded tier matrix in JSX).
- [ ] Loading state renders skeleton (no blank screen flash).
- [ ] Error state falls back gracefully (free-tier display + console warn).
- [ ] `npm run build` exit 0, bundle delta ≤ +2KB raw (TanStack Query already in deps).
- [ ] `npm run lint` zero new errors.
- [ ] Manual smoke: 4 tier cards render with correct $/quotas.
- [ ] Scope: only §IV files touched.

---

## IX. Worker Prompt

```text
You are claude. Execute T-20260517-012 per dossier. Depends on T-010
endpoint live on Railway.

Scope: 1 new hook + 7 JSX edits in pi-dashboard-webapp. Risk=low.
Standard archive flow. Run dev server during component sweep to visually
verify each refactor before moving to next.
```

---

## X. Agent Result
Status: `not-started`.

## XI-XVI — pending (filled at execution time)
- Standard rollback: snapshot restore from `changes/T-012-*/before/`
- **2026-05-18 09:31**: State drafted → dispatched
- **2026-05-18 09:31**: State dispatched → returned
- **2026-05-18 09:31**: State returned → verified
