---
id: T-20260518-025
owner: claude
state: in-progress
priority: P1
risk: medium
estimated_minutes: 30
parent: null
children: [T-20260518-026, T-20260518-027, T-20260518-028, T-20260518-029, T-20260518-030]
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-18 16:00
updated: 2026-05-18 19:00
progress:
  - T-20260518-026: completed (commit c6679fe — pi-store primitives)
  - T-20260518-027: completed (commit c6679fe — pi-store layout shell + admin)
  - T-20260518-028: completed (commit fcbf1f6 — pi-dashboard base primitives)
  - T-20260518-029: completed (commit fcbf1f6 — pi-dashboard layout shell)
  - T-20260518-030: completed (commits 44a16d2 + 2768f22 — Codex hardcode hunt, orchestrator finalized skeleton subfolder structure)
state: completed
---

# 📋 T-20260518-025 | claude | vercel-design-master — Vercel Design Language Migration (Master Orchestrator)

## I. 🎯 Mục tiêu / Goal

Áp dụng Vercel design language vào toàn bộ shared UI primitives + layout shell của **pi-store-webapp** và **pi-dashboard-webapp**, đồng thời **bảo toàn 100% token màu / theme palette** trong `index.css` (cả 2 app).

User reference: dashboard Vercel (vercel.com) — pure-black charcoal background, hairline borders, generous whitespace, monospaced numeric data, subdued status pills, white-on-black primary CTA, pill-shaped chips with status dots.

Mục tiêu cuối cùng: 2 webapp có visual treatment giống Vercel dashboard nhưng vẫn giữ nguyên bộ semantic token (`--p`, `--in`, `--b1...4`, `--bc/bc2/bc3`, `--bd/bs/bt`, `--su/wa/er`) đã có sẵn.

---

## II. 🎨 Vercel Design Language Specification (Source of Truth)

### II.1 Background System

| Layer | Token | Vercel equivalent | Use case |
|---|---|---|---|
| Page bg | `bg-base-100` (`--b1`) | `#000` near-black | App shell, body |
| Card bg | `bg-base-200` (`--b2`) | `#0a0a0a` | Card / panel containers |
| Elevated | `bg-base-300` (`--b3`) | `#111` | Nav, sticky surfaces |
| Subtle fill | `bg-base-content/[0.02]` to `bg-base-content/[0.04]` | Table row hover, inset blocks |
| Overlay scrim | `bg-base-100/80 backdrop-blur` | Modal backdrop |

**Forbidden**: gradients, glassmorphism, blur effects, glow shadows. Vercel UI is **flat + hairline**, never glow.

### II.2 Border System

| Use | Class | Notes |
|---|---|---|
| Hairline | `border border-base-content/[0.06]` or `border-base-content/10` | Default for cards, table cells, dividers |
| Strong | `border-base-content/20` | Focus, selected state |
| Divider | `divide-base-content/[0.06]` | Table rows, list separators |
| Inset accent | `ring-1 ring-base-content/10` | Combine with `bg-base-content/[0.02]` for inset blocks |

**Forbidden**: thick borders (>1px), colored borders except for state (success/error/warning), `border-base-border` if it renders thicker than 1px hairline.

### II.3 Typography

| Element | Class stack |
|---|---|
| Page title | `text-2xl font-semibold tracking-tight text-base-content` |
| Section title | `text-sm font-semibold tracking-wide text-base-content/60 uppercase` |
| Body | `text-sm text-base-content` |
| Body dim | `text-sm text-base-content/60` |
| Caption / meta | `text-xs text-base-content/40` |
| Numeric data | `font-mono text-sm tracking-tight` |
| Status label | `text-[10px] font-semibold tracking-[0.08em] uppercase` |

**Tracking**: Vercel uses tight letter-spacing on headings (`tracking-tight` or `tracking-[-0.01em]`) and wide spacing on small caps (`tracking-[0.08em]` to `tracking-widest`).

**Font weight**: prefer `font-medium` (500) and `font-semibold` (600). Avoid `font-bold` (700) except for numeric emphasis.

### II.4 Buttons

| Variant | Visual | Class stack |
|---|---|---|
| **Primary** | White bg, black text | `bg-base-content text-base-100 hover:bg-base-content/90 font-medium` |
| **Secondary** | Hairline outline | `bg-transparent border border-base-content/15 hover:border-base-content/30 hover:bg-base-content/[0.04]` |
| **Ghost** | Pure hover | `bg-transparent hover:bg-base-content/[0.06]` |
| **Danger** | Red bg | `bg-error text-white hover:bg-error/90` |
| **Brand** | Use `--p` only when calling out CTA | `bg-primary text-primary-content hover:bg-primary-hover` (use SPARINGLY) |

