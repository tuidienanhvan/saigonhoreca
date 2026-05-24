# 🔒 Lock Mechanism Protocol (v4.1)

## I. 🎯 Purpose
Prevent concurrent agents from working on the same task. Heartbeat-based, with auto-reap of stale locks (pattern from [claude_code_agent_farm](https://github.com/Dicklesworthstone/claude_code_agent_farm)).

## II. 📐 Lock File Format
Each lock: `system/locks/T-YYYYMMDD-NNN.lock` (YAML):

```yaml
task_id: T-20260513-001
agent: codex
model: gpt-5.5
session_id: <uuid-v4>
started_at: 2026-05-13T22:38:00+07:00
last_heartbeat: 2026-05-13T22:40:00+07:00
pid: 12345
```

## III. 💓 Heartbeat TTL
- **TTL**: 180 seconds (3 minutes).
- Holder must `touch` the lock file every 60–120s while working.
- File `mtime` is the authoritative heartbeat (any write to lock = heartbeat).

## IV. 🔄 Lifecycle

### 1. Acquire
```bash
# 🔒 Check no active lock
[[ -f system/locks/T-XXX.lock ]] && { echo "Locked"; exit 1; }

cat > system/locks/T-XXX.lock <<EOF
task_id: T-XXX
agent: codex
model: gpt-5.5
session_id: $(uuidgen 2>/dev/null || date +%s)
started_at: $(date -Iseconds)
last_heartbeat: $(date -Iseconds)
pid: $$
EOF
```

### 2. Heartbeat (every 60–120s)
```bash
touch system/locks/T-XXX.lock
# 💓 Or update last_heartbeat field explicitly:
sed -i "s/^last_heartbeat:.*/last_heartbeat: $(date -Iseconds)/" system/locks/T-XXX.lock
```

### 3. Release
```bash
rm -f system/locks/T-XXX.lock
```

### 4. Auto-reap (stale > TTL)
```bash
bash system/scripts/reconcile.sh --reap-stale       # dry-run reports
bash system/scripts/reconcile.sh --fix              # applies (includes reap)
```

Reconcile deletes any lock whose `mtime > 180s` ago. Next agent can acquire.

## V. 💓 Why heartbeat-mtime instead of YAML field?
Filesystem `mtime` is atomic (single syscall), tamper-evident (no parse needed), and works across all shells (PowerShell, Git-Bash, Linux). YAML field is human-readable mirror only.

## VI. 🔌 Integration
- `archive-task.sh` releases lock on Phase D success.
- `reconcile.sh --reap-stale` for crash recovery.
- `lint-handoffs.sh --strict` does NOT check locks (locks are runtime, not state-of-truth).
