---
skill_id: promote-to-canonical
type: promote
invokable_by: orchestrator | user
target_files: [SKILL.md, AGENTS/<agent>.md, QUALITY-GATES.md, _brain/memory/<entry> (back-link), _brain/memory/INDEX.md]
preconditions: [memory entry exists with confidence ≥3 OR hit_count ≥3, canonical doc target identified]
reconstructed: 2026-06-03
---

<!-- Nội dung dựng lại sau sự cố mất file — verify lại. -->

# Skill: Promote to Canonical — nâng lesson chín lên docs chính thức

> Khi 1 memory entry đã chín (`confidence ≥3` cho pattern, `hit_count ≥3` cho pitfall, hoặc quirk đã confirm nhiều lần) → promote nội dung cốt lõi vào **canonical docs** (`SKILL.md`, `AGENTS/<agent>.md`, `QUALITY-GATES.md`) để mọi agent đọc mặc định, không phải tra memory. Memory entry giữ lại làm audit trail + back-link `promoted_to:`.

## When to invoke

Trigger phrases:
- "pattern này chín rồi, promote lên docs"
- "promote to canonical"
- "đưa lesson này vào SKILL/AGENTS"
- "lesson lặp ≥3 lần, lên rule chính thức"

Auto-detect conditions:
- `create-pattern.md` vừa bump `confidence` lên ≥3
- `create-pitfall.md` vừa bump `hit_count` lên ≥3
- `agent-quirks/<agent>.md` có quirk `Promoted to AGENTS?: pending` + đã confirm ≥3 task

## Inputs needed

| Input | Required? | How to get |
|---|---|---|
| **source entry** | yes | Path `_brain/memory/<type>/<slug>.md` |
| **maturity proof** | yes | confidence/hit_count field + `source_tasks` list |
| **target doc** | yes | SKILL.md (workflow rule) · AGENTS/<agent>.md (agent quirk) · QUALITY-GATES.md (gate) |
| **target section** | yes | §number trong target doc |
| **distilled rule** | yes | 1-3 dòng rule ngắn gọn (canonical ≠ copy nguyên entry) |

## Procedure

1. **Read context**:
   ```bash
   cat _brain/memory/<type>/<slug>.md     # source lesson
   cat _brain/memory/README.md            # §III.5 promote rule (confidence ≥3)
   cat SKILL.md                           # hoặc AGENTS/<agent>.md target
   ```

2. **Verify maturity** (gatekeeper — KHÔNG promote non non):
   - pattern: `confidence ≥3`
   - pitfall: `hit_count ≥3`
   - quirk: observed in ≥3 distinct `source_tasks`
   - Nếu chưa đủ → REFUSE, report "chưa chín — cần thêm hit"

3. **Distill** entry thành rule canonical:
   - Canonical = **ngắn, mệnh lệnh, không kèm raw evidence**
   - Memory = chi tiết + audit; docs = "PHẢI làm X / TRÁNH Y"
   - Vd: pitfall `static-cache-stale-on-redesign` → SKILL.md §IV add gate "Sau redesign theme: PHẢI purge static cache + verify hash đổi trước khi set-state verified"

4. **Choose target**:
   | Loại lesson | Target doc | Section |
   |---|---|---|
   | Workflow / verification rule | `SKILL.md` | §III 6-step hoặc §IV Quality Gates |
   | Agent runtime behavior | `AGENTS/<agent>.md` | §9 Known Quirks / §4 Weaknesses |
   | Hard quality gate | `QUALITY-GATES.md` | relevant gate table |

5. **Edit target doc** (use Edit tool) — chèn distilled rule vào đúng section, giữ format hiện có.

6. **Back-link memory entry** (Edit) — thêm vào frontmatter:
   ```yaml
   promoted_to: SKILL.md §IV   # (hoặc AGENTS/qwen.md §9.1)
   promoted_date: <today>
   ```
   Và trong `agent-quirks` summary table đổi cột "Promote?" → `✓ promoted`.

7. **Update INDEX** (`_brain/memory/INDEX.md`): mark entry `→ promoted` trong cột ghi chú / move xuống §V Promotion candidates → DONE.

8. **Report to USER**:
   ```
   ✓ Promoted: _brain/memory/<type>/<slug>.md
     → <target doc> §<n> (distilled rule added)
     Back-link: promoted_to set in entry frontmatter
     INDEX updated
   → Mọi agent giờ đọc rule này mặc định, không cần tra memory.
   ```

## Output

- **Files updated**: target canonical doc (SKILL.md / AGENTS/<agent>.md / QUALITY-GATES.md), source memory entry (back-link), `_brain/memory/INDEX.md`
- **No file deleted** — memory entry GIỮ LẠI làm audit trail

## Failure modes

| If | Then |
|---|---|
| confidence/hit_count <3 | REFUSE — "chưa chín, cần thêm proof" |
| Distilled rule quá dài / kèm raw log | Rút gọn — canonical là mệnh lệnh ngắn |
| Target section không tồn tại | HỎI USER section đúng |
| Rule mâu thuẫn rule có sẵn trong docs | STOP — escalate USER reconcile trước |
| Entry đã `promoted_to` rồi | Report "đã promote, skip" |

## Example invocation

> **USER**: "pitfall qwen-xhigh-overthink-stun lặp 3 lần rồi, promote đi"
>
> **AI** invokes `_brain/skills/promote-to-canonical.md`:
> 1. Reads entry → `hit_count: 3` ✓ mature
> 2. Distill: "Qwen agentic: PHẢI cap thinking 32K khi detect tools (tránh overthink stun)"
> 3. Target: `AGENTS/qwen.md §9 Known Quirks`
> 4. Edits AGENTS/qwen.md + sets `promoted_to: AGENTS/qwen.md §9` in entry
> 5. INDEX: mark promoted
> 6. Reports: "✓ Promoted → AGENTS/qwen.md §9. Mọi qwen dispatch giờ đọc rule mặc định."