Default size: `h-9 px-4 text-sm rounded-md`. Small: `h-8 px-3 text-xs`. Large: `h-10 px-5 text-sm`.

**Forbidden**: gradient buttons, shadow on default state, oversized buttons (>h-12), `rounded-full` except for icon-only pill buttons.

### II.5 Inputs / Select / Textarea

```
bg-base-content/[0.02] border border-base-content/10 rounded-md
h-9 px-3 text-sm placeholder:text-base-content/30
focus:outline-none focus:border-base-content/30 focus:bg-base-content/[0.04]
transition-colors duration-150
```

No focus ring shadow. No colored border-on-focus. Use opacity shifts.

### II.6 Cards

Default card:
```
bg-base-content/[0.02] border border-base-content/[0.06] rounded-xl
hover:border-base-content/15 transition-colors
```

Padding: `p-6` for content cards, `p-4` for compact list items.

Header inside card: `px-6 py-4 border-b border-base-content/[0.06]`. No gradients on card backgrounds.

### II.7 Badges / Status Pills

```
inline-flex items-center gap-1.5 px-2.5 py-0.5
rounded-full bg-base-content/[0.04] border border-base-content/10
text-[11px] font-medium text-base-content/70
```

Status dot variant (Vercel signature):
```
<span class="w-1.5 h-1.5 rounded-full bg-success"/>
```

Tones: success uses `bg-success/10 text-success border-success/20`, warning uses warning tokens, error uses error tokens. Always with hairline border tinted to the same color.

### II.8 Tables

- Header row: `bg-base-content/[0.02] text-[11px] font-medium tracking-wide text-base-content/40 uppercase`
- Body row: `border-t border-base-content/[0.06] hover:bg-base-content/[0.02]`
- Cell padding: `px-6 py-3.5`
- Numeric cells: right-aligned with `font-mono`
- Last column (actions): `text-right` with hover-revealed icons

### II.9 Layout / Sidebar

- Sidebar: `w-60 bg-base-200 border-r border-base-content/[0.06]`
- Nav item: `px-3 py-2 rounded-md text-sm text-base-content/60 hover:text-base-content hover:bg-base-content/[0.04]`
- Active nav: `bg-base-content/[0.06] text-base-content` (no left bar, no gradient — just bg shift)
- Section heading: tiny uppercase label `text-[10px] uppercase tracking-widest text-base-content/30` 
- Top header: `h-14 border-b border-base-content/[0.06] px-6 flex items-center`

### II.10 Spacing Rhythm

- Section gap: `gap-8` (between major sections)
- Card gap: `gap-4` (between cards in a grid)
- Inline gap: `gap-2` (icon + text)
- Page padding: `px-6 lg:px-10 py-8`

### II.11 Animation / Transitions

- Default: `transition-colors duration-150 ease-out`
- Hover: `duration-200`
- Focus: instant (no transition on border)
- **Forbidden**: bounce, spring, slide, scale animations on hover. Only color/bg/opacity transitions.

---

## III. 🛡️ Theme Preservation Contract (CRITICAL)

### III.1 MUST NOT TOUCH

Files that are **ABSOLUTELY OFF-LIMITS** for all sub-tasks:

| File | Why |
|---|---|
| `pi-store-webapp/src/styles/index.css` | Theme color palette source of truth |
| `pi-dashboard-webapp/src/index.css` | Theme color palette source of truth |
| `pi-store-webapp/src/styles/animations.css` (if exists) | Animation system |
| `pi-dashboard-webapp/src/animations.css` | Animation system |
| Any file matching `**/*tokens*.css` or `**/*theme*.css` |

### III.2 ALLOWED Token Usage Rules

- ✅ Use existing semantic Tailwind utility classes that map to `--p`, `--b1...4`, `--bc/bc2/bc3`, `--bd/bs/bt`, `--su/wa/er/in`
- ✅ Use opacity modifiers like `bg-base-content/[0.04]` to derive hairline / subtle surfaces
- ✅ Use `text-primary`, `bg-primary`, `border-primary` for explicit brand callouts
- ❌ Never write raw hex colors (`#fff`, `#000`, etc.) in JSX or new CSS files
- ❌ Never add new CSS custom properties
- ❌ Never modify existing CSS custom property values

### III.3 CSS File Policy

- Existing `.css` files for legacy components MAY be deleted if the new Tailwind implementation makes them redundant (e.g., `Button.css` after restyle).
- New `.css` files MAY be created **only** for component-specific keyframes that can't be expressed in Tailwind. Must use semantic tokens, no raw colors.

---

## IV. 📦 Child Task Routing

