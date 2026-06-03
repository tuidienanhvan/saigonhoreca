# 📖 Hướng dẫn vận hành hàng ngày / Daily Guide — v4.3

Manual for creating, delegating, reviewing, archiving work trong 5-agent system (3 Orchestrator + 2 Worker). Scripts-first workflow.

> **Full lifecycle**: `system/WORKFLOW.md`. Behavioral rules: `SKILL.md`. Agent roster: `system/__roster.json`.

---

## 0. 🩺 Healthcheck trước khi dispatch

```bash
bash system/scripts/lint-handoffs.sh --strict   # phải exit 0
# nếu fail:
bash system/scripts/reconcile.sh --dry-run      # xem drift
bash system/scripts/reconcile.sh --fix          # auto-fix
```

---

## I. 🎯 Tạo công việc mới / Create A Task (v4.3 atomic)

```bash
bash system/scripts/new-task.sh \
  --owner <agent> --priority P2 --risk medium \
  --slug refactor-x --description "Refactor X to feature-slice" \
  --intent "<paste user message verbatim>"
```

Script tự động:
1. Auto-generate task ID (T-YYYYMMDD-NNN)
2. Copy `system/templates/TASK.md` → `active/T-YYYYMMDD-NNN-<owner>-<slug>.md`
3. Fill frontmatter (id, owner, priority, risk, created/updated, escalation_path)
4. Inject Phase 0 user intent block
5. Acquire lock `system/locks/T-{ID}.lock`
6. Append STATUS §I row

→ Open dossier, fill §II Goal + §IV Allowed Scope + §VII Verification Commands.

---

## II. 📤 Giao việc cho Worker / Delegation (v4.3 atomic state)

Khi giao việc, orchestrator dùng:
```bash
bash system/scripts/set-state.sh T-YYYYMMDD-NNN dispatched
```

→ Tự cập nhật YAML `state:` + `updated:` + STATUS row cell + lock heartbeat.

Copy prompt từ §IX dossier → paste vào chat agent đích.

**Worker workflow**:
1. **Self-check**: Read `AGENTS/<self>.md` → verify capability match. Mismatch → return `state: rejected`.
2. Read dossier + `SKILL.md` + `project/PROJECT.md`
3. Audit phase: verify problem exists (grep/ls)
4. Implementation: atomic edits in `## Allowed Scope`
5. Verification: run `## Verification Commands`, paste raw output vào `## Evidence`
6. Return REPORT block. Orchestrator chạy: `set-state.sh <ID> returned`

**Worker KHÔNG được**:
- ❌ Đụng file ngoài `## Allowed Scope`
- ❌ Skip Verification Commands
- ❌ Claim done không paste raw output
- ❌ Tự `state: archived` (only Orchestrator archives)
- ❌ Paraphrase build/lint output

---

## III. 🔍 Kiểm tra và Xác minh / Review & Verification

User → Claude:

```text
Review task T-YYYYMMDD-XXX
```

Claude executes Phase C:
1. **Independent audit**: Run `git status --short` + `git diff --stat HEAD` separately
2. **4 Quality Gates**:
   - 🏗️ Build: `npm run build` exit 0
   - 🧹 Lint: `npm run lint` zero errors
   - 📂 Scope: `git status` no file outside Allowed Scope
   - 🎯 Logic: matches user intent 100%
3. **Cross-family spot check** (1/5 tasks): different family agent verifies
4. **Tick checkboxes**: `- [ ]` → `- [x]` for passed criteria
5. **Verdict**: Set `state: verified` if ALL gates green

Pass → Phase D atomic archive (next step).
Fail → fill `## Escalation`, set `state: blocked`, retry/split/reroute.

---

## IV. 📁 Lưu trữ công việc / Archive (Phase D Atomic)

Sau khi verified, user → Claude:

```text
Archive T-YYYYMMDD-XXX
```

