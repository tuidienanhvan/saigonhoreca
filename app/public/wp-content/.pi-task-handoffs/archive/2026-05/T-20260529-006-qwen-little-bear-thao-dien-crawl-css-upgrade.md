---
id: T-20260529-006
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
created: 2026-05-29 13:45
updated: 2026-05-31 16:02
archived: 2026-05-31 16:02
archived: null
requires_user_approval: false
confidence_threshold: 0.8
hitl_triggers: []
pause_after_phase: null
dispatch_mode: handoff
worktree_path: null
project_slug: little-bear-thao-dien
project_abbr: lbear
---

## 0. User Original Intent (Phase 0 — Verbatim)

> full cac project chua lam — upgrade CSS quality cho little-bear-thao-dien len gold-standard ngang rokafella/casa-maria

Context: Batch upgrade theo master plan project/PILLAR-UPGRADE-PLAN.md.

# T-20260529-006 | qwen | little-bear-thao-dien Crawl + CSS Upgrade

## I. Frontmatter
| Field | Value |
|---|---|
| owner | qwen (Qwen3.7-Max via Luna-Proxy :8088) |
| dispatched_by | claude · self_implement_forbidden: true |
| risk | medium (PHASE 0 crawl static-mirror + refactor CSS 1 project) |
| project | Little Bear Thao Dien (little-bear-thao-dien), abbr lbear, ~8 sections |

## II. Goal
**Workflow**: PHASE 0 = crawl static-mirror (§G) → PHASE 1+ = nâng CSS. Nang CSS/layout/animation cua template-parts/project-pillar/little-bear-thao-dien/ tu render PHANG len gold-standard. Giu noi dung text + class prefix. Chi nang VISUAL theo quality-bar.

## III. Required Reading
- Quality bar (BAT BUOC): system/templates/tasks/project-pillar-upgrade.md §B
- Reference gold: roka-fella-tinh-hoa-am-thuc-nhat-ban/ + casa-maria/
- assets/css/_tokens.css · project-pillar/_container.css · _caption.css
- AGENTS/qwen.md

