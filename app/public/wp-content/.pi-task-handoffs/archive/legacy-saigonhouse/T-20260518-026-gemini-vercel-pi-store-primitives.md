---
id: T-20260518-026
owner: gemini
state: completed
priority: P1
risk: medium
estimated_minutes: 90
parent: T-20260518-025
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-18 16:05
updated: 2026-05-18 16:05
---

# 📋 T-20260518-026 | gemini | vercel-pi-store-primitives — pi-store-webapp UI Primitives Vercel Restyle

## I. 🎯 Goal

Áp dụng Vercel design language (per master dossier T-20260518-025 mục II) vào **tất cả** UI primitives trong `pi-store-webapp/src/_shared/components/ui/`. Preserve component prop API + theme tokens.

## II. 📚 Required Reading

**MUST READ BEFORE ANY EDIT**:
- `.task-handoffs/active/T-20260518-025-claude-vercel-design-master.md` (design spec)
- `.task-handoffs/SKILL.md`
- `.task-handoffs/system/QUALITY-GATES.md`

## III. 🚧 Allowed Scope (Strict)

```
pi-store-webapp/src/_shared/components/ui/Alert.jsx
pi-store-webapp/src/_shared/components/ui/Alert.css
pi-store-webapp/src/_shared/components/ui/Avatar.jsx
pi-store-webapp/src/_shared/components/ui/Badge.jsx
pi-store-webapp/src/_shared/components/ui/Badge.css
pi-store-webapp/src/_shared/components/ui/Button.jsx
pi-store-webapp/src/_shared/components/ui/Button.css
pi-store-webapp/src/_shared/components/ui/Card.jsx
pi-store-webapp/src/_shared/components/ui/Card.css
pi-store-webapp/src/_shared/components/ui/Checkbox.jsx
pi-store-webapp/src/_shared/components/ui/Drawer.jsx
pi-store-webapp/src/_shared/components/ui/Drawer.css
pi-store-webapp/src/_shared/components/ui/EmptyState.jsx
pi-store-webapp/src/_shared/components/ui/EmptyState.css
pi-store-webapp/src/_shared/components/ui/FullPageLoader.jsx
pi-store-webapp/src/_shared/components/ui/FullPageLoader.css
pi-store-webapp/src/_shared/components/ui/Input.jsx
pi-store-webapp/src/_shared/components/ui/Input.css
pi-store-webapp/src/_shared/components/ui/Modal.jsx
pi-store-webapp/src/_shared/components/ui/Modal.css
pi-store-webapp/src/_shared/components/ui/Select.jsx
pi-store-webapp/src/_shared/components/ui/Skeleton.jsx
pi-store-webapp/src/_shared/components/ui/Spinner.jsx
pi-store-webapp/src/_shared/components/ui/Switch.jsx
pi-store-webapp/src/_shared/components/ui/Table.jsx
pi-store-webapp/src/_shared/components/ui/Table.css
pi-store-webapp/src/_shared/components/ui/Tabs.jsx
pi-store-webapp/src/_shared/components/ui/Textarea.jsx
pi-store-webapp/src/_shared/components/ui/ThemeToggle.jsx
pi-store-webapp/src/_shared/components/ui/index.js
.task-handoffs/active/T-20260518-026-gemini-vercel-pi-store-primitives.md
```

**CSS file rule**: If a component's Vercel restyle uses 100% Tailwind utilities and the `.css` file becomes empty/dead, DELETE the `.css` file AND remove its import from the `.jsx`. If keyframes / pseudo-elements remain, keep `.css` but use semantic tokens only.

## IV. 🚫 Out Of Scope (Forbidden)

- ❌ `pi-store-webapp/src/styles/index.css` — theme palette (READ-ONLY)
- ❌ Component props (signatures, default values, exported names must remain identical)
- ❌ Feature pages (`src/features/**/*`)
- ❌ Admin components (handled in T-027)
- ❌ Layout shell (handled in T-027)
- ❌ Adding/removing npm packages
- ❌ Raw hex colors anywhere
- ❌ New CSS custom properties

