---
type: pitfall
title: Qwen at xhigh reasoning over-thinks and stuns (39 tool calls overflow client cap)
severity: high
domain: qwen · llm-proxy · agentic-coding · reasoning-budget
source_tasks: [session-2026-05-30 Antigravity CSS, T-20260529-009]
hit_count: 3
detection_signature: |
  Symptom: qwen agentic response emits 30-40 tool calls in ONE turn, output tokens
  spike >32K, gets truncated mid-call → last tool call empty params → todos left
  half-done. reasoning_effort=xhigh (64K thinking) + tools enabled.
  Quick check: count <ml_tool_call> in response; if >15 and output truncated → this.
last_updated: 2026-05-30
reconstructed: 2026-06-03
---

<!-- Nội dung dựng lại sau sự cố mất file — verify lại. -->

# Qwen xhigh overthink → stun (stall/truncate)

## Symptom (cách nhận ra)

Khi dispatch qwen với `reasoning_effort=xhigh` (thinking 64K) AND tools enabled (agentic):
- Model "overthink" → đẻ **30-40 `<ml_tool_call>` trong 1 response**
- Output tokens vọt lên ~39K → vượt client `max_tokens=32K`
- Response bị cắt giữa tool call cuối → params rỗng → client reject / todos dở
- Triệu chứng bề mặt: qwen "đứng hình" (stun), task không tiến.

```
output_tokens: 39102  (client max_tokens: 32768)   ← overflow
<ml_tool_call> ... </ml_tool_call>   × 39, cái cuối bị cắt
```

## Root cause

Qwen3.7-Max-Preview là **forced thinking model** (luôn reason, không tắt được — xem `agent-quirks/qwen.md` Q3). Với thinking budget 64K + agentic mode, model planning quá sâu → liệt kê 30-40 bước tool trong MỘT turn thay vì chia nhỏ → tổng output (thinking + tool calls) vượt client output cap → truncate.

Agentic task cần model **gọn** (5-10 tool/turn), không phải 1 mega-turn 40 tool. 64K thinking phù hợp chat sâu, KHÔNG phù hợp agent loop.

## Fix (lệnh chính xác)

**Auto-cap thinking 32K khi detect agentic** (proxy-side, không cần đổi client):
```ts
// Luna-Proxy qwen-ai.ts
if (isAgentic && adaptiveBudget > 32768) {
  console.log(`Agentic → cap thinking ${adaptiveBudget}→32768`);
  adaptiveBudget = 32768;
}
```
`isAgentic` = ANY của 4 signal (tools array / `<ml_tool_call>` / `<tool_calls>` / Cline XML tags — xem `patterns/cline-xml-tool-detection.md`).

Quick manual mitigation nếu chưa có cap: dispatch qwen agentic với `reasoning_effort=medium` (≤32K), KHÔNG xhigh.

## Prevention

1. **Default rule**: agentic + qwen → thinking ≤32K (implement Luna auto-cap, xem `patterns/agentic-thinking-cap.md`).
2. Đừng set `reasoning_effort=xhigh` cho qwen agentic task — chỉ dùng xhigh cho chat/reasoning thuần.
3. Detection script: đếm tool calls/response; >15 → warn overthink.

## First hit

- **Task**: session-2026-05-30 (Antigravity godmother-friendship CSS upgrade)
- **Date**: 2026-05-30
- **Time wasted**: ~40m (task stun, phải re-dispatch)

## Subsequent hits

- T-20260529-009 (2026-05-29) — same overthink, confirmed pattern
- session-2026-05-30 retest sau cap → 0 stun (fix verified)

## See also

- `patterns/agentic-thinking-cap.md` — the fix (3-layer adaptive cap)
- `patterns/cline-xml-tool-detection.md` — agentic detection signal
- `agent-quirks/qwen.md` Q1, Q3
