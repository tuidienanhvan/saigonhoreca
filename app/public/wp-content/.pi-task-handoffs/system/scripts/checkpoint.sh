#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# checkpoint.sh — snapshot a task's state into system/checkpoints/ (v1.2)
# (reconstructed 2026-06-03 after data-loss incident — verify against usage)
# ──────────────────────────────────────────────────────────────────
# Saves a timestamped, immutable copy of a task's dossier (+ lock + return
# if present) so progress can be restored / diffed if a worker corrupts it.
# Each checkpoint lives in:  system/checkpoints/<TASK_ID>/<UTC-timestamp>/
#
# Usage:
#   bash system/scripts/checkpoint.sh <TASK_ID>            # create checkpoint
#   bash system/scripts/checkpoint.sh <TASK_ID> --note "before risky edit"
#   bash system/scripts/checkpoint.sh <TASK_ID> --list     # list checkpoints
#   bash system/scripts/checkpoint.sh <TASK_ID> --restore <timestamp>
# ──────────────────────────────────────────────────────────────────

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
ACTIVE_DIR="$HANDOFFS_DIR/active"
LOCKS_DIR="$HANDOFFS_DIR/system/locks"
RETURNS_DIR="$HANDOFFS_DIR/returns"
CHECKPOINTS_DIR="$HANDOFFS_DIR/system/checkpoints"

# (v1.3) dual-mode dossier resolver (folder model + legacy file model)
source "$SCRIPT_DIR/lib/dossier-lib.sh"

red()    { printf '\033[31m%s\033[0m\n' "$*"; }
green()  { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
blue()   { printf '\033[34m%s\033[0m\n' "$*"; }
die()    { red "❌ $*" >&2; exit 1; }

# ─── Parse args ───────────────────────────────────────────────────
TASK_ID=""; ACTION="create"; NOTE=""; RESTORE_TS=""
while [[ $# -gt 0 ]]; do
  case "$1" in
    --list)    ACTION="list"; shift ;;
    --restore) ACTION="restore"; RESTORE_TS="${2:-}"; shift 2 ;;
    --note)    NOTE="${2:-}"; shift 2 ;;
    --help|-h) sed -n '2,20p' "${BASH_SOURCE[0]}" | sed 's/^# \?//'; exit 0 ;;
    T-*)       TASK_ID="$1"; shift ;;
    *)         die "Unknown arg: $1" ;;
  esac
done

[[ -n "$TASK_ID" ]] || die "Usage: $0 <TASK_ID> [--note ...|--list|--restore <ts>]"
[[ "$TASK_ID" =~ ^T-[0-9]{8}-[0-9]+[A-Z]?(-[0-9]+)?$ ]] || die "Invalid task ID: $TASK_ID"

TASK_CP_DIR="$CHECKPOINTS_DIR/$TASK_ID"

# ─── List mode ────────────────────────────────────────────────────
if [[ "$ACTION" == "list" ]]; then
  blue "Checkpoints for $TASK_ID:"
  if [[ -d "$TASK_CP_DIR" ]]; then
    found=0
    for cp in "$TASK_CP_DIR"/*/; do
      [[ -d "$cp" ]] || continue
      ts="$(basename "$cp")"
      note=""
      [[ -f "${cp}NOTE.txt" ]] && note="  — $(cat "${cp}NOTE.txt")"
      echo "  • $ts$note"
      found=1
    done
    [[ $found -eq 1 ]] || yellow "  (no checkpoints yet)"
  else
    yellow "  (no checkpoints yet)"
  fi
  exit 0
fi

# ─── Locate dossier (dual-mode; active then archive) ──────────────
DOSSIER="$(resolve_dossier "$TASK_ID" 2>/dev/null || true)"
[[ -n "$DOSSIER" ]] || die "No dossier matching $TASK_ID in active/ or archive/"
LOCK="$LOCKS_DIR/${TASK_ID}.lock"

# ─── Restore mode ─────────────────────────────────────────────────
if [[ "$ACTION" == "restore" ]]; then
  [[ -n "$RESTORE_TS" ]] || die "--restore needs a <timestamp> (see --list)"
  SRC="$TASK_CP_DIR/$RESTORE_TS"
  [[ -d "$SRC" ]] || die "Checkpoint not found: $SRC"
  [[ -f "$SRC/dossier.md" ]] || die "Checkpoint has no dossier.md: $SRC"
  cp "$SRC/dossier.md" "$DOSSIER"
  green "✓ Restored dossier for $TASK_ID from checkpoint $RESTORE_TS"
  [[ -f "$SRC/lock" ]] && { cp "$SRC/lock" "$LOCK"; green "✓ Restored lock"; }
  exit 0
fi

# ─── Create mode ──────────────────────────────────────────────────
TS="$(date -u +%Y%m%dT%H%M%SZ)"
DEST="$TASK_CP_DIR/$TS"
mkdir -p "$DEST"

cp "$DOSSIER" "$DEST/dossier.md"
green "✓ Saved dossier → $DEST/dossier.md"

if [[ -f "$LOCK" ]]; then
  cp "$LOCK" "$DEST/lock"
  green "✓ Saved lock"
fi

RETURN="$RETURNS_DIR/${TASK_ID}-return.md"
if [[ -f "$RETURN" ]]; then
  cp "$RETURN" "$DEST/return.md"
  green "✓ Saved return.md"
fi

# Metadata
{
  echo "task_id: $TASK_ID"
  echo "checkpoint: $TS"
  echo "source_dossier: ${DOSSIER#$HANDOFFS_DIR/}"
  echo "created_by: $(whoami 2>/dev/null || echo unknown)"
  echo "created_at: $(date -Iseconds 2>/dev/null || date)"
} > "$DEST/META.txt"

[[ -n "$NOTE" ]] && { echo "$NOTE" > "$DEST/NOTE.txt"; green "✓ Saved note"; }

green ""
green "✅ Checkpoint created: $DEST"
echo "  Restore with: bash system/scripts/checkpoint.sh $TASK_ID --restore $TS"