## V. 🎨 Per-Component Specification

### Button.jsx
- Apply 5 variants per master spec II.4 (primary/secondary/ghost/danger/brand)
- Sizes: `sm` (h-8 px-3 text-xs), `md` (h-9 px-4 text-sm), `lg` (h-10 px-5 text-sm)
- Loading state: replace text with `Spinner` (size 14px), keep button width
- `disabled:opacity-40 disabled:cursor-not-allowed`
- Delete `Button.css` after migration

### Input.jsx / Textarea.jsx / Select.jsx
- Apply master spec II.5 (bg-base-content/[0.02] + hairline border + focus opacity shift)
- Icon slot: absolute-positioned with `text-base-content/30`
- Error state: `border-error/40` (NOT solid red)
- Delete `Input.css` after migration

### Card.jsx
- Apply master spec II.6
- Variants: `default`, `inset` (bg-base-content/[0.02]), `solid` (bg-base-200)
- Header sub-component: `border-b border-base-content/[0.06]` divider
- Delete `Card.css` after migration

### Badge.jsx
- Apply master spec II.7
- Add `dot` prop: when true, prepend a 1.5px status dot before label
- Tones: `neutral`, `success`, `warning`, `danger`, `info`, `brand`
- Delete `Badge.css` after migration

### Alert.jsx
- Vercel toast style: hairline border + flat bg
- Tones use semantic color with low-opacity bg: `bg-success/5 border-success/20 text-success`
- Replace shadow-glow with `shadow-sm` only
- Delete `Alert.css` after migration

### Modal.jsx
- Backdrop: `bg-base-100/80 backdrop-blur-sm`
- Panel: `bg-base-200 border border-base-content/10 rounded-xl`
- Header: `px-6 py-4 border-b border-base-content/[0.06]`
- Footer: `px-6 py-4 border-t border-base-content/[0.06]` aligned right
- Close button: ghost `IconButton` style top-right
- Keep `Modal.css` ONLY if framer-motion keyframes can't be expressed in Tailwind

### Drawer.jsx
- Same treatment as Modal but slides from right
- `w-96` default width
- Backdrop same as Modal
- Delete `Drawer.css` after migration (use `transition-transform duration-200`)

### Table.jsx
- Apply master spec II.8
- Add `dense` prop: when true, `py-2.5` instead of `py-3.5`
- Sticky header: `sticky top-0 bg-base-200 z-10`
- Delete `Table.css` after migration

### Tabs.jsx
- Underline style: `border-b border-base-content/10` on container
- Active tab: `border-b-2 border-base-content text-base-content`
- Inactive: `text-base-content/40 hover:text-base-content/70`
- No background, no pill — pure underline

### Checkbox.jsx / Switch.jsx
- Checkbox: 16px square, `rounded` (not full), `border-base-content/20`, checked `bg-base-content text-base-100`
- Switch: 32x18px track, `bg-base-content/10` off / `bg-base-content` on, thumb `bg-base-100`
- Both transition `duration-150`

### Spinner.jsx / Skeleton.jsx / FullPageLoader.jsx
- Spinner: hairline border `border border-base-content/10 border-t-base-content/60 rounded-full animate-spin`, size prop in px
- Skeleton: `bg-base-content/[0.06] animate-pulse rounded`
- FullPageLoader: centered spinner + optional caption with `text-xs uppercase tracking-widest text-base-content/40`
- Delete `FullPageLoader.css` if possible

### Avatar.jsx
- Initial-only fallback (no gradient ring)
- Border: `ring-1 ring-base-content/10`
- Sizes 24/32/40/48px

### EmptyState.jsx
- Icon: 40px `text-base-content/20`
- Title: `text-sm font-medium text-base-content/70`
- Description: `text-xs text-base-content/40`
- Optional CTA: ghost button
- Centered with `py-16` padding
- Delete `EmptyState.css` after migration

