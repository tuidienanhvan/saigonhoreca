---
id: T-20260529-003
owner: qwen
dispatched_by: claude
self_implement_forbidden: true
state: archived
priority: P2
risk: medium
estimated_minutes: 90
parent: null
children: []
depends_on: []
parallelization_ok: true
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-29 13:44
updated: 2026-05-29 20:18
archived: 2026-05-29 20:18
archived: null
requires_user_approval: false
confidence_threshold: 0.8
hitl_triggers: []
pause_after_phase: null
dispatch_mode: handoff
worktree_path: null
project_slug: the-cheezy-time
project_abbr: tct
---

## 0. User Original Intent (Phase 0 — Verbatim)

> full cac project chua lam — upgrade CSS quality cho the-cheezy-time len gold-standard ngang rokafella/casa-maria

Context: Batch upgrade theo master plan project/PILLAR-UPGRADE-PLAN.md.

# T-20260529-003 | qwen | the-cheezy-time CSS Upgrade

## I. Frontmatter
| Field | Value |
|---|---|
| owner | qwen (Qwen3.7-Max via Luna-Proxy :8088) |
| dispatched_by | claude · self_implement_forbidden: true |
| risk | medium (refactor CSS 1 project, no crawl/delete) |
| project | The Cheezy Time (the-cheezy-time), abbr tct, ~8 sections |

## II. Goal
Nang CSS/layout/animation cua template-parts/project-pillar/the-cheezy-time/ tu render PHANG len gold-standard. Giu noi dung text + class prefix. Chi nang VISUAL theo quality-bar.

## III. Required Reading
- Quality bar (BAT BUOC): system/templates/tasks/project-pillar-upgrade.md §B
- Reference gold: roka-fella-tinh-hoa-am-thuc-nhat-ban/ + casa-maria/
- assets/css/_tokens.css · project-pillar/_container.css · _caption.css
- AGENTS/qwen.md

## IV. Allowed Scope
- template-parts/project-pillar/the-cheezy-time/**  (refactor CSS + markup)
- single-project/the-cheezy-time.php + .css  (chi neu can class root/order)
- assets/css/imports/_imports-project.css  (CHI block the-cheezy-time)

KHONG: project khac · shared _container/_caption/_cta/_related · _tokens.css (chi DUNG) · inc/core · functions.php

## V. Out Of Scope
- Doi noi dung text (da dung live) · doi class prefix pp-*-tct
- Hardcode hex mau brand -> CHI var(--p)/--bc/color-mix
- Touch project khac / shared / tokens

## VI. Phases
Theo system/templates/tasks/project-pillar-upgrade.md §A:
1. Doc reference (rokafella/casa-maria) + _tokens/_container/_caption
2. Refactor tung section: wrap pp-container-shared, anh boc pp-image-caption-shared, CSS -> token vars + >=6/9 techniques §B.3
3. Animation: scroll-reveal + keyframes + prefers-reduced-motion
4. Verify + browser smoke vs rokafella

## VII. Verification Commands
- grep hardcode hex: template-parts/project-pillar/the-cheezy-time/*.css (phai 0 brand hex)
- grep pp-container-shared + pp-image-caption-shared (phai >0)
- grep technique count: ken-burns|@keyframes|backdrop-filter|clamp|radial-gradient|scroll-reveal|prefers-reduced-motion (>=6)
- npm run build:project
- curl -k -I https://saigonhoreca.local/du-an/the-cheezy-time/
- git status --short

## VIII. Acceptance Criteria
- [ ] ZERO hex mau brand hardcode (chi token vars / color-mix)
- [ ] pp-container-shared + pp-image-caption-shared ap dung du
- [ ] >=6/9 CSS techniques present
- [ ] Class prefix toan pp-*-tct + BEM
- [ ] prefers-reduced-motion fallback
- [ ] Build pass + route 200
- [ ] Browser smoke: giau chieu sau, KHONG phang (vs rokafella)
- [ ] Scope: chi the-cheezy-time files

## IX. Copy-Paste Prompt cho Qwen
You are Qwen3.7-Max (Tier 2, Luna-Proxy). Execute T-20260529-003: upgrade CSS quality cho "The Cheezy Time" (the-cheezy-time) len gold-standard.
READ: system/templates/tasks/project-pillar-upgrade.md §B (quality bar) + reference rokafella/ + casa-maria/ + _tokens.css + _container.css + _caption.css
TARGET: saigonhoreca-theme | SCOPE: template-parts/project-pillar/the-cheezy-time/** + single-project/the-cheezy-time.php + _imports-project.css(block the-cheezy-time)
DO: refactor CSS -> token vars (var(--p)/--bc/color-mix); >=6/9 techniques; wrap pp-container-shared; anh boc pp-image-caption-shared; giu text + prefix pp-*-tct; prefers-reduced-motion.
DONT: doi text/prefix, touch project khac/shared/tokens, hardcode hex brand.
VERIFY (paste raw): token compliance + shared partials + technique count + build + route + git status. REPORT block cuoi. Luna loi -> STOP state rejected.

## X. Agent Result
Status: not-started

## XI. Quality Gate Matrix
| Gate | Status | Description |
|---|---|---|
| Build | pending | npm run build:project exit 0 |
| Token/Prefix | pending | 0 hex brand, prefix pp-*-tct |
| Techniques | pending | >=6/9 |
| Scope | pending | chi the-cheezy-time |
| Logic | pending | browser smoke khong phang |

## XII. Evidence
(qwen fills)

## XVI. CHANGE LOG
- 2026-05-29 13:44: Dossier created (batch upgrade). Drafted by claude -> ready dispatch qwen.
- **2026-05-29 15:59**: State drafted → dispatched
