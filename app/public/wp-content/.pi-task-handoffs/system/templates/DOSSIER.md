---
id: T-YYYYMMDD-XXX
owner: <agent-name>
state: drafted
priority: P2
risk: low
requires_user_approval: false
confidence_threshold: 0.8
hitl_triggers: []          # vd [delete_files, schema_change, public_api_break]
depends_on: []
updated: YYYY-MM-DD HH:MM
---

<!--
═══════════════════════════════════════════════════════════════════
📌 ĐỌC TRƯỚC KHI LÀM / READ FIRST (bootstrap block — v1.5)
═══════════════════════════════════════════════════════════════════
1. Chạy: bash system/scripts/prime-context.sh   (active tasks + memory + skills)
2. Đọc §III Required Reading + §III.M Memory References (pitfall/pattern liên quan)
3. Đọc ../SKILL.md (luật vận hành) · system/QUALITY-GATES.md (4+HITL gate) · system/REPORTING.md (evidence)
4. Self-ID role (SKILL §1.3): Worker chỉ làm trong §IV Allowed Scope.
5. Gặp HITL trigger (xem frontmatter hitl_triggers / confidence_threshold) → system/HITL.md → state: awaiting_approval.
   Chuỗi đọc đầy đủ theo role/phase: system/READ-CHAINS.md
═══════════════════════════════════════════════════════════════════
-->

# 📋 T-YYYYMMDD-XXX | <agent> | <slug> — Dossier (master skeleton 17 sections) — v1.5

> Reconstructed: 2026-06-03. Base: `PI-CONTENT-ENGINES.md §IV` (canonical 17-section convention).

## 0. 🎤 User Original Intent (verbatim)
(Paste NGUYÊN VĂN yêu cầu user. KHÔNG paraphrase. Ghi timestamp + medium chat/voice/screenshot. Mất verbatim = mất audit trail — SKILL §2.5.)

---

## I. 📊 Frontmatter & Risk Matrix
| Field | Values | Mô tả |
|---|---|---|
| `id` | `T-YYYYMMDD-XXX` | 🆔 Định danh duy nhất theo ngày. |
| `owner` | tên viết thường | 👤 Agent được giao (vd codex, qwen, gemini). |
| `state` | drafted…archived | 🔄 Vòng đời — xem `system/LIFECYCLE.md`. |
| `priority` | P0…P3 | 🚥 P0 khẩn · P1 cao · P2 chuẩn · P3 cải thiện. |
| `risk` | cosmetic…critical | ⚠️ cosmetic/low/medium/high/critical. |
| `requires_user_approval` | bool | 🙋 true = bắt buộc HITL trước khi published. |
| `confidence_threshold` | 0–1 | 🎯 Dưới ngưỡng → pause `awaiting_approval`. |
| `hitl_triggers` | list keyword | 🚨 Action chạm list → pause (xem `system/HITL.md`). |
| `depends_on` | list ID | 🔗 Task phải xong trước. |

---

## II. 🎯 Goal & Strategic Objective
(Nhiệm vụ là gì? Giá trị business? Vấn đề kỹ thuật? Định nghĩa end-state mong muốn.)

---

## III. 📚 Required Reading (Context)
- 🛡️ `../SKILL.md` — operating protocol + anti-hallucination.
- 🏗️ `project/PROJECT.md` — tech stack + conventions.
- 🏆 `system/QUALITY-GATES.md` — nghiệm thu 4+HITL.
- 📤 `system/REPORTING.md` — evidence format.
- 🔄 `system/LIFECYCLE.md` — state machine.

### III.M 🧠 Memory References
(Từ `_brain/memory/INDEX.md` — pitfall/pattern liên quan task này. Skill `reference-memory` fill. Vd: "Pitfall #12: Tailwind v4 @apply broke", "Pattern: crawl static-mirror trước khi refactor".)

---

## IV. 🚧 Allowed Scope (Strict)
(Liệt kê file/folder cụ thể agent được chạm. Không glob mơ hồ. Chạm ngoài = critical violation.)
- 📄 `path/to/file`

---

## V. 🚫 Out-of-Scope (Strictly Forbidden)
- ❌ Refactor không liên quan / ghost refactor.
- ❌ Đổi UI/UX nếu không yêu cầu.
- ❌ Đụng `package.json` / root configs.
- ❌ Thêm/xóa npm dependencies tùy tiện.

---

## VI. 🛠️ Phases of Execution
1. **Audit & Baseline** 🔍 — đọc file scope, grep/ls verify problem có thật.
2. **Implementation** 🛠️ — atomic edits, UTF-8 + diacritic VN.
3. **Verification** 🧪 — chạy §VII, capture RAW stdout/stderr.
4. **Reporting** 📤 — REPORT block (§X + REPORTING.md).

---

## VII. 🔍 Verification Commands (Mandatory)
```bash
npm run lint
npm run build
git status --short
```

---

## VIII. ✅ Acceptance Criteria
- [ ] RAW output đầy đủ cho TẤT CẢ lệnh §VII.
- [ ] 0% thay đổi ngoài §IV Allowed Scope.
- [ ] Không lỗi build/lint mới.
- [ ] Đúng 100% user intent (§0).
- [ ] UTF-8 nguyên vẹn, 0 mojibake.
- [ ] HITL `n/a | resolved` (nếu có trigger).

---

## IX. 📋 Operator Prompt (Copy-Paste cho Worker)
(Universal prompt đã điền đầy đủ — Identity + dossier path + task type + est. time + hard rules + reply format. ≤30 dòng. Xem `system/templates/PROMPT.md`.)

---

## X. 📥 Result (Populated by Orchestrator)
Status: `not-started`

---

## XI. 📊 Gate Matrix (4 + HITL)
| Gate | Status | Evidence Ref | Mô tả |
|---|---|---|---|
| **Build** | 🏗️ `pending` | | Production build exit 0, bundle ±10%. |
| **Lint** | 🧹 `pending` | | 0 error, UTF-8 intact. |
| **Scope** | 📂 `pending` | | Không drift ngoài §IV. |
| **Logic** | 🎯 `pending` | | Match user intent 100%. |
| **+HITL** | 🙋 `n/a` | | Mọi checkpoint resolved (`system/HITL.md`). |

---

## XII. 📁 Evidence (Raw Terminal Output)
```text
$ <command>
<exact raw output — KHÔNG paraphrase>
```

---

## XIII. 📉 Asset Summary
| File | +Lines | -Lines | Type |
|---|---|---|---|
| | | | |

---

## XIV. 🛡️ Orchestrator Review & Final Decision
Status: `pending`  (verified | reopened | rejected)

---

## XV. 🆘 Escalation, Errors & Rollback
- **Failure Type**:
- **Rollback Procedure**:
  1. `git checkout -- <files>`
  2. `rm <new_files>`
- **Next Step**:

---

## XVI. 📑 Change Log & Audit Trail
- **YYYY-MM-DD HH:MM**: Dossier created.

---

## XVII. 🙋 HITL Log
(Mọi pause `awaiting_approval`: trigger · timestamp · nội dung chờ duyệt · quyết định USER (approve/replan/cancel). Cơ chế: `system/HITL.md`.)
- _none_
