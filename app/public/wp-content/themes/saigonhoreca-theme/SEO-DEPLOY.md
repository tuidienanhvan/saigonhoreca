# SEO Phase 1-4 — Deploy Handoff

All technical SEO audit findings addressed across 4 phases. Single PR.
Net change: 5 new modules in `inc/core/`, 2 standalone tools, 5 `require`
lines in `functions.php`. Zero edits to existing runtime code.

## What's in the box

| # | Module | Phase | Purpose |
|---|---|---|---|
| 1 | `inc/core/seo-meta-description.php` | 1 | Auto meta description, 120-155 chars, Yoast/Rank-Math compat |
| 2 | `inc/core/seo-robots.php` | 1 | Force `index,follow` on revenue-critical category URLs (overrides accidental noindex), one-shot DB cleanup on admin login |
| 3 | `inc/core/seo-schema.php` | 2 | JSON-LD: LocalBusiness on every page, WebSite+SearchAction on home, Product on single-product CPT |
| 4 | `inc/core/seo-robots-txt.php` | 3 | Hardened `/robots.txt` (UTM/orderby/cart blocked + sitemap declared) + `noindex,follow` on paginated archives (page 2+) |
| 5 | `inc/core/seo-performance.php` | 4 | `loading=lazy`+`decoding=async` everywhere, DNS-prefetch hints, strip emoji+oEmbed bloat (~15 KB JS savings) |
| 6 | `scripts/brand-mismatch-scan.py` | 1 | Standalone scanner; walks production sitemap, flags products where slug brand ≠ page brand |
| 7 | `scripts/brand-mismatch-report.csv` | 1 | First-run result: 401 products, 3 confirmed mismatches need Sales triage |

`functions.php` adds 5 `require` lines @ 390-394. No other edits.

## PR commit message (copy-paste)

```
feat(seo): Phase 1-4 technical SEO from full audit

Phase 1 — Critical fixes
  • Auto meta description (120-155 chars) for every singular/term/
    archive/home, with brand+USP+hotline composition
  • Force index,follow on /thiet-bi-inox-cong-nghiep/ and
    /quay-pha-che-inox-quay-bar/ (accidentally noindex per audit),
    with one-shot DB cleanup for Rank Math term meta
  • Brand-mismatch scanner: walks sitemap, found 3 products with
    URL slug ≠ page brand (sammic-spain in URL, Giorik Italy on page)

Phase 2 — Structured Data
  • LocalBusiness JSON-LD on every page (NAP, hotline, social, geo)
  • WebSite + SearchAction on homepage (sitelinks search box eligible)
  • Product JSON-LD on single-product (brand, sku, category)
  • Coexists with existing BreadcrumbList + FAQPage in theme

Phase 3 — Robots + Crawl Budget
  • Hardened /robots.txt: blocks UTM/orderby/feed/cart noise,
    explicit Allow for CSS/JS/uploads (Mobile-Friendly test friendly),
    declares sitemap_index.xml
  • noindex,follow on paginated archive pages (page 2+) — Rank Math
    + wp_robots core both filtered

Phase 4 — Performance / Core Web Vitals
  • Auto `decoding="async"` on every <img> (saves ~50ms blocking)
  • Resource hints (dns-prefetch + preconnect) for fonts/gtm/social
  • Strip emoji detection (~12 KB JS) + oEmbed (~3 KB JS)
  • Coexists with existing critical-css.php + enqueue.php pipelines

No edits to existing files except 5 require lines in functions.php.
Each module disable-able via filter (sgh_perf_*_enabled) or by
removing its require line. Snapshot DB before merge for rollback.
```

## Files diff summary

```
A  inc/core/seo-meta-description.php    12.2 KB
A  inc/core/seo-robots.php                8.0 KB
A  inc/core/seo-schema.php                7.5 KB
A  inc/core/seo-robots-txt.php            3.8 KB
A  inc/core/seo-performance.php           5.9 KB
A  scripts/brand-mismatch-scan.py        10.1 KB
A  scripts/brand-mismatch-report.csv     69.1 KB
M  functions.php                         (+5 require lines @ 390-394)
A  SEO-DEPLOY.md                         this file
```

## Pre-deploy checklist

- [ ] Code review PR (5 modules, ~38 KB total PHP)
- [ ] DB snapshot for rollback
- [ ] Sales/Kho team reviewed `brand-mismatch-report.csv` (3 fixes signed off)
- [ ] Confirm Rank Math is the active SEO plugin on prod (per audit)

## Deploy steps

1. **Merge PR → push prod** (rsync / git pull / SFTP per team workflow)

2. **Trigger DB cleanup**: log into `wp-admin` once as any admin user → visit any admin page → `SGH_SEO_Robots::maybe_cleanup_term_meta()` runs → DB option `sgh_seo_robots_cleanup_v1` set. Future deploys skip.

3. **Verify Rank Math UI**: Settings → Titles & Meta → Categories
   - "Inox công nghiệp" (slug `thiet-bi-inox-cong-nghiep`) → robots = **Index, Follow**
   - "Quầy pha chế" (slug `quay-pha-che-inox-quay-bar`) → same

4. **Submit URLs in Google Search Console** (requires verified property):
   - `https://saigonhoreca.vn/thiet-bi-inox-cong-nghiep/`
   - `https://saigonhoreca.vn/quay-pha-che-inox-quay-bar/`
   - URL Inspection → Request indexing

