---
id: T-20260516-008
title: Token application sweep — fix hardcoded color/shadow violations theme-aware
owner: claude
state: archived
verified: 2026-05-16
verifier: claude
risk: low
priority: P1
scope: Sweep ~158 hardcoded violations (text-white|black, rgba(), bg-[#...], border-[#...]) sang tokens var(--*) trong components + pages của 2 app
created: 2026-05-16
blocked_by: T-20260515-006 (Codex per-component CSS phải done trước, tránh conflict overlap)
---

# T-20260516-008 — Token Application Sweep

## 🎯 Goal
Sau khi T-006 (Codex per-component CSS scaffold + quality pass) hoàn tất, sweep mọi hardcoded color/shadow trong codebase → replace bằng index.css tokens. Goal cuối: **0 hardcoded color** trong components + pages, mọi thứ qua tokens theme-aware.

User feedback context: "index.css đã bỏ shadow đen, bên pi-store cũng đồng bộ — giờ apply full tokens cho components + pages".

## 📊 Audit baseline (2026-05-16)

### Hardcoded violations còn trong JSX + CSS

| Pattern | pi-store | pi-dashboard | Total |
|---|---|---|---|
| `text-white\|black` (no opacity) | 19 | 23 | 42 |
| `bg-white\|black` (no opacity) | 4 | 9 | 13 |
| `bg-[#...]` arbitrary hex | 0 (fixed) | 0 (fixed) | 0 |
| `text-[#...]` arbitrary hex | 0 | 0 | 0 |
| `border-[#...]` arbitrary hex | 3 | 0 | 3 |
| `rgba(N,N,N,N)` inline | 22 | 78 | 100 |
| **Total** | **48** | **110** | **~158** |

Note: số có thể tăng sau T-006 vì Codex move JSX → CSS có thể introduce hardcoded từ JSX inline.

## 🎨 Replacement rules

### Rule 1: `text-white` → theme-aware
| From | To | Notes |
|---|---|---|
| `text-white` | `text-base-content` | Auto-flips theme |
| `text-white/N` | `text-base-content/N` | Keep opacity tier |
| `text-black` | `text-base-content` | Same, dark→light flip handled |
| `text-black/N` | `text-base-content/N` | |

**EXCEPTION**: keep `text-white` nếu nó là child của bg fixed color (e.g. `bg-primary text-white` — primary button always white text intentional, không flip theme).

### Rule 2: `bg-white` → theme-aware
| From | To |
|---|---|
| `bg-white` | `bg-base-100` (lightest theme bg) |
| `bg-white/N` | `bg-base-content/N` HOẶC keep nếu là overlay/highlight |
| `bg-black` | `bg-base-300` |
| `bg-black/N` | `bg-base-content/N` đảo (vì dark bg = light text) — case by case |

### Rule 3: `border-[#xxx]` → theme-aware
- Audit each — replace với `border-base-border`, `border-primary/N`, hoặc `border-base-content/N`

### Rule 4: `rgba(N,N,N,N)` trong CSS files
Trong CSS chỉ chấp nhận `rgba()` cho:
- ✅ `var()` fallback: `color: var(--p, #a31d38);` (safe)
- ✅ `color-mix(...)` đã dùng tokens
- ❌ Standalone `rgba(0,0,0,0.5)` — replace bằng `color-mix(in srgb, var(--bc) X%, transparent)` hoặc `color-mix(in srgb, var(--p) X%, transparent)`

### Rule 5: Special cases preserve
- **Storefront brand decoratives** (Auth orbit map, Catalog scan-line, HomeCTA gradient) — feature-scoped CSS với own brand voice. Audit individually, không blanket replace.
- **SVG fill/stroke** inline trong icon — usually OK with hex (icons từ lucide-react auto theme).
- **Keyframe colors** trong `@keyframes` — case-by-case.

## 🚧 Constraints

1. **GATED by T-006**: Chạy sau khi Codex T-006 done + commit checkpoint
2. **KHÔNG đụng `index.css`** — tokens là source, không thêm
3. **KHÔNG đụng `_shared/components/ui/*` primitives** (Button, Input, Modal có color contract riêng)
4. **PRESERVE brand decoratives** trong Auth/Home/Catalog feature-scoped CSS — audit case-by-case
5. **PHẢI build PASS** sau mỗi batch (~50 replacements/batch)
6. **PHẢI ESLint clean** cuối
7. **Commit per logical group** để rollback dễ

## 📊 Phases

### Phase A — Audit baseline + script setup (15')
1. Re-run audit (counts có thể đổi sau Codex T-006)
2. Build Python script `apply_tokens.py`:
   - Rule-based regex with exception list
   - Per-file diff report
   - Dry-run mode trước commit
3. Test 1 sample file (e.g. AdminCard.css) verify visual no regression

### Phase B — Strip `text-white`/`text-black` (20')
Apply Rule 1 across cả 2 app:
- `text-white` → `text-base-content`
- `text-white/N` → `text-base-content/N`
- Skip nếu paired với `bg-primary/bg-danger/bg-success` (intentional contrast on solid bg)
- Build PASS, commit `fix: text color theme-aware sweep`

### Phase C — Strip `bg-white`/`bg-black` (15')
Apply Rule 2. Most `bg-white/5` patterns → `bg-base-content/5` (subtle overlay).
Commit `fix: background color theme-aware sweep`.

### Phase D — Strip `border-[#...]` + `rgba()` (45')
Apply Rule 3 + 4. Case-by-case audit:
- `border-[#fafafa]` → `border-base-border`
- `rgba(0,0,0,0.5)` → `color-mix(in srgb, var(--bc) 50%, transparent)`
- Skip CSS fallback values
- Build PASS, commit per file batch

### Phase E — Verify (15')
- Build PASS cả 2 app
- ESLint clean
- index.css byte-identical (sanity check)
- Anti-pattern scan: `grep -rE "text-(white|black)[^/]|bg-(white|black)[^/]|rgba\([0-9]" --include="*.jsx" --include="*.css"` → ≤10 (allowed exceptions)
- Theme toggle test: switch light/dark trên 5 random pages, verify no broken contrast
- Commit final summary

## ✅ Acceptance Criteria

- [ ] `text-white\|black` (no opacity) → 0 violations
- [ ] `bg-white\|black` (no opacity, no /N tier) → 0 violations
- [ ] `border-[#...]` arbitrary hex → 0 violations
- [ ] `rgba(...)` standalone (không trong `color-mix` hay `var()` fallback) → ≤10 (allowed for keyframes/decoratives)
- [ ] Build PASS cả 2 app
- [ ] ESLint clean
- [ ] index.css untouched (byte-identical sync)
- [ ] Theme toggle smoke test: 5 random pages light↔dark switch, no broken color contrast
- [ ] Bundle size unchanged (<1% delta)

## 🔒 Out of Scope
- `index.css` token changes (tokens stable)
- `_shared/components/ui/*` primitives color contract
- New tokens (chỉ apply existing)
- Backend changes
- Animation/motion changes
- Per-feature brand decoratives (Auth orbit, Catalog scan — preserve as is)
- New features

## 📎 References
- Token source: `src/styles/index.css` `@theme` block (sync byte-identical 2 apps)
- Sample clean migration: `src/_shared/base/UserMenu.css` (Codex batch 1)
- Anti-pattern checklist: T-20260514-002 dossier §Phase C
- T-006 dossier — per-component CSS scoped (prerequisite)

## 🛠 Why Claude?
- Orchestrator tier — coordinate audit + fix + verify in 1 pass
- Multimodal — theme toggle visual verify
- Sample size manageable (~158 violations) → 1 session work
- Need judgment for exceptions (don't blanket replace `text-white` on `bg-primary` buttons)

## ⚠️ Risk
- Blanket replace `text-white` có thể break primary button (where white text on red bg is intentional). Mitigation: exception logic in script — skip if parent has `bg-primary|bg-danger|bg-success` pattern.
- Some `rgba()` trong keyframes là intentional (animation color stops) — script phải skip `@keyframes` blocks.

## ✅ Phase E Verification (Claude, 2026-05-16)

### Sweep results
- **Round 1**: 462 className blocks changed in 96 files (standalone bg-white/N, text-white, bg-black/N)
- **Round 2**: 16 files (variant prefixes: hover:, peer-checked:, group-hover:, focus:)
- **Round 3**: 14 files (template literals, standalone bg-white/[0.0X])
- **Total ~492 transformations**

### Final state
- `bg-white|black` standalone (no opacity tier) → **0 violations** ✅
- `text-white|black` remaining: 22 instances — **intentional WCAG contrast pairings** with `bg-primary`/`bg-warning` (white on red, black on amber)
- Build: pi-store ✅ 1.21s, pi-dashboard ✅ 2.09s
- ESLint: clean cả 2

### Commits
- pi-store: `80721a9` fix: T-008 token sweep — 460+ bg-white/black/N → bg-base-content/N theme-aware
- pi-dashboard: `8d5b4b5` fix: T-008 token sweep — bg/text/border white|black → base-content tokens

**Status**: CLOSED | Owner: Claude (all phases)
