#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# show-task.sh — Quick task summary printer (read-only)
# ──────────────────────────────────────────────────────────────────
# Print: state · owner · created/updated · scope · evidence snapshot.
# Locates dossier in active/ or archive/YYYY-MM/. Read-only — never mutates.
#
# Usage:
#   bash system/scripts/show-task.sh <TASK_ID>
#   bash system/scripts/show-task.sh T-20260529-007
# ──────────────────────────────────────────────────────────────────

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"

# (v1.3) dual-mode dossier resolver (folder model + legacy file model)
source "$SCRIPT_DIR/lib/dossier-lib.sh"

red()    { printf '\033[31m%s\033[0m\n' "$*"; }
green()  { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
blue()   { printf '\033[34m%s\033[0m\n' "$*"; }
bold()   { printf '\033[1m%s\033[0m\n' "$*"; }

die() { red "$*" >&2; exit 1; }

[[ $# -eq 1 ]] || die "Usage: $0 <TASK_ID>  (e.g. T-20260529-007)"
TASK_ID="$1"
[[ "$TASK_ID" =~ ^T-[0-9]{8}-[0-9]+[A-Z]?(-[0-9]+)?$ ]] || die "Invalid task ID: $TASK_ID"

# ─── Locate dossier (v1.3 dual-mode: folder OR legacy; active → archive) ───
DOSSIER="$(resolve_dossier "$TASK_ID" 2>/dev/null || true)"
[[ -n "$DOSSIER" ]] || die "No dossier matching $TASK_ID in active/ or archive/"
LOCATION=$(dirname "${DOSSIER#$HANDOFFS_DIR/}")

# ─── Print header ────────────────────────────────────────────────
bold "═══ Task $TASK_ID ═══"
echo "  Location : $LOCATION"
echo "  File     : $(basename "$DOSSIER")"
echo ""

# ─── Frontmatter (YAML) ──────────────────────────────────────────
blue "Frontmatter"
awk '/^---$/{c++; next} c==1' "$DOSSIER" \
  | grep -E "^(id|owner|dispatched_by|state|priority|risk|created|updated|archived|project_slug|self_implement_forbidden):" \
  | sed 's/^/  /'
echo ""

# ─── Lock status (active only) ───────────────────────────────────
LOCK="$HANDOFFS_DIR/system/locks/${TASK_ID}.lock"
blue "Lock heartbeat"
if [[ -f "$LOCK" ]]; then
  MTIME=$(stat -c %Y "$LOCK" 2>/dev/null || stat -f %m "$LOCK" 2>/dev/null)
  NOW=$(date +%s)
  AGE=$((NOW - MTIME))
  if   [[ $AGE -lt 120 ]]; then green "  ✓ fresh (${AGE}s ago)"
  elif [[ $AGE -lt 180 ]]; then yellow "  warn (${AGE}s ago)"
  else                          red "  ✗ stale (${AGE}s ago) — run 'reconcile.sh --reap-stale'"
  fi
else
  echo "  (no lock file — task likely archived or never dispatched)"
fi
echo ""

# ─── Scope (§IV) ─────────────────────────────────────────────────
blue "Allowed Scope (§IV)"
awk '/^## IV\./,/^## V\./' "$DOSSIER" \
  | grep -vE "^## V\.|^## IV\." | sed 's/^/  /' | head -20
echo ""

# ─── Acceptance criteria status (§VIII) ──────────────────────────
blue "Acceptance Criteria (§VIII)"
TOTAL=$(awk '/^## VIII\./,/^## IX\./' "$DOSSIER" | grep -cE "^\s*-\s*\[" || true)
DONE=$(awk '/^## VIII\./,/^## IX\./' "$DOSSIER" | grep -cE "^\s*-\s*\[x\]" || true)
echo "  $DONE/$TOTAL ticked"
echo ""

# ─── Recent evidence (§XII) ──────────────────────────────────────
blue "Evidence snapshot (§XII — last 15 lines)"
awk '/^## XII\./,/^## XV\./' "$DOSSIER" | grep -vE "^## " | grep -v "^$" | tail -15 | sed 's/^/  /'
echo ""

# ─── Return file (v4.9) ──────────────────────────────────────────
RETURN="$HANDOFFS_DIR/returns/${TASK_ID}-return.md"
blue "Return file (v4.9 deliverable)"
if [[ -f "$RETURN" ]]; then
  green "  ✓ $RETURN"
  awk '/^---$/{c++; next} c==1' "$RETURN" \
    | grep -E "^(status|scope_clean|gates_passed|recommendation):" \
    | sed 's/^/    /'
else
  yellow "  (no return file yet — worker hasn't reported)"
fi
echo ""

# ─── Quick actions ───────────────────────────────────────────────
blue "Quick actions"
STATE=$(awk '/^---$/{c++; next} c==1' "$DOSSIER" | grep -E "^state:" | head -1 | awk '{print $2}')
case "$STATE" in
  drafted)    echo "  → next: dispatch  · bash system/scripts/set-state.sh $TASK_ID dispatched" ;;
  dispatched) echo "  → next: wait worker REPORT, then  · bash system/scripts/set-state.sh $TASK_ID returned" ;;
  returned)   echo "  → next: verify gates, then  · bash system/scripts/set-state.sh $TASK_ID verified" ;;
  verified)   echo "  → next: archive  · bash system/scripts/archive-task.sh $TASK_ID" ;;
  blocked)    echo "  → next: fix Escalation, then  · bash system/scripts/set-state.sh $TASK_ID reopened" ;;
  archived)   echo "  → archived. To reopen: bash system/scripts/set-state.sh $TASK_ID reopened" ;;
  *)          echo "  (state unknown: $STATE)" ;;
esac
echo ""
echo "  Full dossier:  cat \"$DOSSIER\""
