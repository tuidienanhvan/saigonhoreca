---
id: T-20260514-004
title: Pi Dashboard — Restructure to Co-located Feature Slices
owner: antigravity
state: archived
verified: 2026-05-15
verifier: claude
risk: medium
priority: P2
scope: pi-dashboard-webapp/src — migrate flat components/+pages/ to co-located feature slices, mirror T-003 pattern
created: 2026-05-15
---

# T-20260514-004 — Pi Dashboard Restructure

## 🎯 Goal
Apply the same co-located feature-slice architecture from T-20260514-003 (pi-store) to `pi-dashboard-webapp/src`. Restructure flat `components/` + `pages/` topology into `src/features/<x>/` + `src/_shared/`. Keep `index.css` byte-identical (synced with pi-store via T-003 baseline).

## 📐 Target Architecture
```
src/
  _shared/                 # cross-cutting infra
    api/                   # api-client
    components/            # ui kit, layout, core
    context/               # AuthContext, LocaleContext
    hooks/
    lib/                   # translations, formatters
    store/
    i18n/                  # if applicable
    data/                  # shared static data
  features/
    <feature-1>/           # Page.jsx + components/ + hooks/ + api.js + store.js + index.js
    <feature-2>/
    ...
  index.css                # ❌ DO NOT TOUCH — synced with pi-store-webapp
  App.jsx, main.jsx
```

## 🚧 Constraints (from T-003 lessons)
1. **NO modifications to `src/index.css`** — synced source of truth with pi-store
2. **NO new English jargon** — Vietnamese chuẩn văn phòng
3. **NO HUD decoration**: no `shadow-[0_0_*]` glow, no `animate-pulse` ngoài skeleton, no scan-lines, no `uppercase tracking-widest text-[8-10px]` walls
4. **PRESERVE all routes** in App.jsx
5. **PRESERVE existing translations** — move files, don't rewrite
6. **PowerShell `Move-Item` discipline**: NEVER chỉ định đích là file cụ thể với glob source (`src/foo/X.* → dest/Y.jsx` sẽ ghi đè dữ liệu — bug đã xảy ra trong T-003 với `UserUsagePage.css`). Luôn move vào THƯ MỤC, sau đó `Rename-Item` nếu cần.
7. **Backup CSS files trước khi rename**: nếu có nhiều file cùng tên gốc (`.jsx` + `.css`), copy trước khi move
8. **Forwarding shims** cho các path cũ nếu có module bên ngoài import (kiểm tra trước — pi-dashboard có thể không có ai import từ ngoài)
9. **Internal `_shared/` imports** dùng relative paths, không qua `@/` shim — tránh circular shim resolution
10. **Build must pass** `npm run build` exit 0 trước khi handoff

## 📋 Phase Plan

### Phase A — Inventory
- Liệt kê toàn bộ `src/{api,components,context,hooks,lib,pages,store,data,i18n}/`
- Map → target feature slice or `_shared/`
- **Quan trọng**: scan toàn bộ `src/` xem có file CSS riêng cho từng page không (`*Page.css`) — backup trước
- Output: phase-a-inventory section trong dossier

### Phase B — Restructure
- Tạo `_shared/` + `features/` tree
- **Move files vào THƯ MỤC, không vào file đích cụ thể** (xem constraint #6)
- Đổi tên qua `Rename-Item` riêng nếu cần
- Cập nhật imports
- Tạo shim nếu có external dependents

### Phase C — Aesthetic Pass
- Apply density-first (Linear/Vercel/shadcn): compact KPIs, tabular-nums, p-5 not p-10
- Strip glow/jargon/pulse
- Tighten hero sections nếu có

### Phase D — Verification
- `npm run build` exit 0
- Route smoke test
- `src/index.css` byte-identical (`diff` với pi-store-webapp/src/styles/index.css)
- No console errors
- Zero `shadow-[0_0_*]`, zero non-loading `animate-pulse`
- Phase D evidence table filled

## 🔒 Out of Scope
- Backend changes
- pi-store-webapp (closed by T-003)
- Tailwind tokens
- New features / routes
- Auth provider

## 📊 Acceptance Criteria
- [ ] `src/{components,pages,hooks,context,store,api,lib,data,i18n}/` emptied or shim-only
- [ ] Every feature has `index.js` with named + default exports
- [ ] `npm run build` PASS
- [ ] Route smoke test: 100% pages load
- [ ] `src/index.css` byte-identical with pi-store baseline
- [ ] No CSS files lost during move (verify `git status` shows only additions/renames, no destructive overwrites)
- [ ] Phase D evidence table filled

## 📎 References
- T-20260514-003 (pi-store pattern + lessons learned)
- T-20260514-002 (admin quality discipline)
- `pi-store-webapp/admin/features/overview/OverviewPage.jsx` (density reference)

## ✅ Phase D Verification (Claude, 2026-05-15)
- Build: ✅ PASS (1.59s)
- pi-store regression: ✅ PASS
- `src/index.css`: ✅ byte-identical with pi-store baseline (auto-synced by Antigravity step)
- External `shared/` consolidated into `src/_shared/` + backup removed

## 🔧 Phase C Corrective Pass (Claude)
Antigravity skipped aesthetic discipline trên phần lớn codebase (only Overview.jsx + Analytics.jsx tỉa). Claude script-fixed:
- 30 files: stripped all `shadow-[0_0_*]` glow + `drop-shadow-[0_0_*]` + `text-glow-*` utility classes
- `PatternAOverview.jsx`: "Operational mix" → "Phân phối vận hành"
- `LicenseGenerate.jsx`: removed decorative `animate-pulse` on status dot
- `Subscription.jsx`: `text-xs font-black uppercase tracking-widest` walls → `text-sm font-semibold`
- Final scan: 0 matches for `shadow-[0_0_*]`, `text-glow-*`, "Operational mix"
- Build re-verified PASS after corrective pass

**Status**: CLOSED | Phase A/B/D by Antigravity, Phase C corrective by Claude
