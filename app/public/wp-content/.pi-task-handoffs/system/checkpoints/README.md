# Checkpoints — Dossier State Snapshots (v4.8)

Time-travel restore + audit trail cho dossier state transitions.

> Inspired by [LangGraph checkpointing](https://www.langchain.com/langgraph) + [Microsoft agent-framework state persistence](https://github.com/microsoft/agent-framework).

## I. Purpose

Snapshot dossier YAML + lock file mỗi state transition quan trọng → cho phép:
- **Time-travel restore**: rollback dossier về state trước nếu cần
- **Audit trail**: who changed what, when
- **Phase E safety**: snapshot trước khi verified → có "undo button" nếu regression
- **Diff comparison**: so sánh dossier giữa 2 state transitions

## II. Folder Structure

```
system/checkpoints/
├── README.md
└── T-{id}/
    ├── 000-drafted-20260528T123045/
    │   ├── dossier.md       ← full dossier snapshot
    │   ├── lock.yml         ← lock state snapshot
    │   └── meta.yml         ← snapshot metadata
    ├── 001-dispatched-20260528T143012/
    │   └── ...
    ├── 002-returned-20260528T173855/
    │   └── ...
    └── 003-verified-20260528T180230/
        └── ...
```

Naming: `<seq>-<state>-<ISO timestamp>/`. Seq auto-increments per task.

## III. meta.yml Format

```yaml
task_id: T-20260528-001
sequence: 2
state_at_snapshot: returned
snapshot_at: 2026-05-28T17:38:55
dossier_size: 12450
git_head: a3f7e2c8b9d1e6...
```

## IV. Operations

### Take snapshot (manual hoặc auto)
```bash
bash system/scripts/checkpoint.sh T-20260528-001
# → tạo system/checkpoints/T-20260528-001/<seq>-<state>-<timestamp>/
```

### List snapshots
```bash
bash system/scripts/checkpoint.sh T-20260528-001 --list
# Output:
#   #    STATE               TIMESTAMP            SIZE
#   000  drafted             20260528T123045      8542B
#   001  dispatched          20260528T143012      9123B
#   002  returned            20260528T173855      12450B
```

### Restore (time-travel)
```bash
bash system/scripts/checkpoint.sh T-20260528-001 --restore 1
# → tự take safety snapshot trước
# → restore dossier.md + lock.yml từ snapshot #1
# → orchestrator phải update STATUS.md state cell sau đó
```

### Diff 2 snapshots
```bash
bash system/scripts/checkpoint.sh T-20260528-001 --diff 1 2
# → unified diff giữa snapshot #1 và #2
```

### Garbage collect (>30 days)
```bash
bash system/scripts/checkpoint.sh --gc
# → xóa checkpoint folders của tasks archived > 30 days
```

## V. When to Snapshot

**Recommended snapshot points** (orchestrator/worker tự ý thức):

| Trigger | Why |
|---|---|
| Before Phase B start (`dispatched`) | Baseline trước worker edit |
| Before HITL pause (`awaiting_approval`) | Capture context for user decision |
| Before Phase C verify | Safety net trước verified |
| Before Phase D archive | Last chance to rollback |
| Manual khi orchestrator detect risk | "I'm not sure, let me snapshot first" |

**KHÔNG snapshot** mỗi heartbeat (overhead) — chỉ tại state transitions.

## VI. Restore Discipline

Khi `--restore`:
1. **Auto safety snapshot taken first** — không mất state hiện tại
2. Dossier.md được overwrite
3. Lock được restore nếu captured
4. **Orchestrator phải manually**:
   - Update STATUS.md state cell match với dossier YAML
   - Notify user qua inbox
   - Document trong `## XVI. CHANGE LOG`

## VII. Integration

- **Phase E rollback** ([`LIFECYCLE.md`](../LIFECYCLE.md) §III): khi regression detected post-archive → `--restore` về state verified, fix, re-archive.
- **HITL workflow**: snapshot ngay khi `awaiting_approval` → user có context đầy đủ để quyết định.
- **Audit**: orchestrator review snapshot sequence để hiểu task evolution.

## VIII. Storage

- Mỗi snapshot ~10-50KB (dossier + lock + meta)
- Mỗi task active có 3-8 snapshots typical
- 30-day retention default — adjust qua `--gc` hoặc env var
- Total size: ~5MB per 100 tasks lifecycle
