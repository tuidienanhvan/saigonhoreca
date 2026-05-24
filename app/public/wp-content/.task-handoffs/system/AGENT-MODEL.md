# 🤖 Mô hình Agent / Agent Model — v4.3

9 AI agents trong roster (3 orchestrators + 6 workers). Routing tối ưu = đúng agent + đúng task.

> **Naming convention**: Lowercase canonical (`claude`, `codex`, `gemini`, `poolside`, `arcee`, `nemotron`, `grok`, `ling`, `stepfun`). Aliases: ChatGPT=Codex, Antigravity=Gemini. Per-agent: `AGENTS/<name>.md`.
>
> **Family classification** (for Cross-Family Spot Check, v3.0): see `system/WORKFLOW.md` §3.7

---

## I. 📊 Bảng trạng thái hoạt động / Status Dashboard

> User update status field bên dưới khi agent off/quota hết. Claude check trước khi delegate.
>
> **Lần đồng bộ cuối / Last sync:** 2026-05-10 (v3.1)

| # | Agent | Model | Tier | Status | Note |
|---|---|:---:|:---:|:---:|---|
| 1 | **Claude Code** | opus-4.7 / 4.6 | 1 | 🟢 ACTIVE | Primary orchestrator |
| 2 | **ChatGPT / Codex** | 5.5 / 5.4 | 1 | 🟢 ACTIVE | — |
| 3 | **Gemini / Antigravity** | 3-flash | 1 | 🟢 ACTIVE | — |
| 4 | **Poolside Laguna M.1** | poolside-m1 | 2 | 🟢 ACTIVE | — |
| 5 | **Arcee Trinity Large** | arcee-v2 | 2 | 🟢 ACTIVE | Preview model — đôi khi lag |
| 6 | **NVIDIA Nemotron 3 Super** | nemotron-4 | 2 | 🟢 ACTIVE | — |
| 7 | **xAI Grok Code Fast 1** | grok-2 | 2 | 🟢 ACTIVE | — |
| 8 | **inclusionAI Ling-2.6-1T** | ling-v1 | 2 | 🟢 ACTIVE | — |
| 9 | **StepFun Step 3.5 Flash** | stepfun-v1 | 2 | 🟢 ACTIVE | — |

**Status legend:**
- 🟢 `ACTIVE` — sẵn sàng nhận handoff
- 🟡 `LIMITED` — còn hoạt động nhưng quota gần hết / chậm / preview lag
- 🔴 `INACTIVE` — off / quota hết / paused — KHÔNG delegate
- ⚫ `DEPRECATED` — bỏ khỏi roster

---

## II. 👑 Tầng 1 — Điều phối viên / Tier 1 — Orchestrators

Orchestrators có thể lập kế hoạch, delegate, và tự implement. Ràng buộc: không delegate cho nhau — chỉ delegate xuống Tier 2.

### 🟢 Claude Code (primary orchestrator)

**Status:** ACTIVE — always on

Responsibilities:
- Plan generation / lập kế hoạch dossier
- Direct implementation / tự sửa khi task < 30m
- Browser MCP smoke test (real visual verify)
- Multi-tool integration (file + web + git + docker)
- Verification / chạy build/test/lint gates
- Final reporting / archive handoff
- Review output từ tất cả delegated agents

Must protect: user intent, file scope, production behavior.

### 🟢 ChatGPT / Codex

**Status:** ACTIVE

**Surgeon** — per-file refactor với clean output.

Orchestrator khi: task có scope rõ, file list cố định, acceptance criteria viết được thành checklist.

Best for:
- Single-file mechanical refactor (lint fix, type migration)
- Test writing per-component
- Apply patches từ audit results
- Task < 5 files với verification commands rõ ràng

Limit: không scan toàn codebase (paste-based).

### 🟢 Gemini / Antigravity

**Status:** ACTIVE

**Auditor** — broad codebase scan + cross-file pattern detection.

