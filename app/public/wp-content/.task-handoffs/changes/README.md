# 📁 Changes Archive — High-Risk Decision Persistence

## I. 🎯 Purpose

This folder stores **detailed decision records ONLY for high-risk or architecturally-significant tasks** — those that may need rollback or future justification.

> **Scope tightened (2026-05-08)**: Originally required for every verified task. Reality showed adoption ~2% (1 entry / 59 archived tasks). Realistic scope below.

## II. 📌 When to Create a `changes/T-{id}/` Entry

✅ **REQUIRED** for tasks with:
- `risk: high` OR `risk: critical` in dossier frontmatter
- Tasks that modify shared schemas, public APIs, database structure
- Tasks with architectural impact (new patterns, framework migrations)
- Tasks where rollback would be non-trivial (>1 file revert)

❌ **NOT required** for tasks with:
- `risk: low` / `risk: medium` / `risk: cosmetic`
- Single-file refactors, lint fixes, typo corrections
- UI polish, copy changes, content updates
- Audit-only tasks (no code change)

For low/medium risk tasks: dossier in `archive/YYYY-MM/` + LEADERBOARD entry are sufficient.

## III. 📂 Folder Structure (when applicable)

```text
.task-handoffs/changes/
├── README.md                    ← This file
└── T-{id}/                      ← One folder per high-risk task
    (templates → system/templates/DECISION_TEMPLATE.md + CHANGES-FORMAT.md)
    ├── decision.md              ← Why this approach? Alternatives considered?
    ├── diff.patch               ← Unified diff (output of git diff)
    ├── rollback-plan.md         ← Exact revert steps + validation
    ├── before/                  ← Optional: snapshots of changed files
    └── after/                   ← Optional: snapshots after changes
```

## IV. 📌 Required Fields in `decision.md`

- **Task ID** + Date + Owner
- **Why** (problem statement)
- **Alternatives considered** (≥2 options + trade-offs)
- **Decision** (chosen approach + reasoning)
- **Trade-offs accepted**
- **Rollback trigger** (when would we revert?)

## V. 🔌 Workflow Integration

| Phase | Action |
|-------|--------|
| Phase A (Genesis) | Orchestrator marks `risk: high\|critical` in frontmatter if applicable |
| Phase C (Quality Gate) | Reviewer creates `changes/T-{id}/` if risk warrants |
| Phase D (Archive) | If `risk: high\|critical` AND no `changes/T-{id}/` exists → BLOCK archive |

## VI. 📌 Lint Check

`system/scripts/lint-handoffs.sh` validates: every archived task with `risk: high\|critical` has a corresponding `changes/T-{id}/` folder.

## VII. 📌 Examples

- ✅ `T-20260503-031-gemini-css-architecture-review/` — full entry: CSS architecture review (architectural impact)
- ❌ Most refactors / lint fixes → no entry needed

## VIII. 📌 Rationale

Documentation cost ≈ 30 min per entry (decision rationale + alternatives + rollback plan). Apply to **architecturally-significant** tasks only. For routine work, `archive/YYYY-MM/T-*.md` already captures sufficient context.