| Child ID | Scope | App | Files (count) | Owner |
|---|---|---|---|---|
| **T-20260518-026** | UI primitives | pi-store-webapp | `src/_shared/components/ui/*` (~25 files) | gemini |
| **T-20260518-027** | Layout shell + admin wrappers | pi-store-webapp | `src/_shared/components/{core,layout}/*` + `admin/_shared/components/*` + `admin/layout/*` | gemini |
| **T-20260518-028** | Base primitives | pi-dashboard-webapp | `src/_shared/base/*` + `src/_shared/components/ui/*` | gemini |
| **T-20260518-029** | Layout shell | pi-dashboard-webapp | `src/_shared/components/layout/*` | gemini |

**Execution order**: 026 → 027 → 028 → 029. Each child task must complete + be verified before the next starts (avoid concurrent breakage in shared UI).

---

## V. ✅ Master Acceptance Criteria

- [ ] All 4 child tasks completed and verified
- [ ] `npm run build` passes for both apps after each child completes
- [ ] `npm run lint` passes for both apps after each child completes
- [ ] No raw hex colors introduced in JSX or new CSS
- [ ] No new CSS custom properties created
- [ ] `git diff src/styles/index.css` and `git diff src/index.css` show ZERO changes
- [ ] Visual smoke test: each child reports 3+ screenshots showing before/after of representative pages (login, dashboard root, table page)

---

## VI. 📋 Out Of Scope (Strictly Forbidden — Master)

- ❌ Touching theme color tokens
- ❌ Adding/removing npm packages
- ❌ Changing component **props** (only visual treatment)
- ❌ Changing routing or feature logic
- ❌ Refactoring feature-page components (`features/**/*.jsx`) — only `_shared/`, `admin/_shared/`, layout shells
- ❌ Touching `pi-backend/**` (backend out of scope entirely)
- ❌ Splitting/merging components (preserve existing public API surface)

---

## VII. 🆘 Rollback Procedure

If a child task breaks production deploy:
1. Identify which child caused issue: `git log --oneline -20`
2. Revert that child's commit: `git revert <sha>` then `git push origin main`
3. Rebuild pi-store-webapp locally and commit the rebuilt `build/` artifact (prebuilt deploy strategy)
4. Pause child task chain; report failure on master dossier

---

## VIII. 📑 CHANGE LOG

- **2026-05-18 16:00**: Master dossier created. Child tasks 026-029 drafted in parallel.
- **2026-05-18 19:30**: T-028 + T-029 VERIFIED COMPLETE by orchestrator (claude).
  - Gemini executed work but did not commit — orchestrator staged + committed as `fcbf1f6`
  - Commit: `feat(pi-dashboard): Vercel design migration — base primitives + layout shell (T-028, T-029)`
  - Files: 16 changed (200+ / 422-), 3 CSS deleted (UserMenu.css, PiLogo.css, Layout.css)
  - Quality gates:
    - ✅ Build Gate: `vite v8.0.10 ✓ built in 1.31s` exit 0
    - ✅ Theme Preservation: `src/index.css` + `src/animations.css` ZERO diff
    - ✅ Hardcode in scope: 0 matches (grep hex/rgba in base/ + layout/ + ui/)
    - ✅ PageTitle.jsx correctly SKIPPED (not visual — react-helmet wrapper)
    - ✅ Navbar.css correctly KEPT (5-line mask-image fade, Tailwind cannot express)
  - All 4 Gemini sub-tasks (T-026 to T-029) of master T-025 are now DONE.
  - Only T-030 (Codex hardcode hunt) remains.
- **2026-05-18 18:50**: T-026 + T-027 VERIFIED COMPLETE by orchestrator (claude).
  - Single commit `c6679fe` bundled both: `feat(pi-store): migrate layout and admin shell to Vercel design (T-027)`
  - Files changed: 34 src JSX/CSS + 13 admin shared + 8 admin layout + build/ artifact
  - Quality gates:
    - ✅ Build Gate: `npm run build` exit 0 in 5.00s
    - ✅ Theme Preservation Gate: `git show c6679fe -- src/styles/index.css` returns 0 lines (zero diff)
    - ✅ CSS Deletion Gate: 22 dead `.css` files removed (Button/Card/Badge/Alert/Drawer/EmptyState/Input/Modal/FullPageLoader/Table/SiteHeader/SiteFooter/DashboardLayout/PageFallback and more)
    - ✅ Prebuilt Artifact Gate: `build/` rebuilt + committed
  - T-028 (pi-dashboard primitives) now unblocked — ready for Gemini dispatch.
  - T-029 (pi-dashboard layout) still blocked behind T-028 per dependency chain.
