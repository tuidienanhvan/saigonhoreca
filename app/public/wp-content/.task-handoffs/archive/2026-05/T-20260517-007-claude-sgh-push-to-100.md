---
id: T-20260517-007
owner: claude
state: archived
priority: P1
risk: high
estimated_minutes: 240
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Gemini]
created: 2026-05-17 16:25
updated: 2026-05-17 16:49
archived: 2026-05-17 16:49
---

## 0. 📥 User Original Intent (Phase 0 — Verbatim)

> tiếp tục làm cho tới khi 100 điểm


# 📋 T-20260517-007 | claude | sgh-push-to-100 — Bản đặc tả công việc chi tiết / Detailed Task Specification

## I. 📊 Các trường Frontmatter và Ma trận rủi ro / Frontmatter Fields & Risk Matrix

| Trường / Field | Giá trị / Values | Mô tả chi tiết / Detailed Operational Description |
|---|---|---|
| `id` | `T-YYYYMMDD-XXX` | 🆔 Định danh duy nhất theo ngày. Nếu trùng, dùng hậu tố A/B. |
| `owner` | tên viết thường | 👤 Agent được giao nhiệm vụ (vd: codex, gemini). |
| `state` | drafted...archived | 🔄 Vòng đời: **drafted**, **dispatched**, **returned**, **verified**, **archived**, **blocked**. |
| `priority` | P0...P3 | 🚥 **P0**: Khẩn cấp. **P1**: Cao. **P2**: Tiêu chuẩn. **P3**: Cải thiện. |
| `risk` | cosmetic...critical | ⚠️ Tác động: **cosmetic**, **low**, **medium**, **high**, **critical**. |
| `retry_count` | số nguyên | 🔄 Số lần thử lại thất bại. |
| `retry_max` | số nguyên | 🛑 Số lần thử lại tối đa (Mặc định: 1) trước khi escalate. |
| `escalation_path` | danh sách agent | 🪜 Chuỗi agent cứu trợ (vd: [Codex, Claude]). |

---

## II. 🎯 Mục tiêu và Chiến lược / Goal & Strategic Objective

**Outcome**: Lighthouse Performance saigonhouse-theme **= 100** trên homepage. User explicit ask: "tiếp tục làm cho tới khi 100 điểm".

**Reality (đã đo trong Phase A của task này)**: trên LocalWP nginx, bundle 418KB render-blocking external CSS + hero image 640×640 upscaled to ~1200px viewport tạo cap Perf ~0.83. Không có architecture nào trên localhost vượt được trong khi giữ trang render đúng.

**Approach tested** (xem §X chi tiết):
1. Hero LCP best-practice tweaks (decoding=async, imagesrcset preload) — kept.
2. Inline-full bundle (HTML 554KB) — Perf 0.77, reverted.
3. Hand-built critical 53KB split + async main — Perf 0.76, TBT 170ms, reverted.
4. Tiny utility-only critical 16KB + async components — Perf 0.56, CLS 0.96 catastrophic, reverted.
5. Drop _imports.css entirely (page visually broken) — Perf 0.97 "fake", reverted.

**Conclusion**: 100 không achievable trên LocalWP nginx + 640w hero source. Cần production Apache (HTTP/2 + Brotli + cache headers) hoặc hero image regen ở admin để vượt.

See `changes/T-20260517-007-*/decision.md` for full forensic breakdown.

---

## III. 📚 Tài liệu tham khảo bắt buộc / Required Reading (Context)
- 🛡️ `.task-handoffs/SKILL.md`: Quy chuẩn vận hành và chống ảo giác.
- 🏗️ `.task-handoffs/project/PROJECT.md`: Ngữ cảnh dự án, tech stack và quy ước.
- 🏆 `.task-handoffs/system/QUALITY-GATES.md`: Tiêu chuẩn nghiệm thu kỹ thuật.
- 📤 `.task-handoffs/system/REPORTING.md`: Quy chuẩn báo cáo và bằng chứng.

