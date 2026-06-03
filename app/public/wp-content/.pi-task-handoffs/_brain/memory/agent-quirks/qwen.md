---
type: agent-quirk
agent: qwen
quirks_count: 4
last_updated: 2026-05-30
---

# Qwen3.7-Max-Preview — Runtime quirks (observed in production tasks)

> Beyond `AGENTS/qwen.md` (canonical CV). Memory log for behaviors discovered in real session 2026-05-30 (Luna-Proxy tuning + agentic task testing).

---

## Q1. Overthink + 39 tool calls per response when xhigh + agentic

- **Observed in**: session-2026-05-30 (Antigravity godmother-friendship CSS upgrade)
- **Behavior**: Khi `reasoning_effort=xhigh` (thinking 64K) AND tools enabled → model **đẻ 30-40 `<ml_tool_call>` trong 1 response**. Output tokens vọt lên 39K → vượt client `max_tokens=32K` → cắt → tool call cuối hỏng → todos dở.
- **Workaround**: Auto-cap thinking 32K khi agentic detected (see `patterns/agentic-thinking-cap.md`)
- **Promoted to AGENTS/qwen.md?**: pending (waiting for hit_count ≥3 confirmation)

## Q2. Cookies needed beyond JWT for long responses

- **Observed in**: session-2026-05-30 multiple tasks
- **Behavior**: Token-only setup → responses cut at `status: typing` (no `finished`) on long output (>15K tokens). Log "No cookies provided. This may cause Bad_Request error".
- **Workaround**: POST full cookies (`cna; xlly_s; acw_tc; token; ssxmod_itna; ...`) to Luna `/api/provider/token` (see `patterns/qwen-luna-cookies-fix-stream-cut.md`)
- **Promoted to AGENTS/qwen.md?**: yes — added to §4 Weaknesses + §9 Known Quirks

## Q3. Forced thinking model — always reasons regardless of effort

- **Observed in**: all tasks via Luna-Proxy
- **Behavior**: Model là "exclusively thinking mode" (per Qwen3.7-Max-Preview spec). KHÔNG tắt thinking được. `enable_thinking: false` ignored.
- **Workaround**: Control DEPTH instead of on/off — set `thinking_budget` 2K (minimal) up to 65K (max). `effort=low` → 2K thinking still happens, just shallow.
- **Promoted to AGENTS/qwen.md?**: yes — already in §4 Weaknesses

## Q4. Output XML format quirks needing parser robustness

- **Observed in**: session-2026-05-30 Luna parser fixes
- **Behavior**: Qwen XML output có 3 issues:
  1. Quên close wrapper `</ml_tool_calls>` (single `<ml_tool_call>` không nested)
  2. Multi tool call cuối list bị cắt giữa → params rỗng
  3. Param value array/object → tự bịa nested XML thay vì JSON in CDATA → client reject "Expected array"
- **Workaround**: Luna parser 3-layer:
  1. Accept `<ml_tool_call>` standalone (không cần wrapper)
  2. Filter out tool calls với empty params
  3. Contract dạy model: array/object → JSON in CDATA, không nested XML
- **Promoted to AGENTS/qwen.md?**: pending (recent finding)

---

## Summary table

| Quirk | Severity | Workaround Status | Promote to AGENTS? |
|---|---|---|---|
| Q1 Overthink agentic | high | ✓ implemented Luna auto-cap | pending hit_count ≥3 |
| Q2 Cookies needed | medium | ✓ implemented setup workflow | ✓ promoted |
| Q3 Forced thinking | low (by design) | ✓ control depth via budget | ✓ promoted |
| Q4 XML parser edge cases | medium | ✓ implemented 3 fixes | pending |

> Khi `confidence ≥3` đạt được, promote workaround vào `AGENTS/qwen.md §9 Known Quirks` và add reference back here `promoted_to: AGENTS/qwen.md §9.X`.
