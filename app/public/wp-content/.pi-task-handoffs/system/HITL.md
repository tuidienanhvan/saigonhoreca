# Human-in-the-Loop (HITL) Checkpoints — v1.5

> **1 nhà cho CƠ CHẾ HITL** (trigger · dossier fields · flow · default-safe). Tách từ `LIFECYCLE.md §IX` (v1.5) để gom 1 chỗ.
> Liên hệ: **state** `awaiting_approval` ở [`LIFECYCLE.md`](LIFECYCLE.md) (state machine) · **HITL Gate** (kiểm đã resolved trước `verified`) ở [`QUALITY-GATES.md`](QUALITY-GATES.md) · operating rules [`../SKILL.md`](../SKILL.md).
> Inspired by [Microsoft agent-framework HITL](https://learn.microsoft.com/en-us/agent-framework/workflows/human-in-the-loop) + [LangChain HITL middleware](https://docs.langchain.com/oss/python/langchain/human-in-the-loop).

---

## I. Khi nào tạo HITL checkpoint?

Worker pause execution + ghi `state: awaiting_approval` khi:
- **Confidence threshold**: action confidence < `confidence_threshold` field.
- **Risk threshold**: action chạm `hitl_triggers` list (vd `[delete_files, schema_change, public_api_break]`).
- **Risk level**: dossier `risk: critical` AND task chưa được explicit user approve.
- **Explicit pause**: Orchestrator viết `pause_after_phase: 3` trong dossier.

## II. Dossier YAML fields

```yaml
requires_user_approval: false  # default false; true = mandatory HITL before dispatched→returned
confidence_threshold: 0.8      # 0-1, default 0.8; below = pause
hitl_triggers:                 # list of action keywords forcing pause
  - delete_files
  - schema_change
  - public_api_break
pause_after_phase: null        # 1|2|3|4|null — pause after specific phase
```

## III. Flow

```
Worker @ Phase B
  ↓
Detects: confidence < 0.8 OR action ∈ hitl_triggers
  ↓
set-state.sh <ID> awaiting_approval --reason "..."
  ↓
Worker pauses, writes "## XVII. HITL Checkpoint" với context cho user
  ↓
User decides:
  ├─ approve → set-state.sh <ID> dispatched (resume)
  ├─ replan  → set-state.sh <ID> replanned (rescope)
  └─ cancel  → set-state.sh <ID> cancelled
```

## IV. Default-safe (khi chưa hỏi được user)
- **KHÔNG** tự thực thi action chạm `hitl_triggers` / **KHÔNG** `published` khi `requires_user_approval` chưa resolve.
- Ghi đầy đủ context + đề xuất vào dossier `## XVII. HITL Checkpoint Log` (ai quyết · timestamp · decision).
- Còn `awaiting_approval` chưa quyết = **chặn `verified`** (HITL Gate, `QUALITY-GATES.md`).

## Changelog
- **v1.5** — Tách cơ chế HITL từ `LIFECYCLE.md §IX` sang file riêng (1 nhà). Nội dung giữ nguyên (no behavior change); LIFECYCLE §IX + QUALITY-GATES HITL-Gate trỏ về đây.
