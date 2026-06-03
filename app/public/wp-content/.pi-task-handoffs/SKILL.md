# 🛡️ Quy chuẩn vận hành Agent / AI Agent Operational Protocol — v4.3

Bắt buộc cho MỌI AI Agent (Orchestrator + Worker) trong Pi Ecosystem. Đây là codex of skills — đọc trước khi làm task đầu tiên.

> **For full lifecycle**: see `system/WORKFLOW.md`. This file = behavioral rules.

---

## I. 🏗️ Kiến trúc đa Agent (2 tầng) / Multi-Agent Architecture (2 Tier)

### 1.1 👑 Điều phối viên (Tầng 1) / Orchestrator (Tier 1)
- **Members**: Claude, Codex, Gemini
- **Responsibility**: Plan, dispatch, audit, verify, archive
- **Verification Rule**: KHÔNG BAO GIỜ trust worker's summary. PHẢI verify raw terminal output + independent `git status --short`

### 1.2 👷 Thực thi viên (Tầng 2) / Worker (Tier 2)
- **Members**: Poolside (Laguna), Arcee, Nemotron, Grok, Ling, StepFun
- **Responsibility**: Execute task trong Allowed Scope
- **Reporting Rule**: PASTE raw terminal evidence. Paraphrase = critical violation

### 1.3 🎭 🆔 Tự xác định vai trò / Role Self-Identification (v3.1 NEW)

Khi nhận message từ user, AI xác định role dựa trên input pattern:

| Input Pattern | Role | Behavior |
|---|---|---|
| Có `## Prompt` block + dossier path | **Worker** | Chỉ làm trong Allowed Scope, báo cáo REPORT block |
| "Lên plan / tạo task / review / archive / audit" | **Orchestrator** | Plan, dispatch, verify, archive |
| "Fix X / sửa Y" trực tiếp + scope ≤30m | **Orchestrator-direct** | Tự implement, tự verify, tự archive |
| Mơ hồ / không rõ scope | **→ HỎI user** | Clarify trước khi action |

**Rule**: Nếu không chắc role → HỎI. Không bao giờ tự assume role rồi phá scope.

> Detail per-agent: `AGENTS/<name>.md`. Family classification: `system/WORKFLOW.md` §3.7.

---

## II. 🧠 Giao thức chống ảo giác / Anti-Hallucination Protocol

### 2.1 🧪 Bằng chứng là trên hết / Evidence-First
"Build passed" = invalid. "Build passed + raw exit-0 log pasted" = valid.

### 2.2 🚧 Ranh giới phạm vi / Scope Boundary
File ngoài `Allowed Scope` = critical violation. Bug ngoài scope → log vào `## Out-of-scope Findings`, NEVER fix.

### 2.3 🌍 Bảo vệ mã hóa / Encoding Guard
UTF-8 + Vietnamese diacritics 100%. Mojibake (`Ã`, `â€`, `ðŸ`) = task fail.

### 2.4 ✂️ Tối giản hóa việc sửa đổi / Minimalist Refactoring
Mỗi dòng sửa phải đóng góp trực tiếp goal. Cấm ghost refactor (whitespace/import/style).

### 2.5 🎯 Trung thành với ý định người dùng / User Intent Fidelity (v3.0 NEW)
Phase 0: paste user message verbatim vào dossier `## 0. User Original Intent`. KHÔNG paraphrase user. Mất verbatim record = mất audit trail.

---

## III. 🚀 6 bước thực thi bắt buộc / Mandatory 6-Step Execution

Phase A→D đầy đủ ở `system/WORKFLOW.md`. Worker focus 6 bước:

1. **🔍 Ngữ cảnh / Context**: Read dossier + `project/PROJECT.md` + `AGENTS/<self>.md`
2. **🛡️ Tự kiểm tra / Self-check** (v3.0 NEW): verify task type matches own capability + context size ≤ 80% window. Mismatch → return `state: rejected` BEFORE wasting time.
3. **📋 Kiểm toán / Audit**: Verify problem exists (grep/ls). Not exist → STOP, fill `## Escalation`
4. **🛠️ Triển khai / Implement**: Atomic edits only
5. **🧪 Xác minh / Verify**: Run `## Verification Commands`. Capture stdout + stderr
6. **📤 Báo cáo / Report**: Standardized REPORT block (see `system/REPORTING.md`)

---

## IV. 🏆 Cổng kiểm soát chất lượng kỹ thuật / Technical Quality Gates

