---
skill_id: create-playbook
type: write
invokable_by: orchestrator | user
target_files: [_brain/memory/playbooks/<slug>.md, _brain/memory/INDEX.md]
preconditions: [_brain/memory/ exists, e2e workflow validated end-to-end]
---

# Skill: Create Playbook — ghi e2e workflow đã validate

> Tạo `_brain/memory/playbooks/<slug>.md` cho workflow nhiều bước phức tạp đã chạy thành công. Mục tiêu: copy-paste-able recipe cho người khác (hoặc bạn 6 tháng sau).

## When to invoke

Trigger phrases:
- "workflow vừa rồi ghi playbook đi"
- "lưu playbook: <topic>"
- "ghi e2e setup này thành playbook"
- "save this as playbook"

Auto-detect conditions:
- Multi-step workflow (≥5 steps) vừa hoàn tất validated
- USER nói "lần sau làm lại cũng vậy" hoặc "share team cách này"

## Inputs needed

| Input | Required? | How to get |
|---|---|---|
| **title** | yes | E2e workflow name |
| **use_case** | yes | Khi nào dùng playbook này |
| **prerequisites** | yes | Cần có gì TRƯỚC (accounts, tools, ...) |
| **steps** | yes | List bước copy-paste với commands |
| **verification** | yes | Cách verify sau khi xong |
| **common_deviations** | optional | Lỗi hay gặp + fix |
| **maintenance** | optional | Schedule cập nhật (nếu có) |
| **duration_est** | yes | First time vs re-run |
| **source_tasks** | yes | Task IDs / session date validated |

## Procedure

1. **Read context**:
   - `_brain/memory/README.md` §II.4 (playbook format)
   - `_brain/memory/INDEX.md` §IV Playbooks (check duplicates)
   - Dossier hoặc session log nguồn

2. **Confirm scope** — playbook KHÔNG phải pattern + KHÔNG phải pitfall:
   - Pattern = "1 approach insight" (technical)
   - Playbook = "step-by-step recipe" (operational, ≥5 steps usually)
   - Pitfall = "fail mode + fix"

3. **Extract steps** from source:
   - Đọc evidence § của dossier (commands + outputs)
   - Đọc returns file §5 RAW evidence
   - Compile thành steps có thể copy-paste

4. **Fill template** (per `_brain/memory/README.md` §II.4):
   ```yaml
   ---
   type: playbook
   title: <title>
   duration_est: <first time> · <re-run>
   last_validated: <YYYY-MM-DD on T-XXX>
   ---

   # <title>

   ## Use case
   <use_case>

   ## Prerequisites
   - <prereq 1>
   - <prereq 2>

   ## Steps (copy-paste)

   ### Step 1 — <name>
   ```bash
   <commands>
   ```
   <explanation>

   ### Step 2 — <name>
   ```bash
   <commands>
   ```

   <...repeat for each step...>

   ## Verification
   ```bash
   <verify commands>
   # Expected: <output>
   ```

   ## Common deviations
   | Symptom | Cause | Fix |
   |---|---|---|

   ## Maintenance schedule
   - <recurring task>

   ## See also
   - <related patterns/pitfalls>
   ```

5. **Write file**: `_brain/memory/playbooks/<slug>.md`

6. **Update INDEX** (`_brain/memory/INDEX.md` §IV Playbooks table)

7. **Cross-link** related patterns/pitfalls:
   - Edit each related entry → add `## See also` reference to new playbook
   - Maintains bi-directional navigation

8. **Report to USER**:
   ```
   ✓ Playbook created:
     _brain/memory/playbooks/<slug>.md
     INDEX updated (§IV)
     Cross-links added to <N> related entries
   → Review: cat _brain/memory/playbooks/<slug>.md
   ```

## Output

- **File created**: `_brain/memory/playbooks/<slug>.md`
- **Files updated**: `_brain/memory/INDEX.md`, related memory entries (bi-directional links)

## Failure modes

| If | Then |
|---|---|
| Workflow <5 steps | Suggest using `create-pattern` instead (playbook overkill) |
| Steps not validated (haven't run all) | Refuse — playbook PHẢI là validated e2e |
| Commands have secrets/tokens | Ask USER redact before writing (memory is git-versioned) |

## Example invocation

> **USER**: "ghi playbook: luna-proxy-full-tuning — session vừa làm xong"
>
> **AI** invokes:
>
> 1. Reads README §II.4 + INDEX
> 2. Confirms scope (7+ steps Luna setup = playbook material)
> 3. Reads session evidence (curl commands, config updates, smoke tests)
> 4. Asks USER:
>    - "duration_est first-time? (vd 3h)"
>    - "Re-run with cached cookies? (vd 30m)"
> 5. Fills template, writes `_brain/memory/playbooks/luna-proxy-full-tuning.md`
> 6. Updates INDEX
> 7. Cross-links: adds "See also: playbooks/luna-proxy-full-tuning.md" to all 7 related pitfalls + 3 patterns
> 8. Reports: "✓ Playbook created (7 steps), cross-linked 10 entries"
