# 🛡️ PI ECOSYSTEM | TASK-HANDOFF ARCHITECTURE (Infinity Edition v4.3)

Khế ước ràng buộc mọi AI Agent — từ Orchestrator (Claude/Codex/Gemini) đến Worker (Tier 2 specialists) — trong workspace `wp-content/`. Quy trình tối thượng cho consistency, transparency, performance — với automation enforcement.

---

## I. 🏛️ TRIẾT LÝ CỐT LÕI (CORE PHILOSOPHY)

### 1.1 🧪 Technical Integrity (Evidence-First)
Không chấp nhận kết quả nếu không có bằng chứng kỹ thuật. Tuyên bố "build pass", "lint clean", "tests green" **vô giá trị** nếu thiếu raw terminal output. Agent claim "đã xong" mà không paste log = process violation, task auto `blocked`.

### 1.2 📁 Atomic Persistence
Mọi thay đổi phải ghi vết qua **Dossier** và lưu trữ vào `archive/YYYY-MM/`. Không exception. Code đã commit nhưng không có dossier = không tồn tại. Đây là cơ chế chống "ghost change".

### 1.3 🧠 Anti-Hallucination
AI dễ tự thuyết phục đã làm đúng dù chưa verify. Áp dụng **Independent Audit Loop**: Worker thực thi → Orchestrator chạy lại verify → Cross-family agent spot check 1/5 task. Không Agent nào tự duyệt tác phẩm của chính mình.

### 1.4 🔒 Single Source of Truth
`.task-handoffs/` là nơi DUY NHẤT chứa thông tin task. Slack chat, screenshot, voice note **không có giá trị pháp lý**. Chỉ dossier markdown trong filesystem mới là sự thật.

### 1.5 📜 6 Nguyên Tắc Vàng (The 6 Golden Rules)
1. **Không Scope Creep**: Cấm tự ý thêm feature "tiện thể" ngoài Allowed Scope.
2. **Tư Vấn Trước Khi Code**: Luôn CONSULT (đưa 2-3 options) dù user yêu cầu làm ngay.
3. **Một File, Một Trách Nhiệm**: Tuân thủ Single Responsibility, không nhồi nhét logic.
4. **Quyền Tối Thiểu**: Chỉ yêu cầu file access vừa đủ.
5. **Observable**: Mọi lỗi phải log rõ ràng. Cấm dùng `try { } catch { }` nuốt lỗi.
6. **Bảo Mật Dữ Liệu**: Cấm log thông tin nhạy cảm (tokens, keys) ra terminal/report.

---

## II. 🚀 QUY TRÌNH 7 GIAI ĐOẠN (STANDARD OPERATING PROCEDURE)

### 2.0 📝 Phase 0 — User Intent Capture
Trước khi drafting, Orchestrator lưu verbatim yêu cầu của user vào dossier section `## 0. User Original Intent`. Ghi rõ timestamp, medium (chat/voice/screenshot).
*Why*: Khi task fail, đây là trail để phân biệt worker misread hay orchestrator misroute.

### 2.1 📂 Phase A — Planning & Drafting (Genesis)
1. **Dossier Path**: `.task-handoffs/active/T-YYYYMMDD-XXX-<agent>-<slug>.md`. Tên file là contract.
2. **YAML Frontmatter**: id, owner, state: drafted, priority, risk, estimated_minutes, parent, children, depends_on, parallelization_ok, created, updated.
3. **Allowed Scope**: Liệt kê file path cụ thể. Không glob mơ hồ. Chạm file ngoài scope = critical violation.
4. **Out Of Scope**: CẤM rõ ràng — không refactor "for cleanup", không touch `package.json`, không add npm dependencies tùy tiện.
5. **Phases**: Steps tuần tự (Audit → Implementation → Verification → Reporting).
6. **Status Sync**: Update `STATUS.md` trong 60s sau khi tạo dossier.
7. **Lock Acquisition**: Atomic lock (xem §4).

---

### 2.2 🛠️ 👷 Phase B: Thực thi công việc / Execution (Worker)

1. **Worker Self-Check & Role ID**: Đọc `AGENTS/<self>.md` verify năng lực. Nhận diện Role qua input pattern theo `SKILL.md` §1.3. Vượt context window hoặc mismatch → return `state: rejected`.
2. **Context Loading**: Đọc PROJECT.md, SKILL.md, dossier.
3. **Audit Phase**: Grep/ls verify problem có thật. Vấn đề không tồn tại → DỪNG, điền `## Escalation`.
4. **Implementation Atomic**: Mỗi thay đổi = 1 unit logic. Không "improve" surrounding code.
5. **Heartbeat**: Update `last_heartbeat` mỗi 2–3 phút.
6. **Evidence-First**: PASTE RAW OUTPUT vào `## Evidence`. Cấm paraphrase.
7. **Diff Summary**: List từng file với +X -Y lines.
8. **Encoding Guard**: UTF-8 + diacritic Tiếng Việt 100%.

---

### 2.3 🧪 🔍 Phase C: Kiểm tra và Đánh giá / Review & Verification (Orchestrator)

