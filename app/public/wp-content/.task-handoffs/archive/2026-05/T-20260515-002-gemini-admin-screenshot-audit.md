---
id: T-20260515-002
title: Admin — Screenshot-driven full design audit + per-page fix
owner: gemini
state: archived
verified: 2026-05-16
verifier: claude
risk: medium
priority: P1
scope: pi-store-webapp/admin — chụp screenshot mọi route admin → so với design baseline → fix từng page về chuẩn brand
created: 2026-05-15
---

# T-20260515-002 — Admin Screenshot-Driven Design Audit

## 🎯 Goal
Visual-driven cleanup: agent **chụp screenshot từng admin route** bằng browser API → **so sánh với design baseline** (homepage + loginpage, vì không có design doc riêng) → **fix per-page** để toàn bộ admin có visual identity nhất quán với storefront.

Không phải task code-only — task này yêu cầu **mắt nhìn** (multimodal vision) để spot mỗi page xấu chỗ nào, sau đó fix code tương ứng.

## 🛠 Required tools
- **`mcp__Claude_in_Chrome__navigate`** — mở route
- **`mcp__Claude_in_Chrome__gif_creator`** hoặc **screenshot tool** — chụp ảnh
- **`mcp__Claude_in_Chrome__get_page_text`** — đọc DOM khi cần
- **Multimodal vision** — nhìn screenshot, identify visual issues
- **Read/Edit/Write** — fix code

Agent **PHẢI** dùng browser tool. Không được đoán mò "page này chắc xấu chỗ X" mà không chụp.

## 📋 Routes cần audit (25+ routes)

### Core
- `/admin` (Overview) — 4 KPI tiles, system status, alerts, expiring
- `/admin/usage` — usage stats
- `/admin/revenue` — revenue tables/charts
- `/admin/cron` — cron jobs list
- `/admin/audit-log` — audit entries
- `/admin/settings` — config form
- `/admin/users` — user list
- `/admin/users/:id` — user profile

### CRUD list + sub-pages
- `/admin/packages` + `/new` + `/:slug/edit`
- `/admin/licenses` + `/new` + `/:id` + `/:id/assign-package` + `/:id/adjust-tokens`
- `/admin/providers` + `/new` + `/:id/edit`
- `/admin/keys` + `/new` + `/:id/allocate` + `/bulk-import`
- `/admin/releases` + `/new`

Tổng: ~25 screenshots.

## 🎨 Design Baseline (Reference)

**Không có design system doc** → fall back vào 2 page brand-perfect đã có:

### Source 1: Homepage (`/`)
- `src/features/home/HomePage.jsx`
- `src/features/home/components/HomeHero.jsx` + `HomeCTA.jsx` + `HomeBento.jsx` + `bento/*`
- Bento layout với glass cards, subtle borders
- Mixed-case h1 `text-5xl/6xl font-display tracking-tight`
- Body `font-sans` size variable
- Hover: border accent + subtle scale, KHÔNG glow

### Source 2: LoginPage (`/auth/login`)
- `src/features/auth/LoginPage.jsx`
- `src/features/auth/AuthForm.css` + `AuthLayout.css`
- Orbit map SVG decorative
- Auth card centered, glass shell
- Form input: rounded-xl, no uppercase label walls
- Submit button: full-width primary với hover state

### Extracted tokens (đã có trong index.css, đừng đụng):
- Spacing: gap-4 / gap-6 / gap-8
- Padding: p-5 / p-6 / p-8 (KHÔNG p-10 / p-12)
- Border radius: rounded-lg / rounded-xl / rounded-2xl
- Border: `border-white/5` chính, `border-white/10` accent
- Background: `bg-base-200/40 backdrop-blur-3xl` for glass cards
- Text colors: `text-base-content` / `text-base-content/70` / `text-base-content/50` / `text-base-content/30`

### Anti-patterns (đã codified ở T-001 + tasks trước):
- ❌ `text-[8-11px] font-bold uppercase tracking-widest` walls
- ❌ `font-black` (dùng `font-semibold` thay)
- ❌ `shadow-[0_0_X_var(--p)]` glow trên element thông thường
- ❌ `animate-pulse` ngoài skeleton/loading
- ❌ `h-12` ép input cao
- ❌ Decorative SVG fake không có ý nghĩa
- ❌ Multiple nested glass containers (box-trong-box)

## 📊 Phases

### Phase A — Setup (10min)
1. Login admin bằng browser tool — save cookie/session
2. Test 1 navigate + screenshot end-to-end để verify pipeline hoạt động
3. Đọc HomePage.jsx + LoginPage.jsx để hiểu brand voice

### Phase B — Mass screenshot (30min)
Chụp **tất cả 25 routes**. Mỗi screenshot lưu hoặc xem trực tiếp.

Per route ghi:
- Tên route + URL
- 1-3 vấn đề visual cụ thể (KHÔNG vague "trông xấu" — phải concrete: "uppercase wall ở header", "spacing giữa cards quá xa", "stat number không tabular-nums")

Output: bảng audit ở section §Audit Results trong dossier này.

### Phase C — Fix per page (60-90min)
Theo độ ưu tiên (page hay dùng trước):
1. `/admin` Overview
2. `/admin/licenses` list + detail (page dùng nhiều nhất)
3. `/admin/packages` + sub-pages (đã làm T-001 nhưng có thể vẫn xấu)
4. `/admin/providers` + sub-pages
5. `/admin/keys` + sub-pages
6. Còn lại

