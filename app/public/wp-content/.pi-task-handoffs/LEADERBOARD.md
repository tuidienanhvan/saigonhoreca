# 🏆 Leaderboard / Bảng Điểm Agent

Append-only log. 1 dòng per task khi archive. Format pipe-delimited để dễ parse.

## 1. 📝 Format

```text
YYYY-MM-DD | task_id | agent | task_type | result | turns | notes
```

| Field | Values |
|---|---|
| `YYYY-MM-DD` | Date archived |
| `task_id` | T-YYYYMMDD-XXX |
| `agent` | claude/codex/gemini/poolside/arcee/nemotron/grok/ling/stepfun |
| `task_type` | css-migration, lint-fix, refactor, audit, test, content, debug, etc. |
| `result` | pass / pass-warn / fail-recovered / fail-escalated |
| `turns` | int — number of agent dispatches before pass (1 = first try) |
| `notes` | short text, no pipes |

## 2. 🔍 Reading

Per-agent perf: `grep " | <agent> | " LEADERBOARD.md | wc -l` (count tasks).

Pass rate per agent: `grep " | <agent> | " LEADERBOARD.md | grep " | pass " | wc -l`.

Update `system/ROUTING.md` scores monthly từ data này.

## 3. 📜 Log

```text
2026-05-02 | T-20260502-001 | gemini | css-migration | pass | 1 | Pi-API dashboard license CSS migration
2026-05-02 | T-20260502-002 | gemini | audit | pass | 1 | Tailwind usage audit
2026-05-02 | T-20260502-003 | gemini | css-migration | pass | 1 | Tailwind token bridge
2026-05-02 | T-20260502-004 | gemini | css-migration | pass | 1 | License gate Tailwind migration
2026-05-02 | T-20260502-005 | gemini | css-migration | pass | 1 | Primitives Tailwind
2026-05-02 | T-20260502-006 | gemini | css-migration | pass | 1 | Command Palette Tailwind
2026-05-02 | T-20260502-007A | gemini | css-migration | pass | 1 | System Dashboard Tailwind (renamed from T-007)
2026-05-02 | T-20260502-007B | gemini | css-migration | pass | 1 | Audit Log Tailwind (renamed from T-007)
2026-05-02 | T-20260502-008 | gemini | css-migration | pass | 1 | Plugin/Theme Manager Tailwind
2026-05-02 | T-20260502-009 | gemini | css-migration | pass | 1 | LogViewer/Webhooks/ApiKeyVault Tailwind
2026-05-02 | T-20260502-010 | gemini | css-migration | pass | 1 | Backup/Restore Tailwind
2026-05-02 | T-20260502-011 | gemini | css-migration | pass-warn | 1 | Users/Roles Tailwind, full lint not green
2026-05-02 | T-20260502-012 | gemini | css-migration | pass-warn | 1 | Health/Cron Tailwind, full lint not green
2026-05-02 | T-20260502-013 | gemini | lint-fix | pass-warn | 1 | Global lint 90% (6 files remaining)
2026-05-02 | T-20260502-014 | gemini | css-migration | pass | 1 | V1-controls migration, bundle -57% (581→247.9 KB)
2026-05-02 | T-20260502-015 | gemini | lint-fix | pass | 1 | Final lint sanitization (0 errors, 0 warnings)
2026-05-03 | T-20260503-016 | grok | css-cleanup | pass | 1 | Dead CSS tokens cleanup
2026-05-03 | T-20260503-017 | orchestrator | system-upgrade | pass | 1 | English translation and v2.0 upgrade
2026-05-03 | T-20260503-018 | codex | refactor | pass | 1 | Sync Auth Logo & Brand Identity (Infinity Edition)
2026-05-03 | T-20260503-019 | codex | refactor | pass | 1 | Fix Auth Visual Parity & Typography (Bounce + Staggered)
2026-05-03 | T-20260503-020 | codex | refactor | pass | 1 | Refine Marketing Alignment (Revert Store stagger)
2026-05-03 | T-20260503-021 | codex | ui-design | pass | 1 | Premium Background Redesign (Grid + Noise + Scan)
2026-05-03 | T-20260503-022 | codex | ui-design | pass | 1 | Ultimate Auth UI Polish (Glow + Vibrant Grid)
2026-05-03 | T-20260503-023 | codex | optimization | pass | 1 | Perf Optimization (Static BG) & Theme Color Audit
2026-05-03 | T-20260503-025 | codex | refactor | pass | 1 | Theme System Optimization (Legacy Bridge Isolation)
2026-05-03 | T-20260503-027 | orchestrator | chore | pass | 1 | Silence IDE CSS Warnings (VS Code Settings)
2026-05-03 | T-20260503-028 | codex | chore | pass | 1 | Global Repository Sanitization (Cleanup Junk/Backups)
2026-05-03 | T-20260503-029 | codex | chore | pass | 1 | Deep Repository Sanitization (Python Caches & Dev Logs)
2026-05-03 | T-20260503-031 | antigravity | refactor | pass | 1 | About & Docs Refactor (Infinity Edition)
2026-05-03 | T-20260503-032 | laguna | system-upgrade | pass | 1 | Task Handoffs v2.1 Multi-Agent Safe
2026-05-04 | T-20260504-001 | antigravity | refactor | pass | 1 | Dashboard sanitization & storefront modernization
2026-05-04 | T-20260504-001 | antigravity | refactor | pass | 1 | Help typography & content modernization
2026-05-04 | T-20260504-002 | antigravity | audit | pass | 1 | Lesson plan audit (9000 words, 15 slides)
2026-05-04 | T-20260504-003 | antigravity | refactor | pass | 1 | Prefix refactor (0 _sh_ left in plugin code)
2026-05-04 | T-20260504-005 | antigravity | backend | pass | 1 | Backend & admin sync (JSON logs, BaseResponse)
2026-05-05 | T-20260505-001 | antigravity | bugfix | pass | 1 | Content list UI fix
2026-05-05 | T-20260505-001 | antigravity | refactor | pass | 1 | CSS architecture refactor (Modular & Shared UI)
2026-05-05 | T-20260505-002 | antigravity | bugfix | pass | 1 | Content delete unresponsive fix
2026-05-05 | T-20260505-002 | antigravity | css-migration | pass | 1 | Tailwind CSS v4 integration
2026-05-05 | T-20260505-003 | antigravity | ui-polish | pass | 1 | Toast border progress UI
2026-05-05 | T-20260505-004 | antigravity | ui-polish | pass | 1 | Search UX optimization
2026-05-05 | T-20260505-006 | antigravity | refactor | pass | 1 | Premium editor refactor
2026-05-05 | T-20260505-007 | antigravity | css-migration | pass | 1 | Dashboard theme unification (no --pi)
2026-05-05 | T-20260505-008 | antigravity | css-migration | pass | 1 | Theme Tailwind v4 refactor
2026-05-05 | T-20260505-009 | antigravity | ui-polish | pass | 1 | Editor perfect fidelity
2026-05-05 | T-20260505-010 | antigravity | refactor | pass | 1 | Architecture refactor
2026-05-06 | T-20260506-011 | antigravity | ui-polish | pass | 1 | Editor visual fidelity
2026-05-06 | T-20260506-012 | antigravity | refactor | pass | 1 | TipTap refactor
2026-05-06 | T-20260506-013 | gemini | audit | pass | 1 | Audit Antigravity work T-002+T-003
2026-05-07 | T-20260507-001 | antigravity | bugfix | pass | 1 | Fix editor action buttons
2026-05-07 | T-20260507-002 | antigravity | bugfix | pass | 1 | Toast border clipping fix
2026-05-07 | T-20260507-003 | antigravity | ui-polish | pass | 1 | Enhance editor toolbar & features
2026-05-07 | T-20260507-004 | antigravity | ui-polish | pass | 1 | Clean editor content styles (no effects)
2026-05-07 | T-20260507-004 | claude | css-migration | pass | 1 | Dashboard CSS Tailwind v4 optimization
2026-05-07 | T-20260507-005 | claude | css-migration | pass | 1 | SaigonHouse theme full Tailwind v4 refactor
2026-05-08 | T-20260508-001 | antigravity | refactor | pass | 1 | Featured image widget
2026-05-08 | T-20260508-001 | antigravity | ui-polish | pass | 1 | UI cleanup
2026-05-08 | T-20260508-001 | antigravity | backend | pass | 1 | Backend wipe & re-seed (pi-backend DB)
2026-05-08 | T-20260508-002 | claude | system-upgrade | pass | 1 | Task-handoffs v3.0 — Phase 0+E, automation scripts, atomic locks
2026-05-08 | T-20260508-003 | antigravity | ui-polish | pass | 1 | Premium Admin Overview UI/UX Optimization
2026-05-08 | T-20260508-004 | antigravity | ui-polish | pass | 1 | Admin UI refinement
2026-05-08 | T-20260508-005 | antigravity | refactor | pass | 1 | AI Providers refactor
2026-05-08 | T-20260508-006 | antigravity | refactor | pass | 1 | Usage refactor
2026-05-08 | T-20260508-007 | antigravity | refactor | pass | 1 | Wallet refactor
2026-05-08 | T-20260508-008 | antigravity | bugfix | pass | 1 | Fix UploadReleaseModal duplicate
2026-05-08 | T-20260508-009 | antigravity | bugfix | pass | 1 | Fix Tailwind @reference directive
2026-05-09 | T-20260509-001 | claude | css-migration | pass | 1 | Theme CSS cleanup phase 4 (1772 fallbacks → 6)
2026-05-09 | T-20260509-002 | claude | css-migration | reverted | 1 | Full bundle attempted, reverted (per-page +12-24KB worse vs split)
2026-05-09 | T-20260509-003 | claude | css-migration | pass | 1 | theme 100 tailwind v4
2026-05-10 | T-20260510-001 | antigravity | debug | pass | 1 | Fix sonner notifications triggered with null component
2026-05-10 | T-20260510-003 | antigravity | refactor | pass | 1 | Standardize Tailwind v4 Tokens, 44 files refactored
2026-05-10 | T-20260510-004 | antigravity | css-migration | pass | 1 | 100% semantic tokens dashboard, 195 files refactored
2026-05-10 | T-20260510-005 | antigravity | css-migration | pass | 1 | 100% semantic tokens store, full CSS cleanup
2026-05-11 | T-20260510-007 | laguna | ui-polish | pass | 1 | Admin Layout Fix (Sidebar fixed, header aligned)
2026-05-11 | T-20260511-001 | antigravity | ui-polish | pass | 1 | Fix Admin Tables Layout (7 pages)
2026-05-11 | T-20260511-002 | antigravity | ui-polish | pass | 1 | Fix Admin Global Layout Spacing (pt-16 + stable sidebar footer)
2026-05-11 | T-20260511-008 | antigravity | recovery | pass | 1 | Laguna M1 Recovery complete
2026-05-11 | T-20260511-009 | laguna | encoding-fix | pass | 1 | Dashboard UTF-8 Restore (110 files)
2026-05-11 | T-20260511-010 | antigravity | css-cleanup | pass | 1 | Dashboard CSS @apply purge
2026-05-11 | T-20260511-011 | antigravity | css-cleanup | pass | 1 | Store CSS @apply purge
2026-05-13 | T-20260513-001 | claude | refactor | pass | 1 | ai provider routing optimization
2026-05-13 | T-20260513-002 | antigravity | refactor | pass | 1 | store admin completeness
2026-05-10 | T-20260510-002 | antigravity | refactor | pass | 1 | add bulk publish
2026-05-10 | T-20260510-006 | antigravity | refactor | pass | 1 | full flow optimization
2026-05-11 | T-20260511-003 | antigravity | refactor | pass | 1 | css refactor layout retry
2026-05-11 | T-20260511-003 | Gemini | refactor | pass | 1 | gemini customer profile
2026-05-11 | T-20260511-004 | antigravity | refactor | pass | 1 | admin atomic refactor
2026-05-11 | T-20260511-004 | antigravity | refactor | pass | 1 | refactor dashboard layout split
2026-05-11 | T-20260511-005 | antigravity | refactor | pass | 1 | css purge tailwind v4
2026-05-11 | T-20260511-006 | antigravity | refactor | pass | 1 | global fix encoding css purge
2026-05-11 | T-20260511-007 | antigravity | refactor | pass | 1 | dashboard css purge tailwind v4
2026-05-12 | T-20260512-001 | gemma-31b | refactor | pass | 1 | pricing optimization
2026-05-12 | T-20260512-002 | antigravity | refactor | pass | 1 | build quantum docs center
2026-05-12 | T-20260512-002 | codex | refactor | pass | 1 | refactor homestore
2026-05-12 | T-20260512-003 | antigravity | refactor | pass | 1 | modernize catalog storefront
2026-05-12 | T-20260512-003 | codex | refactor | pass | 1 | refactor product offerings
2026-05-14 | T-20260514-001 | antigravity | refactor | pass | 1 | admin quantum redesign
2026-05-14 | T-20260514-002 | antigravity | refactor | pass | 1 | admin quality pass
2026-05-14 | T-20260514-003 | antigravity | refactor | pass | 1 | T 20260514 003 pi store src restructure
2026-05-14 | T-20260514-004 | antigravity | refactor | pass | 1 | T 20260514 004 pi dashboard restructure
2026-05-15 | T-20260515-001 | gemma | refactor | pass | 1 | admin modal to subpage
2026-05-15 | T-20260515-003 | codex | refactor | pass | 1 | confirm modal unification
2026-05-15 | T-20260515-004 | codex | chore | pass | 1 | typography density cleanup
2026-05-15 | T-20260515-005 | gemini | css-migration | pass | 1 | remove quantum hud css
2026-05-15 | T-20260515-002 | gemini | audit | pass | 1 | admin screenshot audit
2026-05-16 | T-20260516-009 | claude | refactor | pass | 1 | v43 smoke
2026-05-15 | T-20260515-006 | codex | css-migration | pass | 1 | per component css
2026-05-16 | T-20260516-001 | claude | refactor | pass | 1 | task handoffs v4.3
2026-05-16 | T-20260516-008 | claude | refactor | pass | 1 | token application sweep
2026-05-16 | T-20260516-007 | claude | refactor | pass | 1 | T 20260516 007 gemini per feature skeleton
2026-05-16 | T-20260516-010 | claude | refactor | pass | 1 | token compliance sweep
2026-05-16 | T-20260516-011 | claude | refactor | pass | 1 | rq hooks migration
2026-05-16 | T-20260516-012 | claude | css-migration | pass | 1 | sgh theme lighthouse opt
2026-05-17 | T-20260517-001 | claude | refactor | pass | 1 | remove all shadows
2026-05-17 | T-20260517-002 | claude | css-migration | pass | 1 | sgh theme lighthouse restore
2026-05-17 | T-20260517-003 | claude | audit | pass | 1 | sgh bundle split and 3 audits
2026-05-17 | T-20260517-005 | claude | refactor | pass | 1 | remove pi dashboard and optimize iframe
2026-05-17 | T-20260517-004 | claude | css-migration | pass | 1 | sgh tailwind utilities refactor
2026-05-17 | T-20260517-007 | claude | refactor | pass | 1 | sgh push to 100
2026-05-17 | T-20260517-008 | claude | backend | pass | 1 | switch pi api to vercel url
2026-05-17 | T-20260517-009 | claude | bugfix | pass | 1 | sgh a11y fixes
2026-05-17 | T-20260517-010 | claude | refactor | pass | 1 | tier spec consolidation
2026-05-17 | T-20260517-014 | claude | backend | pass | 1 | split backend vs dashboard url
2026-05-17 | T-20260517-006 | claude | refactor | pass | 1 | iframe jwt postmessage handshake
2026-05-17 | T-20260517-016 | claude | refactor | pass | 1 | tenant saas mgmt pi store admin
2026-05-17 | T-20260517-017 | claude | refactor | pass | 1 | token ledger admin
2026-05-17 | T-20260517-018 | claude | refactor | pass | 1 | ai usage drilldown
2026-05-17 | T-20260517-011 | claude | backend | pass | 1 | tier spec pi api consumer
2026-05-17 | T-20260517-012 | claude | refactor | pass | 1 | tier spec dashboard consumer
2026-05-17 | T-20260517-013 | claude | refactor | pass | 1 | tier spec store consumer
2026-05-17 | T-20260517-015 | claude | refactor | pass | 1 | pi store admin operator console
2026-05-19 | T-20260519-037 | gemini | ui-polish | pass | 1 | rebuild header hero production parity
```