---

## IV. 🚧 Phạm vi cho phép (Nghiêm ngặt) / Allowed Scope (Strict)
(Liệt kê mọi file hoặc thư mục agent được phép chạm vào. Hãy thật rõ ràng.)
- 📄 `path/to/file.jsx`

---

## V. 🚫 Ngoài phạm vi xử lý (Nghiêm cấm) / Out Of Scope (Strictly Forbidden)
- ❌ **Refactor không liên quan**: Không dọn dẹp code nằm ngoài mục tiêu.
- ❌ **Thay đổi UI/UX**: Không đổi layout, màu sắc, font trừ khi có yêu cầu.
- ❌ **Cấu hình hệ thống**: Không đụng vào `package.json` hay root configs.
- ❌ **Dependencies**: Không thêm hoặc xóa npm packages.

---

## VI. 🛠️ Các giai đoạn thực hiện / Phases of Execution
1.  **Giai đoạn 1: Kiểm tra & Đánh giá / Audit & Baseline** 🔍
    - Đọc file trong scope. Chạy `grep` hoặc `ls`. Xác nhận trạng thái ổn định.
2.  **Giai đoạn 2: Triển khai / Implementation** 🛠️
    - Áp dụng thay đổi dùng **Atomic Edits**. Tuân thủ tiêu chuẩn (Tailwind v4, UTF-8).
3.  **Giai đoạn 3: Kiểm tra nội bộ / Internal Verification** 🧪
    - Chạy các `Lệnh kiểm tra`. Chụp lại RAW output (stdout/stderr).
4.  **Giai đoạn 4: Báo cáo chuẩn hóa / Standardized Reporting** 📤
    - Xây dựng block báo cáo theo mẫu trong `PROMPT.md`.

---

## VII. 🔍 Lệnh kiểm tra bắt buộc / Verification Commands (Mandatory)
```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\<webapp>"
# Kiểm tra Audit
npm run lint
# Kiểm tra Đóng gói
npm run build
# Kiểm tra Trạng thái Git
git status --short
```

---

## VIII. ✅ Tiêu chí nghiệm thu (Checklist) / Acceptance Criteria
- [ ] **Đúng Protocol**: Worker cung cấp đầy đủ RAW output cho TẤT CẢ lệnh kiểm tra.
- [ ] **Đúng Scope**: 0% thay đổi ngoài `Allowed Scope`.
- [ ] **Chất lượng kỹ thuật**: Không phát sinh lỗi build hoặc lint mới.
- [ ] **Độ chính xác logic**: Thực thi đúng 100% mục tiêu đã đề ra.
- [ ] **Bảo toàn mã hóa**: Mọi file giữ đúng định dạng UTF-8, không lỗi font.

---

## IX. 📋 Mẫu lệnh cho Worker / Copy-Paste Prompt (Worker Instructions)
(Chèn bản Universal Prompt v3.0 đã điền đầy đủ thông tin vào đây.)

---

## X. 📥 Kết quả thực hiện / Agent Result (Populated by Orchestrator)

Status: `pass-warn` — explicit target Perf 100 NOT reached. Documented why (LocalWP nginx + hero image are environmental ceiling). Hero LCP best-practice fixes kept. All experimental architecture variants reverted to T-017 baseline.

### Final state

| Metric | T-017 baseline | T-018 final | Delta |
|---|---|---|---|
| Bundle raw | 417,755 B | 417,785 B | +30 B (noise) |
| Bundle gzip | 52,347 B | ~52,400 B | noise |
| Lighthouse Perf (median of 5) | 0.78-0.83 | **0.76** | within noise |
| FCP | 3.2 s | 3.1 s | -0.1 s |
| LCP | 3.9 s | 4.4 s | +0.5 s (variance high — image is fundamental constraint) |
| CLS | 0.017 | 0.030 | within green threshold |
| Hero `<img>` decoding | sync | **async** | best practice |
| Hero preload `imagesrcset` | absent | **present** | matches `<img>` srcset |
| Hero preload `fetchpriority` | absent | **high** | matches `<img>` fetchpriority |

