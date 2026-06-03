---
type: pattern
title: Detect Cline agentic mode via 19 XML tool tags in message history
domain: llm-proxy · agentic-coding · cline
source_tasks: [session-2026-05-30 Luna adapter, T-20260529-008]
confidence: 3
last_updated: 2026-05-30
reconstructed: 2026-06-03
---

<!-- Nội dung dựng lại sau sự cố mất file — verify lại. -->

# Detect Cline agentic mode via XML tool tags

## Problem

Cline (và fork Roo/Kilo ở một số chế độ) KHÔNG gửi `request.tools` array như OpenAI native function-calling. Thay vào đó nó dạy model gọi tool bằng **XML tags** nhúng thẳng trong text response (vd `<read_file>`, `<execute_command>`). 

Hệ quả: proxy (Luna) nếu chỉ check `request.tools` để phát hiện agentic mode → MISS Cline → không cap thinking budget → qwen overthink → stun (xem `pitfalls/qwen-xhigh-overthink-stun.md`).

## Approach (steps)

1. Khi nhận request, ngoài check `request.tools`, scan **message history** (system + assistant turns) cho XML tool tags của Cline.
2. Maintain danh sách 19 Cline tool tags:
   ```
   read_file · write_to_file · replace_in_file · execute_command ·
   search_files · list_files · list_code_definition_names ·
   browser_action · use_mcp_tool · access_mcp_resource ·
   ask_followup_question · attempt_completion · new_task ·
   plan_mode_respond · load_mcp_documentation · web_fetch ·
   apply_diff · insert_content · search_and_replace
   ```
3. Nếu match ANY tag (regex `<(read_file|write_to_file|...)>`) → set `isAgentic = true`.
4. Feed `isAgentic` vào adaptive thinking cap (xem `patterns/agentic-thinking-cap.md` Layer 1 signal #4).

## Why it works

Cline's system prompt luôn chứa tool definitions dưới dạng XML schema + few-shot examples → các tag này XUẤT HIỆN trong history NGAY từ turn đầu, kể cả trước khi model gọi tool. Vì vậy detect sớm, cap thinking đúng từ response đầu tiên.

XML-tag detection bù cho việc Cline không dùng `tools` field chuẩn — đây là signal #4 trong 4-signal agentic detection của Luna.

## When to use

- Proxy phục vụ Cline / Roo Code / Kilo (XML tool convention)
- Bất kỳ client nào nhúng tool call dạng XML thay vì function-calling API

## When NOT to use

- Client dùng OpenAI native `tools` array → signal #1 đã đủ, không cần scan XML
- Pure chat (không tool) → đừng false-positive trên text chứa `<...>` tình cờ → giới hạn match đúng 19 tag tên cố định

## Variations tried

- **Chỉ check `request.tools`** → miss Cline hoàn toàn → 80% stun trên Cline task
- **Match mọi `<tag>`** → false positive trên code blocks chứa HTML/XML → cap nhầm chat task
- **Whitelist 19 tag tên cố định** (this) → 0 false positive, bắt đúng Cline agentic

## Implementation reference

`Luna-Proxy/src/main/proxy/adapters/qwen-ai.ts` — `isAgentic` detection, signal #4 (Cline XML tag scan).

## See also

- `patterns/agentic-thinking-cap.md` — consumes this signal (Layer 1)
- `pitfalls/qwen-xhigh-overthink-stun.md` — what mis-detection causes
- `agent-quirks/qwen.md` Q1
