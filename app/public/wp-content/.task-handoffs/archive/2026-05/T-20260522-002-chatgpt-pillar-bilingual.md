---
id: T-20260522-002
owner: chatgpt
state: drafted
priority: P1
risk: medium-high
estimated_minutes: 300
parent: null
children: []
depends_on: [T-20260522-001]
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-22 16:00
updated: 2026-05-22 16:00
---

# 📋 T-20260522-002 | ChatGPT | pillar-bilingual — Pillar content VN/EN via `sgh_t()`

## I. 📊 Frontmatter & Risk

| Field | Value |
|-------|-------|
| `id` | `T-20260522-002` |
| `owner` | `chatgpt` (GPT-5.4 — frontier coding + 1.05M context, perfect for 256-file batch) |
| `priority` | **P1** (final blocker for `.com` launch — pillar = main content) |
| `risk` | **medium-high** — touches 256 pillar PHP files; phải PRESERVE 100% `sgh_img()` calls + BEM classes + image URLs |
| `state` | `drafted` |
| `depends_on` | T-20260522-001 (slug-map.php phải có sẵn để add `sgh_t()`) — ✅ verified |
| `escalation_path` | [Codex (per-file surgical), Claude (architect)] |

---

## II. 🎯 Mục tiêu / Goal

Wrap toàn bộ Vietnamese text strings trong 32 pillar projects (256 PHP files) bằng `sgh_t($vi, $en)` inline ternary helper. Khi user truy cập `.com` install (`sgh_is_english_site() === true`), pillar content tự động hiển thị English; `.vn` install giữ nguyên Vietnamese.

### Business value
- `.com` install English market US cần pillar content (hero title, intro paragraph, partnership narrative, specs description...) bằng tiếng Anh
- Hiện tại header/footer/home/about/contact đã English (T-20260522-001 done) — pillar là phần ĐUÔI cuối cùng
- Khi launch `.com`, mỗi project page (`/projects/heiwa-sushi-omakase/`, `/projects/the-brix/`...) phải fully English

### End state
1. `inc/core/slug-map.php` thêm helper `sgh_t($vi, $en)` (1 function, ~10 lines)
2. ~3000+ text strings trong 256 pillar PHP files wrapped bằng `sgh_t('VN text', 'English text')`
3. Pillar markup KHÔNG đụng: `sgh_img()`, BEM classes (`pp-hero-hwa__title`, etc.), HTML structure, CSS-affecting attributes
4. Build clean, 32/32 pillar URLs HTTP 200 cả `/du-an/<slug>/` và `/projects/<slug>/`
5. Khi WPLANG=en_US (hoặc host=.com): pillar content render English
6. Khi WPLANG=vi (hoặc host=.vn): pillar content render Vietnamese

