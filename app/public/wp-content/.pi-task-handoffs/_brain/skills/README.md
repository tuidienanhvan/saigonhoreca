# Skills — Layer 4: invokable memory workflows (v1.2 NEW)

> **Reconstructed: 2026-06-03** — Nội dung dựng lại sau sự cố mất file — verify lại.

`_brain/skills/` là **Layer 4** của Pi Task Handoffs memory architecture. Trong khi `_brain/memory/` (Layer 2) chứa *nội dung* học được, `_brain/skills/` chứa *quy trình* để AI orchestrator **tạo, surface, promote** memory entries một cách có kỷ luật.

AI KHÔNG đọc memory thụ động — nó **invoke skill** khi user nói trigger phrase, hoặc khi script (new-task.sh / archive-task.sh) auto-call. Mỗi skill = 1 file Markdown có YAML frontmatter + procedure step-by-step + failure modes + example.

---

## I. Skill catalog (6 invokable)

| Skill | Type | Invoked by | Mục đích |
|---|---|---|---|
| [`create-pitfall.md`](create-pitfall.md) | write | orchestrator · user | Ghi root cause của task fail + cách tránh → `memory/pitfalls/<slug>.md` |
| [`create-pattern.md`](create-pattern.md) | write | orchestrator · user | Ghi approach thắng cho tái sử dụng → `memory/patterns/<slug>.md` |
| [`create-playbook.md`](create-playbook.md) | write | orchestrator · user | Ghi e2e workflow đã validate → `memory/playbooks/<slug>.md` |
| [`reference-memory.md`](reference-memory.md) | read | orchestrator · new-task.sh hook | Surface lessons liên quan vào dossier §III.M |
| [`harvest-and-review.md`](harvest-and-review.md) | process | orchestrator · archive-task.sh hook | Xử lý `_pending/` suggestions → finalize memory entries |
| [`promote-to-canonical.md`](promote-to-canonical.md) | promote | orchestrator · user | Nâng lesson chín (confidence/hit_count ≥3) lên SKILL.md / AGENTS docs |

---

## II. Trigger phrases (quick reference)

| Bạn nói... | AI invokes |
|---|---|
| "task vừa fail vì X, ghi pitfall đi" / "save this gotcha" | `create-pitfall.md` |
| "approach này hay, lưu pattern" / "this worked, log it" | `create-pattern.md` |
| "ghi e2e setup này thành playbook" / "save as playbook" | `create-playbook.md` |
| "tìm memory liên quan task này" / "fill memory references" | `reference-memory.md` |
| "review pending suggestions" / "harvest archive vừa rồi" | `harvest-and-review.md` |
| "pattern này chín rồi, promote lên docs" / "promote to canonical" | `promote-to-canonical.md` |

---

## III. Lifecycle hooks (auto-invoke)

```
new-task.sh    → AUTO invoke reference-memory.md      (fill §III.M of new dossier)
archive-task.sh Step 8.5 → harvest-lessons.sh writes _pending/ stubs
                         → suggest invoke harvest-and-review.md (USER review)
create-pattern/pitfall → if confidence/hit_count ≥3 → suggest promote-to-canonical.md
```

---

## IV. Cách viết skill mới

Copy [`_template.md`](_template.md) → điền frontmatter (`skill_id`, `type`, `invokable_by`, `target_files`, `preconditions`) + các section: When to invoke / Inputs needed / Procedure / Output / Failure modes / Example invocation.

> Quy ước: skill PHẢI deterministic (step-by-step), KHÔNG fabricate input — thiếu field thì HỎI user. Mọi skill ghi file đều update `memory/INDEX.md` ở cuối.

---

## V. Roadmap

- **v1.2 (NOW)**: 6 skills trên — DONE
- **v1.3**: `create-skill.md` — AI tự sinh skill mới (self-extending)
- **v1.4**: chained skills (vd `review-and-promote-all-pending`)

> Xem thêm: [`../memory/README.md`](../memory/README.md) §I (3-layer architecture).
