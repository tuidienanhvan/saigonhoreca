---
skill_id: <kebab-case-id>
type: write | read | process | promote
invokable_by: orchestrator | user | <script>.sh hook
target_files: [<file paths skill creates/edits>]
preconditions: [<must be true before invoke>]
reconstructed: 2026-06-03
---

<!-- Nội dung dựng lại sau sự cố mất file — verify lại. Blank skill scaffold; copy this file để tạo skill mới. -->

# Skill: <Name> — <one-line purpose>

> <2-3 dòng mô tả: skill này làm gì, output ra đâu, mục tiêu giúp gì cho task sau.>

## When to invoke

Trigger phrases:
- "<phrase 1>"
- "<phrase 2>"

Auto-detect conditions:
- <condition 1>
- <condition 2>

## Inputs needed

| Input | Required? | How to get |
|---|---|---|
| **<input>** | yes/optional | <source> |

## Procedure

1. **Read context**:
   ```bash
   cat _brain/memory/README.md
   cat _brain/memory/INDEX.md
   ```

2. **<step>**:
   - <detail>

3. **Fill template / do work**:
   ```yaml
   <frontmatter + sections if writing a file>
   ```

4. **Write/Edit file**: `<path>` (use Write/Edit tool — KHÔNG dùng scripted regex)

5. **Update INDEX** (`_brain/memory/INDEX.md` §<section>): add/update row

6. **Report to USER**:
   ```
   ✓ <what was done>
   → Review: cat <path>
   ```

## Output

- **File created/updated**: `<path>`
- **USER message**: confirmation

## Failure modes

| If | Then |
|---|---|
| <bad input> | <ask USER / refuse — KHÔNG fabricate> |

## Example invocation

> **USER**: "<example request>"
>
> **AI** invokes `_brain/skills/<id>.md`:
> 1. <step>
> 2. <step>
> → Result: <outcome>
