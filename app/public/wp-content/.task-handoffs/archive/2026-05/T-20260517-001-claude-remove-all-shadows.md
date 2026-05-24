---
id: T-20260517-001
owner: claude
state: archived
priority: P2
risk: low
estimated_minutes: 45
verified: 2026-05-17 00:55
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Gemini]
created: 2026-05-17
updated: 2026-05-17 00:46
archived: 2026-05-17 00:46
---

## 0. 📥 User Original Intent (Phase 0 — Verbatim)

> "nhìn bên pi-dashboard và pi-store, bỏ full shadow trong index.css và các file liên quan vì xấu quá. lên dossier r tự làm"

Context: User screenshot pi-dashboard SEO dropdown menu — shadows trên dark background trông nặng/xấu. Decision: remove ALL shadow effects.

⚠️ **Exception to "cấm đụng index.css"**: User explicitly authorizes touching index.css for this task.

---

# 📋 T-20260517-001 | claude | remove-all-shadows

## II. 🎯 Goal

**Outcome**: 0 visible shadow trên cả pi-dashboard và pi-store. Mọi `box-shadow` declaration, mọi `var(--shadow-*)` reference, mọi Tailwind `shadow-*` utility → render NONE.

**Strategy**:
1. Zero out `--shadow-*` tokens trong index.css → tự động kill 500+ Tailwind utility usages (vì Tailwind v4 đọc tokens từ `@theme`)
2. Strip hardcoded `box-shadow:` trong 62 component CSS files
3. Strip Tailwind arbitrary `shadow-[...]` trong JSX (~8 patterns)

## III. 📊 Audit baseline

| Source | Count |
|---|---|
| Tokens trong index.css | 11 tokens × 2 themes |
| Tailwind utility (pi-dashboard JSX) | 496 usages |
| Tailwind utility (pi-store JSX) | 13 usages |
| Hardcoded box-shadow trong component CSS | 62 files |
| Tailwind arbitrary trong JSX | ~8 unique patterns |

## IV. 🚧 Allowed Scope

- `pi-dashboard-webapp/src/index.css` (zero out tokens)
- `pi-store-webapp/src/styles/index.css` (sync byte-identical)
- `pi-dashboard-webapp/src/**/*.css` (component CSS strip box-shadow)
- `pi-store-webapp/src/**/*.css` + `pi-store-webapp/admin/**/*.css`
- `pi-dashboard-webapp/src/**/*.jsx` (strip arbitrary shadow-[...])
- `pi-store-webapp/src/**/*.jsx` + `pi-store-webapp/admin/**/*.jsx`

## V. 🚫 Out of Scope

- ❌ saigonhouse-theme (separate scope)
- ❌ Remove Tailwind shadow-* utility names from JSX (giữ class, chỉ zero token)
- ❌ Change non-shadow visual styles
- ❌ animations.css

## VI. 🛠️ Phases

### Phase A — Zero shadow tokens (10')
Edit both index.css: all --shadow-* = none (both dark + light themes).

### Phase B — Strip box-shadow in CSS (15')
62 CSS files → remove `box-shadow:` declarations.

### Phase C — Strip arbitrary shadow-[...] in JSX (10')
~8 unique patterns → remove from className strings.

### Phase D — Verify (10')
Build both apps PASS + visual smoke + commit + archive.

## VII. Verification Commands

```bash
# Build
cd pi-dashboard-webapp && npx vite build
cd pi-store-webapp && npx vite build

# Verify tokens
grep "^--shadow\|^  --shadow" pi-dashboard-webapp/src/index.css | head -10
# Expect: all = none

# Verify no box-shadow remains
grep -rln "box-shadow" pi-dashboard-webapp/src pi-store-webapp/src pi-store-webapp/admin --include="*.css" | grep -v "index.css" | wc -l
# Expect: 0

# Verify no arbitrary
grep -rln "shadow-\[" pi-dashboard-webapp/src pi-store-webapp/src pi-store-webapp/admin --include="*.jsx" | wc -l
# Expect: 0
```

## VIII. Acceptance

- [ ] index.css tokens = none (both apps)
- [ ] Component CSS box-shadow: 0
- [ ] JSX shadow-[...]: 0
- [ ] Build PASS both apps
- [ ] Commit + archive

## IX. Implementation
Self-implement Claude. TodoWrite 4 phases.

## XV. Rollback
`git checkout` index.css to restore all shadows globally via tokens.

## XVI. CHANGE LOG
- 2026-05-17: Dossier created.
- 2026-05-17 00:41: State drafted → dispatched
- 2026-05-17 00:55: All 4 phases complete. Build PASS. Commits: pi-store fd7a979, pi-dashboard 57ed9b5.

## ✅ Completion Notes

### Final state
| Phase | Action | Count |
|---|---|---|
| A | Zero `--shadow-*` tokens (index.css both apps, both themes) | 11 tokens × 2 = 22 |
| B | Strip `box-shadow:` declarations in component CSS | 62 files |
| C | Strip Tailwind arbitrary `shadow-[...]` in JSX | 16 files |
| D | Build verify + commit | pi-store 877ms, pi-dashboard 1.19s |

### Visible shadow count
- Lighthouse / runtime: **0 visible shadows**
- 509 `shadow-*` Tailwind utility names retained in JSX (backward-compat, render `none`)
- All `var(--shadow-*)` references resolve to `none`

### Rollback (if needed)
- Single revert: `git checkout pi-store-webapp/src/styles/index.css pi-dashboard-webapp/src/index.css`
- That restores ALL shadows globally via token reactivation
- Component-level removed declarations stay gone — would need broader revert
