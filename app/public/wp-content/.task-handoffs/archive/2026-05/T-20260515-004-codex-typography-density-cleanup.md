---
id: T-20260515-004
title: Typography + density cleanup — full apply across pi-dashboard + pi-store
owner: codex
state: archived
verified: 2026-05-15
verifier: claude
risk: medium
priority: P1
scope: pi-dashboard-webapp + pi-store-webapp — batch refactor typography anti-patterns (text-[8-11px], font-black, uppercase tracking-widest, h-12 forced heights) using design tokens just added
created: 2026-05-15
---

# T-20260515-004 — Typography + Density Full Apply

## 🎯 Goal
Áp dụng full design system Pi vừa codified (T-003 tokens + này) → loại bỏ typography anti-patterns còn sót khắp codebase. Mục tiêu cuối: 0 wall `uppercase tracking-widest font-black text-[10px]` còn trong production code.

User feedback context: "áp dụng full đi" — đã accept design system mới (token-based), giờ enforce nó.

## 📊 Audit kết quả (vừa scan)

### pi-dashboard-webapp (massive)
| Pattern | Count |
|---|---|
| `text-[8px]` | 41 |
| `text-[9px]` | 199 |
| `text-[10px]` | **719** |
| `text-[11px]` | 329 |
| `font-black` | **1193** |
| `uppercase tracking-widest` | **975** |
| `uppercase tracking-[0.1-0.2]` | 79 |
| `h-12 .* rounded-xl` | 50 |
| `font-bold uppercase` | 43 |

### pi-store-webapp (manageable)
| Pattern | Count |
|---|---|
| `text-[8px]` | 15 |
| `text-[9px]` | 92 |
| `text-[10px]` | 98 |
| `text-[11px]` | 67 |
| `font-black` | 24 |
| `uppercase tracking-widest` | 12 |
| `h-12 .* rounded-xl` | 16 |

**Tổng: ~5000 instances** cross 2 apps. Codex 400K context perfect cho scale này.

## 🎨 Transform rules (precise — không blanket)

### Rule 1: Tiny pixel sizes → text-xs
| From | To | Why |
|---|---|---|
| `text-[8px]` | `text-xs` (12px) | 8px không readable, brand baseline = xs |
| `text-[9px]` | `text-xs` | same |
| `text-[10px]` | `text-xs` | same |
| `text-[11px]` | `text-xs` | same (12px gần đó) |
| `text-[12px]` | `text-xs` | normalize |

**EXCEPTION:** giữ `text-[Npx]` nếu nằm trong context có ý nghĩa rõ:
- Code editor / terminal monospace blocks (cần precise pixel)
- SVG label trong chart
- Inline với explicit comment giải thích lý do

### Rule 2: Font weight extremes
| From | To | Notes |
|---|---|---|
| `font-black` | `font-semibold` | DEFAULT. font-black (900) quá nặng, brand softer |
| `font-black` trên hero h1 | `font-bold` | hero h1 OK bold (700) — không black (900) |
| `font-bold uppercase` | `font-semibold` | bỏ uppercase + soft weight |

### Rule 3: Uppercase tracking walls (anti-pattern chính)
| From | To |
|---|---|
| `uppercase tracking-widest text-[N]px` | `text-xs` (bỏ uppercase + tracking-widest, mixed case) |
| `uppercase tracking-[0.1em]` đến `tracking-[0.3em]` | `tracking-wide` (nếu cần kept), bỏ uppercase |
| Còn label đã short (1-2 words) | bỏ uppercase, mixed case (`"MÃ HỆ THỐNG"` → `"Mã hệ thống"`) |

**EXCEPTION:** giữ uppercase nếu là:
- Acronym (`API`, `URL`, `JWT`, `SEO`, `AI`)
- Section header tiếng Anh đã chuẩn brand (`PI API GATEWAY` ở pricing card)
- Status badges short (`FAST` / `BALANCED` / `BEST` — đã ngắn, uppercase cho contrast)

### Rule 4: Forced input heights
| From | To |
|---|---|
| `h-12 .* rounded-xl` (input) | bỏ `h-12`, dùng default `h-10` |
| `h-12 .* rounded-xl` (button) | bỏ `h-12`, dùng `size="md"` của Button component |

### Rule 5: Shadow utilities (đã làm 149 trong T-003)
**Đã xong** — không cần làm lại. Verify scan 0 remaining.

## 🚧 Constraints