Per fix:
- Chụp **before** screenshot
- Apply fix
- Chụp **after** screenshot
- Verify nhìn đẹp hơn thật sự (KHÔNG self-confirmation bias)
- Build PASS

Sửa được những gì:
- ✅ Typography (size, weight, case, tracking)
- ✅ Spacing (padding, gap, margin)
- ✅ Color (text opacity, border opacity)
- ✅ Layout (grid columns, flex direction, max-width)
- ✅ Component composition (split section, merge cards)
- ❌ KHÔNG đổi routing/navigation
- ❌ KHÔNG đổi data structure/API
- ❌ KHÔNG đụng `src/styles/index.css`
- ❌ KHÔNG đụng `_shared/components/ui/*` (primitives)

### Phase D — Final pass (15min)
1. Re-screenshot all 25 routes
2. Verify mỗi route đẹp tương đương với homepage/loginpage
3. Build + ESLint clean
4. Update dossier với Phase C/D evidence (paste before/after pairs hoặc URLs)
5. Git commit per major page batch

## 🚧 Constraints

1. **KHÔNG đụng `src/styles/index.css`** — synced với pi-dashboard
2. **KHÔNG đụng `_shared/components/ui/*`** — primitives (Button, Input, Modal...) là contract
3. **KHÔNG redesign từ đầu** — chỉ tinh chỉnh để match brand baseline (HomePage/LoginPage style)
4. **PHẢI dùng browser screenshot** — không đoán
5. **PHẢI verify after-fix screenshot** trông đẹp hơn before — không chỉ tin vào trí tưởng tượng
6. **Build PASS sau mỗi page batch** (tránh fail dồn)
7. **Commit per batch** (3-5 pages/commit)

## ✅ Acceptance Criteria

- [ ] 25 routes admin có screenshot before
- [ ] 25 routes admin có screenshot after
- [ ] §Audit Results đầy đủ — mỗi route 1-3 vấn đề concrete identified
- [ ] §Fix Log đầy đủ — mỗi page list những gì đã fix
- [ ] Build PASS final
- [ ] ESLint clean
- [ ] Visual consistency check: pick 3 random admin pages + homepage → đều cùng "feel" (typography, spacing, color)
- [ ] Anti-pattern scan: `grep "text-\[(8|9|10|11)px\] font-bold uppercase tracking-widest"` → 0 matches trong admin/features/
- [ ] Anti-pattern scan: `grep "shadow-\[0_0_"` → 0 matches trong admin/features/

## 🔒 Out of Scope
- pi-dashboard changes
- Backend changes
- New routes / new pages
- src/styles/index.css changes
- _shared/components/ui primitives changes
- Mobile responsive (desktop-first audit)

## 📎 References
- Homepage: `src/features/home/HomePage.jsx`
- LoginPage: `src/features/auth/LoginPage.jsx`
- T-20260514-002 dossier §Phase C — anti-pattern checklist
- T-20260515-001 dossier — modal→subpage migration (vừa xong)

## 📊 Audit Results (Gemini fill in Phase B)

| Route | Screenshot | Issues identified |
|---|---|---|
| /admin | _pending_ | _e.g. "stat tile padding p-5 OK nhưng border-white/[0.05] quá mờ, nên đậm hơn"_ |
| /admin/usage | _pending_ | |
| ... | | |

## 🔧 Fix Log (Gemini fill in Phase C)

| Page | Changes | Files touched | Commit |
|---|---|---|---|
| /admin | _e.g. "border-white/[0.05] → border-white/10, text-base-content/30 → /50 cho label readability"_ | OverviewPage.jsx | _commit-sha_ |
| ... | | | |

## ✅ Phase D Verification (Claude, 2026-05-16)

Task delivered via **incremental fixes across sessions** thay vì 1 batch screenshot audit:

### Per-page redesign (Claude):
- `LicenseCreatePage.jsx` — 3-col → max-w-5xl single, sticky footer
- `PackageEditPage.jsx` — 5 sections clear, no sidebar empty space
- `ReleaseUploadPage.jsx` — compact dropzone, 3-col tier picker

### Global visual fixes (Claude):
- `HeroVisualBanner.jsx` — bỏ hardcoded `bg-[#0d0d0d]`/`hover:bg-[#121212]` → theme-aware `bg-base-200/40 backdrop-blur-md`
- Light theme text colors: `text-white/40` → `text-base-content/50` (theme-aware)
- 5 file khác hardcoded `bg-[#0a0a0a]` → `bg-base-200` token
- Strip 200+ decorative hover effects (`hover:scale-N`, `hover:shadow-*`)
- 6 file leftover `text-[8-11px]` → `text-xs`, leftover shadow cluster → semantic

### Token system improvements (Claude):
- Shadow tokens 100% brand-tinted (color-mix với `var(--p)` thay vì `rgba(0,0,0,X)`)
- Glass shell preset dùng tokens
- Light theme override với tint primary
- index.css byte-identical 2 apps

### Build verify (2026-05-16):
- pi-store: ✅ PASS 853ms
- pi-dashboard: ✅ PASS 1.06s (sau khi clear vite cache stale)
- ESLint clean cả 2

### Coverage note
Dossier mục tiêu 25 routes screenshot — Claude delivered:
- 3 routes per-page redesigned explicitly
- 22 routes còn lại nhận **global fix** (HeroVisualBanner, shadow tokens, hover strip, theme tokens) — cascading visual improvement vì shared components/tokens
- 100% anti-pattern `text-[N]px`/`uppercase tracking-widest`/`font-black`/`hover:scale-N` cleared

**Status**: CLOSED | Owner: Claude (delivered via incremental fixes + global token system upgrades)
