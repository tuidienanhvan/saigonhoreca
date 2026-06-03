# Master Plan — saigonhoreca Project-Pillar CSS Quality Upgrade

> **Mục tiêu**: nâng toàn bộ project-pillar từ render PHẲNG lên **gold-standard** (ngang rokafella/casa-maria). Mỗi project = 1 dossier riêng, owner `qwen` (Qwen3.7-Max), reference quality-bar: [`system/templates/tasks/project-pillar-upgrade.md`](../system/templates/tasks/project-pillar-upgrade.md).
>
> **Repo target**: `C:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content\themes\saigonhoreca-theme\`
> **Tổng**: 32 projects = 5 gold-reference (skip) + 27 cần upgrade.

---

## I. Gold-Standard Reference (5 — KHÔNG refactor CSS; có dossier reorg+verify)

> Các project gemini đã làm đẹp. Dossier 028-032 = **re-crawl static-mirror theo dự án (§G) + verify gold bar maintained**, KHÔNG refactor CSS.

| Slug | Abbr | Dossier | Đặc trưng kỹ thuật để học |
|---|---|---|---|
| `roka-fella-tinh-hoa-am-thuc-nhat-ban` | rkf | T-20260529-028 | Ken-Burns hero · glassmorphism card · corner ornament · ambient glow · SVG watermark |
| `casa-maria` | cm | T-20260529-029 | Split 55/45 reverse · staggered entry rise · backdrop pill badge |
| `bambino-saigonhoreca` | bambino | T-20260529-030 | Video bg (YouTube iframe) · SVG geometric panels · asymmetric grid |
| `amdang-typhoon` | amdang | T-20260529-031 | (audit khi crawl) |
| `bling-bling-club` | bbc | T-20260529-032 | (audit khi crawl) |

---

## II. 27 Projects cần Upgrade (mỗi cái 1 dossier)

Trạng thái: chưa tạo dossier · dossier drafted · dispatched · done

| # | Slug | Abbr đề xuất | Sections | Dossier | Status |
|---|---|---|---|---|---|
| 1 | `g-cup-coffee-bistro` | gcb | 8 | T-20260529-001 (Phase 7) | dispatched |
| 2 | `pho-24` | ph24 | 8 | — | |
| 3 | `the-cheezy-time` | tct | 8 | — | |
| 4 | `the-brix` | brix | 8 | — | |
| 5 | `the-royal-all-day-dining` | royal | 8 | — | |
| 6 | `little-bear-thao-dien` | lbear | 8 | — | |
| 7 | `godmother-friendship` | gmf | 8 | — | |
| 8 | `skyloft-by-glow` | sky | 8 | — | |
| 9 | `yuzu-omakase` | yuzu | 8 | — | |
| 10 | `heiwa-sushi-omakase` | heiwa | 8 | — | |
| 11 | `grand-marble-thuong-hieu-banh-cao-cap-nhat-ban` | gmarb | 8 | — | |
| 12 | `mam-mam-eatery-lounge-nang-tam-mam-com-viet` | mam | 8 | — | |
| 13 | `ganh-hao-noi-hon-bien-trong-tung-net-kien-truc` | ganh | 8 | — | |
| 14 | `sol-kitchen-bar` | sol | 8 | — | |
| 15 | `sol-kitchen-bar-saigon-horeca` | solshr | 8 | — | |
| 16 | `renovate-sol-kitchen-bar-quan-7` | solq7 | 8 | — | |
| 17 | `mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam` | mua | 8 | — | |
| 18 | `du-an-bep-cang-tin-cong-ty-nhat-nichiyo` | nichiyo | 8 | — | |
| 19 | `du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca` | kdl | 8 | — | |
| 20 | `moa-moa` | moa | 13 | — | |
| 21 | `du-nam-an-an` | dnaa | 13 | — | |
| 22 | `tales-by-chapter` | tales | 13 | — | |
| 23 | `hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien` | hemma | 13 | — | |
| 24 | `du-an-vinh-hiep` | vhiep | 13 | — | |
| 25 | `bep-an-truong-mam-non-tu-thuc-trinh-vuong` | trinhv | 11 | — | |
| 26 | `bep-canteen-nha-may-sheh-fung` | sheh | 11 | — | |
| 27 | `spice-world-hotpot` | spice | 14 | — | |

> **Lưu ý abbr**: phải UNIQUE toàn theme (tránh collision class `pp-*-<abbr>`). Confirm bằng `grep -r "pp-hero-<abbr>"` trước khi dùng.

---

## III. Batch Plan (đề xuất thứ tự)

| Batch | Projects | Lý do nhóm |
|---|---|---|
| **B0 (pilot)** | g-cup-coffee-bistro | Đang chạy — chuẩn hóa quy trình Phase 7 |
| **B1** (8-section đơn giản) | pho-24, the-cheezy-time, the-brix, the-royal-all-day-dining | F&B casual, structure gọn — nhân bản nhanh |
| **B2** (omakase/Nhật) | yuzu-omakase, heiwa-sushi-omakase, grand-marble | Cùng phong cách Nhật, học từ rokafella |
| **B3** (bar/lounge) | sol-kitchen-bar (×3 variant), skyloft, mam-mam, ganh-hao | Bar/lounge tối, ambient glow hợp |
| **B4** (còn lại 8-sec) | little-bear, godmother, mua-craft-sake, nichiyo, kdl | |
| **B5** (extended 11-14 sec) | moa-moa, du-nam-an-an, tales, hemma, vinh-hiep, trinh-vuong, sheh-fung, spice-world | Nhiều section hơn → effort cao, làm cuối |

→ B0 xong + verify đạt bar → dùng làm template thực tế cho B1, rồi cuốn chiếu.

---

## IV. Quy trình tạo dossier per-project (nhân bản)

```bash
cd .pi-task-handoffs
bash system/scripts/new-task.sh \
  --owner qwen --priority P2 --risk medium \
  --slug <slug>-css-upgrade \
  --description "Upgrade <slug> project-pillar CSS to gold-standard (token + _container/_caption + animations)" \
  --intent "<quote user>"