Claude runs:
```bash
bash .task-handoffs/system/scripts/archive-task.sh T-YYYYMMDD-XXX
```

Script atomic 6 steps:
1. ✅ Validate state=verified
2. ✅ Check changes/T-{id}-*/ if risk=high|critical
3. ✅ Update state→archived + add archived timestamp
4. ✅ Move active/ → archive/YYYY-MM/
5. ✅ Remove STATUS.md row Section 1, restore None placeholder
6. ✅ Append LEADERBOARD.md line

**Verify**:
```bash
bash .task-handoffs/system/scripts/lint-handoffs.sh
# Exit 0 = clean, exit 1 = drift detected
```

---

## V. 🔄 Hoàn tác sau khi lưu trữ / Post-Archive Rollback (v3.0 NEW)

Phát hiện regression sau archive:

| Time | Action |
|------|--------|
| < 1h | Set `state: reopened`, fix in-place, re-verify |
| 1-24h | Tạo task mới `T-{new}-rollback-of-{old}` |
| > 24h | Bug fix task riêng, link parent trong `## References` |

For high-risk: follow `changes/T-{id}-*/rollback-plan.md`.

---

## VI. ⚠️ Quy trình xử lý thất bại / Failure Flow

| Failure | Action |
|---------|--------|
| **Lần 1**: Build/lint fail | Reviewer feedback in `## Escalation` → retry_count++, re-delegate cùng agent |
| **Lần 2**: Build/lint fail | Escalate per `escalation_path` (Grok→Codex→Claude) |
| **Scope quá lớn** | Decompose `T-{parent}-1`, `T-{parent}-2`. Parent → `decomposed` |
| **Wrong agent** | Re-route per ROUTING.md, log LEADERBOARD |
| **Silent fail** | Critical violation. Auto `blocked`. Agent rating deduct |

---

## VII. 🚦 Cập nhật trạng thái Agent / Update Agent Status

Khi agent off/limited/active:
```text
<Agent name> off / limited / active
```

Claude update `AGENTS/<name>.md` `status:` field + `system/AGENT-MODEL.md` dashboard. Routing auto-skips agents with `status: inactive`.

---

## VIII. 🚫 Những điều cấm kỵ / Hard Don'ts

- ❌ Không gửi task mơ hồ cho worker khi chưa có dossier
- ❌ Không accept chỉ vì report nói "done" — phải verify Evidence
- ❌ Không touch plugin ngoài `plugins/pi-api/` trừ khi user gọi tên rõ
- ❌ Không bỏ qua `git status --short` nếu task claim working tree sạch
- ❌ Không retry blind — luôn đính reviewer feedback
- ❌ Không manual archive (skip script) — drift sẽ tái diễn (v3.0)
- ❌ Không skip Phase 0 user intent capture (v3.0) — mất audit trail

---

## IX. 🔧 Các lệnh và tự động hóa phổ biến / Common Scripts

```bash
# 📁 Archive verified task (atomic Phase D)
bash .task-handoffs/system/scripts/archive-task.sh T-20260510-001

# Detect drift before commit
bash .task-handoffs/system/scripts/lint-handoffs.sh

# Per-agent task count
grep " | claude | " .task-handoffs/LEADERBOARD.md | wc -l

# Pass rate per agent
grep " | gemini | " LEADERBOARD.md | grep " | pass " | wc -l
```

---

## X. 📜 Version

**v4.3** (2026-05-16): Phase A/B automation: `new-task.sh` + `set-state.sh` + `prune-changes.sh` + `install-hooks.sh` + `lint --fix`.
**v4.2** (2026-05-15): Roster trim 9→5 agents, merged HOW-TO-USE.md + AI-COLLAB.md, added §0 healthcheck.
**v3.1** (2026-05-10): Role Self-ID, tier model fix, all docs v3.1. See `system/WORKFLOW.md` changelog.
**v3.0** (2026-05-08): Phase 0 + E, atomic scripts, worker self-check, post-archive rollback.