Orchestrator khi: task cần sweep nhiều file, tìm pattern, lập audit report để Tier 2 apply.

Best for:
- Audit nhiều file (>20)
- Tìm tất cả instance của pattern
- Mobile/responsive sweep
- Visual audit + auto-fix plan
- Documentation inventory
- Consistency sweep

Limit: ít deep refactor, đôi khi miss subtle bugs.

---

## III. 🛠️ Tầng 2 — Thực thi viên / Tier 2 — Workers

Workers nhận handoff từ Tier 1, thực thi task cụ thể, báo cáo kết quả. Không tự delegate.

### 🟢 Poolside Laguna M.1 — Code Master

**Status:** ACTIVE

**Refactor & deep understanding.**

Best for:
- CSS migration (Tailwind ↔ CSS modules)
- React hook design
- Component refactor giữ behavior
- Code có context phức tạp giữa nhiều file

Weakness: chat khô khan, ít sáng tạo content.

### 🟢 Arcee Trinity Large — Logical Planner

**Status:** ACTIVE — Preview, đôi khi lag

**Multi-step task orchestration.**

Best for:
- Lên kế hoạch architecture
- Task chia nhiều bước có dependency
- Thiết kế data flow / API contract
- Migration plan (DB schema, framework upgrade)

Weakness: preview model — đôi khi lag/timeout.

### 🟢 NVIDIA Nemotron 3 Super — Technical Reasoner

**Status:** ACTIVE

**Deep technical analysis.**

Best for:
- Debug lỗi khó (race condition, memory leak)
- Tối ưu performance (profile + recommend)
- Math / algorithm correctness check
- Viết technical docs (RFC, ADR)

Weakness: dài dòng, phải prompt cụ thể "concise output".

### 🟢 xAI Grok Code Fast 1 — Speed Demon

**Status:** ACTIVE

**Tốc độ phản hồi cao nhất.**

Best for:
- Viết script utility ngắn
- Fix syntax error
- Boilerplate generation (Vue component, API route stub)
- Commit message / PR description

Weakness: thiếu chiều sâu cho logic phức tạp.

### 🟢 inclusionAI Ling-2.6-1T — Knowledge Polyglot

**Status:** ACTIVE

**Content + multilingual.**

Best for:
- Viết blog / SEO content
- Dịch tiếng Việt ↔ tiếng Anh
- Marketing copy (landing page, email)
- Documentation user-facing
- README, changelog, migration guide

Weakness: code có thể "lan man", chậm hơn các model khác.

### 🟢 StepFun Step 3.5 Flash — Ultra Light

**Status:** ACTIVE

**Tra cứu nhanh.**

Best for:
- Câu hỏi 1-2 dòng có answer cụ thể
- Tóm tắt nội dung ngắn
- Light UI tweak (đổi màu, đổi font)
- Validate snippet code nhỏ (< 50 dòng)

Weakness: không xử lý file dài / nested logic.

---

## IV. 🚥 Giao thức cập nhật trạng thái / Status Update Protocol

Khi agent đổi status, update bảng dashboard ở đầu file + phần status từng agent. Format:

```
🟡 Arcee Trinity Large
**Status:** LIMITED — Preview backlog 6h queue (2026-05-03 14:30)
```

```
🔴 Ling-2.6-1T
**Status:** INACTIVE — Quota hết, reset 2026-05-04 00:00 UTC
```

Claude tự động skip agent có status `🔴 INACTIVE` và route sang fallback theo bảng `ROUTING.md`.

Khi user chỉ nói tên agent off/on, Claude update file ngay không cần hỏi lại.

---

## V. 👤 Vai trò của người dùng / User Role

User quyết định:
- Product direction
- Khi nào delegate
- Final approval cho production change
- **Update agent status** khi biết agent off/on

User KHÔNG cần maintain low-level workflow files manually — Claude tự handle ngoài trừ status field.
