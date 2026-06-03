# Task Template — Project-Pillar Upgrade (crawl + CSS gold-standard) — v1.5

> **reconstructed: 2026-06-03** (file NULL — rebuild tối thiểu từ `tasks/README.md` + dossier skeleton `../DOSSIER.md`. Đây là **spec/quality-bar**, KHÔNG có frontmatter, KHÔNG tự load — orchestrator đọc rồi copy/fill §VI Phases + §VIII Acceptance vào dossier thật).
>
> **Task type**: Crawl static-mirror → refactor CSS gold-standard cho 1 trang `template-parts/project-pillar/<slug>`.
> **Default agent**: qwen / gemini · **Risk**: medium · **Est. time**: ~90 min.
> **Cách dùng**: tạo dossier bằng `new-task.sh` (xem `tasks/README.md` §Usage), rồi fill scope theo §A/§B/§G dưới đây.

---

## §A. Quality Bar (acceptance floor — fill vào dossier §VIII)

- [ ] **Visual parity ≥ 95%** với static-mirror đã crawl (so screenshot before/after).
- [ ] **0 mojibake** — UTF-8 + diacritic tiếng Việt nguyên vẹn.
- [ ] **Responsive** — pass ở 3 breakpoint: mobile 390px · tablet 768px · desktop 1440px.
- [ ] **CSS hợp chuẩn project** — Tailwind v4 conventions; không inline style rác; không `!important` trừ khi justify.
- [ ] **0 regression** — các pillar khác không bị ảnh hưởng (scope-isolated CSS).
- [ ] **Build + lint exit 0** (raw evidence dán §XII).
- [ ] **Scope clean** — `git status --short` chỉ chạm `template-parts/project-pillar/<slug>/*`.

## §B. CSS Techniques (gold-standard — guideline cho §VI Phases)

- Fluid type scale: `clamp()` thay vì breakpoint cứng cho font-size.
- Layout: CSS Grid / Flexbox; tránh absolute positioning trừ overlay.
- Spacing scale nhất quán (Tailwind spacing tokens, không magic number).
- Color: dùng theme token / CSS var, không hardcode hex lặp.
- Animation: `transition` mượt, respect `prefers-reduced-motion`.
- Image: `loading="lazy"` + aspect-ratio để tránh CLS.

## §G. Crawl Spec (Phase 1 — Audit & Baseline)

1. Crawl static-mirror của trang gốc → lưu HTML + asset vào `scratch/` (read-only reference).
2. Diff cấu trúc DOM hiện tại vs mirror — list khác biệt cần đạt.
3. Screenshot baseline (3 breakpoint) làm before-evidence.
4. Grep CSS hiện có trong `<slug>/` — xác định cái giữ / cái refactor.

---

## §H. Phases (copy vào dossier §VI, thay `<slug>`)

1. **Audit & Baseline** 🔍 — chạy §G crawl spec; confirm trang render ổn định trước khi sửa.
2. **Implementation** 🛠️ — refactor CSS theo §B; atomic edits trong `template-parts/project-pillar/<slug>/*`.
3. **Verification** 🧪 — `npm run lint` + `npm run build` + `git status --short` + screenshot 3 breakpoint (after).
4. **Reporting** 📤 — REPORT block, dán before/after + raw build/lint.

## §I. Allowed Scope (gợi ý §IV — customize path thật)
- 📄 `template-parts/project-pillar/<slug>/` (CSS + template-part)

## §J. Out-of-Scope (gợi ý §V)
- ❌ Pillar khác ngoài `<slug>`.
- ❌ Global CSS / theme config.
- ❌ `package.json`, build config.
- ❌ Thêm npm dependency.

---

> See also: `tasks/README.md` (usage) · `../DOSSIER.md` (skeleton 17 sections) · `../../QUALITY-GATES.md` (4+HITL) · `../../LIFECYCLE.md` (state machine).
