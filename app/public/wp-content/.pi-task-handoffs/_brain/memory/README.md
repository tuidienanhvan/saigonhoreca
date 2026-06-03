# Memory — System's accumulated learning (v1.1 NEW)

> **Vision** (USER, 2026-05-30): _"Pi Task Handoffs được sinh ra để học hỏi từ task và biến nó thành trí nhớ của riêng hệ thống — sau N task, hệ thống PHẢI giỏi hơn N task trước."_

`_brain/memory/` là **Layer 2** trong kiến trúc 3-layer của Pi Task Handoffs — nơi **synthesize** raw dossier thành reusable knowledge. Khác với LEADERBOARD (counters) và AGENTS metrics (stats), memory chứa **nội dung học được**: pattern thắng, pitfall biết tránh, agent quirk biết hành xử.

---

## I. Kiến trúc 3-layer memory

```
┌─────────────────────────────────────────────────────────────────┐
│ Layer 1 (RAW) — what happened                                   │
│   active/ · archive/YYYY-MM/ · returns/ · LEADERBOARD.md        │
│   • Per-task records (dossier YAML + evidence + return file)    │
│   • Append-only, immutable after archive                        │
└─────────────────────────────────────────────────────────────────┘
                          ↓ harvest-lessons.sh extracts
┌─────────────────────────────────────────────────────────────────┐
│ Layer 2 (SYNTH) — what we learned     ← THIS DIRECTORY          │
│   _brain/memory/patterns/   · approaches that worked (reusable)        │
│   _brain/memory/pitfalls/   · root causes of blocked states + fixes    │
│   _brain/memory/agent-quirks/ · runtime observations beyond AGENTS/*  │
│   _brain/memory/playbooks/  · end-to-end workflows tested in practice  │
│   _brain/memory/INDEX.md    · cross-reference (task-id ↔ lesson)       │
└─────────────────────────────────────────────────────────────────┘
                          ↓ surface in new tasks
┌─────────────────────────────────────────────────────────────────┐
│ Layer 3 (APPLY) — how it helps NEXT task                        │
│   • new-task.sh suggests relevant memory entries (slug-match)   │
│   • DOSSIER.md §III.M Memory References (auto-include)             │
│   • show-task.sh surfaces related lessons                       │
│   • Lint warns nếu high-impact pitfall không reference          │
└─────────────────────────────────────────────────────────────────┘
```

→ Hệ thống ngày càng giỏi hơn KHÔNG nhờ "AI thông minh" mà nhờ **structured accumulation** — mỗi task archived đóng góp 0–3 lesson; sau 100 task có catalog đủ dùng.

> **v1.2 NEW — Layer 4 SKILL**: AI orchestrator KHÔNG đọc memory thụ động — nó **invoke skills** từ `_brain/skills/` để tạo, surface, promote memory entries. Xem [`../skills/README.md`](../skills/README.md). User nói "ghi pitfall đi" → AI invokes `_brain/skills/create-pitfall.md` → workflow tự chạy.

---

## II. 4 loại memory + cách phân loại

### 2.1 `patterns/` — Approach thắng
**Khi nào ghi**: task `verified` với approach **mới/sáng tạo** đáng tái sử dụng. KHÔNG ghi nếu approach là pattern hiển nhiên (vd "chạy npm run lint").

**Format file** (`patterns/<slug>.md`):
```yaml
---
type: pattern
title: <one-line approach name>
domain: css-migration | crawl | api-refactor | tooling | ... (free tag)
source_tasks: [T-20260529-005, T-20260530-001]   # tasks nơi approach proved
confidence: 1   # 1 = thử 1 lần · 3 = >=3 lần · 5 = standard
last_updated: YYYY-MM-DD
---

# <Title>

## Problem
<bối cảnh: kiểu task này hay gặp khó gì>

## Approach (steps)
1. ...
2. ...

## Why it works
<insight kỹ thuật>

## When to use
- ...
- ...

## When NOT to use
- ...

## Variations tried
- T-XXX dùng cách A → fail vì Y
- T-YYY dùng cách B (this) → pass

## See also
- patterns/<related>.md
- pitfalls/<related>.md
```

### 2.2 `pitfalls/` — Root cause của blocked + fix
**Khi nào ghi**: task `blocked` với root cause có thể tái phát ở task khác. Ghi cả **detection signature** (cách nhận ra sớm) và **fix**.

**Format** (`pitfalls/<slug>.md`):
```yaml
---
type: pitfall
title: <one-line problem>
severity: critical | high | medium | low
domain: <free tag>
source_tasks: [T-XXX]
hit_count: 1   # tăng mỗi lần gặp lại
detection_signature: |
  <grep/symptom giúp nhận ra>
last_updated: YYYY-MM-DD
---

# <Title>

## Symptom (cách nhận ra)
<error message / behavior / log line>

## Root cause
<tại sao xảy ra — technical>

## Fix (lệnh chính xác)
```bash
<exact commands>
```

## Prevention (làm sao tránh lần sau)
- ...

## First hit
- Task: T-XXX
- Date: YYYY-MM-DD
- Time wasted: ~Nm

## Subsequent hits
- T-YYY (YYYY-MM-DD)

## See also
- patterns/<related-success>.md
```

