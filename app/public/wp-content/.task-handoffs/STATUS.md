# 🟢 Bảng theo dõi trạng thái công việc / Handoff Status Dashboard

**Ngày hiện tại / Current Date**: 2026-05-22
**Workspace**: Saigon Horeca (saigonhoreca.vn + saigonhoreca.com bilingual)
**Phiên bản hệ thống / System Version**: 🚀 v4.3 (Phase A/B Automation Edition)
**Chế độ điều phối / Orchestrator Mode**: 👑 Agnostic (Role Self-ID per SKILL.md §1.3)
**Tự động hóa / Automation**: ✅ `new-task.sh` + `set-state.sh` + `archive-task.sh` + `reconcile.sh` + `lint-handoffs.sh --strict|--fix` + `prune-changes.sh` + `install-hooks.sh`
**Roster nguồn / Roster source**: `system/__roster.json` (canonical)
**Agent telemetry**: per-agent metrics in `AGENTS/<name>.md` `## 📊 Performance Metrics` block (auto-derived from LEADERBOARD)

---

## I. 🟢 Công việc đang xử lý / Active Tasks (In Progress)
| ID | Owner | State | Scope | Dossier | Gates | Notes |
|---|---|---|---|---|---|---|
| `T-20260523-001` | 🚀 gemini | decomposed | template-parts/project-pillar/* | [Dossier](active/T-20260523-001-gemini-rebuild-project-pillars.md) | pending | Parent task |
| `T-20260523-002` | 🚀 gemini | verified | template-parts/project-pillar/bambino-saigonhoreca/* | [Dossier](active/T-20260523-002-gemini-bambino-saigonhoreca.md) | pending | Bambino Superclub - Cyberpunk Neon Glow (Club & Lounge) |
| `T-20260523-003` | 🚀 gemini | verified | template-parts/project-pillar/bling-bling-club/* | [Dossier](active/T-20260523-003-gemini-bling-bling-club.md) | pending | Bling Bling Club - Cyberpunk Neon Glow (Glamour Glow) |
| `T-20260523-004` | 🚀 gemini | drafted | template-parts/project-pillar/skyloft-by-glow/* | [Dossier](active/T-20260523-004-gemini-skyloft-by-glow.md) | pending | Skyloft by Glow - Cyberpunk Neon Glow (Skyline Bar) |
| `T-20260523-005` | 🚀 gemini | drafted | template-parts/project-pillar/sol-kitchen-bar/* | [Dossier](active/T-20260523-005-gemini-sol-kitchen-bar.md) | pending | Sol Kitchen & Bar District 1 - Cyberpunk Neon Glow (Latin Tropical Cyber) |
| `T-20260523-006` | 🚀 gemini | drafted | template-parts/project-pillar/sol-kitchen-bar-saigon-horeca/* | [Dossier](active/T-20260523-006-gemini-sol-kitchen-bar-saigon-horeca.md) | pending | Sol Kitchen & Bar Corporate Spec - Cyberpunk Neon Glow (Corporate Latin Glow) |
| `T-20260523-007` | 🚀 gemini | drafted | template-parts/project-pillar/the-brix/* | [Dossier](active/T-20260523-007-gemini-the-brix.md) | pending | The Brix Eatery & Bar - Cyberpunk Neon Glow (Aqua Luxury Oasis) |
| `T-20260523-008` | 🚀 gemini | drafted | template-parts/project-pillar/mam-mam-eatery-lounge-nang-tam-mam-com-viet/* | [Dossier](active/T-20260523-008-gemini-mam-mam-eatery-lounge-nang-tam-mam-com-viet.md) | pending | Măm Măm Eatery Lounge - Cyberpunk Glow (Neo-Vietnamese Cyberpunk) |
| `T-20260523-009` | 🚀 gemini | drafted | template-parts/project-pillar/yuzu-omakase/* | [Dossier](active/T-20260523-009-gemini-yuzu-omakase.md) | pending | Yuzu Omakase Sushi - Organic Wabi-Sabi (Yuzu Minimalist Zen) |
| `T-20260523-010` | 🚀 gemini | drafted | template-parts/project-pillar/moa-moa/* | [Dossier](active/T-20260523-010-gemini-moa-moa.md) | pending | Moa Moa Lounge & Bistro - Organic Wabi-Sabi (Tropical Wild Wabi-Sabi) |
| `T-20260523-011` | 🚀 gemini | drafted | template-parts/project-pillar/mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/* | [Dossier](active/T-20260523-011-gemini-mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam.md) | pending | Mua Craft Sake - Organic Wabi-Sabi (Industrial Brewery Zen) |
| `T-20260523-012` | 🚀 gemini | drafted | template-parts/project-pillar/roka-fella-tinh-hoa-am-thuc-nhat-ban/* | [Dossier](active/T-20260523-012-gemini-roka-fella-tinh-hoa-am-thuc-nhat-ban.md) | pending | Roka Fella Restaurant - Organic Wabi-Sabi (Charcoal Luxury Zen) |
| `T-20260523-013` | 🚀 gemini | drafted | template-parts/project-pillar/hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien/* | [Dossier](active/T-20260523-013-gemini-hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien.md) | pending | Hemma Desserts Thảo Điền - European Elegance (Scandinavian Cozy) |
| `T-20260523-014` | 🚀 gemini | drafted | template-parts/project-pillar/godmother-friendship/* | [Dossier](active/T-20260523-014-gemini-godmother-friendship.md) | pending | Godmother Bake & Brunch Friendship - European Elegance (Brunch Chic) |
| `T-20260523-015` | 🚀 gemini | drafted | template-parts/project-pillar/casa-maria/* | [Dossier](active/T-20260523-015-gemini-casa-maria.md) | pending | Casa Maria Restaurant - European Elegance (Italian Warmth) |
| `T-20260523-016` | 🚀 gemini | drafted | template-parts/project-pillar/g-cup-coffee-bistro/* | [Dossier](active/T-20260523-016-gemini-g-cup-coffee-bistro.md) | pending | G-Cup Coffee Bistro - European Elegance (Cozy Cafe Bistro) |
| `T-20260523-017` | 🚀 gemini | drafted | template-parts/project-pillar/grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/* | [Dossier](active/T-20260523-017-gemini-grand-marble-thuong-hieu-banh-cao-cap-nhat-ban.md) | pending | Grand Marble Bakery Nhật Bản - European Elegance (Japanese Luxury Bakery) |
| `T-20260523-018` | 🚀 gemini | drafted | template-parts/project-pillar/little-bear-thao-dien/* | [Dossier](active/T-20260523-018-gemini-little-bear-thao-dien.md) | pending | Little Bear Thảo Điền - European Elegance (Botanical Bistro) |
| `T-20260523-019` | 🚀 gemini | drafted | template-parts/project-pillar/tales-by-chapter/* | [Dossier](active/T-20260523-019-gemini-tales-by-chapter.md) | pending | Tales by Chapter Restaurant - European Elegance (Cinematic Film Noir) |
| `T-20260523-020` | 🚀 gemini | drafted | template-parts/project-pillar/the-cheezy-time/* | [Dossier](active/T-20260523-020-gemini-the-cheezy-time.md) | pending | The Cheezy Time - European Elegance (Warm Cheese Bistro) |
| `T-20260523-021` | 🚀 gemini | drafted | template-parts/project-pillar/the-royal-all-day-dining/* | [Dossier](active/T-20260523-021-gemini-the-royal-all-day-dining.md) | pending | The Royal All Day Dining - European Elegance (Victorian Regal) |
| `T-20260523-022` | 🚀 gemini | drafted | template-parts/project-pillar/ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/* | [Dossier](active/T-20260523-022-gemini-ganh-hao-noi-hon-bien-trong-tung-net-kien-truc.md) | pending | Nhà Hàng Hải Sản Gành Hào Vũng Tàu - Michelin Heritage (Maritime Marine Heritage) |
| `T-20260523-023` | 🚀 gemini | drafted | template-parts/project-pillar/pho-24/* | [Dossier](active/T-20260523-023-gemini-pho-24.md) | pending | Chuỗi Phở 24 - Michelin Heritage (Modern Vietnamese Heritage) |
| `T-20260523-024` | 🚀 gemini | drafted | template-parts/project-pillar/spice-world-hotpot/* | [Dossier](active/T-20260523-024-gemini-spice-world-hotpot.md) | pending | Spice World Hotpot - Michelin Heritage (Sichuan Dragon Heritage) |
| `T-20260523-025` | 🚀 gemini | drafted | template-parts/project-pillar/bep-an-truong-mam-non-tu-thuc-trinh-vuong/* | [Dossier](active/T-20260523-025-gemini-bep-an-truong-mam-non-tu-thuc-trinh-vuong.md) | pending | Bếp Ăn Trường Mầm Non Trinh Vương - Safe-Kitchen Industrial (Bếp Học Đường) |
| `T-20260523-026` | 🚀 gemini | drafted | template-parts/project-pillar/bep-canteen-nha-may-sheh-fung/* | [Dossier](active/T-20260523-026-gemini-bep-canteen-nha-may-sheh-fung.md) | pending | Bếp Canteen Nhà Máy Sheh Fung - Industrial Technical Spec (Bếp Công Nghiệp) |
| `T-20260523-027` | 🚀 gemini | drafted | template-parts/project-pillar/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/* | [Dossier](active/T-20260523-027-gemini-du-an-bep-cang-tin-cong-ty-nhat-nichiyo.md) | pending | Bếp Căng Tin Công Ty Nhật Nichiyo - Zen Industrial Spec (Tối Giản Nhật Bản) |
| `T-20260523-028` | 🚀 gemini | drafted | template-parts/project-pillar/du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca/* | [Dossier](active/T-20260523-028-gemini-du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca.md) | pending | Bếp KDL Rừng Thông Núi Voi - Eco-Industrial Spec (Thiên Nhiên & Bền Bỉ) |
| `T-20260523-029` | 🚀 gemini | drafted | template-parts/project-pillar/du-an-vinh-hiep/* | [Dossier](active/T-20260523-029-gemini-du-an-vinh-hiep.md) | pending | Nhà Máy Cà Phê Xuất Khẩu Vinh Hiệp - Industrial Coffee Factory Spec (Công Nghệ Cao) |
| `T-20260523-030` | 🚀 gemini | drafted | template-parts/project-pillar/du-nam-an-an/* | [Dossier](active/T-20260523-030-gemini-du-nam-an-an.md) | pending | Bếp Ăn Công Nghiệp Nam An An - Clean-Room Blueprint (Bếp Vô Trùng) |

## II. 🟡 Đang chờ kiểm tra / Waiting for Review (Verification)

| Task ID | Owner | Session | Heartbeat | State | Priority | Scope | Dossier |
|---|---|---|---|---|---|---|---|

---




















## III. 🔴 Đang bị kẹt hoặc khiếu nại / Blocked (Escalated)

| Task ID | Owner | Session | Heartbeat | State | Priority | Failure Reason | Evidence |
|---|---|---|---|---|---|---|---|

---

## IV. 📁 Lịch sử lưu trữ (48h gần nhất) / Recently Archived (Last 48 Hours)

| Task ID | Owner | Result | Scope | Dossier Link | Technical Evidence |
|---|---|---|---|---|---|
| `T-20260522-002` | 🤖 ChatGPT (GPT-5.4) | ✅ pass | 28 pillar projects bilingual `__()` + .po/.mo (pivoted from sgh_t) | [Dossier](archive/2026-05/T-20260522-002-chatgpt-pillar-bilingual.md) | 1106 `__()` calls, 814 msgid, 302KB .mo, 5/5 HTTP 200, locale filter deterministic |
| `T-20260522-001` | 🤖 ChatGPT (GPT-5.4) | ✅ pass | saigonhoreca-theme i18n bilingual VN/EN | [Dossier](archive/2026-05/T-20260522-001-chatgpt-wp-i18n-bilingual.md) | 649 i18n calls, 132 msgid, .mo valid binary, 10/10 HTTP 200, English rendering verified |

---

## V. 📜 Operational Rules for this Dashboard

1.  **Atomic Updates**: This file must be updated immediately upon task creation.
2.  **Unique IDs**: Task IDs (`T-YYYYMMDD-XXX`) must be strictly unique.
3.  **Evidence Requirement**: Do not archive without technical evidence.
4.  **No Hallucination**: Do not claim "pass" if the dossier is still `returned`.
5.  **Phase D Atomicity**: Khi archive, phải update CẢ STATUS.md (Section 1 → Section 4) VÀ append LEADERBOARD.md trong cùng commit. Không để drift.
6.  **Section 4 Rotation (v4.0)**: Auto-rotated by `archive-task.sh` → `reconcile.sh`. Keep ≤15 entries, drop entries older than 48h.
7.  **Roster Authority (v4.0)**: `system/__roster.json` = single source of truth for agents + tiers. Mutating roster requires `reconcile.sh --fix` to cascade.
8.  **Zero-Drift Gate (v4.0)**: `bash system/scripts/lint-handoffs.sh --strict` must exit 0 before any new dispatch.