## IV. Allowed Scope
- template-parts/project-pillar/little-bear-thao-dien/**  (refactor CSS + markup)
- single-project/little-bear-thao-dien.php + .css  (chi neu can class root/order)
- assets/css/imports/_imports-project.css  (CHI block little-bear-thao-dien)

- static-mirror/saigonhoreca.vn/little-bear-thao-dien/** + saigonhoreca.com/little-bear-thao-dien/**  (PHASE 0 crawl output)

KHONG: project khac · shared _container/_caption/_cta/_related · _tokens.css (chi DUNG) · inc/core · functions.php

## V. Out Of Scope
- Doi noi dung text (da dung live) · doi class prefix pp-*-lbear
- Hardcode hex mau brand -> CHI var(--p)/--bc/color-mix
- Touch project khac / shared / tokens

## VI. Phases
Theo system/templates/tasks/project-pillar-upgrade.md §A:
0. **PHASE 0 — CRAWL static-mirror (BAT BUOC TRUOC CSS)** theo §G+§G.1a+§G.5 — ⚠️ CRAWL CẢ 2 MIRROR (.vn VÀ .com, KHÔNG bỏ cái nào): .vn <- local /du-an/little-bear-thao-dien/ ; .com <- LIVE https://saigonhoreca.com/little-bear-thao-dien/ (English, top-level, KHONG /project/). Anh PROJECT-ONLY (loc content-region + token), strip header/footer/related, localize css/. -> static-mirror/<domain>/little-bear-thao-dien/{index.html,images/,css/}
1. Doc reference (rokafella/casa-maria) + _tokens/_container/_caption
2. Refactor tung section: wrap pp-container-shared, anh boc pp-image-caption-shared, CSS -> token vars + >=6/9 techniques §B.3
3. Animation: scroll-reveal + keyframes + prefers-reduced-motion
4. Verify + browser smoke vs rokafella

## VII. Verification Commands
- grep hardcode hex: template-parts/project-pillar/little-bear-thao-dien/*.css (phai 0 brand hex)
- grep pp-container-shared + pp-image-caption-shared (phai >0)
- grep technique count: ken-burns|@keyframes|backdrop-filter|clamp|radial-gradient|scroll-reveal|prefers-reduced-motion (>=6)
- PHASE 0 GATE (§G.6): for f in static-mirror/saigonhoreca.*/<slug>/images/*; do file -b "$f"|grep -qi image||echo FAKE; done (rong) ; grep -c "saigonhoreca.(com|vn|local)/wp-content" index.html (=0 localized)
- PHASE 0 mirror: find static-mirror/saigonhoreca.vn/little-bear-thao-dien/images -type f | wc -l (>0) ; grep lang= static-mirror/saigonhoreca.com/little-bear-thao-dien/index.html (phai en)
- npm run build:project
- curl -k -I https://saigonhoreca.local/du-an/little-bear-thao-dien/
- git status --short

## VIII. Acceptance Criteria
- [ ] ZERO hex mau brand hardcode (chi token vars / color-mix)
- [ ] pp-container-shared + pp-image-caption-shared ap dung du
- [ ] >=6/9 CSS techniques present
- [ ] Class prefix toan pp-*-lbear + BEM
- [ ] prefers-reduced-motion fallback
- [ ] Build pass + route 200
- [ ] Browser smoke: giau chieu sau, KHONG phang (vs rokafella)
- [ ] PHASE 0 .vn: anh project-only THAT (0 fake-HTML, `file` ra image), 0 URL tuyet doi, 0 broken (tru favicon/logo <head>), stripped
- [ ] PHASE 0 .com: — crawled tu LIVE /little-bear-thao-dien/ (lang=en, anh project-only, 0 broken)
- [ ] Scope: chi little-bear-thao-dien files

## IX. Copy-Paste Prompt cho Qwen
You are Qwen3.7-Max (Tier 2, Luna-Proxy). Execute T-20260529-006: upgrade CSS quality cho "Little Bear Thao Dien" (little-bear-thao-dien) len gold-standard.
READ: system/templates/tasks/project-pillar-upgrade.md §B (quality bar) + reference rokafella/ + casa-maria/ + _tokens.css + _container.css + _caption.css
TARGET: saigonhoreca-theme | SCOPE: template-parts/project-pillar/little-bear-thao-dien/** + single-project/little-bear-thao-dien.php + _imports-project.css(block little-bear-thao-dien)
PHASE 0 (BAT BUOC TRUOC CSS) — crawl static-mirror theo §G+§G.5 — ⚠️ CRAWL ĐỦ CẢ 2 (.vn VÀ .com): .vn <- local https://saigonhoreca.local/du-an/little-bear-thao-dien/ ; .com <- LIVE https://saigonhoreca.com/little-bear-thao-dien/ (English). Anh PROJECT-ONLY (loc content-region §G + token), strip header/footer/related, localize css/. ⚠️§G.6 (BAT BUOC dung): tai anh TU PRODUCTION (saigonhoreca.com hoac saigonhoreca.vn — KHONG .local vi local 404 ra HTML), VERIFY moi anh LA IMAGE THAT (`file -b <f> | grep -i image`, XOA file 404-HTML doi lot .jpg), localize HET URL tuyet doi (0 con lai), images PHAI >0 (=0 hoac co fake-HTML -> REJECTED). ROI MOI:
DO: refactor CSS -> token vars (var(--p)/--bc/color-mix); >=6/9 techniques; wrap pp-container-shared; anh boc pp-image-caption-shared; giu text + prefix pp-*-lbear; prefers-reduced-motion.
DONT: doi text/prefix, touch project khac/shared/tokens, hardcode hex brand.
VERIFY (paste raw): token compliance + shared partials + technique count + build + route + git status. Khi XONG: tao file returns/T-20260529-006-return.md theo system/templates/RETURN-REPORT.md (scaffold: bash system/scripts/new-return.sh T-20260529-006) — dien frontmatter (status/scope_clean/gates_passed/recommendation) + paste RAW evidence. File .md NAY la deliverable, KHONG chi dan chat. Luna loi -> STOP state rejected.

## X. Agent Result
Status: not-started

## XI. Quality Gate Matrix
| Gate | Status | Description |
|---|---|---|
| Build | pending | npm run build:project exit 0 |
| Token/Prefix | pending | 0 hex brand, prefix pp-*-lbear |
| Techniques | pending | >=6/9 |
| Scope | pending | chi little-bear-thao-dien |
| Logic | pending | browser smoke khong phang |

## XII. Evidence
(qwen fills)

## XV. 🆘 Escalation / Errors / Rollback (điền khi state = blocked | rejected | reopened)

> Xem LIFECYCLE.md §II.a phân biệt 3 state. Lý do PHẢI khớp `set-state --reason`.

- **State lỗi**: `<blocked | rejected | reopened>`
- **Loại lỗi**: `<gate-fail | capability-mismatch | scope-violation | regression | missing-asset>`
- **Lý do (raw, khớp --reason)**: <…>
- **Evidence**: <paste lệnh + output chứng minh lỗi>
- **Rollback**:
  1. `git checkout -- <files>` (revert sửa sai)
  2. `rm <new_files>` (xóa file thừa)
- **Next Step**: `<retry | re-scope (→drafted) | decompose | re-route agent khác | fix-in-place (reopened)>`
## XVI. CHANGE LOG
- 2026-05-29 13:45: Dossier created (batch upgrade). Drafted by claude -> ready dispatch qwen.
- **2026-05-29 15:59**: State drafted → dispatched
- **2026-05-29 21:30**: PHASE 0 crawl static-mirror bổ sung (workflow crawl→section→CSS) — fix thiếu crawl. By claude.
- **2026-05-30**: siết §G.6 (tải ảnh từ PRODUCTION + verify ảnh thật, chống lưu 404-HTML). By claude.
- **2026-05-31 16:02**: State dispatched → returned
- **2026-05-31 16:02**: State returned → verified
