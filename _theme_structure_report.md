# Báo cáo cấu trúc folder — So sánh 2 Theme WordPress

---

## 1. Tìm kiếm Skill báo cáo (Report Skill)

Từ skill finder, các skill liên quan đến "báo cáo / report" được tìm thấy:

| Skill | Nguồn | Mô tả |
|---|---|---|
| **generate-status-report** | partner-plugins (Atlassian) | Tạo project status report từ Jira issues, publish lên Confluence |
| **session-report** | partner-plugins | Tạo báo cáo HTML khai thác usage của Claude Code session |
| **timeline-report** | claude-mem | Tạo narrative report phân tích lịch sử phát triển project |
| **daily-news-report** | antigravity | Scraping + filter technical info, sinh daily Markdown report |
| **campaign-report** | ingested | Quick campaign performance report từ data source |
| **reports-pdfs-and-slide-automation** | imported-plugins | Layout & export data-rich reports, PDFs, PowerPoint |

Skill phù hợp nhất cho báo cáo cấu trúc project: **generate-status-report**.

---

## 2. Theme saigonhoreca-theme

**Đường dẫn:** `C:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content\themes\saigonhoreca-theme`

**Tổng quan:** 1,136 files (không kể node_modules), 108 directories + ~27,480 files trong node_modules.

### 2.1 Cấu trúc gốc chính

```
saigonhoreca-theme/
├── .gitignore
├── .htaccess.production
├── 404.php
├── archive.php
├── archive-product.php
├── archive-project.php
├── author.php
├── category.php
├── category-video-du-an.php
├── comments.php
├── footer.php
├── front-page.php
├── functions.php
├── header.php
├── index.php
├── page.php
├── search.php
├── sidebar.php
├── single.php
├── tag.php
├── taxonomy-product_brand.php
├── taxonomy-product_cat.php
├── taxonomy-product_category.php
├── style.css
├── theme.json
├── lighthouserc.json
├── manifest.json
├── budget.json
├── DEPLOY-NOTES.txt
├── SEO-DEPLOY.md
├── package.json / package-lock.json
├── languages/
│   ├── en_US.mo
│   ├── en_US.po
│   └── saigonhoreca.pot
├── assets/
│   ├── css/
│   │   ├── style.css, critical.css, critical-routes.css
│   │   ├── _brand.css, _imports.css, _tokens.css, _tailwind-project.css
│   │   ├── reveal.css, scroll.css, fonts-self-host.css
│   │   ├── dist/ (theme-*.css, ~32 project CSS files)
│   │   ├── entrypoints/ (style-*.css, ~38 entrypoint files)
│   │   ├── imports/ (_imports-*.css, ~38 import files)
│   │   ├── legacy/ (404.css, archive.css,author.css, etc.)
│   │   └── shared/ (buttons.css, cards.css, typography.css, etc.)
│   ├── fonts/self-host/ (~60 .woff2 files)
│   ├── images/ (~40 webp/ico files + favicons, logos)
│   ├── js/ + js/dist/ (calculator, contact-form, gallery, hero-carousel, etc.)
│   └── mce/ (TinyMCE plugin dirs: anchor, code, emoticons, table, etc.)
├── inc/
│   ├── config/ (constants.php, fonts.php, routes.php, domains.php, etc.)
│   ├── customizer/ (colors.php, footer.php, front-page.php, init.php, top-bar.php)
│   ├── engine/ (~25 PHP modules: cpt-registration, seo-meta, critical-css, webp-conversion, etc.)
│   └── site/
│       ├── admin/ (dashboard, front-page-meta, sync/)
│       ├── auto-setup.php, pi-seo-resolver.php, project-cover.php, section-images.php
│       └── tools/ (import-content, import-gdocs, import-projects, seed-content, convert-webp, etc.)
├── docs/
│   ├── ARCHITECTURE.md, README.md, PERFORMANCE.md, PILLAR-STATUS.md
│   ├── perf-baselines/ (scoreboard JSON + MD)
│   └── product-catalog/ (27 brand folders + scripts + audit files)
├── page-templates/ (3 templates: gioi-thieu, lien-he, project)
├── scratch/ (Python helper scripts)
├── scripts/ (~30 build/deploy/scaffold scripts + project-data JSON files)
├── node_modules/ (webpack, react, tailwind, etc. — ~27,480 files)
└── template-parts/
    ├── about/ (cta, hero, introduction, partners, values)
    ├── archive-product/ (filter-sidebar, grid, hero, pagination)
    ├── archive-project/ (hero, projects-grid, trust)
    ├── components/ (post-card, product-card, share-buttons, faq-accordion, before-after, wave-divider)
    ├── contact/ (form, hero, map)
    ├── footer/ (company-info, contact-info, copyright, floating-buttons, mobile-nav, quick-links, widgets)
    ├── header/ (logo, navigation, mobile-menu, top-bar)
    ├── home/ (hero, services, featured-projects, latest-news, partners, testimonials, why-choose, work-process, consult-cta)
    ├── project-pillar/ (global styles + 26 project sub-dirs, each with concept/intro/gallery/hero/partnership/specs)
    └── single-post/ (layout.css, sidebar.css, sidebar.php)
```

### 2.2 Thống kê

