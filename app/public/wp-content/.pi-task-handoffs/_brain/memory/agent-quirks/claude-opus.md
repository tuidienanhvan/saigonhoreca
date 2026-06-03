---
type: agent-quirk
agent: claude
quirks_count: 1
last_updated: 2026-05-30
---

# Claude Opus 4.8 — Runtime quirks (observed in production tasks)

> Beyond `AGENTS/claude.md` (canonical CV).

---

## Q1. Tendency to spawn agents at low-effort settings when task is large

- **Observed in**: session-2026-05-30 audit task (read 10K lines docs + propose fixes)
- **Behavior**: Default reasoning effort thấp → Claude pick "Plan agent" hoặc "Explore agent" để delegate thay vì tự đọc full. Tốt cho parallelism nhưng đôi khi miss context.
- **Workaround**: Khi user ask cho "đọc full + đánh giá kỹ" → user hint `+500k` token budget hoặc `ultracode` mode → Claude tự đọc thay vì delegate.
- **Promoted to AGENTS/claude.md?**: no (this is Claude Code default behavior, not Claude model quirk)

---

## Summary table

| Quirk | Severity | Workaround Status | Promote? |
|---|---|---|---|
| Q1 Delegate-vs-self for large reads | low | user hint via budget | no — Claude Code feature |