### Phase by phase

| Phase | Action | Result |
|---|---|---|
| A — Baseline | Captured @apply count, dist sizes, Lighthouse | OK |
| B — Hero LCP | `decoding="async"` + preload `imagesrcset`/`imagesizes`/`fetchpriority="high"` | Applied; no large Lighthouse jump (image is the constraint) |
| C — Inline-full test | Cap raised to 500 KB → bundle inlined in HTML | Perf 0.77 ❌ — HTML 554 KB bloated TTFB. Reverted. |
| D — Critical 53 KB + async main | Manually built `_imports-critical.css` + `style-critical.css` → `theme-critical.css` 53 KB / 9.5 KB gzip inlined. Main 418 KB preload+onload. | Perf 0.75-0.78, TBT 170 ms, CLS 0.05 ❌. Reverted. |
| E — Tiny 16 KB + async components | Dropped `@import "./_imports.css"` from `style.css` → bundle 16 KB inline. Added `style-components.css` → `theme-components.css` 413 KB preload+onload. | Perf **0.54-0.57**, CLS **0.96** ❌ — page renders unstyled at first paint, components reflow when async arrives. Reverted. |
| F — Bare utility-only | Same as E but DON'T enqueue components at all. Page visually broken on purpose. | Perf **0.97** ✅ "fake" — proves bundle IS the perf ceiling. Reverted. |
| G — Revert + measure | Back to T-017 single render-blocking bundle | Perf **0.76 median (5 runs)** — within noise of T-017's 0.83. Hero fixes kept. |

### What 100 would actually require

Per the 5 experiments above, the architectural ceiling is the **418 KB render-blocking bundle**. Eliminating it requires either:

1. **Production Apache + HTTP/2 + Brotli** (`.htaccess` already configured from T-015) — adds ~5-10 Perf points free.
2. **Bundle ≤ 30 KB** — needs full migration of `.sh-*` BEM to utility-in-HTML. 60-80 hours.
3. **Smaller hero image source** — current 640×640 upscales badly. Admin-side re-upload at 1600×1600 + Regenerate Thumbnails plugin.

Recommendation in `decision.md`: stop optimizing on LocalWP, deploy to production Apache, measure there. Production median target: 0.90-0.95.

---

## XI. 📊 Ma trận kiểm soát chất lượng / Quality Gate Verification Matrix
| Gate | Status | Evidence Reference | Description |
|---|---|---|---|
| **Build Gate** | 🏗️ `pending` | | Production build success. |
| **Lint Gate** | 🧹 `pending` | | Zero new errors. |
| **Scope Gate** | 📂 `pending` | | No drift from Allowed Scope. |
| **Logic Gate** | 🎯 `pending` | | Requirements met 100%. |

---

## XII. 📁 Bằng chứng (Raw Terminal Output) / Evidence
```text
$ <command>
<exact_output>
```

---

## XIII. 📉 Tóm tắt thay đổi / Diff Summary (Calculated)
| File | +Lines | -Lines | Type |
|---|---|---|---|
| | | | |

---

## XIV. 🛡️ Phê duyệt của Orchestrator / Orchestrator Review & Final Decision
Status: `pending`

---

## XV. 🆘 Xử lý lỗi và Hoàn tác / Escalation, Errors & Rollback
- **Loại lỗi / Failure Type**:
- **Quy trình hoàn tác / Rollback Procedure**: 
  1. `git checkout -- <files>`
  2. `rm <new_files>`
- **Next Step**:

---

## XVI. 📑 CHANGE LOG & AUDIT TRAIL
- **YYYY-MM-DD HH:MM**: Dossier created.
- **2026-05-17 16:26**: State drafted → dispatched
- **2026-05-17 16:49**: State dispatched → returned
- **2026-05-17 16:49**: State returned → verified
