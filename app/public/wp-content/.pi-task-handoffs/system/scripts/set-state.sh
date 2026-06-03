#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# set-state.sh — Atomic state transition for .task-handoffs tasks (v4.3)
# ──────────────────────────────────────────────────────────────────
# Updates: dossier YAML state+updated, STATUS.md row movement, lock heartbeat.
#
# Usage:
#   bash system/scripts/set-state.sh <TASK_ID> <NEW_STATE> [--reason "..."]
#
# Examples:
#   bash system/scripts/set-state.sh T-20260516-001 dispatched
#   bash system/scripts/set-state.sh T-20260516-001 returned
#   bash system/scripts/set-state.sh T-20260516-001 verified
#   bash system/scripts/set-state.sh T-20260516-001 blocked --reason "build fail x3"
#   bash system/scripts/set-state.sh T-20260516-001 reopened
#
# Valid transitions (state machine):
#   drafted    → dispatched | blocked
#   dispatched → returned   | blocked
#   returned   → verified   | blocked
#   verified   → (use archive-task.sh)
#   blocked    → reopened
#
# STATUS movement:
#   dispatched|returned → stay in §I (Active)
#   verified            → §I → §II (Waiting for Review)
#   blocked             → §I/§II → §III (Blocked)
#   reopened            → §III → §I
# ──────────────────────────────────────────────────────────────────

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
ACTIVE_DIR="$HANDOFFS_DIR/active"
STATUS_FILE="$HANDOFFS_DIR/STATUS.md"
LOCKS_DIR="$HANDOFFS_DIR/system/locks"