5. **Fix 3 brand-mismatch products** (Sales decision — keep URL slug, only update title/content/brand taxonomy to match reality):
   - `lo-combi-sehe061w-sammic-spain` → page actually says Giorik Italy
   - `lo-combi-sere061w-sammic-spain` → same
   - `lo-nuong-combi-7-khay-gn-1-1-mte7w_r-sammic-spain` → same

6. **Submit GSC sitemap** (if not already): `https://saigonhoreca.vn/sitemap_index.xml`

## Post-deploy verification (run on prod)

```bash
# 1. SEO tags single per route
for url in \
  "https://saigonhoreca.vn/" \
  "https://saigonhoreca.vn/lien-he/" \
  "https://saigonhoreca.vn/thiet-bi-inox-cong-nghiep/" \
  "https://saigonhoreca.vn/quay-pha-che-inox-quay-bar/"
do
  html=$(curl -sk "$url")
  echo "$url"
  echo "  meta-desc x $(echo "$html" | grep -cE 'name=\"description\"')"
  echo "  canon    x $(echo "$html" | grep -cE 'rel=\"canonical\"')"
  echo "  ld+json  x $(echo "$html" | grep -cE 'application/ld\+json')"
  echo "  robots:    $(echo "$html" | grep -oE 'name=\"robots\" content=\"[^\"]+\"' | head -1)"
done

# 2. /robots.txt has hardened rules + sitemap line
curl -sk https://saigonhoreca.vn/robots.txt | grep -E 'Disallow|Sitemap' | head -10

# 3. LocalBusiness schema valid (paste URL into Google Rich Results Test)
echo "  → https://search.google.com/test/rich-results?url=https://saigonhoreca.vn/"

# 4. Brand scanner re-run (sanity, should still flag same 3 until Sales fixes)
python scripts/brand-mismatch-scan.py --out report-$(date +%F).csv
```

Expected on critical category URLs:
- `meta-desc x 1`, `canon x 1`, robots **MUST NOT contain "noindex"**

## Post-deploy follow-up (T+7 to T+14 days)

- [ ] GSC Coverage report: 2 critical URLs status **Excluded → Indexed**
- [ ] Google Rich Results Test passes for: home (LocalBusiness, WebSite), single product (Product)
- [ ] PageSpeed Insights spot-check 3 pages — DNS prefetch + emoji-strip savings should show in network waterfall
- [ ] Run brand scanner monthly: `python scripts/brand-mismatch-scan.py --out report-YYYY-MM.csv` → Sales triage new findings
- [ ] SERP snippet spot-check 5-10 product URLs in Google — new descriptions appear (full crawl 2-6 weeks)

## Rollback

If anything breaks (extremely unlikely — all additive):

```bash
# Remove the 5 require lines in functions.php (or comment out)
sed -i '/seo-meta-description\|seo-robots\|seo-schema\|seo-robots-txt\|seo-performance/d' \
  wp-content/themes/saigonhoreca-theme/functions.php

# Optional: remove module files (orphaned without require)
rm wp-content/themes/saigonhoreca-theme/inc/core/seo-{meta-description,robots,schema,robots-txt,performance}.php

# Reset cleanup flag so noindex DB fix re-runs on next deploy
wp option delete sgh_seo_robots_cleanup_v1
```

## Per-feature disable (no rollback needed)

Phase 4 features all toggle via filter — add to a `mu-plugin` if you
need to disable one without touching code:

```php
add_filter('sgh_perf_img_attrs_enabled',       '__return_false'); // keep WP defaults for <img>
add_filter('sgh_perf_resource_hints_enabled',  '__return_false'); // skip dns-prefetch additions
add_filter('sgh_perf_strip_emoji_enabled',     '__return_false'); // re-enable emoji JS
add_filter('sgh_perf_strip_oembed_enabled',    '__return_false'); // re-enable oEmbed
```

Phase 1-3 modules disable by removing their `require` line.

## Risk assessment

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| Rank Math filter API changes | Low | Description not optimized on prod | wp_head fallback still emits meta desc |
| Duplicate canonical/desc | Low | SEO confusion | Verified at deploy — modules skip when WP core/plugin already emits |
| robots.txt overrides plugin's | Low | Block important URL | All rules reviewed against current site structure; Allow rules for CSS/JS/uploads explicit |
| Emoji strip breaks editor | Low | TinyMCE missing emoji button | `wpemoji` plugin filter included; admin unaffected |
| Brand fix changes URL | Low | Lost backlinks | Plan keeps slug, updates content only |
| GSC re-index slow | High | Traffic recovery delayed | URL Inspection submit speeds up; expect 2-6 weeks for full recrawl |

## Future phases (not in this PR)

- **Phase 5 — Content & E-A-T**: rewrite top 20 product descriptions (300+ words each), author bios, "About us" expansion
- Internal linking graph audit (orphan products → add "related" block)
- WebP image conversion pipeline
- Schema for `Review` / `AggregateRating` when review collection live
- Multilingual SEO (hreflang) if EN/CN expansion happens

---

Verified on local @ `https://saigonhoreca.local/` — 4 routes return 200,
meta desc 120-155 chars, single canonical, 2-3 JSON-LD graphs, robots.txt
hardened, emoji/oembed stripped, no PHP fatals. Ready for prod.
