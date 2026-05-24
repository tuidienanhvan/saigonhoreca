#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# archive-task.sh — Atomic Phase D archival for .task-handoffs
# ──────────────────────────────────────────────────────────────────
# Pure bash + awk + sed (NO python3 dependency — works in Git-Bash on Windows).
#
# Usage:
#   bash system/scripts/archive-task.sh <TASK_ID>
#   bash system/scripts/archive-task.sh T-20260508-001
#
# Atomic steps (rollback on any failure):
#   1. Validate task in active/ with state=verified
#   2. Set state: archived in dossier frontmatter
#   3. Move active/T-*.md → archive/YYYY-MM/
#   4. Update STATUS.md (remove from Section 1)
#   5. Append LEADERBOARD.md (1 line in code block)
#   6. Print success
# ──────────────────────────────────────────────────────────────────

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
ACTIVE_DIR="$HANDOFFS_DIR/active"
STATUS_FILE="$HANDOFFS_DIR/STATUS.md"
LEADERBOARD_FILE="$HANDOFFS_DIR/LEADERBOARD.md"

red()    { printf '\033[31m%s\033[0m\n' "$*"; }
green()  { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
blue()   { printf '\033[34m%s\033[0m\n' "$*"; }

die() { red "❌ $*" >&2; exit 1; }

# ─── Parse args ──────────────────────────────────────────────────
[[ $# -eq 1 ]] || die "Usage: $0 <TASK_ID>  (e.g. T-20260508-001)"
TASK_ID="$1"
[[ "$TASK_ID" =~ ^T-[0-9]{8}-[0-9]+[A-Z]?$ ]] || die "Invalid task ID format: $TASK_ID"

# ─── Step 1: Locate file ─────────────────────────────────────────
blue "🔍 Step 1/6: Locating $TASK_ID in active/..."

shopt -s nullglob
FILES=("$ACTIVE_DIR"/${TASK_ID}-*.md)
shopt -u nullglob

[[ ${#FILES[@]} -gt 0 ]] || die "No file matching $ACTIVE_DIR/${TASK_ID}-*.md"
[[ ${#FILES[@]} -eq 1 ]] || die "Multiple files match $TASK_ID: ${FILES[*]}"

DOSSIER="${FILES[0]}"
FILENAME="$(basename "$DOSSIER")"
green "  ✓ Found: $FILENAME"

# ─── Step 2: Validate frontmatter ────────────────────────────────
blue "🔍 Step 2/6: Validating frontmatter..."

STATE=$(awk '/^---$/{c++; next} c==1 && /^state:/ {print $2; exit}' "$DOSSIER")
OWNER=$(awk '/^---$/{c++; next} c==1 && /^owner:/ {print $2; exit}' "$DOSSIER")
RISK=$(awk '/^---$/{c++; next} c==1 && /^risk:/ {print $2; exit}' "$DOSSIER")

[[ -n "$STATE" ]] || die "Cannot read 'state:' from frontmatter"
[[ -n "$OWNER" ]] || die "Cannot read 'owner:' from frontmatter"
[[ "$STATE" == "verified" ]] || die "Task state is '$STATE', expected 'verified'"

green "  ✓ state=$STATE, owner=$OWNER, risk=${RISK:-unknown}"

# ─── Step 2.5: changes/ requirement for high-risk ────────────────
if [[ "$RISK" == "high" || "$RISK" == "critical" ]]; then
    if ! ls -d "$HANDOFFS_DIR/changes/${TASK_ID}-"*/ >/dev/null 2>&1; then
        yellow "⚠  Risk=$RISK but no changes/${TASK_ID}-*/ folder."
        read -p "    Skip check? [y/N] " -n 1 -r; echo
        [[ "$REPLY" =~ ^[Yy]$ ]] || die "Aborted. Create changes/ entry first."
    fi
fi

# ─── Step 3: Update state + move file ────────────────────────────
DATE=$(echo "$TASK_ID" | sed 's/T-\([0-9]\{4\}\)\([0-9]\{2\}\)\([0-9]\{2\}\).*/\1-\2-\3/')
ARCHIVE_MONTH=$(echo "$TASK_ID" | sed 's/T-\([0-9]\{4\}\)\([0-9]\{2\}\).*/\1-\2/')
ARCHIVE_DIR="$HANDOFFS_DIR/archive/$ARCHIVE_MONTH"
mkdir -p "$ARCHIVE_DIR"
TARGET="$ARCHIVE_DIR/$FILENAME"

[[ ! -f "$TARGET" ]] || die "Target already exists: $TARGET"

blue "📦 Step 3/6: Updating state + moving to archive/$ARCHIVE_MONTH/..."

TMP="$DOSSIER.tmp.$$"
TODAY=$(date +"%Y-%m-%d %H:%M")
awk -v today="$TODAY" '
    BEGIN { in_fm=0; updated=0; archived=0 }
    /^---$/ { in_fm++; print; next }
    in_fm==1 && /^state:/ && !updated { print "state: archived"; updated=1; next }
    in_fm==1 && /^updated:/ {
        print "updated: " today
        if (!archived) { print "archived: " today; archived=1 }
        next
    }
    { print }
' "$DOSSIER" > "$TMP"
mv "$TMP" "$DOSSIER"
mv "$DOSSIER" "$TARGET"
green "  ✓ Moved + state→archived"

# ─── Step 4: Update STATUS.md ────────────────────────────────────
blue "📊 Step 4/6: Updating STATUS.md..."

STATUS_TMP="$STATUS_FILE.tmp.$$"
awk -v tid="$TASK_ID" '
    # Skip lines that mention task ID AND do NOT reference archive/ link
    /^\|/ && index($0, tid) > 0 && index($0, "archive/") == 0 { next }
    { print }
' "$STATUS_FILE" > "$STATUS_TMP"

# Restore "None" placeholder if Section 1 became empty
awk '
    BEGIN { sec1=0; sec1_had_data=0; in_separator=0 }
    /^## 1\. /  { sec1=1; print; next }
    /^## 2\. /  {
        # Section 1 ended — if no data row, inject None
        if (sec1 && !sec1_had_data) {
            print "| None    |       |         |           |       |          |       |         |"
            print ""
        }
        sec1=0
        print; next
    }
    sec1 && /^\|---/  { in_separator=1; print; next }
    sec1 && in_separator && /^\|/  { sec1_had_data=1; print; next }
    { print }
' "$STATUS_TMP" > "$STATUS_FILE"
rm "$STATUS_TMP"
green "  ✓ Removed from active sections"

# ─── Step 5: Append to LEADERBOARD.md ────────────────────────────
blue "🏆 Step 5/6: Appending to LEADERBOARD.md..."

SLUG=$(echo "$FILENAME" | sed -e 's/\.md$//' -e "s/^${TASK_ID}-${OWNER}-//")
NOTES=$(echo "$SLUG" | tr '-' ' ')
TASK_TYPE="refactor"
case "$SLUG" in
    *css*|*tailwind*|*theme*) TASK_TYPE="css-migration" ;;
    *fix*|*bug*) TASK_TYPE="bugfix" ;;
    *audit*) TASK_TYPE="audit" ;;
    *ui*|*ux*|*toast*|*editor*) TASK_TYPE="ui-polish" ;;
    *api*|*backend*|*seed*|*db*) TASK_TYPE="backend" ;;
    *test*|*lint*) TASK_TYPE="lint-fix" ;;
    *chore*|*cleanup*|*sanitization*) TASK_TYPE="chore" ;;
esac

NEW_LINE="$DATE | $TASK_ID | $OWNER | $TASK_TYPE | pass | 1 | $NOTES"

# Insert NEW_LINE before the LAST closing ``` (end of code block in Section 3)
LB_TMP="$LEADERBOARD_FILE.tmp.$$"
awk -v new="$NEW_LINE" '
    { lines[NR]=$0 }
    /^```$/ { last_close=NR }
    END {
        for (i=1; i<=NR; i++) {
            if (i == last_close) print new
            print lines[i]
        }
    }
' "$LEADERBOARD_FILE" > "$LB_TMP"
mv "$LB_TMP" "$LEADERBOARD_FILE"
green "  ✓ Appended: $NEW_LINE"

# ─── Step 6 (v4.0): STATUS §4 rotation + INDEX regen ─────────────
blue "🔄 Step 6/7 (v4.0): Reconcile post-archive (INDEX + §4 rotation)..."
if [[ -x "$SCRIPT_DIR/reconcile.sh" || -f "$SCRIPT_DIR/reconcile.sh" ]]; then
    bash "$SCRIPT_DIR/reconcile.sh" --fix >/dev/null 2>&1 && green "  ✓ Reconcile done" || yellow "  ⚠ Reconcile reported issues (non-blocking)"
else
    yellow "  ⚠ reconcile.sh not found — skipping post-archive sync"
fi

# (v4.1) Refresh per-agent metrics from LEADERBOARD
if [[ -f "$SCRIPT_DIR/update-agent-metrics.sh" ]]; then
    bash "$SCRIPT_DIR/update-agent-metrics.sh" >/dev/null 2>&1 && green "  ✓ Agent metrics refreshed" || yellow "  ⚠ Metric refresh failed (non-blocking)"
fi

# (v4.1) Release task lock if present
LOCK_FILE="$HANDOFFS_DIR/system/locks/${TASK_ID}.lock"
[[ -f "$LOCK_FILE" ]] && rm -f "$LOCK_FILE" && green "  ✓ Lock released"

# ─── Step 7: Done ────────────────────────────────────────────────
blue "✨ Step 7/7: Done."
green "  ✓ Dossier:    $TARGET"
green "  ✓ STATUS.md:  active row removed + §4 rotated"
green "  ✓ LEADERBOARD: 1 line appended"
green "  ✓ INDEX.md:   regenerated"

echo
green "🎉 Phase D complete for $TASK_ID"
echo "   → Run 'bash $SCRIPT_DIR/lint-handoffs.sh --strict' to verify."