### 2.3 `agent-quirks/` — Runtime observations
**Khi nào ghi**: phát hiện behavior của agent KHÔNG có trong `AGENTS/<name>.md` (docs canonical). Memory cập nhật trước; nếu lặp lại nhiều, promote sang AGENTS/.

**Format** (`agent-quirks/<agent>.md`):
```yaml
---
type: agent-quirk
agent: qwen | claude | gemini | ...
quirks_count: N
last_updated: YYYY-MM-DD
---

# <Agent> — Runtime quirks (observed in production tasks)

## Q1. <Title quirk>
- **Observed in**: T-XXX, T-YYY
- **Behavior**: <description>
- **Workaround**: <fix>
- **Promoted to AGENTS/<agent>.md?**: yes/no
```

### 2.4 `playbooks/` — End-to-end workflows
**Khi nào ghi**: bạn (hoặc agent) làm xong 1 workflow phức tạp (nhiều task, nhiều bước) → copy-paste-able cho người khác (hoặc bạn 6 tháng sau).

**Format** (`playbooks/<slug>.md`):
```yaml
---
type: playbook
title: <workflow name>
duration_est: 30m | 2h | 1d
last_validated: YYYY-MM-DD on T-XXX
---

# <Title>

## Use case
<khi nào dùng>

## Prerequisites
- ...

## Steps (copy-paste)
```bash
# Step 1
...
# Step 2
...
```

## Verification
- ...

## Common deviations
- ...
```

---

## III. Quy tắc ghi memory (đừng spam)

1. **1 task → 0-3 lessons**, không phải mọi task đều generate memory entry. Task nhỏ/lặp lại không cần ghi.
2. **Lesson PHẢI có `source_tasks:`** trỏ về dossier gốc — audit trail.
3. **Đừng duplicate dossier `## XV Escalation`** — memory CHỈ ghi root cause + fix CÓ THỂ TÁI SỬ DỤNG; raw error giữ ở dossier.
4. **Confidence/hit_count tăng dần** khi pattern/pitfall lặp lại — đây là tín hiệu promote vào docs canonical (SKILL/QUALITY-GATES/AGENT-MODEL).
5. **Memory không thay docs** — nó là sandbox để observation chín; promote vào docs khi `confidence: >=3` (hoặc `hit_count: >=3` cho pitfall).

---

## IV. Cách dùng (3 hướng)

### IV.1 Trước khi dispatch task mới — tra cứu

```bash
# Tìm memory liên quan slug task mới
grep -rln "<keyword>" _brain/memory/

# Hoặc dùng INDEX (auto-generated)
cat _brain/memory/INDEX.md | grep -i "<task-type>"
```

→ Thấy entry liên quan → add vào dossier §III Required Reading.

### IV.2 Khi task `blocked` — ghi pitfall

```bash
# Tạo pitfall entry từ template
cp _brain/memory/_template-pitfall.md _brain/memory/pitfalls/<slug>.md
# Edit: fill symptom + root cause + fix từ §XV Escalation của dossier
```

### IV.3 Auto-harvest sau archive

```bash
bash system/scripts/harvest-lessons.sh
# Đọc archive/YYYY-MM/T-*.md + returns/T-*-return.md gần nhất
# Suggest memory entries từ:
#   - §XV Escalation (blocked tasks → pitfalls)
#   - §6 Residual Risks / Caveats (patterns to remember)
#   - §9 Orchestrator Notes (verified learnings)
# Output: _brain/memory/_pending/<task-id>-suggested.md (USER review trước khi commit)
```

→ `archive-task.sh` Step 8.5 (v1.1) auto-call harvest sau khi archive thành công.

---

## V. Roadmap memory layer

| Version | Trạng thái | Nội dung |
|---|---|---|
| v1.1 seed | DONE | structure + seed 13 entries từ session 2026-05-30 |
| v1.1 harvest | DONE | `harvest-lessons.sh` auto-extract; integration hook in archive-task.sh |
| v1.1 surface | DONE | `new-task.sh` suggest relevant entries via slug grep; DOSSIER.md §III.M field |
| **v1.2 SKILLS** (NOW) | DONE | Layer 4 — `_brain/skills/` directory với 6 invokable workflows (create-pitfall, create-pattern, create-playbook, reference-memory, harvest-and-review, promote-to-canonical) |
| v1.3 metaskills | Planned | `_brain/skills/create-skill.md` — AI tạo skill mới (self-extending) |
| v1.4 chained | Planned | Multi-skill workflows (e.g., "review-and-promote-all-pending") |
| v2.0 semantic | Future | Optional: embedding-based similarity search (instead of slug-grep) |

> Memory layer là **additive** lên Pi Task Handoffs v1.0 — KHÔNG break state machine, lifecycle, gates, hay scripts hiện có. Là tầng riêng "trên nóc" — bỏ đi vẫn chạy bình thường.

---

## VI. Lịch sử

- **2026-05-30**: USER vision: "task handoffs sinh ra để học hỏi từ task → trí nhớ riêng". Build _brain/memory/ skeleton + seed từ session Luna-Proxy tuning + Pi Task Handoffs v1.0 rename.
