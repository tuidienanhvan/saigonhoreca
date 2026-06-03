---
skill_id: create-pitfall
type: write
invokable_by: orchestrator | user
target_files: [_brain/memory/pitfalls/<slug>.md, _brain/memory/INDEX.md]
preconditions: [_brain/memory/ directory exists, source task identified]
---

# Skill: Create Pitfall — ghi root cause của task fail + cách tránh

> Tạo file `_brain/memory/pitfalls/<slug>.md` từ task vừa fail/blocked. Mục tiêu: lần sau gặp symptom tương tự → AI/USER thấy ngay root cause + fix.

## When to invoke

Trigger phrases:
- "task vừa fail vì X, ghi pitfall đi"
- "lưu lại pitfall: <topic>"
- "đây là pitfall hay gặp, ghi vào memory"
- "save this pitfall" / "log this gotcha"

Auto-detect conditions:
- Task vừa transitioned to `blocked` với raw error trong §XV Escalation
- USER mô tả 1 lỗi đã/đang gặp với mức độ cần nhớ về sau

## Inputs needed

| Input | Required? | How to get |
|---|---|---|
| **title** | yes | 1 dòng tóm tắt pitfall |
| **symptom** | yes | Cách nhận ra (error message / behavior / log line) |
| **root_cause** | yes | Tại sao xảy ra (technical) |
| **fix** | yes | Lệnh chính xác để khắc phục |
| **severity** | yes | critical / high / medium / low |
| **domain** | yes | Free tag (vd "wordpress · cache · theme") |
| **source_task** | yes | Task ID nơi gặp lần đầu (hoặc "session-YYYY-MM-DD" nếu workshop) |
| **detection_signature** | optional | Grep/log line giúp nhận ra sớm |
| **prevention** | optional | Cách tránh hoàn toàn lần sau |

## Procedure

1. **Read context**:
   ```bash
   cat _brain/memory/README.md          # nắm quy tắc ghi pitfall (§II.2)
   cat _brain/memory/INDEX.md            # xem pitfall đã có, tránh duplicate
   ls _brain/memory/pitfalls/            # check naming conflicts
   ```

2. **Confirm slug** (kebab-case, descriptive):
   - Ask USER: "Slug pitfall? (vd `static-cache-stale-on-redesign`)"
   - Verify `_brain/memory/pitfalls/<slug>.md` KHÔNG tồn tại → nếu có: ask "append hit_count hay overwrite?"

3. **Gather inputs**:
   - Đọc dossier `active/<TASK_ID>-*.md` hoặc `archive/*/T-<TASK_ID>-*.md` §XV Escalation → extract raw error
   - Đọc `returns/<TASK_ID>-return.md` §6 Residual Risks nếu có
   - Hỏi USER những field còn thiếu

4. **Fill template** (per `_brain/memory/README.md` §II.2):
   ```yaml
   ---
   type: pitfall
   title: <title>
   severity: <severity>
   domain: <domain>
   source_tasks: [<source_task>]
   hit_count: 1
   detection_signature: |
     <detection_signature or symptom-grep>
   last_updated: <today YYYY-MM-DD>
   ---

   # <title>

   ## Symptom (cách nhận ra)
   <symptom paragraph + code block với error/log>

   ## Root cause
   <root_cause paragraph — technical>

   ## Fix (lệnh chính xác)
   ```bash
   <exact commands>
   ```

   ## Prevention
   1. <prevention step 1>
   2. <...>

   ## First hit
   - **Task**: <source_task>
   - **Date**: <today>
   - **Time wasted**: ~<N>m (USER estimate)

   ## See also
   - <related patterns/pitfalls if any>
   ```

5. **Write file**: `_brain/memory/pitfalls/<slug>.md` (use Write tool)

6. **Update INDEX** (`_brain/memory/INDEX.md` §II Pitfalls table):
   ```markdown
   | [<slug>](pitfalls/<slug>.md) | <severity> | <source_task> | 1 |
   ```

7. **Append to source dossier** (if exists in active/):
   - Add to `## XVI. CHANGE LOG`:
     `- **<today>**: Pitfall logged → _brain/memory/pitfalls/<slug>.md`

8. **Report to USER**:
   ```
   ✓ Pitfall created:
     _brain/memory/pitfalls/<slug>.md
     INDEX updated (§II Pitfalls)
     Source dossier change-log updated
   → Review: cat _brain/memory/pitfalls/<slug>.md
   ```

## Output

- **File created**: `_brain/memory/pitfalls/<slug>.md`
- **Files updated**: `_brain/memory/INDEX.md`, optionally source dossier `## XVI`
- **USER message**: confirmation with cat command

## Failure modes

| If | Then |
|---|---|
| slug already exists | Ask USER: bump `hit_count` (append "Subsequent hits") vs new slug |
| symptom/root_cause/fix unclear | Ask USER specific questions; KHÔNG đoán |
| source_task not found | Ask USER for task ID or accept "session-YYYY-MM-DD" |
| _brain/memory/ not exist | Refuse — guide USER to run `mkdir -p _brain/memory/pitfalls _brain/memory/patterns _brain/memory/agent-quirks _brain/memory/playbooks` first |

## Example invocation

> **USER**: "task Casa Maria vừa rồi bị stale cache, ghi pitfall đi"
>
> **AI** (orchestrator) invokes `_brain/skills/create-pitfall.md`:
>
> 1. Reads `_brain/memory/README.md` §II.2 + `_brain/memory/INDEX.md`
> 2. Reads `archive/2026-05/T-Casa-Maria-*.md` §XV Escalation
> 3. Asks USER:
>    - "Slug? Suggest: `static-cache-stale-on-redesign`. OK?"
>    - "Severity: high (đoán từ context)?"
>    - "Time wasted estimate? (vd 45m)"
> 4. USER confirms answers
> 5. AI fills template + writes `_brain/memory/pitfalls/static-cache-stale-on-redesign.md`
> 6. AI updates `_brain/memory/INDEX.md` (adds row to Pitfalls table)
> 7. AI updates `archive/2026-05/T-Casa-Maria-*.md` `## XVI` (logs pitfall ref)
> 8. AI reports: "✓ Pitfall created. Review: cat _brain/memory/pitfalls/static-cache-stale-on-redesign.md"

→ Result: pitfall persisted, future tasks invoke `reference-memory` skill sẽ tự surface entry này.