| Gate | Command | Pass criteria |
|------|---------|---------------|
| 🏗️ **Build** | `npm run build` (relevant webapp) | Exit 0, bundle ±10% |
| 🧹 **Lint** | `npm run lint` | Zero errors. Warnings justified |
| 📂 **Scope** | `git status --short` | No file outside Allowed Scope |
| 🎯 **Logic** | Manual / Browser MCP | Match user intent 100% |

ALL 4 must pass for `state: verified`. Then `archive-task.sh` for atomic archival.

---

## V. 📁 Tính lưu trữ vĩnh cửu / Atomic Persistence

### Standard tasks (risk: low/medium/cosmetic)
Dossier in `archive/YYYY-MM/T-*.md` + LEADERBOARD entry là đủ.

### High-risk tasks (risk: high/critical)
PHẢI tạo `changes/T-{id}-{owner}-{slug}/` với:
- `decision.md` — Why this approach? Alternatives? Trade-offs?
- `diff.patch` — Output `git diff > diff.patch`
- `rollback-plan.md` — Exact revert steps + validation
- (Optional) `before/`, `after/` — file snapshots

`archive-task.sh` checks this requirement before archiving.

---

## VI. 🆘 Phân cấp xử lý lỗi / Escalation & Failure

**Honest reporting**: Nếu stuck, state explicit reason. Examples:
- "Dependency conflict between X and Y"
- "Logic contradiction: requirement A vs B"
- "Capability mismatch: task needs context > 200K but my window is 100K"

**Retry rule**: Orchestrator decides retry (max 1 + feedback) or escalate. Per `escalation_path` in frontmatter.

**Audit log**: Failures logged in `## Escalation` section của dossier. Improves future routing.

**Silent fail = banned**: Hide build/lint failure → claim pass = MOST SEVERE violation. Agent rating deducted.

---

## VII. 🔒 Hệ thống khóa (An toàn đa Agent) / Lock System (Multi-Agent Safety)

### 7.1 ⚡ Chiếm dụng Lock / Atomic Acquisition (v3.0 — race-free)

```bash
# Bash (Git-Bash on Windows)
LOCK="system/locks/T-{ID}.lock"
TMP="$LOCK.tmp.$$"

cat > "$TMP" <<EOF
agent: claude
session_id: $(uuidgen 2>/dev/null || echo "$$-$(date +%s)")
started_at: $(date -u +%FT%TZ)
last_heartbeat: $(date -u +%FT%TZ)
EOF

if mv -n "$TMP" "$LOCK" 2>/dev/null; then
    echo "✓ Acquired"
else
    rm "$TMP"
    echo "✗ Already locked"; exit 1
fi
```

### 7.2 💓 Nhịp đập hệ thống / Heartbeat (every 2-3 min)
Update `last_heartbeat: $(date -u +%FT%TZ)` in lock file.

### 7.3 🔓 Ghi đè khóa cũ / Stale lock override
If `last_heartbeat > 10 min` ago → another agent can delete + recreate.

### 7.4 🔑 Giải phóng khóa / Release
Delete `system/locks/T-{ID}.lock` when state → `verified` or `archived`.

### 7.5 🔄 Đồng bộ hóa Status / STATUS.md sync
Active task row MUST include `Session ID` + `Heartbeat` columns.

---

## VIII. 📋 Tra cứu nhanh quy trình / Workflow Quick Reference

| Command | Use |
|---------|-----|
| `cat system/templates/TASK.md` | Dossier template |
| `cat system/templates/PROMPT.md` | Worker prompt template |
| `bash system/scripts/archive-task.sh T-...` | **Phase D atomic archival** (v3.0) |
| `bash system/scripts/lint-handoffs.sh` | **Drift detector** (v3.0) |
| `cat system/ROUTING.md` | Task type → agent mapping |
| `cat system/AGENT-MODEL.md` | Tier 1/2 agents detail |
| `cat AGENTS/<name>.md` | Per-agent capability + context window |

---

## IX. 📜 Changelog

- **v3.1** (2026-05-10):
  - ➕ Role Self-Identification (§1.3) — AI xác định role dựa trên input pattern
  - 🔧 All docs unified to v3.1
- **v3.0** (2026-05-08):
  - ➕ User Intent Fidelity (§2.5)
  - ➕ Worker Self-check (§3 step 2)
  - ➕ Atomic Lock Acquisition (§7.1)
  - 🔧 Aligned 2-tier model (Tier 1: Claude/Codex/Gemini; Tier 2: 6 specialists)
  - 🔧 Reference automation scripts (archive-task.sh, lint-handoffs.sh)
  - 🔧 Section ordering rationalized
- **v2.1** (2026-05): Lock system, multi-agent safe
- **v2.0** (2026-05): English translation
- **v1.0** (2026-04): Initial
