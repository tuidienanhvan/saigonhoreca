# 🎯 📍 Ngữ cảnh dự án / Project Context — Saigon Horeca

This is the source of truth for ChatGPT / Codex / Gemini tasks in this workspace.

## 1. 📂 🏠 Thư mục làm việc / Workspace

`C:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content`

Theme root: `themes/saigonhoreca-theme/`

## 2. 📍 🚀 Các khu vực đang hoạt động / Active Areas

- `themes/saigonhoreca-theme/` — main theme, BEM pillar architecture, Tailwind v4 bundles per route
- `themes/saigonhoreca-theme/inc/core/` — `slug-map.php`, `img-helper.php`, `pillar-routes.php`, `enqueue.php`, etc.
- `themes/saigonhoreca-theme/template-parts/project-pillar/<slug>/` — 32 pillar projects (8 semantic slots each)
- `themes/saigonhoreca-theme/page-templates/page-project-<slug>.{php,css}` — pillar page templates
- `themes/saigonhoreca-theme/assets/css/` — per-route bundles (`style-{home,project,about,contact}.css` + dist)
- `themes/saigonhoreca-theme/scripts/` — Python/Bash automation (extract, render, suffix, generate)
- `themes/saigonhoreca-theme/languages/` — i18n .pot/.po/.mo (NEW — to be populated by i18n task)
- `themes/saigonhoreca-theme/static-mirror/` — scraped HTML + Elementor CSS reference (DO NOT edit)

## 3. 🌐 🇻🇳🇺🇸 Bilingual deployment topology

| Domain | Lang | WPLANG | Content | URL examples |
|--------|------|--------|---------|--------------|
| `saigonhoreca.vn` | Vietnamese (default) | `vi` | DB-A | `/du-an/heiwa-sushi-omakase/`, `/lien-he/`, `/ve-saigon-horeca/` |
| `saigonhoreca.com` | English | `en_US` | DB-B | `/projects/heiwa-sushi-omakase/`, `/contact/`, `/about/` |
| `saigonhoreca.local` | Local dev (treated as .vn) | `vi` | DB-local | mirrors .vn routing |

- Theme code **shared** (git submodule across both installs)
- Routing handled by `inc/core/pillar-routes.php` (matches both `/du-an/` and `/projects/`)
- Slug map: `inc/core/slug-map.php` (`sgh_slug`, `sgh_url`, `sgh_translate_path`, `sgh_counterpart_url`, `sgh_hreflang_tags`, `sgh_is_english_site`)
- Image URLs: `inc/core/img-helper.php` → `sgh_img($path)` returns `https://saigonhoreca.vn/wp-content/uploads/...` (always production CDN)
- Switcher button: `header.php` calls `sgh_counterpart_url()`
- SEO: `sgh_hreflang_tags()` injects `<link rel="alternate" hreflang>` for cross-domain

## 4. 🚧 🚫 Phạm vi ngoài dự kiến / Out Of Scope (default)

- `static-mirror/` files — read-only reference, never edit
- Pillar PHP markup (`template-parts/project-pillar/<slug>/*.php`) — image URLs must stay identical to prod
- Plugin files outside theme
- WordPress core
- Speculative redesigns without explicit user request
- Build artifact churn unless task is specifically about build output
- Reference pillar projects (Gemini-polished): `amdang-typhoon`, `godmother-friendship`, `grand-marble-thuong-hieu-banh-cao-cap-nhat-ban`, `bambino-saigonhoreca` — DO NOT modify CSS
- DB content / posts / pages — managed by content team, not theme dev

## 5. 📜 🏗️ Architecture conventions

### CSS
- **100% token-based**: `var(--gold)`, `var(--b1)`/`--b2`/`--b3`/`--bd`, `var(--bc)`/`--bc2`/`--bc3`, `var(--ease-spring)`, `var(--radius-md)`/`--sm`/`--full`, `var(--font-display)`
- NO hardcoded hex except `rgba(0,0,0,X)` for shadows
- BEM namespace suffix per pillar (`-at`, `-hwa`, `-gmb`, etc.) for isolation — NEVER add/remove suffixes
- Section CSS lives in slot CSS siblings (NOT page-template.css which is layout-only)
- Per-route bundles: `style-{home,project,about,contact,...}.css` → `dist/theme-*.css`

### PHP
- Image URLs: ALWAYS use `sgh_img('YYYY/MM/file.jpg')` — never hardcode mirror path
- Internal URLs: ALWAYS use `sgh_url('section_key')` — never hardcode `/du-an/`, `/lien-he/` etc.
- Text strings: prefer `__('text', 'saigonhoreca')` (i18n) over hardcoded Vietnamese
- All BEM classes already suffixed — do not modify

### Build
- `npm run build:project` — pillar CSS bundle
- `npm run build:home` — home CSS bundle
- `npm run build` — full build (skips theme.css due to single.css ref issue, harmless)

## 6. 🔑 🧰 Helper APIs

| Function | Returns | Use case |
|----------|---------|----------|
| `sgh_img($path)` | absolute prod URL | `<img src="<?php echo sgh_img('2024/01/foo.jpg'); ?>">` |
| `sgh_url($key, $sub='')` | section URL in current lang | `home_url('/du-an/')` → `sgh_url('projects_index')` |
| `sgh_slug($key)` | slug string | `sgh_slug('contact')` → `'lien-he'` or `'contact'` |
| `sgh_translate_path($path, $to_en)` | translated path | Internal — slug-map regex swap |
| `sgh_counterpart_url()` | URL on opposite-lang site | Switcher button href |
| `sgh_hreflang_tags()` | echoes `<link rel="alternate">` | Called from `<head>` |
| `sgh_is_english_site()` | bool | `if (sgh_is_english_site()) { ... }` |

## 7. 🛠️ Verification stack

```bash
cd "C:/Users/Administrator/Local Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme"
npm run build:project    # build CSS
npm run build:home       # build home CSS
touch template-parts/project-pillar/<slug>/hero.php   # opcache invalidate
curl -skL -o /dev/null -w "%{http_code}" "http://saigonhoreca.local/du-an/<slug>/"
```

## 8. 📊 Current state snapshot (2026-05-22)

- 32/32 pillar projects HTTP 200 (15 in `/du-an/` + 17 root-level)
- Both `/du-an/<slug>/` and `/projects/<slug>/` routes serve identically (pillar-routes.php regex updated)
- Bilingual hreflang emit working on all pages
- Switcher button switches domain + translates path correctly
- Image URLs: 100% production CDN (`sgh_img()` helper, all 170+ PHP files refactored)
- Internal URLs: 14 templates refactored to `sgh_url()` (homepage, header, footer, nav, etc.)
- i18n: **NOT YET IMPLEMENTED** (next task)
- Theme stable, build clean ~500-700ms per bundle

## 9. 🔗 Related handoff docs (Temp dir, outside .task-handoffs)

- `C:\Users\Administrator\AppData\Local\Temp\saigonhoreca-project-pillar-handoff.md` — pillar architecture + rules
- `C:\Users\Administrator\AppData\Local\Temp\saigonhoreca-creative-plan.md` — Gemini-style creative direction plan
- `C:\Users\Administrator\AppData\Local\Temp\saigonhoreca-agent-brief.md` — agent brief for creative redesign work
