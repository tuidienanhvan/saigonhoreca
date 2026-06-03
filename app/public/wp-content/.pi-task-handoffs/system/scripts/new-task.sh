#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# new-task.sh — Atomic Phase A: create dossier + lock + STATUS row (v4.3)
# ──────────────────────────────────────────────────────────────────
# Pure bash + awk + sed (Git-Bash compatible).
#
# Usage:
#   bash system/scripts/new-task.sh \
#     --owner codex \
#     --priority P2 \
#     --risk medium \
#     --slug refactor-store-admin \
#     --description "Refactor store admin pages to feature-slice"
#
#   Optional: --estimated-minutes 60 (default 30)
#   Optional: --intent "<user message verbatim>" (Phase 0 capture)
#
# Atomic steps (rollback on any failure):
#   1. Validate flags + owner exists in __roster.json
#   2. Auto-generate task ID (max NNN of today + 1)
#   3. Copy template → active/T-...-<owner>-<slug>.md
#   4. Fill frontmatter (sed-replace placeholders)
#   5. Inject Phase 0 user intent block
#   6. Create lock file
#   7. Append STATUS.md §I row
#   8. Print dossier path
# Exit codes: 0=ok, 1=runtime fail, 2=usage error
# ──────────────────────────────────────────────────────────────────

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
ACTIVE_DIR="$HANDOFFS_DIR/active"
ARCHIVE_DIR="$HANDOFFS_DIR/archive"
TEMPLATE="$HANDOFFS_DIR/system/templates/TASK.md"
STATUS_FILE="$HANDOFFS_DIR/STATUS.md"
ROSTER="$HANDOFFS_DIR/system/__roster.json"
LOCKS_DIR="$HANDOFFS_DIR/system/locks"

red()    { printf '\033[31m%s\033[0m\n' "$*"; }
green()  { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
blue()   { printf '\033[34m%s\033[0m\n' "$*"; }

die() { red "❌ $*" >&2; exit 1; }
usage_err() { red "❌ $*" >&2; print_usage; exit 2; }

print_usage() {
  cat <<EOF
Usage: $0 --owner <agent> --priority <P0..P3> --risk <cosmetic|low|medium|high|critical> \\
          --slug <kebab-case> --description "<scope summary>" \\
          [--estimated-minutes <N>] [--intent "<phase 0 user msg>"]

Example:
  $0 --owner codex --priority P2 --risk medium \\
     --slug refactor-store-admin \\
     --description "Refactor store admin pages to feature-slice"
EOF
}

# ─── Parse args ──────────────────────────────────────────────────
OWNER=""
PRIORITY=""
RISK=""
SLUG=""
DESCRIPTION=""
EST_MIN="30"
INTENT=""

while [[ $# -gt 0 ]]; do
  case "$1" in
    --owner)              OWNER="$2"; shift 2 ;;
    --priority)           PRIORITY="$2"; shift 2 ;;
    --risk)               RISK="$2"; shift 2 ;;
    --slug)               SLUG="$2"; shift 2 ;;
    --description)        DESCRIPTION="$2"; shift 2 ;;
    --estimated-minutes)  EST_MIN="$2"; shift 2 ;;
    --intent)             INTENT="$2"; shift 2 ;;
    --help|-h)            print_usage; exit 0 ;;
    *)                    usage_err "Unknown flag: $1" ;;
  esac
done

# ─── Validation ──────────────────────────────────────────────────
[[ -n "$OWNER" ]]       || usage_err "Missing --owner"
[[ -n "$PRIORITY" ]]    || usage_err "Missing --priority"
[[ -n "$RISK" ]]        || usage_err "Missing --risk"
[[ -n "$SLUG" ]]        || usage_err "Missing --slug"
[[ -n "$DESCRIPTION" ]] || usage_err "Missing --description"

[[ "$PRIORITY" =~ ^P[0-3]$ ]] || usage_err "Invalid priority: $PRIORITY (expected P0|P1|P2|P3)"
[[ "$RISK" =~ ^(cosmetic|low|medium|high|critical)$ ]] || usage_err "Invalid risk: $RISK"
[[ "$SLUG" =~ ^[a-z0-9]+(-[a-z0-9]+)*$ ]] || usage_err "Invalid slug: $SLUG (must be kebab-case)"
[[ "$EST_MIN" =~ ^[0-9]+$ ]] || usage_err "Invalid --estimated-minutes: $EST_MIN"

