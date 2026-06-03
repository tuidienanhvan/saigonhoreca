# DOSSIER | T-20260505-002 | Tailwind CSS v4 Integration

- **Agent**: Antigravity
- **Scope**: `package.json`, `assets/css/`, `inc/core/enqueue.php`, `functions.php`
- **Goal**: Integrate Tailwind v4 into the theme, keeping existing Design Tokens and Shared UI.
- **Status**: [ ] GENESIS | [/] ATOMIC BUILD | [ ] QUALITY GATE | [ ] PERSISTENCE

---

## 🏗️ EXECUTION LOG

### [2026-05-05 11:33] KHỞI TẠO PLAN
- Created `implementation_plan.md` in artifacts.
- Target: Tailwind v4 (CSS-first engine).

### [2026-05-05 11:34] CHUẨN BỊ THỰC THI
- [ ] Step 1: Initialize `package.json` and install Tailwind v4 CLI.
- [ ] Step 2: Configure `style.css` with `@import "tailwindcss"` and `@theme`.
- [ ] Step 3: Setup build scripts.
- [ ] Step 4: Update `enqueue.php` to load compiled CSS.

---

## 🧪 EVIDENCE & LOGS
(Will be updated during execution)

---

## 🧹 QUALITY GATES
- [ ] Build Gate: `npm run build` generates a minified CSS file.
- [ ] Lint Gate: N/A
- [ ] Integrity Gate: Verify `--pi-*` variables are still working.
- [ ] Logic Gate: Tailwind utilities (`flex`, `p-4`, etc.) are functional in PHP templates.

---

**Mantra**: *"Code in English, Think in Logic, Record in Dossier, Speak in Vietnamese."*
