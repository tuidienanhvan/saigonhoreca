---
id: T-20260517-013
owner: claude
state: archived
priority: P3
risk: low
estimated_minutes: 45
parent: T-20260517-010
children: []
depends_on: [T-20260517-010]
parallelization_ok: true
retry_count: 0
retry_max: 1
escalation_path: [Codex, Gemini]
created: 2026-05-17 20:13
updated: 2026-05-18 09:32
archived: 2026-05-18 09:32
---

## 0. 📥 User Original Intent (Phase 0 — Verbatim)

> **Split from T-20260517-010** per user "Option Tách nhỏ" — Phase E (pi-store-webapp pricing page consumer sync).
>
> Parent context: T-010 archived backend SoT. T-011/T-012 cover pi-api + dashboard consumers. This is the final consumer in T-010 series.

---

# 📋 T-20260517-013 | claude | tier-spec-store-consumer

## I. Frontmatter

| Field | Value |
|---|---|
| `priority` | P3 — store pricing page is customer-facing but no real customer onboarded yet |
| `risk` | low — read-only refactor |
| `depends_on` | T-010 PR #1 merged + endpoint live |
| `estimated_minutes` | 45 |

---

## II. Goal

Pricing page in store-webapp currently hardcodes prices, quotas, and feature lists across 5 components. Replace with fetch from `/v1/tiers/spec` so changing a number in `TIER_MATRIX` propagates to the public pricing page within 1 hour (TanStack Query stale time).

After this, when product team changes Pro price $29 → $39, only one edit needed (`app/saas/tiers.py`), customer pricing page updates automatically without rebuild.

---

## III. Required Reading

- `pi-backend/TIER-MATRIX.md`
- `pi-store-webapp/src/features/pricing/` — full component tree
- 5 components:
  - `PricingGrid.jsx` — orchestrator
  - `PricingCard.jsx` — individual tier card
  - `PricingBundles.jsx` — combo offerings (if tier-aware)
  - `PricingFAQ.jsx` — references prices/quotas in copy
  - `PricingEnterprise.jsx` — enterprise tier section
- Existing API client pattern in `pi-store-webapp/src/_shared/` (TBD audit)

---

## IV. Allowed Scope

- 📄 `pi-store-webapp/src/_shared/hooks/useTierSpec.js` (NEW) — similar pattern to T-012's hook but for store webapp's TanStack setup (verify it has TanStack — if not, use native fetch + Suspense). 1h cache.
- 📄 `pi-store-webapp/src/features/pricing/components/PricingGrid.jsx` — fetch spec, render N PricingCard components from `public_slugs`.
- 📄 `pi-store-webapp/src/features/pricing/components/PricingCard.jsx` — accept `tierSpec` prop instead of hardcoded `{ price, tokens, features }`.
- 📄 `pi-store-webapp/src/features/pricing/components/PricingBundles.jsx` — sync any tier references.
- 📄 `pi-store-webapp/src/features/pricing/components/PricingFAQ.jsx` — interpolate prices/quotas from spec into FAQ entries (or accept as props).
- 📄 `pi-store-webapp/src/features/pricing/components/PricingEnterprise.jsx` — enterprise card pulls `tier_spec('enterprise')`.

---

## V. Out Of Scope

- ❌ Visual redesign — pixel-identical to current except data source
- ❌ Adding new CTA buttons, comparison tables, etc.
- ❌ Stripe checkout button changes
- ❌ FAQ content changes beyond price/quota interpolation
- ❌ Dashboard webapp (T-012)

---

## VI. Phases

1. **A — Snapshot** 5'
2. **B — Audit store-webapp API client** 5' — find/confirm TanStack or alternative pattern
3. **C — Hook** 10' — `useTierSpec()` (may differ slightly from T-012 hook if store webapp uses different query lib)
4. **D — Component sweep** 20' — 5 components refactored
5. **E — Verify** 5' — `npm run build && npm run lint`, manual smoke

---

## VII. Verification Commands

```bash
cd "C:/Users/Administrator/Local Sites/saigonhouse/app/public/wp-content/pi-store-webapp"

npm run lint
npm run build

# Verify no hardcoded prices/tokens
grep -rn "\$29\|\$99\|50_000\|1_000_000\|3_000_000" src/features/pricing/ \
  | grep -v "useTierSpec.js" | grep -v ".test."

# Dev smoke
npm run dev
# Browse to localhost:5173/pricing → confirm 3 tier cards (free + pro + max) +
# enterprise section all render with backend-fetched data
```

---

## VIII. Acceptance Criteria

- [ ] `useTierSpec()` hook exists in store webapp (idiomatic to that codebase).
- [ ] All 5 pricing components consume hook.
- [ ] Loading state: skeleton.
- [ ] Error state: hide pricing grid, show "Pricing temporarily unavailable. [Refresh]" notice.
- [ ] `public_slugs` from response drives which cards render (3 today: free/pro/max).
- [ ] Enterprise section pulls its own spec via `tier_spec('enterprise')`.
- [ ] Build + lint clean.
- [ ] Manual smoke: pricing page pixel-identical to before (data sourced from endpoint).

---

## IX. Worker Prompt

```text
You are claude. Execute T-20260517-013 per dossier. Depends on T-010
endpoint live + recommend doing AFTER T-012 (dashboard) so hook
pattern is proven first.

Scope: 1 new hook + 5 JSX edits in pi-store-webapp. Risk=low.
Standard archive flow.
```

---

## X. Agent Result
Status: `not-started`.

## XI-XVI — pending
- Standard rollback: snapshot restore from `changes/T-013-*/before/`
- **2026-05-18 09:31**: State drafted → dispatched
- **2026-05-18 09:31**: State dispatched → returned
- **2026-05-18 09:32**: State returned → verified