| Hạng mục | Số lượng |
|---|---|
| Tổng files (excl. node_modules) | 1,136 |
| Tổng directories (excl. node_modules) | 108 |
| Project CSS files (assets/css/dist/) | ~32 |
| Project pillar subdirs | 26 |
| Product catalog brand folders | 27 |
| TinyMCE plugin dirs | 10 |
| Font files (self-host) | ~60 |
| Scripts build/deploy | ~30 |

---

## 3. Theme saigonhouse-theme

**Đường dẫn:** `C:\Users\Administrator\Local Sites\saigonhoreca\.kilo\worktrees\bfc-audit\app\public\wp-content\.pi-task-handoffs\changes\T-20260517-004-claude-sgh-tailwind-utilities-refactor\before\themes\saigonhouse-theme`

**Lưu ý:** Theme này **không tồn tại** trong thư mục themes chính (`app\public\wp-content\themes\`). Nó chỉ có trong một git worktree thuộc thay đổi `T-20260517-004-claude-sgh-tailwind-utilities-refactor`.

**Tổng quan:** 164 files, 14 directories. Không có node_modules.

### 3.1 Cấu trúc folder

```
saigonhouse-theme/
├── 404.php
├── archive.php
├── category.php
├── footer.php
├── front-page.php
├── header.php
├── page.php
├── search.php
├── single.php
├── tag.php
├── style.css
├── assets/
│   └── css/
│       ├── style.css (_imports.css, sgh-aos.css)
│       ├── components/ (entry-content, footer, header, page-hero, sidebar, with-sidebar)
│       ├── pages/ (404, archive, category-video, page, search, single)
│       └── editor-admin.css, editor-content.css
├── page-templates/
│   ├── page-bang-gia.php / .css
│   ├── page-du-toan.php / .css
│   ├── page-gioi-thieu.php / .css
│   ├── page-lien-he.php / .css
│   ├── page-portfolio.php / .css
│   └── page-tin-tuc.php / .css
└── template-parts/
    ├── about/ (cta, hero, introduction, story, values)
    ├── components/ (post-card, faq-accordion, before-after, share-buttons, wave-divider)
    ├── contact/ (form, hero, map)
    ├── footer/ (company-info, contact-info, cookie-consent, copyright, floating-buttons, mobile-nav, quick-links, widgets)
    ├── header/ (logo, navigation, mobile-menu, top-bar)
    ├── home/ (hero-carousel, latest-news, featured-projects, customer-reviews, service-links, testimonials, township-designs, villa-designs, work-process)
    └── pricing/ (arch-calc, arch-hero, arch-packages, arch-process, arch-trust, arch-turnkey, content, cta, hero, interior-combo, interior-renovation, interior-style, rough-area-calc, rough-commitments, rough-hero, rough-pricing-table, rough-process, rough-work-items, turnkey-area-calc, turnkey-hero, turnkey-pricing-table, turnkey-work-items)
```

### 3.2 Thống kê

| Hạng mục | Số lượng |
|---|---|
| Tổng files | 164 |
| Tổng directories | 14 |
| Page templates (PHP) | 6 |
| Template parts sections | 7 (about, components, contact, footer, header, home, pricing) |
| CSS files (assets/css/) | ~25 |

---

## 4. So sánh tổng quan

| Tiêu chí | saigonhoreca-theme | saigonhouse-theme |
|---|---|---|
| **Vị trí** | themes/ (main) | worktree (git stash before) |
| **Tổng files** | 1,136 (excl. node_modules) | 164 |
| **Tổng directories** | 108 | 14 |
| **node_modules** | Có (huge) | Không |
| **inc/ engine** | Có (25+ PHP modules) | Không có |
| **inc/ config** | Có (routes, constants, domains) | Không có |
| **docs/** | Có (ARCHITECTURE, product-catalog 27 brands) | Không có |
| **scripts/ build** | Có (~30 build scripts) | Không có |
| **Project pillar system** | Có (26 project sub-dirs) | Không có |
| **Product catalog system** | Có (27 brand DBs + import tools) | Không có |
| **assets/mce/** | Có (TinyMCE plugins) | Không có |
| **CSS architecture** | Tailwind-based + dist/build system | Flat CSS with components/pages dirs |
| **page-templates** | 3 templates | 6 templates |
| **theme.json** | Có | Không (không rõ) |
| **Languages/.pot** | Có | Không có |
| **Loại theme** | Restaurant/HoReCa group | Real estate/residential ("house") |
| **Build system** | npm/webpack (package.json) | Không rõ (không có package.json) |

### 4.1 Kết luận

- **saigonhoreca-theme** là một theme WordPress production-complete, có hệ thống engine (inc/), build tools (node_modules, scripts/), tài liệu (docs/), và hệ thống project pillar phức tạp với 26 dự án riêng biệt.
- **saigonhouse-theme** là một theme nhẹ hơn, nằm trong git worktree thuộc branch thay đổi Tailwind utilities refactor, chỉ có 164 files, không có hệ thống engine hay build tools riêng. Có thể đây là bản sao hoặc phiên bản đơn giản hóa cho mục đích testing/refactoring.
- Theme saigonhouse **chưa được deploy** vào thư mục themes chính — nó chỉ tồn tại trong worktree lưu trữ state trước khi refactor.

---

*Report generated on 2026-07-25 by Kilo (skill: generate-status-report / timeline-report)*