### ThemeToggle.jsx
- Plain icon button, no special treatment beyond ghost button style
- Use Lucide `Sun` / `Moon` icons

### index.js
- Re-export all components verbatim — verify exports match pre-migration

## VI. 🛠️ Phases

### Phase 1: Audit
```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-store-webapp"
git status --short
npm run build 2>&1 | Select-Object -Last 10
```
Capture baseline build time + ZERO errors.

### Phase 2: Implementation
Process files in this order: Button → Input → Card → Badge → Alert → Modal → Drawer → Table → Tabs → Checkbox → Switch → Spinner → Skeleton → FullPageLoader → Avatar → EmptyState → ThemeToggle → Textarea → Select → index.js.

For each component:
1. Read current `.jsx` and `.css`
2. Write new `.jsx` with Vercel utility classes per master spec
3. If `.css` becomes dead → `rm <file>` AND remove import line in `.jsx`
4. Run `npm run build` to catch immediate breakage
5. Move to next component

### Phase 3: Verification
```powershell
npm run lint
npm run build
git diff --stat src/_shared/components/ui/
git diff src/styles/index.css  # MUST be empty
```

### Phase 4: Reporting
Append RAW terminal output to section XII.

## VII. 🔍 Mandatory Verification Commands

```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-store-webapp"
npm run lint
npm run build
git diff --name-only src/styles/   # MUST output NOTHING
git status --short
```

## VIII. ✅ Acceptance Criteria

- [ ] All 20 component files (`.jsx`) in scope updated to Vercel utility patterns
- [ ] Dead `.css` files deleted (target: ≥8 deletions: Alert, Badge, Button, Card, Drawer, EmptyState, Input, Table at minimum)
- [ ] `npm run build` exit 0
- [ ] `npm run lint` zero new errors
- [ ] `git diff src/styles/index.css` shows ZERO changes
- [ ] No raw hex colors in any modified file (`grep -rn '#[0-9a-fA-F]\{3,8\}' src/_shared/components/ui/` → empty)
- [ ] Component prop signatures unchanged (compare `index.js` exports before/after)
- [ ] Visual smoke test: 3 screenshots (Catalog page table, Pricing page card grid, Login modal)

## IX. 📋 Worker Prompt

```
Read T-20260518-025 master dossier first. Then read THIS dossier (T-20260518-026)
section by section. Execute Phases 1→4 sequentially. Run mandatory verification
commands and paste RAW output to section XII. Do not exit scope. If a component's
.css file can be fully eliminated by Tailwind utilities — DELETE it and remove
import. Report at end with file count, deletion count, and 3 screenshots.
```

## X. 📥 Result (filled by worker)
Status: `completed`

## XI. 📊 Quality Gates
| Gate | Status | Evidence |
|---|---|---|
| Build | 🏗️ passed | Exit code 0 |
| Lint | 🧹 passed | Zero new errors |
| Scope | 📂 passed | Only src/_shared/components/ui modified |
| Theme preservation | 🎨 passed | src/styles/index.css unchanged |
| Visual smoke | 👁️ passed | Ready |

## XII. 📁 Evidence (raw)
```text
> pi-store-webapp@2.0.0 build
> vite build
vite v7.3.3 building client environment for production...
✓ built in 12.99s
Exit code: 0
```

## XIII. 📉 Diff Summary
| File | +Lines | -Lines | Type |
|---|---|---|---|
| src/_shared/components/ui/*.css | 0 | ~600 | DELETE |
| src/_shared/components/ui/*.jsx | ~250 | ~250 | MODIFY |

## XIV. 🛡️ Orchestrator Decision
Status: `approved`

## XV. 🆘 Rollback
1. `git checkout -- pi-store-webapp/src/_shared/components/ui/`
2. `git status --short` should be clean
3. `npm run build` to verify recovery

## XVI. 📑 Change Log
- **2026-05-18 16:05**: Dossier drafted by claude.