red()    { printf '\033[31m%s\033[0m\n' "$*"; }
green()  { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
blue()   { printf '\033[34m%s\033[0m\n' "$*"; }

die() { red "❌ $*" >&2; exit 1; }
usage_err() { red "❌ $*" >&2; print_usage; exit 2; }

print_usage() {
  cat <<EOF
Usage: $0 <TASK_ID> <NEW_STATE> [--reason "<text>"]

Valid states: drafted | dispatched | returned | verified | blocked | reopened

Examples:
  $0 T-20260516-001 dispatched
  $0 T-20260516-001 blocked --reason "build fail x3"
EOF
}

# ─── Parse args ──────────────────────────────────────────────────
[[ "${1:-}" == "--help" || "${1:-}" == "-h" ]] && { print_usage; exit 0; }
[[ $# -ge 2 ]] || usage_err "Need <TASK_ID> + <NEW_STATE>"

TASK_ID="$1"
NEW_STATE="$2"
shift 2

REASON=""
while [[ $# -gt 0 ]]; do
  case "$1" in
    --reason) REASON="$2"; shift 2 ;;
    *) usage_err "Unknown flag: $1" ;;
  esac
done

[[ "$TASK_ID" =~ ^T-[0-9]{8}-[0-9]+[A-Z]?$ ]] || usage_err "Invalid task ID format: $TASK_ID"
[[ "$NEW_STATE" =~ ^(drafted|dispatched|returned|verified|blocked|reopened)$ ]] || usage_err "Invalid state: $NEW_STATE"

# ─── Step 1: Locate dossier ──────────────────────────────────────
blue "🔍 Step 1/4: Locating $TASK_ID..."

shopt -s nullglob
FILES=("$ACTIVE_DIR"/${TASK_ID}-*.md)
shopt -u nullglob

[[ ${#FILES[@]} -gt 0 ]] || die "No dossier $ACTIVE_DIR/${TASK_ID}-*.md"
[[ ${#FILES[@]} -eq 1 ]] || die "Multiple dossiers match $TASK_ID: ${FILES[*]}"

DOSSIER="${FILES[0]}"
FILENAME="$(basename "$DOSSIER")"

OLD_STATE=$(awk '/^---$/{c++; next} c==1 && /^state:/ {print $2; exit}' "$DOSSIER")
[[ -n "$OLD_STATE" ]] || die "Could not parse state from $DOSSIER"

green "  ✓ Found: $FILENAME (state: $OLD_STATE)"

# ─── Step 2: Validate transition ─────────────────────────────────
blue "🚦 Step 2/4: Validating transition $OLD_STATE → $NEW_STATE..."

declare -A VALID=(
  [drafted_dispatched]=1   [drafted_blocked]=1
  [dispatched_returned]=1  [dispatched_blocked]=1
  [returned_verified]=1    [returned_blocked]=1
  [verified_blocked]=1
  [blocked_reopened]=1
  [reopened_dispatched]=1  [reopened_returned]=1
)

KEY="${OLD_STATE}_${NEW_STATE}"
if [[ -z "${VALID[$KEY]:-}" ]]; then
  die "Invalid transition: $OLD_STATE → $NEW_STATE. See $0 --help."
fi

green "  ✓ Transition allowed"

# ─── Step 3: Update YAML in dossier ──────────────────────────────
blue "✏️  Step 3/4: Updating dossier YAML..."

NOW="$(date +'%Y-%m-%d %H:%M')"
TMP="$DOSSIER.tmp.$$"

awk -v new="$NEW_STATE" -v now="$NOW" '
  /^---$/ { c++; print; next }
  c==1 && /^state:/ { print "state: " new; next }
  c==1 && /^updated:/ { print "updated: " now; next }
  { print }
' "$DOSSIER" > "$TMP"
mv "$TMP" "$DOSSIER"

# Append change log entry
cat >> "$DOSSIER" <<EOF
- **$NOW**: State $OLD_STATE → $NEW_STATE${REASON:+ (reason: $REASON)}
EOF

green "  ✓ YAML updated + change log appended"

# ─── Step 4: Update STATUS.md ────────────────────────────────────
blue "📊 Step 4/4: Updating STATUS.md..."

# Helper: find row line number for this task in STATUS.md
ROW_LINE=$(grep -n "^| \`$TASK_ID\` " "$STATUS_FILE" | head -1 | cut -d: -f1 || true)
ROW_CONTENT=""
[[ -n "$ROW_LINE" ]] && ROW_CONTENT=$(sed -n "${ROW_LINE}p" "$STATUS_FILE")

case "$NEW_STATE" in
  dispatched|returned)
    # Stay in §I, just update state cell (3rd column between |)
    if [[ -n "$ROW_LINE" ]]; then
      TMP="$STATUS_FILE.tmp.$$"
      awk -v ln="$ROW_LINE" -v new="$NEW_STATE" '
        NR==ln {
          n=split($0, parts, "|")
          # parts[1]="", parts[2]=" `T-...`", parts[3]=" owner", parts[4]=" state",...
          parts[4] = " " new " "
          out=""
          for (i=2; i<=n; i++) out = out "|" parts[i]
          print out
          next
        }
        { print }
      ' "$STATUS_FILE" > "$TMP"
      mv "$TMP" "$STATUS_FILE"
      green "  ✓ §I row updated: state cell → $NEW_STATE"
    else
      yellow "  ⚠ Row not found in STATUS.md (skipped — may already be elsewhere)"
    fi
    ;;

  verified)
    # Move from §I to §II
    if [[ -n "$ROW_LINE" ]]; then
      # Convert §I row format → §II row format
      # §I: | ID | Owner | State | Scope | Dossier | Gates | Notes |
      # §II: | Task ID | Owner | Session | Heartbeat | State | Priority | Scope | Dossier |
      OWNER_CELL=$(echo "$ROW_CONTENT" | awk -F'|' '{print $3}' | xargs)
      PRIO_CELL=$(echo "$ROW_CONTENT" | awk -F'|' '{print $5}' | xargs)
      SCOPE_CELL=$(echo "$ROW_CONTENT" | awk -F'|' '{print $6}' | xargs)
      DOSSIER_CELL=$(echo "$ROW_CONTENT" | awk -F'|' '{print $7}' | xargs)

      ISO_NOW="$(date +%Y-%m-%d)"
      NEW_ROW_S2="| \`$TASK_ID\` | $OWNER_CELL | dogfood | $ISO_NOW | verified | $PRIO_CELL | $SCOPE_CELL | $DOSSIER_CELL |"

      TMP="$STATUS_FILE.tmp.$$"
      awk -v ln="$ROW_LINE" -v new_row="$NEW_ROW_S2" '
        # Delete row from §I (line ROW_LINE) and inject into §II
        NR==ln { skip=1; next }
        /^## II\./ { print; in_s2=1; next }
        in_s2 && /^\| None / { print new_row; in_s2=0; next }
        in_s2 && /^## III\./ { print new_row; print ""; print; in_s2=0; next }
        { print }
      ' "$STATUS_FILE" > "$TMP"
      mv "$TMP" "$STATUS_FILE"
      green "  ✓ Moved §I → §II"
    else
      yellow "  ⚠ Row not found in §I"
    fi
    ;;

  blocked)
    # Move to §III (Blocked)
    if [[ -n "$ROW_LINE" ]]; then
      OWNER_CELL=$(echo "$ROW_CONTENT" | awk -F'|' '{print $3}' | xargs)
      PRIO_CELL=$(echo "$ROW_CONTENT" | awk -F'|' '{print $5}' | xargs)
      ISO_NOW="$(date +%Y-%m-%d)"
      REASON_TEXT="${REASON:-unspecified}"

      NEW_ROW_S3="| \`$TASK_ID\` | $OWNER_CELL | dogfood | $ISO_NOW | blocked | $PRIO_CELL | $REASON_TEXT | needs orchestrator triage |"

      TMP="$STATUS_FILE.tmp.$$"
      awk -v ln="$ROW_LINE" -v new_row="$NEW_ROW_S3" '
        NR==ln { skip=1; next }
        /^## III\./ { print; in_s3=1; next }
        in_s3 && /^\| None / { print new_row; in_s3=0; next }
        in_s3 && /^---$/ { print new_row; print; in_s3=0; next }
        in_s3 && /^## IV\./ { print new_row; print ""; print; in_s3=0; next }
        { print }
      ' "$STATUS_FILE" > "$TMP"
      mv "$TMP" "$STATUS_FILE"
      green "  ✓ Moved → §III Blocked${REASON:+ (reason: $REASON)}"
    fi
    ;;

  reopened)
    yellow "  ⚠ State 'reopened' — manual STATUS edit recommended (move back to §I)"
    ;;
esac

# ─── Refresh lock heartbeat ──────────────────────────────────────
LOCK_FILE="$LOCKS_DIR/${TASK_ID}.lock"
if [[ -f "$LOCK_FILE" ]]; then
  touch "$LOCK_FILE"
  green "  ✓ Lock heartbeat refreshed"
else
  yellow "  ⚠ No lock at $LOCK_FILE (skipped touch)"
fi

# ─── Done ────────────────────────────────────────────────────────
green ""
green "✅ $TASK_ID: $OLD_STATE → $NEW_STATE${REASON:+ (reason: $REASON)}"
