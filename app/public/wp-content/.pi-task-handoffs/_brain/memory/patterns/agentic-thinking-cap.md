---
type: pattern
title: Adaptive thinking budget cap when agentic mode detected
domain: llm-proxy · agentic-coding
source_tasks: [session-2026-05-30 Luna adapter]
confidence: 3
last_updated: 2026-05-30
---

# Adaptive thinking budget cap when agentic mode detected

## Problem
Reasoning model (Qwen3.7-Max-Preview, GPT-o3, Claude extended thinking, ...) khi gọi với:
- High thinking budget (32K+)
- AND tools enabled (agentic mode)

→ Overthink → đẻ 30+ tool calls trong 1 response → output overflow client `max_tokens` (32K) → stun/truncate (xem `pitfalls/qwen-xhigh-overthink-stun.md`).

## Approach (3 layers)

### Layer 1: Detect agentic mode
4 signals trong request body — ANY = agentic:
1. `request.tools` array có nội dung (Kilo/Antigravity native)
2. `<ml_tool_call>` trong message history (Luna inject format)
3. `<tool_calls>` `<tool_use>` (OpenAI/Claude legacy)
4. Cline XML tags: `<read_file>`, `<write_to_file>`, `<execute_command>`, `<attempt_completion>` (19 tags total)

### Layer 2: Smart default budget (when no effort given)
```ts
if (isAgentic) {
    budget = 32768;       // 32K — enough for multi-step code, no overflow
} else if (isShortPrompt) {  // <250 chars
    budget = 4096;        // 4K — quick chat ~3-5s
} else {
    budget = 65536;       // 64K — deep reasoning for complex chat
}
```

### Layer 3: Force-cap when high effort + agentic
Even nếu client gửi `reasoning_effort=xhigh` (= 64K), nếu detect agentic → **cap về 32K**:
```ts
if (isAgentic && adaptiveBudget > 32768) {
    console.log(`Agentic → cap thinking ${adaptiveBudget}→32768`);
    adaptiveBudget = 32768;
}
```

## Why it works

Agentic task có constraint khác chat:
- **Chat thuần**: 1 long thoughtful answer → 64K thinking OK
- **Agentic**: nhiều turn ngắn + tool execution → cần model **gọn**, không overthink

32K thinking đủ để model planning 5-10 tool calls/turn (sweet spot). >32K → đẻ 30+ → vỡ client cap 32K output.

## When to use
- Any reasoning model qua proxy (Luna, custom OpenAI-compatible)
- Client là agentic IDE (Kilo, Cline, Antigravity, Cursor)

## When NOT to use
- Client chỉ chat (no tools) → để model dùng full budget
- Model không phải reasoning (vanilla GPT-4) → no thinking budget concept

## Variations tried
- **No cap (always xhigh)** → 80% stun rate ở agentic task
- **Hard cap 16K** → too conservative, model thiếu reasoning depth cho refactor phức tạp
- **Adaptive 32K cap** (this) → 0% stun rate verified over ~15 agentic task

## Implementation reference
`Luna-Proxy/src/main/proxy/adapters/qwen-ai.ts` lines 738-790:
- `isAgentic` detection
- `isShortPrompt` check
- 4-tier smart default
- Auto-cap when client explicit xhigh + agentic

## See also
- `pitfalls/qwen-xhigh-overthink-stun.md` — what this fixes
- `agent-quirks/qwen.md` Q1 Overthink behavior
- Industry parallel: Claude Code defaults thinking ~16K for agent loops, full 64K only on "ultrathink"
