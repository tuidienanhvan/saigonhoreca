---
skill_id: harvest-and-review
type: process
invokable_by: orchestrator | user | archive-task.sh hook
target_files: [_brain/memory/_pending/<task-id>-suggested.md (read+delete), _brain/memory/{pitfalls,patterns,playbooks,agent-quirks}/<slug>.md, _brain/memory/INDEX.md]
preconditions: [_brain/memory/_pending/ has ≥1 pending stub, _brain/memory/ directory exists]
reconstructed: 2026-06-03
---

<!-- Nội dung dựng lại sau sự cố mất file — verify lại. -->

# Skill: Harvest & Review — xử lý `_pending/` suggestions → finalize memory entries

> Đọc các stub trong `_brain/memory/_pending/` (do `harvest-lessons.sh` auto-sinh sau archive), cùng USER quyết định mỗi candidate thành pitfall / pattern / agent-quirk / playbook (hoặc bỏ), finalize entry rồi DELETE stub. Mục tiêu: biến raw archive thành Layer-2 knowledge có kỷ luật, không spam.

## When to invoke

Trigger phrases:
- "review pending suggestions"
- "harvest archive vừa rồi"
- "xử lý _pending đi"
- "process pending memory"

Auto-detect conditions:
- `archive-task.sh` Step 8.5 vừa chạy → có stub mới trong `_pending/`
- `_brain/memory/_pending/` có file `status: pending-review`

## Inputs needed

| Input | Required? | How to get |
|---|---|---|
| **pending file(s)** | yes | `ls _brain/memory/_pending/*-suggested.md` |
| **classification decision** | yes | Hỏi USER per candidate: pitfall / pattern / quirk / playbook / skip |
| **slug** | yes (per kept entry) | Đề xuất từ source_task slug, USER confirm |
| **missing fields** | per entry | Hỏi USER (severity / confidence / time wasted / ...) |

## Procedure

1. **Scan pending**:
   ```bash
   ls _brain/memory/_pending/*-suggested.md
   cat _brain/memory/README.md      # nắm 4 loại + quy tắc "1 task → 0-3 lessons"
   cat _brain/memory/INDEX.md       # tránh duplicate
   ```

2. **Per stub** — đọc `## Raw extracted content`:
   - §XV Escalation block → ứng viên **pitfall**
   - Notes / Out-of-scope / Caveats → ứng viên **pattern**
   - Orchestrator Verdict Notes → verified learning (pattern hoặc quirk)
   - Nếu cả 3 section `(none)` → **skip** (task không sinh lesson) → chỉ DELETE stub

3. **Decide per candidate** (cùng USER, đối chiếu checklist trong stub `## USER decisions`):
   - [ ] pitfall? → invoke `create-pitfall.md` logic (hoặc tự fill template README §II.2)
   - [ ] pattern? → invoke `create-pattern.md` logic (README §II.1)
   - [ ] agent-quirk? → append vào `agent-quirks/<owner>.md` (tăng `quirks_count`)
   - [ ] playbook? → invoke `create-playbook.md` (hiếm, chỉ khi e2e demonstrated)
   - [ ] skip → không tạo gì

4. **Finalize entries**: dùng Write/Edit tool tạo file theo đúng template (KHÔNG fabricate — thiếu field thì HỎI USER).

5. **Update INDEX** (`_brain/memory/INDEX.md`): add row cho mỗi entry mới vào đúng table (§I/§II/§III/§IV).

6. **DELETE stub** sau khi xử lý xong:
   ```bash
   rm "_brain/memory/_pending/<task-id>-suggested.md"
   ```
   → `_pending/` rỗng = harvest backlog clean.

7. **Promotion check**: nếu entry vừa tạo có `confidence ≥3` hoặc `hit_count ≥3` → suggest invoke `promote-to-canonical.md`.

8. **Report to USER**:
   ```
   ✓ Harvest review xong (<N> stubs):
     • created: <X> pitfalls, <Y> patterns, <Z> quirks
     • skipped: <W> (no reusable lesson)
     • deleted: <N> pending stubs
     INDEX updated
   → Promotion candidates: <list nếu có>
   ```

## Output

- **Files created**: memory entries trong `pitfalls/ patterns/ playbooks/ agent-quirks/`
- **Files updated**: `_brain/memory/INDEX.md`, optional `agent-quirks/<owner>.md`
- **Files deleted**: processed `_pending/*-suggested.md`

## Failure modes

| If | Then |
|---|---|
| Stub all-`(none)` | Skip — chỉ DELETE, report "no reusable lesson" |
| USER unsure classification | Default: pitfall nếu từ §XV blocked; pattern nếu từ verified notes |
| Candidate duplicates existing entry | Bump `hit_count`/`confidence` + add `source_tasks`, đừng tạo file mới |
| Missing field (severity/confidence) | HỎI USER, KHÔNG đoán |
| `_pending/` empty | Report "no pending — harvest backlog clean" |

## Example invocation

> **USER**: "review pending suggestions đi"
>
> **AI** invokes `_brain/skills/harvest-and-review.md`:
> 1. `ls _pending/` → 4 stubs (T-...-006/007/008/009)
> 2. T-007 all `(none)` → skip + delete
> 3. T-006 §XV có lỗi ds2api ban → pitfall `ds2api-account-ban-archived-repo.md`
> 4. T-008 verified note "cookies fix stream cut" → pattern
> 5. T-009 quirk qwen overthink → append `agent-quirks/qwen.md`
> 6. Updates INDEX, deletes 4 stubs
> 7. Reports: "✓ 1 pitfall + 1 pattern + 1 quirk; 1 skipped; 4 stubs cleared"
