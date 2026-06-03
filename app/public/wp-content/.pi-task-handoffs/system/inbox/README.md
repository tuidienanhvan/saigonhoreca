# Inbox — Inter-Agent Message Queue (v4.8)

Per-agent append-only message log. 1 file = 1 agent inbox.

> Inspired by [AWS CLI Agent Orchestrator send_message primitive](https://github.com/awslabs/cli-agent-orchestrator).

## I. Purpose

Mỗi agent có 1 inbox `system/inbox/<agent>.md` để nhận messages bất đồng bộ từ orchestrator hoặc agent khác. Không phải dispatch — chỉ là notification queue.

## II. File Naming

```
system/inbox/
├── claude.md           ← Claude's inbox
├── gemini.md           ← Gemini's inbox
├── chatgpt.md          ← ChatGPT's inbox
├── codex.md            ← Codex's inbox
├── gemma.md            ← Gemma's inbox
├── orchestrator.md     ← shared orchestrator inbox
└── .archive/           ← marked-read messages archived here
```

## III. Message Format

Append-only markdown, mỗi message là 1 section separated by `---`:

```markdown
---

## <Subject>
**From**: <sender_agent>
**Sent**: 2026-05-28T14:30:00
**Type**: notice | request | response | broadcast
**Task**: `T-20260528-001` (optional)

<body>
```

## IV. Operations

### Send
```bash
bash system/scripts/send-message.sh \
  --to gemini --from claude --task T-20260528-001 \
  --type notice --body "Phase B started, ETA 4h"
```

### Read
```bash
bash system/scripts/send-message.sh --inbox gemini
```

### Mark all read (archive)
```bash
bash system/scripts/send-message.sh --inbox gemini --mark-read
# → moves to .archive/gemini-<timestamp>.md
```

### Broadcast
```bash
bash system/scripts/send-message.sh --to broadcast --from claude \
  --type broadcast --body "STATUS.md schema locked"
# → delivers to all 5 agents simultaneously
```

## V. Message Types

| Type | Icon | Use case |
|---|---|---|
| `notice` | | Status update, FYI, "phase started/done" |
| `request` | | Asking another agent to verify/review/decide |
| `response` | | Reply to a previous request |
| `broadcast` | | System-wide announcement (all agents) |

## VI. Integration với Workflow

- **Dispatch mode `assign`** (async): orchestrator có thể send-message làm dispatch trigger thay vì sync chat handoff.
- **HITL pause**: worker hit checkpoint → send-message to user/orchestrator + `state: awaiting_approval`.
- **Cross-family spot check**: orchestrator request verify từ agent khác family qua inbox.

## VII. Discipline

- Inbox là **notification only** — không phải task assignment chính (dossier vẫn là SoT).
- Worker đọc inbox ở Phase B step 2 (Context Loading).
- Inbox > 50 messages → cleanup với `--mark-read`.
- KHÔNG dùng inbox cho secrets/tokens (SKILL §I.6).
