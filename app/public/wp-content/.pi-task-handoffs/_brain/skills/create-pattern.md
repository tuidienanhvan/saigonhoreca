---
skill_id: create-pattern
type: write
invokable_by: orchestrator | user
target_files: [_brain/memory/patterns/<slug>.md, _brain/memory/INDEX.md]
preconditions: [_brain/memory/ directory exists, source task or workshop session identified]
---

# Skill: Create Pattern — ghi approach thắng cho tái sử dụng

> Tạo file `_brain/memory/patterns/<slug>.md` từ approach mới/sáng tạo vừa work. Mục tiêu: lần sau gặp problem tương tự → AI/USER apply pattern ngay.

## When to invoke

Trigger phrases:
- "approach này hay, lưu pattern"
- "lưu pattern: <name>"
- "save this pattern" / "this approach worked, log it"
- "tạo pattern memory"

Auto-detect conditions:
- Task verified với approach **NEW** (chưa có pattern tương tự trong _brain/memory/)
- USER nhấn mạnh "cách này work tốt"

## Inputs needed

| Input | Required? | How to get |
|---|---|---|
| **title** | yes | 1 dòng — approach name |
| **problem** | yes | Bối cảnh: pattern này giải quyết gì |
| **approach_steps** | yes | List các bước cụ thể |
| **why_it_works** | yes | Insight kỹ thuật |
| **when_to_use** | yes | List use cases |
| **when_not_to_use** | optional | Anti-cases |
| **variations_tried** | optional | Cách khác đã thử → fail vì sao |
| **domain** | yes | Free tag |
| **source_tasks** | yes | List task IDs nơi pattern proved |
| **confidence** | yes | 1 (thử 1 lần) / 2 / 3 (verified ≥3 lần) / 5 (standard) |

## Procedure

1. **Read context**:
   ```bash
   cat _brain/memory/README.md          # quy tắc ghi pattern (§II.1)
   cat _brain/memory/INDEX.md            # check duplicate
   ls _brain/memory/patterns/            # naming conflicts
   ```

2. **Verify novelty**:
   - Grep `_brain/memory/patterns/` for similar keywords
   - Nếu trùng: ask USER append `source_tasks` hay tạo entry riêng (variation)

3. **Gather inputs**:
   - Đọc dossier §VI Phases of Execution + §VII Verification (cách approach implemented)
   - Đọc `returns/<TASK_ID>-return.md` §1 Summary + §6 Caveats
   - Hỏi USER cho `why_it_works`, `when_to_use`, `confidence`

4. **Fill template** (per `_brain/memory/README.md` §II.1):
   ```yaml
   ---
   type: pattern
   title: <title>
   domain: <domain>
   source_tasks: <source_tasks>
   confidence: <confidence>
   last_updated: <today>
   ---

   # <title>

   ## Problem
   <problem paragraph>

   ## Approach (steps)
   1. <step 1>
   2. <step 2>

   ## Why it works
   <insight>

   ## When to use
   - <case 1>
   - <case 2>

   ## When NOT to use
   - <anti-case>

   ## Variations tried
   - <variation A> → fail vì <reason>
   - <variation B> (this) → pass

   ## See also
   - <related patterns/pitfalls/playbooks>
   ```

5. **Write file**: `_brain/memory/patterns/<slug>.md`

6. **Update INDEX** (`_brain/memory/INDEX.md` §I Patterns table):
   ```markdown
   | [<slug>](patterns/<slug>.md) | <domain> | <source_tasks> | <confidence> |
   ```

7. **If confidence becomes ≥3** (pattern proved multiple times):
   - Suggest invoking `_brain/skills/promote-to-canonical.md` next

8. **Report to USER**:
   ```
   ✓ Pattern created:
     _brain/memory/patterns/<slug>.md
     INDEX updated (§I Patterns, confidence=<n>)
   → Review: cat _brain/memory/patterns/<slug>.md
   → If confidence ≥3 next time: invoke _brain/skills/promote-to-canonical.md
   ```

## Output

- **File created**: `_brain/memory/patterns/<slug>.md`
- **Files updated**: `_brain/memory/INDEX.md`

## Failure modes

| If | Then |
|---|---|
| slug exists, same domain | Ask: bump `confidence` + add source_task, OR new variation entry |
| approach genuinely trivial (vd "run npm install") | Refuse — patterns nên là insight, không phải common knowledge |
| insufficient inputs | Ask USER cụ thể, KHÔNG fabricate |

## Example invocation

> **USER**: "lưu pattern: agentic-thinking-cap — khi detect tools thì cap thinking 32K"
>
> **AI** invokes `_brain/skills/create-pattern.md`:
>
> 1. Reads README §II.1 + INDEX
> 2. Greps existing patterns: no conflict
> 3. Reads dossier (or session log): finds approach context
> 4. Asks USER:
>    - "Confidence? (1=tried once, 3=verified ≥3 times)"
>    - "When NOT to use? (anti-cases)"
> 5. Fills template, writes `_brain/memory/patterns/agentic-thinking-cap.md`
> 6. Updates INDEX (§I Patterns table)
> 7. Reports: "✓ Pattern created. Confidence=3 → next time pattern hits, invoke _brain/skills/promote-to-canonical.md"