# Owner must exist in __roster.json
if [[ -f "$ROSTER" ]]; then
  if ! grep -q "\"$OWNER\"" "$ROSTER"; then
    # Check aliases too
    if ! grep -E "\"aliases\".*\"$OWNER\"" "$ROSTER" >/dev/null; then
      die "Owner '$OWNER' not found in __roster.json. Run: cat $ROSTER | grep -A1 agents"
    fi
  fi
fi

# Template exists
[[ -f "$TEMPLATE" ]] || die "Template missing: $TEMPLATE"

# ─── Step 1: Auto-generate ID ────────────────────────────────────
TODAY="$(date +%Y%m%d)"
CURRENT_MONTH="$(date +%Y-%m)"

blue "🆔 Step 1/7: Generating task ID for $TODAY..."

# Scan active/ + archive/$CURRENT_MONTH/ for max NNN of today
MAX_NNN=0
shopt -s nullglob
for f in "$ACTIVE_DIR"/T-${TODAY}-*.md "$ARCHIVE_DIR/$CURRENT_MONTH"/T-${TODAY}-*.md; do
  base="$(basename "$f")"
  nnn=$(echo "$base" | sed -n 's/^T-[0-9]\{8\}-\([0-9]\+\).*/\1/p')
  [[ -n "$nnn" ]] || continue
  nnn=$((10#$nnn))
  (( nnn > MAX_NNN )) && MAX_NNN=$nnn
done
shopt -u nullglob

NEXT_NNN=$((MAX_NNN + 1))
TASK_ID="T-${TODAY}-$(printf '%03d' $NEXT_NNN)"
FILENAME="${TASK_ID}-${OWNER}-${SLUG}.md"
DOSSIER="$ACTIVE_DIR/$FILENAME"
LOCK_FILE="$LOCKS_DIR/${TASK_ID}.lock"

[[ ! -f "$DOSSIER" ]] || die "Dossier already exists: $DOSSIER"
[[ ! -f "$LOCK_FILE" ]] || die "Lock already exists: $LOCK_FILE"

green "  ✓ ID: $TASK_ID  →  $FILENAME"

# ─── Step 2: Copy template ───────────────────────────────────────
blue "📄 Step 2/7: Copying template..."
cp "$TEMPLATE" "$DOSSIER"
green "  ✓ Copied to active/"

# ─── Step 3: Fill frontmatter ────────────────────────────────────
blue "✏️  Step 3/7: Filling frontmatter..."
NOW="$(date +'%Y-%m-%d %H:%M')"
TODAY_DASH="$(date +%Y-%m-%d)"

# Detect agent's escalation_path from roster (simple fallback)
ESC_PATH="[Codex, Claude]"
if [[ "$OWNER" == "claude" ]]; then ESC_PATH="[Codex, Gemini]"; fi
if [[ "$OWNER" == "codex" ]]; then ESC_PATH="[Claude, Gemini]"; fi
if [[ "$OWNER" == "gemini" ]]; then ESC_PATH="[Claude, Codex]"; fi
if [[ "$OWNER" == "chatgpt" ]]; then ESC_PATH="[Claude, Codex]"; fi
if [[ "$OWNER" == "gemma" ]]; then ESC_PATH="[Codex, Claude]"; fi

# Atomic sed: write to .tmp then mv
# Use ~ as delimiter for last line (contains pipes |); | for others
TMP="$DOSSIER.tmp.$$"
sed -e "s|^id: T-YYYYMMDD-XXX|id: $TASK_ID|" \
    -e "s|^owner: <agent-name>|owner: $OWNER|" \
    -e "s|^priority: P2|priority: $PRIORITY|" \
    -e "s|^risk: low|risk: $RISK|" \
    -e "s|^estimated_minutes: 30|estimated_minutes: $EST_MIN|" \
    -e "s|^escalation_path: \[Codex, Claude\]|escalation_path: $ESC_PATH|" \
    -e "s|^created: YYYY-MM-DD HH:MM|created: $NOW|" \
    -e "s|^updated: YYYY-MM-DD HH:MM|updated: $NOW|" \
    -e "s~^# 📋 T-YYYYMMDD-XXX | <agent> | <slug>~# 📋 $TASK_ID | $OWNER | $SLUG~" \
    "$DOSSIER" > "$TMP"
mv "$TMP" "$DOSSIER"

green "  ✓ Frontmatter filled"

# ─── Step 4: Inject Phase 0 user intent block ────────────────────
blue "📥 Step 4/7: Injecting Phase 0 user intent..."

if [[ -n "$INTENT" ]]; then
  PHASE0_BODY="$INTENT"
else
  PHASE0_BODY="<!-- Paste user message verbatim here. -->"
fi

# Insert "## 0. 📥 User Original Intent" block after frontmatter (line ending ---).
# Find the second ---  line, insert block after it.
TMP="$DOSSIER.tmp.$$"
awk -v body="$PHASE0_BODY" '
  /^---$/ { c++; print; if (c==2) {
    print "";
    print "## 0. 📥 User Original Intent (Phase 0 — Verbatim)";
    print "";
    print "> " body;
    print "";
  }; next }
  { print }
' "$DOSSIER" > "$TMP"
mv "$TMP" "$DOSSIER"

green "  ✓ Phase 0 stub injected"

# ─── Step 5: Create lock file ────────────────────────────────────
blue "🔒 Step 5/7: Acquiring lock..."
mkdir -p "$LOCKS_DIR"
SESSION_ID="$(date +%s)-$$"
ISO_NOW="$(date -Iseconds 2>/dev/null || date +'%Y-%m-%dT%H:%M:%S%z')"

cat > "$LOCK_FILE" <<EOF
task_id: $TASK_ID
agent: $OWNER
model: $(grep -A1 "\"$OWNER\"" "$ROSTER" 2>/dev/null | grep '"model"' | head -1 | sed 's/.*"model": *"\([^"]*\)".*/\1/' || echo 'unknown')
session_id: $SESSION_ID
started_at: $ISO_NOW
last_heartbeat: $ISO_NOW
pid: $$
EOF

green "  ✓ Lock: system/locks/${TASK_ID}.lock"

# ─── Step 6: Append STATUS.md §I row ─────────────────────────────
blue "📊 Step 6/7: Adding STATUS §I row..."

# Build emoji per agent
case "$OWNER" in
  claude)         OWNER_EMOJI="🤖" ;;
  codex)          OWNER_EMOJI="🔧" ;;
  gemini)         OWNER_EMOJI="🚀" ;;
  chatgpt)        OWNER_EMOJI="💬" ;;
  gemma)          OWNER_EMOJI="🦙" ;;
  *)              OWNER_EMOJI="🤖" ;;
