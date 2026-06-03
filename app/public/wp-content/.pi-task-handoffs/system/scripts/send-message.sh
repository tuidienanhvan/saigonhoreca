#!/usr/bin/env bash
# send-message.sh — Inter-agent message queue (inbox primitive).
# Inspired by AWS CAO send_message + madebyaris/agent-orchestration shared memory.
#
# Usage:
#   bash system/scripts/send-message.sh --to <agent> --from <agent> --subject "..." --body "..."
#   bash system/scripts/send-message.sh --to gemini --task T-20260528-001 --type notice --body "Phase B started, ETA 4h"
#   bash system/scripts/send-message.sh --inbox <agent>    # read inbox
#   bash system/scripts/send-message.sh --inbox <agent> --mark-read

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
INBOX_DIR="$HANDOFFS_DIR/system/inbox"
ROSTER="$HANDOFFS_DIR/system/__roster.json"

mkdir -p "$INBOX_DIR"

# ─── Parse args ───────────────────────────────────────────────────
TO=""; FROM=""; SUBJECT=""; BODY=""; TASK=""; TYPE="notice"
READ_INBOX=""; MARK_READ=false

while [[ $# -gt 0 ]]; do
    case "$1" in
        --to)        TO="$2"; shift 2 ;;
        --from)      FROM="$2"; shift 2 ;;
        --subject)   SUBJECT="$2"; shift 2 ;;
        --body)      BODY="$2"; shift 2 ;;
        --task)      TASK="$2"; shift 2 ;;
        --type)      TYPE="$2"; shift 2 ;;  # notice | request | response | broadcast
        --inbox)     READ_INBOX="$2"; shift 2 ;;
        --mark-read) MARK_READ=true; shift ;;
        --help|-h)
            grep '^#' "$0" | head -10
            exit 0 ;;
        *) echo "Unknown: $1"; exit 1 ;;
    esac
done

# Color helpers
green() { printf '\033[32m%s\033[0m\n' "$*"; }
blue()  { printf '\033[34m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
red()   { printf '\033[31m%s\033[0m\n' "$*"; }

# Validate agent name against roster
validate_agent() {
    local agent="$1"
    [[ "$agent" == "user" || "$agent" == "orchestrator" || "$agent" == "broadcast" ]] && return 0
    grep -q "\"$agent\":" "$ROSTER" 2>/dev/null || {
        red "✗ Agent '$agent' not in __roster.json"
        exit 1
    }
}

# ─── Read inbox mode ──────────────────────────────────────────────
if [[ -n "$READ_INBOX" ]]; then
    validate_agent "$READ_INBOX"
    INBOX_FILE="$INBOX_DIR/${READ_INBOX}.md"

    if [[ ! -f "$INBOX_FILE" ]]; then
        blue "Inbox for '$READ_INBOX' is empty."
        exit 0
    fi

    if [[ "$MARK_READ" == "true" ]]; then
        # Move to archive
        ARCHIVE="$INBOX_DIR/.archive"
        mkdir -p "$ARCHIVE"
        mv "$INBOX_FILE" "$ARCHIVE/${READ_INBOX}-$(date +%Y%m%dT%H%M%S).md"
        green "✓ Inbox archived: $ARCHIVE/"
    else
        blue "Inbox: $READ_INBOX"
        echo "─────────────────────────────────────────────"
        cat "$INBOX_FILE"
    fi
    exit 0
fi

# ─── Send mode ────────────────────────────────────────────────────
[[ -z "$TO" ]] && { red "--to required"; exit 1; }
[[ -z "$BODY" ]] && { red "--body required"; exit 1; }
FROM="${FROM:-orchestrator}"
SUBJECT="${SUBJECT:-${TYPE^} message}"

validate_agent "$TO"
validate_agent "$FROM"

ISO_NOW="$(date +%Y-%m-%dT%H:%M:%S)"

# Type icons
case "$TYPE" in
    notice)    ICON="" ;;
    request)   ICON="" ;;
    response)  ICON="" ;;
    broadcast) ICON="" ;;
    *)         ICON="" ;;
esac

# Recipients (broadcast goes to all agents)
if [[ "$TO" == "broadcast" ]]; then
    RECIPIENTS=$(grep -oE '"[a-z]+":\s*\{' "$ROSTER" | grep -oE '"[a-z]+"' | tr -d '"' | grep -v "^orchestrator$")
else
    RECIPIENTS="$TO"
fi

for recipient in $RECIPIENTS; do
    INBOX_FILE="$INBOX_DIR/${recipient}.md"

    # Initialize inbox file if not exists
    if [[ ! -f "$INBOX_FILE" ]]; then
        cat > "$INBOX_FILE" <<EOF
# Inbox — $recipient

> Append-only message queue. Mark read: \`bash system/scripts/send-message.sh --inbox $recipient --mark-read\`

EOF
    fi

    # Append message
    cat >> "$INBOX_FILE" <<EOF
---

## $ICON $SUBJECT
**From**: $FROM
**Sent**: $ISO_NOW
**Type**: $TYPE
${TASK:+**Task**: \`$TASK\`}

$BODY

EOF

    green "✓ Sent to $recipient: $SUBJECT"
done