Orchestrator KHÔNG tin Worker. Tự chạy `git diff HEAD` — empty diff = auto reject.
4 Gates bắt buộc:
1. 🏗️ **Build**: `npm run build` (Exit 0, bundle không tăng >10%)
2. 🧹 **Lint**: `npm run lint` (Zero errors, UTF-8 intact)
3. 📂 **Scope**: `git status --short` (Không file ngoài scope)
4. 🎯 **Logic**: Manual / Browser MCP (100% match user intent)

*Spot Check*: 1/5 task verify bởi agent khác family (xem §6). Disagreement → user quyết. Tick checkboxes, set `state: verified`.

### 2.4 📁 Phase D — Archiving (Atomic Persistence)
Khuyến nghị dùng tool: `bash system/scripts/archive-task.sh T-YYYYMMDD-XXX`
Atomic 6 steps:
1. Verify `state: verified`
2. Check `changes/` nếu risk: high|critical
3. Update `state: archived`
4. Move active/ → archive/YYYY-MM/
5. Update STATUS.md (Remove Section 1, add to Section 4 rotation)
6. Append LEADERBOARD.md (1 dòng pipe-delimited)

Check drift: `bash system/scripts/lint-handoffs.sh`

### 2.5 🔄 Phase E — Post-Archive Rollback
Phát hiện regression sau archive:
- **< 1h**: Set `state: reopened`, fix in-place, re-verify, re-archive.
- **1–24h**: Tạo `T-{new}-rollback-of-{old}` task referencing parent.
- **> 24h**: Treat như bugfix mới, link original ở `## References`.

### 2.6 🔄 Phase F — Chế Độ Đa Tầng & Snapshot (Multi-Tier & Context)
- **4 Modes Thực Thi**: Orchestrator dùng **CONSULT** (đưa options) ở Phase A. Worker dùng **BUILD** (áp dụng TDD) ở Phase B, **DEBUG** (Root cause analysis) khi fail gate, và **OPTIMIZE** (Profile metrics) khi có bottleneck thực tế. Cấm sửa may rủi (Shotgun Debugging).
- **Context Snapshot**: Trước khi kết thúc session >2 tiếng, Orchestrator tạo `Context_Snapshot.md` lưu trạng thái chống "não cá vàng".
- **Đào Kim Cương**: Sau phiên debug khó, Worker trích xuất Root Cause vào `Troubleshooting_Tips.md` để tái sử dụng về sau.

---

## III. 👥 AGENT ROSTER (2-TIER MODEL)

**👑 Tier 1 — Orchestrator**
- **Claude**: The Architect. Complex planning, deep reasoning.
- **Codex (ChatGPT)**: The Surgeon. High-precision implementation.
- **Gemini (Antigravity)**: The Auditor. Broad scan, consistency sweeps (1M context).

**🛠️ Tier 2 — Worker (Specialists)**
- **Poolside (Laguna M.1)**: CSS/Tailwind + React hook.
- **Arcee (Trinity Large)**: Architecture planning.
- **Nemotron (3 Super)**: Deep reasoning, race condition, perf.
- **Grok (Code Fast)**: Speed demon — quick fix, boilerplate.
- **Ling (inclusionAI)**: Polyglot — SEO, translation.
- **Stepfun (Flash)**: Ultra-light — quick lookups.

*(Chi tiết xem `system/AGENT-MODEL.md` và `AGENTS/*.md`. Routing rule tại `system/ROUTING.md`)*

---

## IV. 🔒 LOCK SYSTEM v3.1 (Atomic)

1. **Acquire**: Pattern `mv -n lock.tmp T-{ID}.lock` tránh race condition. Chứa: agent, session_id, started_at, last_heartbeat.
2. **Heartbeat**: Update `last_heartbeat` mỗi 2–3 phút.
3. **Stale**: Heartbeat > 10 phút → agent khác override được.
4. **Release**: Xoá lock khi verified/archived.
5. **STATUS Tracking**: Cột Session ID + Heartbeat cho active task.

---

## V. 🆘 ESCALATION & DECOMPOSITION

| Tình huống | Action |
| --- | --- |
| **Fail lần 1** | Retry cùng agent + reviewer feedback. `retry_count++` |
| **Fail lần 2** | Escalate per `escalation_path` (T2 → T1 Orchestrator). |
| **Scope quá lớn** | Decompose (chia nhỏ) T-{parent}-1, T-{parent}-2. Parent = `decomposed`. (Tiêu chí: >30p, >5 files, cross-domain). |
| **Silent fail** | Worker giấu lỗi build/lint, claim pass. Critical violation. Auto `blocked`. Rating deduct. |
| **Capability mismatch** | Re-route per ROUTING.md. Log LEADERBOARD. |

**Transparency Rule**: Lỗi build/lint phải báo trung thực ngay. "Silent fail" — giấu lỗi để claim pass — là vi phạm nghiêm trọng nhất.

---

## VI. 🧬 AGENT FAMILIES & CONTEXT LIMITS

