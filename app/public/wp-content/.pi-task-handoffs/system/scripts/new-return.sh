#!/usr/bin/env bash
# new-return.sh — scaffold a structured return report from a task dossier.
# Usage: bash system/scripts/new-return.sh T-20260529-002
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT"
HANDOFFS_DIR="$ROOT"
# (v1.3) dual-mode dossier resolver (folder model + legacy file model)
source "$ROOT/system/scripts/lib/dossier-lib.sh"

TID="${1:-}"
if [ -z "$TID" ]; then echo "Usage: new-return.sh <task-id>"; exit 1; fi

# locate dossier (v1.3 dual-mode: folder OR legacy; active first, then archive)
DOSSIER="$(resolve_dossier "$TID" 2>/dev/null || true)"
if [ -z "$DOSSIER" ]; then echo "✗ Dossier không tìm thấy cho $TID"; exit 1; fi

# tolerant: no-match → empty (không kill set -e); strip inline "# comment"
field(){ grep -m1 "^$1:" "$DOSSIER" 2>/dev/null | sed "s/^$1:[[:space:]]*//; s/[[:space:]]*#.*$//; s/[[:space:]]*$//" || true; }
AGENT="$(field owner)"
SLUG="$(field project_slug)"
[ -z "$SLUG" ] && SLUG="$(task_basename "$DOSSIER" | sed -E 's/^T-[0-9]+-[0-9]+-[a-z]+-//')"

# (v1.3) Output location:
#   folder model → co-locate return.md INSIDE the task folder (1 task = 1 folder)
#   legacy file  → returns/<TID>-return.md (separate deliverable queue)
if is_folder_model "$DOSSIER"; then
    OUT="$(task_container "$DOSSIER")/return.md"
else
    OUT="returns/${TID}-return.md"
    mkdir -p returns
fi
if [ -f "$OUT" ]; then echo "$OUT đã tồn tại — không ghi đè (mở để append RETURN v2)."; exit 0; fi

TPL="system/templates/RETURN-REPORT.md"
# substitute placeholders from dossier (date/time để agent tự điền — script không có clock đáng tin)
sed -e "s/T-XXXXXXXX-NNN/${TID}/g" \
    -e "s/<TASK_ID>/${TID}/g" \
    -e "s/agent: <name>/agent: ${AGENT}/" \
    -e "s/| <AGENT> |/| ${AGENT} |/" \
    -e "s/<PROJECT_NAME>/${SLUG}/g" \
    "$TPL" > "$OUT"

echo "✓ Scaffolded $OUT  (task=$TID agent=$AGENT project=$SLUG)"
echo "  → Agent điền §1-8, paste raw evidence §5. Orchestrator verify → §9."
