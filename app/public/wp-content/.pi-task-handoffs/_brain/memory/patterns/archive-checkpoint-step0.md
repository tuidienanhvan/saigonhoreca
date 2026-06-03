---
type: pattern
title: Auto-snapshot via checkpoint.sh at Step 0 of archive-task.sh
domain: tooling · atomicity
source_tasks: [Pi-task-handoffs-v1.0-rename-2026-05-30]
confidence: 1
last_updated: 2026-05-30
---

# Auto-checkpoint Step 0 before archive atomic mutations

## Problem
`archive-task.sh` performs 7 atomic mutations (move file, update STATUS, append LEADERBOARD, reconcile, ...). Nếu midway fail (vd `reconcile.sh --fix` crash because of unrelated drift), dossier có thể ở trạng thái lệch:
- File moved nhưng STATUS chưa update
- LEADERBOARD đã append nhưng metrics chưa refresh

Rollback bằng tay phức tạp.

## Approach

Add Step 0 BEFORE locate-file:

```bash
# ─── Step 0 (v1.0): Pre-archive checkpoint ───
blue "Step 0/7: Pre-archive checkpoint (safety snapshot)..."
CHECKPOINT_SCRIPT="$SCRIPT_DIR/checkpoint.sh"
if [[ -x "$CHECKPOINT_SCRIPT" ]]; then
    if bash "$CHECKPOINT_SCRIPT" "$TASK_ID" 2>/dev/null | tail -1; then
        green "  ✓ Checkpoint snapshot created"
    else
        yellow "  Checkpoint failed (best-effort, proceeding)"
    fi
fi
# ... continue with Step 1-7 atomic operations ...
```

Best-effort: nếu checkpoint fail, KHÔNG block archive (archive là source of truth, checkpoint chỉ safety net).

## Why it works

`checkpoint.sh <TASK_ID>` snapshots:
- Dossier YAML + content → `system/checkpoints/T-XXX/<seq>-<state>-<ts>/dossier.md`
- Lock file → `lock.snapshot`

Nếu archive fail midway:
```bash
bash system/scripts/checkpoint.sh T-XXX --list
# → shows snapshot 005 (pre-archive)
bash system/scripts/checkpoint.sh T-XXX --restore 5
# → dossier restored to pre-archive state
# → fix root cause → re-run archive
```

## When to use
- ANY destructive atomic operation (archive, mass-state-change)
- Lifecycle transitions with multi-file mutations

## Tradeoffs
- Safety: every archive has rollback point
- Disk: 1 snapshot per archive (~5KB each); `checkpoint.sh --gc` cleans >30d
- Time: +0.5s pre-archive (negligible)

## Implementation
`system/scripts/archive-task.sh` lines 40-54 (v1.0+).

## See also
- `system/scripts/checkpoint.sh` — snapshot/restore mechanism
- Lifecycle invariant 5: archive + risk=high need `changes/T-XXX/rollback-plan.md`
- This pattern is **subset** of formal rollback-plan (cheaper, automated)