esac

# Map priority to emoji-prefix
case "$PRIORITY" in
  P0) PRIO_EMOJI="🔴 P0 CRITICAL" ;;
  P1) PRIO_EMOJI="🔴 P1 HIGH" ;;
  P2) PRIO_EMOJI="🟡 P2 MEDIUM" ;;
  P3) PRIO_EMOJI="🔵 P3 LOW" ;;
esac

NEW_ROW="| \`$TASK_ID\` | $OWNER_EMOJI $OWNER | drafted | $PRIO_EMOJI | $DESCRIPTION | [Dossier](active/$FILENAME) | pending | (new) |"

# Insert at end of §I table — before first blank line or None placeholder after rows
TMP="$STATUS_FILE.tmp.$$"
awk -v row="$NEW_ROW" '
  /^## I\./ && !in_s1 { in_s1=1; print; next }
  in_s1 && /^\| None / { print row; inserted=1; in_s1=0; next }
  in_s1 && /^[[:space:]]*$/ {
    if (!inserted) { print row; inserted=1 }
    in_s1=0; print; next
  }
  in_s1 && /^## II\./ {
    if (!inserted) { print row; inserted=1 }
    in_s1=0; print; next
  }
  { print }
' "$STATUS_FILE" > "$TMP"
mv "$TMP" "$STATUS_FILE"

green "  ✓ STATUS §I row appended"

# ─── Step 7: Done ────────────────────────────────────────────────
green ""
green "✅ Task created successfully"
green ""
echo "  ID:       $TASK_ID"
echo "  Owner:    $OWNER"
echo "  Dossier:  active/$FILENAME"
echo "  Lock:     system/locks/${TASK_ID}.lock"
echo ""
yellow "Next steps:"
echo "  1. Edit dossier to fill §II Goal, §IV Allowed Scope, §VII Verification Commands"
echo "  2. Generate worker prompt §IX (or self-implement if owner=claude)"
echo "  3. bash system/scripts/set-state.sh $TASK_ID dispatched   # when handing off"