# → fill dossier: copy system/templates/tasks/project-pillar-upgrade.md §A-F, thay <SLUG>/<ABBR>/<PROJECT_NAME>
# → set-state dispatched → copy §IX prompt cho qwen
```

**Risk**: `medium` (không crawl/xóa, chỉ refactor CSS trong scope 1 project) — KHÔNG cần `changes/` folder như B0.

**Shared requirements mọi dossier** (từ template §B):
- ZERO hardcode hex → `_tokens.css` vars (`--p`, `--bc`, `color-mix`)
- `pp-container-shared` + `pp-image-caption-shared`
- ≥6/9 CSS techniques
- `prefers-reduced-motion` fallback
- Browser smoke vs rokafella

---

## V. Progress Tracker

- **Total dossiers**: 32 (1 g-cup pilot + 26 upgrade + 5 gold reorg)
- **Done**: 0
- **In progress**: 1 (g-cup B0, Phase 7 quality + mirror reorg)
- **Drafted (ready dispatch)**: 31 — upgrade T-002..027 + gold T-028..032
- **Static-mirror convention**: §G (ảnh theo dự án `<slug>/images/`, KHÔNG `uploads/<năm>/`)

_(Update cột Status §II khi mỗi dossier chuyển trạng thái. Master plan này KHÔNG phải task dossier — là planning doc, không có lock/STATUS row.)_

---

## VI. Changelog
- **2026-05-29**: Master plan tạo sau khi audit 32 projects. 5 gold reference identified, 27 cần upgrade. Quality-bar template `project-pillar-upgrade.md` canonical. G-Cup (B0) reopened cho Phase 7.
