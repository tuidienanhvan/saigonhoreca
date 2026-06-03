---
id: T-20260519-003
owner: claude
state: completed
priority: P2
risk: high
estimated_minutes: 480
actual_minutes: 95
parent: T-20260518-001
children: []
depends_on: [T-20260518-001, T-20260518-002]
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex]
created: 2026-05-19 01:00
updated: 2026-05-19 02:35
phase_a_completed: 2026-05-19 01:25
phase_b_completed: 2026-05-19 01:55
phase_c_completed: 2026-05-19 02:15
phase_d_completed: 2026-05-19 02:30
phase_e_completed: 2026-05-19 02:35
final_report: changes/T-20260519-003/FINAL-REPORT.md
project: saigonhoreca-theme
scope:
  allowed:
    - "Local Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/**"
    - "Local Sites/saigonhoreca/app/public/wp-content/.task-handoffs/**"
    - "Local Sites/saigonhoreca/app/public/wp-content/uploads/**"
    - "Local Sites/saigonhoreca/app/public/wp-content/db/**  (mysqldump for backups)"
---

# 📋 T-20260519-003 | claude | saigonhoreca-data-migration

## 0. 📥 User Original Intent (Phase 0 — Verbatim)

> "rồi các bài post data là ở đâu?"

User noticed that browsing saigonhoreca.local shows the scraped pages, but the
WP backend (wp-admin → Posts/Pages) is empty. They want the **content actually
in WordPress** so it can be edited, searched, syndicated, etc.

This is Phase 2 of the mirror→native migration. Until done, saigonhoreca.local
is a **static replica** of saigonhoreca.vn, not an editable WP install.

---

## I. 📊 Frontmatter Snapshot

| Field | Value |
|---|---|
| `id` | T-20260519-003 |
| `state` | drafted (awaiting user go-ahead) |
| `priority` | P2 — important but not blocking; mirror works |
| `risk` | HIGH — touches WP DB; needs full backup before each phase |
| `estimated_minutes` | 480 (8h, multi-session) |

---

## II. 🎯 Mục tiêu / Goal

Convert the 298 scraped HTML pages into real WordPress entities:

| Source HTML pattern | Target WP type | Count est |
|---|---|---|
| `/<post-slug>/` | `post` (existing) | ~50 blog articles |
| `/du-an/<slug>/` | CPT `project` (custom: dự án case study) | ~25 |
| `/san-pham/<slug>/` | CPT `product` (WooCommerce or custom) | ~150 |
| `/ve-saigon-horeca/`, `/lien-he/`, etc. | `page` | ~10 core pages |
| `/danh-muc-san-pham/<cat>/` | `category` (product taxonomy) | ~15 |
| `/tac-gia/<slug>/`, `/tag/<slug>/` | author/tag archives | (taxonomy auto) |

For each entity:
1. Parse the static HTML (Cheerio/BeautifulSoup)
2. Extract: title, slug, content body, featured image, meta description, categories,
   tags, published date, author
3. Insert into `wp_posts` + `wp_postmeta` + `wp_term_relationships`
4. Download featured + content images into `wp_uploads/<year>/<month>/`
5. Rewrite internal links from absolute → WP permalinks
6. Verify wp-admin can edit each post; preview matches scraped HTML closely

Once migrated, the static mirror can be retired (or kept as snapshot reference).

---

## III. 🧩 Sub-phases

### Phase A — Custom Post Types + taxonomies (60 min)
Register CPTs in theme functions or a plugin:
- `project` (sản phẩm case study)
- `product` (if not using WooCommerce; else activate Woo)
- Custom taxonomies: `project_category`, `product_category`, `product_brand`
Add rewrite rules so `/du-an/<slug>/`, `/san-pham/<slug>/` route to the CPT.

### Phase B — HTML parser (120 min)
Python script `parse-scrape.py`:
- Walk every `static-mirror/<slug>/index.html` (skip AMP variants)
- For each: extract Elementor-rendered body (everything inside `<main>` or
  `<article>`) — strip Astra header/footer/widgets
- Output structured JSON: `{slug, post_type, title, content, excerpt, date,
  featured_image, categories, tags}`

### Phase C — Image import (90 min)
Python script `import-images.py`:
- For every image referenced in scraped HTML
- Download (already on disk in `static-mirror/wp-content/uploads/`) to
  `wp_uploads/<year>/<month>/<filename>`
- Generate thumbnail sizes (or rely on WP regenerate)
- Insert `wp_posts` row type=`attachment` + `wp_postmeta` (_wp_attached_file, etc.)
- Build URL map: old `static-mirror/.../img.jpg` → new attachment ID + URL

### Phase D — Post import (120 min)
Python script `import-posts.py`:
- For each entity in JSON from Phase B
- `wp_insert_post()` via WP-CLI or direct SQL with proper escape
- Set featured image (link to attachment from Phase C)
- Set categories/tags via `wp_set_object_terms()`
- Run content through URL-rewrite: scraped image src → new attachment URL
- Set permalink structure to match scraped URLs (so existing inbound links work)

### Phase E — Verification + retire mirror (60 min)
- Browse 10 random URLs: visual match between mirror serve and native WP serve
- Check wp-admin: can edit, can publish, can delete
- Disable `sgh_mirror_serve` action (or add condition: only fire if no matching
  WP post exists)
- Keep `static-mirror/` folder for asset reference (uploads still serve from there
  until image import is 100% complete)

### Phase F — Documentation + archive (30 min)
- Final report
- Schema doc: post_meta keys used, CPT structure, taxonomy slugs

---

## IV. 🚨 Risks

| Risk | Mitigation |
|---|---|
| Elementor content needs Elementor plugin to render properly | Either keep Elementor plugin installed OR strip Elementor markup and convert to clean HTML (lossy) |
| Image deduplication — same image may appear in multiple posts | Hash-based dedup, single attachment per file |
| URL rewrites break inbound links from production | Set permalink to match production paths exactly; add 301 fallback for any deltas |
| WP_insert_post slow at 200+ entities | Use direct SQL with mysqldump style; verify integrity per chunk |
| DB schema conflict — saigonhouse-theme custom tables (pi_*) | DON'T require them; CPTs use native WP tables only |
| Permission / sandbox blocks DB writes | Verify wp-cli works; have fallback PHP script |

---

## V. ⚠️ Open Questions (need user decision before starting)

1. **Elementor plugin install?**
   - Option A: Install Elementor Pro on saigonhoreca.local, content renders via Elementor
   - Option B: Strip Elementor, convert to vanilla blocks (cleaner but lossy)
   - Recommendation: A (matches production stack)

2. **WooCommerce for sản phẩm?**
   - Option A: Use WooCommerce CPT `product` (matches production)
   - Option B: Custom `product` CPT (lighter, but rewrites needed)
   - Recommendation: A

3. **Mirror retirement strategy?**
   - Option A: Hard cutover (disable mirror once import done)
   - Option B: Mirror falls through to native WP if post exists (gradual)
   - Recommendation: B (safer)

---

## VI. 📋 Out-of-scope

- ❌ User accounts / comments / form submissions migration
- ❌ Multilingual (English) — separate task with WPML/Polylang
- ❌ Analytics + Google Tag Manager re-wiring
- ❌ Production deployment (this dossier is local migration only)
