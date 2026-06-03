---
skill_id: reference-memory
type: read
invokable_by: orchestrator | new-task.sh hook
target_files: [active/<TASK_ID>-*.md (§III.M)]
preconditions: [task dossier exists, _brain/memory/ populated]
---

# Skill: Reference Memory — surface relevant lessons trong task mới

> Quét `_brain/memory/` cho lessons liên quan task vừa tạo → fill §III.M Memory References của dossier. Mục tiêu: worker đọc dossier sẽ thấy NGAY pattern/pitfall liên quan, KHÔNG bỏ sót.

## When to invoke

Trigger conditions:
- `new-task.sh` vừa tạo dossier mới → AUTO invoke skill này
- USER nói "tìm memory liên quan task này"
- Trước khi `set-state dispatched` → ensure §III.M filled

Trigger phrases:
- "tìm memory cho task <ID>"
- "fill memory references"
- "có lesson cũ nào liên quan không?"

## Inputs needed

| Input | Required? | How to get |
|---|---|---|
| **task_id** | yes | From new-task.sh output or USER |
| **slug** | yes | From dossier filename |
| **owner** | yes | From dossier frontmatter |
| **domain hints** | optional | From dossier §II Goal |

## Procedure

1. **Read dossier**:
   ```bash
   cat active/<TASK_ID>-*.md
   ```
   Extract: slug, owner, risk, §II Goal, §IV Allowed Scope keywords

2. **Extract significant keywords** from slug + Goal:
   - Slug: split by `-`, skip noise (`fix`, `update`, `refactor`, `add`, `the`)
   - Goal: NER-style — file names, tools, domains (e.g., `cache`, `tailwind`, `qwen`)

3. **Search _brain/memory/** for each keyword:
   ```bash
   for kw in $KEYWORDS; do
       grep -rln "$kw" _brain/memory/ --include="*.md" | grep -vE "_pending|README|INDEX"
   done | sort -u
   ```

4. **Score relevance** (simple heuristic):
   - File matches ≥2 keywords → high relevance
   - File matches 1 keyword in title/frontmatter → medium
   - File matches 1 keyword in body → low (mention but don't push)

5. **Per-category curation**:
   - `_brain/memory/pitfalls/` matching → list at top (avoid known failures)
   - `_brain/memory/patterns/` matching → list 2nd (apply known successes)
   - `_brain/memory/agent-quirks/<owner>.md` → ALWAYS include if owner agent has quirks file
   - `_brain/memory/playbooks/` matching → list if domain match

6. **Update dossier §III.M Memory References**:
   - Use Edit tool to replace placeholder `(none)` lines
   - Format:
     ```markdown
     **Patterns** (approaches that worked for similar tasks):
     - [`_brain/memory/patterns/qwen-luna-cookies-fix-stream-cut.md`](_brain/memory/patterns/qwen-luna-cookies-fix-stream-cut.md) — keyword match: qwen, luna

     **Pitfalls** (mistakes to avoid, with detection signatures):
     - [`_brain/memory/pitfalls/static-cache-stale-on-redesign.md`](_brain/memory/pitfalls/static-cache-stale-on-redesign.md) — keyword match: cache, theme
     - [`_brain/memory/pitfalls/qwen-xhigh-overthink-stun.md`](_brain/memory/pitfalls/qwen-xhigh-overthink-stun.md) — keyword match: qwen

     **Agent quirks**:
     - [`_brain/memory/agent-quirks/qwen.md`](_brain/memory/agent-quirks/qwen.md) — runtime behaviors for owner=qwen
     ```

7. **Report to USER**:
   ```
   ✓ Memory references added to <TASK_ID> §III.M:
     • 2 pitfalls (cache, qwen)
     • 1 pattern (qwen+luna cookies)
     • 1 agent-quirk profile (qwen)
   → Worker đọc dossier sẽ thấy ngay lesson cũ.
   ```

## Output

- **File updated**: `active/<TASK_ID>-*.md` §III.M
- **No new files created** (read-only on memory, write only to dossier)

## Failure modes

| If | Then |
|---|---|
| No relevant entries found | Leave `(none)` placeholder; report "0 matches — task is novel, will likely generate new memory" |
| Slug too generic (no keywords) | Ask USER for domain hint |
| Dossier §III.M already filled | Ask USER: append vs overwrite |
| _brain/memory/ empty | Report "Memory empty (new system) — invoke create-* skills as you go" |

## Example invocation

> **USER**: tạo task mới `T-20260601-001-qwen-refactor-casa-maria-pillar-cache-fix`
>
> `new-task.sh` AUTO-invokes `_brain/skills/reference-memory.md`:
>
> 1. Reads dossier — slug: `refactor-casa-maria-pillar-cache-fix`, owner: qwen
> 2. Extracts keywords: `casa-maria`, `pillar`, `cache`, `qwen`
> 3. Greps _brain/memory/:
>    - `cache` → `pitfalls/static-cache-stale-on-redesign.md` ✓
>    - `qwen` → `pitfalls/qwen-xhigh-overthink-stun.md` ✓, `patterns/qwen-luna-cookies-fix-stream-cut.md` ✓, `patterns/agentic-thinking-cap.md` ✓
>    - owner=qwen → `agent-quirks/qwen.md` ✓
> 4. Scores + filters top 5 most relevant
> 5. Edits dossier §III.M with formatted list
> 6. Reports: "✓ 3 pitfalls + 1 pattern + qwen quirks → §III.M"

→ Worker receiving prompt sẽ đọc §III.M trước khi code → biết tránh stale cache trap + thinking cap đã có.