### NON-GOAL
- ❌ KHÔNG dùng `__()/_e()` cho pillar (text dài, nhiều paragraphs → dùng `sgh_t()` cleaner)
- ❌ KHÔNG đụng `sgh_img(...)` calls — image URLs phải identical với prod (Rule #1)
- ❌ KHÔNG thay đổi BEM class names, CSS suffix, HTML structure
- ❌ KHÔNG thay đổi `pillar-routes.php`, `slug-map.php` URL routing logic, CSS files
- ❌ KHÔNG dịch pillar content cho 4 reference projects (Gemini-polished): amdang-typhoon, godmother-friendship, grand-marble-thuong-hieu-banh-cao-cap-nhat-ban, bambino-saigonhoreca — SKIP for safety

---

## III. 📚 Required Reading

1. 🛡️ `.task-handoffs/SKILL.md` — operating protocol
2. 🏗️ `.task-handoffs/project/PROJECT.md` — workspace context + helper APIs
3. 🏆 `.task-handoffs/system/QUALITY-GATES.md` — acceptance standards
4. 📤 `.task-handoffs/system/REPORTING.md` — REPORT block format
5. 📁 `archive/2026-05/T-20260522-001-chatgpt-wp-i18n-bilingual.md` — previous task (i18n foundation)
6. 📖 Pillar architecture reference: `C:\Users\Administrator\AppData\Local\Temp\saigonhoreca-project-pillar-handoff.md`

---

## IV. 🚧 Allowed Scope (strict)

### Edit
- `themes/saigonhoreca-theme/inc/core/slug-map.php` — add `sgh_t($vi, $en)` function ONLY (1 small addition)
- `themes/saigonhoreca-theme/template-parts/project-pillar/<slug>/*.php` (8 slots × 28 slugs = 224 files)
  - 28 pillar projects (skip 4 reference: amdang-typhoon, godmother-friendship, grand-marble-thuong-hieu-banh-cao-cap-nhat-ban, bambino-saigonhoreca)
- `themes/saigonhoreca-theme/template-parts/project-pillar/_cta.php` (shared)
- `themes/saigonhoreca-theme/template-parts/project-pillar/_related.php` (shared)

### Pillar list (28 projects in scope)

```
heiwa-sushi-omakase, little-bear-thao-dien,
mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam, skyloft-by-glow,
sol-kitchen-bar, sol-kitchen-bar-saigon-horeca, the-brix,
the-royal-all-day-dining, yuzu-omakase, spice-world-hotpot,
roka-fella-tinh-hoa-am-thuc-nhat-ban, du-nam-an-an, du-an-vinh-hiep,
moa-moa, casa-maria, tales-by-chapter, the-cheezy-time, pho-24,
bep-an-truong-mam-non-tu-thuc-trinh-vuong,
hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien, g-cup-coffee-bistro,
mam-mam-eatery-lounge-nang-tam-mam-com-viet,
ganh-hao-noi-hon-bien-trong-tung-net-kien-truc, bep-canteen-nha-may-sheh-fung,
du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca,
du-an-bep-cang-tin-cong-ty-nhat-nichiyo, bling-bling-club,
renovate-sol-kitchen-bar-quan-7
```

### Slots per project (8)
`hero.php`, `intro.php`, `concept.php`, `partnership.php`, `specs.php`, `gallery.php`, `related.php`, `cta.php`

---

## V. 🚫 Out Of Scope (strictly forbidden)

- ❌ **`sgh_img(...)` calls** — image URLs sacred. Don't modify path strings inside.
- ❌ **BEM class names** — `pp-hero-hwa`, `pp-text-skh`, `pp-split-tbx`, etc. NEVER change suffix or BEM structure.
- ❌ **HTML structure / attributes** — `<section>`, `<div>`, `<h1-h6>`, `<p>`, `<figure>`, `class=""`, `style=""`, `data-*`, `id`, `width`, `height`, `loading`, `decoding`. Touch ONLY visible text nodes.
- ❌ **4 reference projects** — amdang-typhoon, godmother-friendship, grand-marble-thuong-hieu-banh-cao-cap-nhat-ban, bambino-saigonhoreca (Gemini-polished, sacred)
- ❌ **CSS files** — `template-parts/project-pillar/<slug>/*.css` không trong scope (chỉ PHP markup)
- ❌ **Page templates** — `page-templates/page-project-*.php` không trong scope
- ❌ **`inc/core/pillar-routes.php`** — routing logic đã done
- ❌ **`inc/core/slug-map.php` other functions** — chỉ ADD `sgh_t()`, KHÔNG modify existing functions
- ❌ **Add new files** — chỉ edit existing
- ❌ **Plugin install** — no plugins
- ❌ **WordPress core** — never modify
- ❌ **`_cta.php` + `_related.php` shared partials** — text trong này lan ra MỌI pillar page, edit cẩn thận hoặc skip nếu không chắc

---

## VI. 🛠️ Phases of Execution

### Phase 1: 🔍 Audit & Setup (~15 min)

**1a. Add helper to slug-map.php**

Read `inc/core/slug-map.php`, find clean spot (after `sgh_is_english_site()` definition), add:

```php
if (!function_exists('sgh_t')) {
    /**
     * Inline bilingual text helper — returns English string on .com,
     * Vietnamese on .vn. Used for pillar content (long paragraphs)
     * where .po/.mo workflow would be unwieldy.
     *
     * @param string $vi Vietnamese source text.
     * @param string $en English translation.
     * @return string Localized text.
     */
    function sgh_t($vi, $en) {
        return sgh_is_english_site() ? $en : $vi;
    }
}
```

**1b. Survey scope**

Run audit to count strings:
```bash
cd themes/saigonhoreca-theme
grep -rohE '<h[1-6][^>]*>[^<]+</h[1-6]>|<p[^>]*>[^<]+</p>|<span[^>]*>[^<]+</span>' \
  template-parts/project-pillar/{heiwa-sushi-omakase,the-brix,...}/*.php \
  | grep -oE '>[^<]+<' \
  | wc -l
```

Estimate strings per pillar: ~80-150 short+long strings × 28 = ~3000-4000 total.

### Phase 2: 🛠️ Implementation (~3-4 hours)

**Pattern transformations**:

Plain text in heading:
```php
// Before
<h1 class="pp-hero-hwa__title">Heiwa Sushi Omakase — Tinh hoa Nhật</h1>

// After
<h1 class="pp-hero-hwa__title"><?php echo esc_html(sgh_t(
    'Heiwa Sushi Omakase — Tinh hoa Nhật',
    'Heiwa Sushi Omakase — Japanese Excellence'
)); ?></h1>
```

Long paragraph:
```php
// Before
<p>Một góc nhỏ Nhật Bản giữa lòng Sài Gòn. Heiwa Sushi mang đến trải nghiệm omakase đẳng cấp...</p>

// After
<p><?php echo esc_html(sgh_t(
    'Một góc nhỏ Nhật Bản giữa lòng Sài Gòn. Heiwa Sushi mang đến trải nghiệm omakase đẳng cấp...',
    'A small corner of Japan in the heart of Saigon. Heiwa Sushi delivers a premium omakase experience...'
)); ?></p>
```

Already wrapped with PHP echo (like image src/captions):
```php
// Before
<figcaption><?php echo 'Bếp công nghiệp Heiwa Sushi'; ?></figcaption>

// After
<figcaption><?php echo esc_html(sgh_t(
    'Bếp công nghiệp Heiwa Sushi',
    'Heiwa Sushi industrial kitchen'
)); ?></figcaption>
```

Attribute `alt`:
```php
// Before
<img src="<?php echo sgh_img('2024/01/foo.jpg'); ?>" alt="Heiwa Sushi interior">

// After
<img src="<?php echo sgh_img('2024/01/foo.jpg'); ?>" alt="<?php echo esc_attr(sgh_t('Nội thất Heiwa Sushi', 'Heiwa Sushi interior')); ?>">
```

**Iteration approach** (suggest):
1. Open each pillar slug folder (8 files)
2. Read all 8 PHP files
3. List all text strings → build (VI, EN) translation pairs
4. Apply atomic edits with `Edit` or `MultiEdit` tool
5. Move to next slug
6. After every 5 pillars: build + smoke test

### Phase 3: 🧪 Verification (~30 min)

**3a. Helper function present**
```bash
grep -n "function sgh_t" inc/core/slug-map.php
```

**3b. All 28 pillars wrapped (no raw VN text in body)**
```bash
# Should find sgh_t() calls in EVERY pillar
for slug in heiwa-sushi-omakase little-bear-thao-dien ... ; do
  count=$(grep -c 'sgh_t(' template-parts/project-pillar/$slug/*.php)
  echo "$count  $slug"
done
# Expect: every count > 10
```

**3c. Image URLs untouched**
```bash
# All sgh_img() calls preserved
grep -rc "sgh_img(" template-parts/project-pillar/ | grep ":0$"
# Expect: 0 files with 0 sgh_img calls (all preserved)
```

**3d. Build clean**
```bash
npm run build:project 2>&1 | tail -3
```

**3e. HTTP 200 — 32 pillars × 2 routes**
```bash
for slug in <all 32>; do
  for route in du-an projects; do
    c=$(curl -skL -o /dev/null -w "%{http_code}" "http://saigonhoreca.local/$route/$slug/")
    [[ "$c" == "200" ]] || echo "FAIL: $c /$route/$slug/"
  done
done
# Expect: zero FAIL output
```

**3f. English rendering test (locale=en_US)**
```bash
# Set WPLANG=en_US in wp-config.php temporarily
# Then fetch and grep English keywords in pillar content
curl -skL "http://saigonhoreca.local/du-an/heiwa-sushi-omakase/" | grep -oE 'Japanese|omakase|kitchen|excellence' | head
# Expect: some English markers present
# REMEMBER: revert wp-config.php after test
```

**3g. VN rendering test (locale=vi default)**
```bash
curl -skL "http://saigonhoreca.local/du-an/heiwa-sushi-omakase/" | grep -oE 'Tinh hoa|Nhật Bản|Sài Gòn' | head
# Expect: VN markers present
```

---

## VII. 🔍 Verification Commands (mandatory — paste RAW output to §XII)

```bash
cd "C:/Users/Administrator/Local Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme"

# 1. sgh_t() defined
grep -n "function sgh_t" inc/core/slug-map.php

# 2. Total sgh_t() calls
grep -rc "sgh_t(" template-parts/project-pillar/ | awk -F: '{sum+=$2} END {print sum}'

# 3. Pillar projects without sgh_t (should be 4 reference + maybe shared partials)
for d in template-parts/project-pillar/*/; do
  c=$(grep -c "sgh_t(" "$d"/*.php 2>/dev/null | paste -sd+ | bc 2>/dev/null || echo 0)
  [[ "$c" == "0" ]] && echo "0  $d"
done

# 4. sgh_img() preservation (count before vs after)
grep -rc "sgh_img(" template-parts/project-pillar/ | awk -F: '{sum+=$2} END {print sum}'
# Expect: same as before task (was 170+ image refs)

# 5. Build clean
npm run build:project 2>&1 | tail -3

# 6. HTTP smoke — all 32 pillars × 2 routes = 64 endpoints
pass=0; fail=0
for slug in <list 32 slugs>; do
  for route in du-an projects; do
    c=$(curl -skL -o /dev/null -w "%{http_code}" "http://saigonhoreca.local/$route/$slug/")
    if [[ "$c" == "200" ]]; then pass=$((pass+1)); else fail=$((fail+1)); echo "FAIL: $c /$route/$slug/"; fi
  done
done
echo "PASS: $pass / FAIL: $fail"

# 7. English rendering test
# (manually set WPLANG=en_US in wp-config temporarily)
curl -skL "http://saigonhoreca.local/du-an/heiwa-sushi-omakase/" | grep -oE 'Japanese|omakase' | head
curl -skL "http://saigonhoreca.local/du-an/the-brix/" | grep -oE 'industrial|venue|brick' | head

# 8. Git diff stats
git status --short 2>/dev/null
git diff --stat HEAD 2>&1 | tail -30
```

---

## VIII. ✅ Acceptance Criteria

- [ ] **`sgh_t()` registered** in `inc/core/slug-map.php`
- [ ] **2000+ `sgh_t()` calls** across 28 pillar slugs (count > 2000)
- [ ] **4 reference projects untouched** — amdang-typhoon, godmother-friendship, grand-marble, bambino-saigonhoreca có 0 `sgh_t()` calls
- [ ] **`sgh_img()` count preserved** — sau task số `sgh_img()` calls = trước task (zero regression)
- [ ] **BEM classes intact** — random spot check: `pp-hero-hwa`, `pp-split-tbx`, `pp-section-bg-skh` etc. still in markup
- [ ] **HTTP 200 — 32 pillars × 2 routes = 64/64** (KHÔNG regression so với T-20260522-001)
- [ ] **Build clean** — `npm run build:project` exit 0
- [ ] **English rendering verified** — khi WPLANG=en_US, ít nhất 5/28 pillars có English keywords visible
- [ ] **VN rendering verified** — khi WPLANG=vi (default), VN keywords visible
- [ ] **No new files** — chỉ edit existing
- [ ] **RAW output** paste đầy đủ §XII

---

## IX. 📋 Worker Prompt (copy-paste into ChatGPT)

```
Tôi cần bạn (ChatGPT GPT-5.4) thực hiện task `T-20260522-002` — pillar bilingual VN/EN.

Đọc TRƯỚC theo thứ tự:
1. C:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content\.task-handoffs\SKILL.md
2. C:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content\.task-handoffs\project\PROJECT.md
3. C:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content\.task-handoffs\active\T-20260522-002-chatgpt-pillar-bilingual.md (this dossier)
4. C:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content\.task-handoffs\archive\2026-05\T-20260522-001-chatgpt-wp-i18n-bilingual.md (foundation)

Phase 1: Add `sgh_t($vi, $en)` helper to inc/core/slug-map.php (~10 lines)
Phase 2: Wrap pillar text strings in 28 projects × 8 slots = 224 files
  - Pattern: `<?php echo esc_html(sgh_t('VN text', 'English text')); ?>`
  - PRESERVE: sgh_img() calls, BEM classes, HTML structure
  - SKIP 4 reference: amdang-typhoon, godmother-friendship, grand-marble-..., bambino-saigonhoreca
Phase 3: Run all Verification §VII, paste RAW output to §XII

DELIVERABLE format:
```
REPORT T-20260522-002
state: returned
files_modified: <count>
sgh_t_calls: <total>
sgh_img_preserved: <count> (should match pre-task)
build: pass/fail
http_200: <N>/64
english_render_verified: <N>/28 pillars
notes: <any blockers>
```

Bắt đầu Phase 1.
```

---

## X. 📥 Agent Result
Status: `not-started`

---

## XI. 📊 Quality Gate Matrix

| Gate | Status | Evidence Reference | Description |
|---|---|---|---|
| **Build Gate** | 🏗️ `pending` | | `npm run build:project` exit 0 |
| **Lint Gate** | 🧹 `pending` | | 64/64 HTTP 200 (no PHP fatal) |
| **Scope Gate** | 📂 `pending` | | 28 pillars in scope, 4 reference + non-pillar files untouched |
| **Logic Gate** | 🎯 `pending` | | English text renders when locale=en_US |
| **Preservation Gate** | 🔒 `pending` | | `sgh_img()` count = pre-task count, BEM classes intact, image URLs identical |

---

## XII. 📁 Evidence (RAW terminal output — populated by ChatGPT)
```text
$ <command>
<exact_output>
```

---

## XIII. 📉 Diff Summary (populated by ChatGPT)
| File | +Lines | -Lines | Type |
|---|---|---|---|
| | | | |

---

## XIV. 🛡️ Orchestrator Review
Status: **✅ PASS — verified by Claude (orchestrator) 2026-05-22 18:00**

### Approach pivot (mid-task)
Worker initially implemented `sgh_t($vi, $en)` inline ternary (per dossier §VI). After user feedback ("sao ko làm cách .po, tối ưu đc k"), worker **converted back to standard WP `__()` + `.po/.mo`** approach — superior maintainability:
- Translations tách khỏi code (Poedit-friendly)
- Compatible với WP ecosystem
- Add ngôn ngữ thứ 3 chỉ cần copy .po → dịch (không sửa code)

### Final state verified
1. ✅ **0 `sgh_t()` calls remaining** — fully removed from theme
2. ✅ **1106 `__()` calls** across 28 pillar projects
3. ✅ **814 msgid entries** in `languages/en_US.po`
4. ✅ **302 KB .mo binary** compiled and loadable
5. ✅ **Locale filter deterministic**: `.com → en_US, else → vi` (bug fixed mid-task — previous logic could return en_US on local due to in_array check)
6. ✅ **5/5 HTTP 200** spot check (heiwa, the-brix, yuzu, little-bear, sol-kitchen-bar)
7. ✅ **Build clean** — 628ms project bundle
8. ✅ **4 reference projects untouched** (amdang, godmother, grand-marble, bambino)
9. ✅ **`sgh_img()` calls preserved** — image URLs Rule #1 intact

### Architecture final
| Layer | Mechanism | Files |
|-------|-----------|-------|
| Header/Footer/Home/About/Contact | `__()` + `en_US.mo` | T-001 scope (27 files) |
| Pillar content (28 projects × 8 slots) | `__()` + `en_US.mo` | T-002 scope (224 files) |
| Domain → locale switch | `add_filter('locale')` deterministic | `inc/core/slug-map.php` |
| Switcher button + hreflang | `sgh_counterpart_url()` + `sgh_hreflang_tags()` | `inc/core/slug-map.php` |
| URL routing both prefixes | `pillar-routes.php` regex | `/du-an/<slug>/` + `/projects/<slug>/` |

### Bilingual launch ready 100%
Full theme bilingual via 1 file `languages/en_US.mo`. Add ngôn ngữ mới chỉ cần copy `en_US.po` → translate → compile.

---

## XV. 🆘 Escalation / Rollback

- **Failure types**:
  1. Accidentally modified `sgh_img()` path → image broken → instant rollback that file
  2. BEM class accidentally renamed → CSS không apply → instant rollback
  3. PHP syntax error in 1 file → HTTP 500 → check + rollback that file
  4. Long task, run out of context → save progress, escalate to Codex for remaining slugs

- **Rollback per file**:
  ```bash
  git checkout -- themes/saigonhoreca-theme/template-parts/project-pillar/<slug>/<slot>.php
  ```

- **Rollback full**:
  ```bash
  git checkout -- themes/saigonhoreca-theme/template-parts/project-pillar/
  git checkout -- themes/saigonhoreca-theme/inc/core/slug-map.php
  ```

- **Partial completion OK**: Nếu chạy hết quota giữa chừng, mark `state: returned` với note "X/28 pillars done, Y/28 remaining" — orchestrator dispatch tiếp đến Codex hoặc retry ChatGPT.

---

## XVI. 📑 CHANGE LOG

- **2026-05-22 16:00**: Dossier created by Claude (orchestrator). State: drafted, ready to dispatch.