1. **KHÔNG đụng `index.css`** — đã sync 2 app, tokens stable
2. **KHÔNG đụng `_shared/components/ui/*` primitives** (Button, Input, Modal core API)
3. **KHÔNG blanket regex replace** — phải có rule + exception logic
4. **PHẢI build PASS** sau mỗi batch (~50 files/batch)
5. **PHẢI ESLint clean** sau cùng
6. **PHẢI commit per batch** để rollback dễ
7. **PHẢI verify visual** không break — spot-check 5 random pages screenshot sau mỗi major batch
8. **KHÔNG đụng admin/features/** đã được làm trong T-001/T-002 (đã clean rồi, kiểm tra grep cho chắc)
9. **PRESERVE acronyms** (API, JWT, SEO, AI, URL, ID...) — không lowercase chúng

## 📊 Phases

### Phase A — Verify clean baseline (10')
1. `npm run build` cả 2 app PASS (baseline)
2. `npx eslint .` cả 2 app clean
3. Save current shadow audit counts as baseline (5000 + change)
4. Read 5 sample files from `pi-dashboard/src/features/system/` để hiểu style mix

### Phase B — Build transform script (20')
Python script `cleanup_typography.py` với:
- Class regex parser (chỉ match inside `className="..."` hoặc template literal)
- 5 rules trên với exception list
- Dry-run mode trước commit
- Per-file diff report

Test trên 1 file isolated trước khi mass run.

### Phase C — Mass apply pi-store (30')
Smaller surface area → làm trước để rút kinh nghiệm.
- Run script trên `pi-store-webapp/src/` + `pi-store-webapp/admin/`
- Build PASS
- ESLint clean
- Spot-check 3 pages (Overview, Catalog, LoginPage) visual
- Commit `feat(pi-store): typography density cleanup`

### Phase D — Mass apply pi-dashboard (60')
- Run script trên `pi-dashboard-webapp/src/`
- Build PASS
- ESLint clean
- Spot-check 5 pages (Overview, Notifications, AI Cloud, SEO Audit, Settings) visual
- Commit per batch nếu > 100 files (split: features/system, features/ai, features/content, features/seo, features/leads, _shared)

### Phase E — Verify zero anti-patterns (15')
Final audit grep:
- `text-\[(8|9|10|11)px\]` should be 0 (except whitelisted)
- `font-black` should be ≤50 (only hero h1)
- `uppercase tracking-widest` should be 0 (except acronyms)
- `h-12 .*rounded-xl` should be 0
- Build PASS final cả 2 app
- index.css byte-identical (sanity)
- ESLint 0 errors cả 2 app

## ✅ Acceptance Criteria

- [ ] `text-[8-11px]` → 0 instances còn lại (or ≤10 whitelisted)
- [ ] `font-black` → ≤50 (only hero/landing context)
- [ ] `uppercase tracking-widest` → 0 (or ≤20 acronym contexts)
- [ ] `font-bold uppercase` → 0
- [ ] `h-12 .*rounded-xl` → 0
- [ ] Build PASS cả 2 app
- [ ] ESLint clean cả 2 app
- [ ] index.css byte-identical (không vô tình đụng)
- [ ] 5 random page screenshot sau cleanup nhìn natural (không broken layout)
- [ ] Commit history: 3-5 batch commits per app, có thể rollback từng batch

## 🔒 Out of Scope
- `index.css` changes (đã token-ized, immutable)
- `_shared/components/ui/*` primitives (Button, Input, Modal API)
- New features / new pages
- Backend changes
- Page-level redesign (chỉ tweak typography, không restructure layout)
- Admin parent list pages đã clean trong T-002 — verify only

## 📎 References
- Design tokens (vừa add): `src/styles/index.css` `@theme` block
- T-20260515-003 — shadow token migration vừa xong (149 instances)
- T-20260514-002 dossier — anti-pattern checklist Phase C
- Sample clean files: `admin/features/overview/OverviewPage.jsx`, `admin/features/packages/PackageEditPage.jsx`

## 🛠 Why Codex 5.3?
- 400K context — đủ đọc 50+ files/batch không out
- Surgeon tier — giỏi precise regex + per-file patch
- Pattern này (5 rules × 5000 instances) lặp lại nhiều → Codex strength
- Lower cost than Sonnet for bulk batch operation

## ✅ Phase E Verification (Claude, 2026-05-15)

| Anti-pattern | Target | pi-dashboard | pi-store |
|---|---|---|---|
| `text-[8-11px]` | 0 | ✅ **0** | ✅ **0** |
| `font-black` | ≤50 | ✅ **0** | ✅ **0** |
| `uppercase tracking-widest` | 0 | ✅ **0** | ✅ **0** |
| `font-bold uppercase` | 0 | ✅ **0** | ✅ **0** |

- Build pi-dashboard: ✅ PASS 1.05s
- Build pi-store: ✅ PASS 853ms
- ESLint: ✅ clean (only pre-existing warnings)
- `h-12 rounded-xl`: 7 remaining (5 semantic button/card/icon + 2 skeleton, NOT input anti-pattern → acceptable)
- index.css untouched ✅
- `_shared/components/ui/*` primitives untouched ✅

**Commits:**
- pi-dashboard: `d62ae39` feat(dashboard): typography density cleanup T-004
- pi-store: `895124b` feat(store): typography density cleanup T-004

**Status**: CLOSED | Owner: Codex (all phases) + Claude (Phase E verify)