**Families (Dùng cho Cross-Family Spot Check):**
- **Anthropic**: Claude
- **OpenAI**: Codex (ChatGPT)
- **Google**: Gemini (Antigravity)
- **xAI**: Grok
- **Independent**: Poolside, Arcee, Nemotron, Ling, Stepfun

**Token Budget Awareness:**
- < 5K LOC (1-3 files): 100KB+ (Codex, Grok, Stepfun)
- 5-50K LOC (4-20 files): 200KB+ (Claude, Codex, Poolside)
- Cross-codebase (>20 files): 1MB+ (Gemini, Claude-chunked)

---

## VII. 📤 REPORTING PROTOCOL

Worker bắt buộc reply block:

```text
=== <TASK_ID> REPORT ===
STATUS: pass | pass-warn | fail
SUMMARY: <3-5 câu technical changes>

FILES_MODIFIED:
- <path> (+X, -Y lines)

FILES_CREATED:
- <path>

VERIFY EVIDENCE:
$ <command>
<RAW OUTPUT — không paraphrase>

NOTES:
- 💡 Caveats: <edge cases>
- 🧹 Warnings: <unfixed warnings>
- 👑 Orchestrator Notice: <action required>
=== END REPORT ===
```

Thiếu block / thiếu raw output = không đủ điều kiện archive.

---

## VIII. ⚠️ COMMON PITFALLS

1. **"Done không dossier"**: Code xong skip dossier = task không tồn tại.
2. **Paraphrase output**: "Build successfully" thay vì paste raw log.
3. **Scope creep**: Sửa file ngoài Allowed Scope "tiện đường".
4. **Ghost refactor**: Đổi whitespace/biến/import "for cleanup".
5. **Self-archive**: Worker tự `state: archived` (Chỉ Orchestrator được archive).
6. **Mojibake commit**: File không UTF-8 → tiếng Việt vỡ.
7. **Blind retry**: Re-delegate fail mà không đính reviewer feedback.
8. **Concurrent same file**: 2 agent edit cùng file. Phải `parallelization_ok: false` + lock.
9. **Skip Phase 0**: Không lưu User Intent gốc → mất audit trail.
10. **Manual Archive**: Dùng tay move file thay vì chạy script → sinh ra drift.

---

## IX. 🔧 COMMON COMMANDS & AUTOMATION

```bash
# 📁 Archive 1 verified task (atomic Phase D)
bash .task-handoffs/system/scripts/archive-task.sh T-YYYYMMDD-XXX

# Detect drift before commit
bash .task-handoffs/system/scripts/lint-handoffs.sh

# 📊 Dashboard / Store gates
cd pi-dashboard-webapp && npm run lint && npm run build

# Independent verify
git status --short && git diff --stat HEAD
```

---

## X. 🎯 PROMPT ENGINEERING (Worker Brief)

Orchestrator viết copy-paste prompt chuẩn:
1. **Identity**: "Bạn là <Agent> trong Pi Ecosystem."
2. **Dossier Path**: Absolute path tới `.md`.
3. **Task Type**: refactor/audit/cleanup/bugfix.
4. **Estimated Time**: Phút dự kiến — calibrate depth.
5. **Hard Rules**: Allowed scope, forbidden actions, evidence requirement.
6. **Reply Format**: Reference REPORT BLOCK.

*Note: Tối ưu prompt ≤30 dòng. Context bổ sung nằm trong dossier, không dồn vào prompt.*

---

## XI. 📚 GLOSSARY

- **Dossier**: File markdown chứa định nghĩa, scope, evidence.
- **Orchestrator**: Master agent (Tier 1: Claude/Codex/Gemini).
- **Worker**: Specialist agent (Tier 2).
- **Heartbeat**: Timestamp định kỳ trong lock file.
- **Stale Lock**: Lock không heartbeat > 10 phút.
- **Cross-Family Audit**: Verify bởi agent khác family chống shared hallucination.
- **Atomic Edit**: 1 thay đổi = 1 unit logic.
- **Silent Fail**: Worker giấu lỗi build/lint, claim pass.
- **Phase 0 (v3.0+)**: User Intent Capture — verbatim record.
- **Phase E (v3.0+)**: Post-Archive Rollback — recovery flow.
- **Role Self-ID (v3.1)**: Input-pattern-based role determination per SKILL.md §1.3.

---

## XII. 📜 CHANGELOG
- **v3.1** (2026-05-10): Tích hợp 6 Golden Rules, Multi-Tier Modes (Consult/Build/Debug/Optimize), và Context Snapshot từ PROMAX v6.0.
- **v3.0** (2026-05-08): Phase 0 + E, atomic scripts, worker self-check.
- **v2.1** (2026-05): Lock System v2.1, Cross-family audit.

---

**Mantra**: *"Code in English, Think in Logic, Record in Dossier, Speak in Vietnamese."*

**Three Laws**:
1. Worker không tự duyệt mình.
2. Orchestrator chỉ tin evidence, không tin báo cáo.
3. Dossier là sự thật duy nhất.

*Pi Ecosystem — Infinity Edition v3.1 — "No evidence, no archive. No dossier, no truth."*
