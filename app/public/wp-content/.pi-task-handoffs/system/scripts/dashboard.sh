#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# dashboard.sh — print a live status dashboard (read-only) (v1.2)
# (reconstructed 2026-06-03 after data-loss incident — verify against usage)
# ──────────────────────────────────────────────────────────────────
# Summarizes the current handoff state by scanning active/ dossiers
# (dual-mode: folder + legacy) and STATUS.md. Shows:
#   • tasks grouped by state (with priority + owner)
#   • lock heartbeat freshness per active task
#   • totals
# Read-only. Used standalone and by watch.sh.
#
# Usage:
#   bash system/scripts/dashboard.sh
#   bash system/scripts/dashboard.sh --no-clear   # don't clear screen (for watch)
# ──────────────────────────────────────────────────────────────────

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
ACTIVE_DIR="$HANDOFFS_DIR/active"
LOCKS_DIR="$HANDOFFS_DIR/system/locks"
STATUS_FILE="$HANDOFFS_DIR/STATUS.md"

# (v1.3) dual-mode dossier resolver (folder model + legacy file model)
source "$SCRIPT_DIR/lib/dossier-lib.sh"

red()    { printf '\033[31m%s\033[0m\n' "$*"; }
green()  { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
blue()   { printf '\033[34m%s\033[0m\n' "$*"; }
bold()   { printf '\033[1m%s\033[0m\n' "$*"; }
dim()    { printf '\033[2m%s\033[0m\n' "$*"; }

NO_CLEAR=0
for arg in "$@"; do
  case "$arg" in
    --no-clear) NO_CLEAR=1 ;;
    --help|-h)  sed -n '2,18p' "${BASH_SOURCE[0]}" | sed 's/^# \?//'; exit 0 ;;
  esac
done

field() {
  awk '/^---$/{c++; next} c==1' "$2" 2>/dev/null \
    | grep -m1 "^$1:" \
    | sed "s/^$1:[[:space:]]*//; s/[[:space:]]*#.*$//; s/[[:space:]]*$//" || true
}

lock_status() {
  local id="$1" lock="$LOCKS_DIR/$1.lock"
  if [[ ! -f "$lock" ]]; then printf '%s' "—"; return; fi
  local mtime now age
  mtime=$(stat -c %Y "$lock" 2>/dev/null || stat -f %m "$lock" 2>/dev/null || echo 0)
  now=$(date +%s)
  age=$((now - mtime))
  if   [[ $age -lt 120 ]]; then printf '\033[32mfresh %ss\033[0m' "$age"
  elif [[ $age -lt 180 ]]; then printf '\033[33mwarn %ss\033[0m'  "$age"
  else                          printf '\033[31mSTALE %ss\033[0m' "$age"
  fi
}

[[ $NO_CLEAR -eq 1 ]] || clear 2>/dev/null || true

# ─── Header ───────────────────────────────────────────────────────
bold "══════════════════════════════════════════════════════════════"
bold " PI TASK HANDOFFS — DASHBOARD"
bold "══════════════════════════════════════════════════════════════"
echo "  Workspace : $(basename "$(cd "$HANDOFFS_DIR/.." && pwd)")"
echo "  Updated   : $(date +'%Y-%m-%d %H:%M:%S')"
if [[ -f "$STATUS_FILE" ]]; then
  SVER=$(grep -m1 -i 'System Version' "$STATUS_FILE" | sed 's/.*: *//; s/\*\*//g' || true)
  [[ -n "$SVER" ]] && echo "  Version   : $SVER"
fi
echo ""

# ─── Collect active tasks ─────────────────────────────────────────
declare -A STATE_COUNT
TOTAL=0
ROWS=()
while IFS= read -r dossier; do
  [[ -n "$dossier" ]] || continue
  id="$(task_basename "$dossier" | grep -oE '^T-[0-9]{8}-[0-9]+[A-Z]?' || true)"
  [[ -n "$id" ]] || continue
  st="$(field state "$dossier")";      [[ -n "$st" ]] || st="(unknown)"
  ow="$(field owner "$dossier")";      [[ -n "$ow" ]] || ow="?"
  pr="$(field priority "$dossier")";   [[ -n "$pr" ]] || pr="--"
  STATE_COUNT["$st"]=$(( ${STATE_COUNT["$st"]:-0} + 1 ))
  TOTAL=$((TOTAL + 1))
  ROWS+=("$st|$pr|$id|$ow|$dossier")
done < <(list_dossiers "$ACTIVE_DIR")

# ─── State summary ────────────────────────────────────────────────
blue "── State Summary ──────────────────────────────────────────────"
if [[ $TOTAL -eq 0 ]]; then
  dim "  (no active tasks)"
else
  for st in $(printf '%s\n' "${!STATE_COUNT[@]}" | sort); do
    printf "  %-14s %s\n" "$st" "${STATE_COUNT[$st]}"
  done
  echo "  ──────────────"
  printf "  %-14s %s\n" "TOTAL" "$TOTAL"
fi
echo ""

# ─── Task table (grouped by state) ────────────────────────────────
if [[ $TOTAL -gt 0 ]]; then
  blue "── Active Tasks ───────────────────────────────────────────────"
  printf "  %-11s %-5s %-20s %-10s %s\n" "STATE" "PRIO" "ID" "OWNER" "LOCK"
  printf "  %s\n" "────────────────────────────────────────────────────────────"
  printf '%s\n' "${ROWS[@]}" | sort | while IFS='|' read -r st pr id ow dossier; do
    printf "  %-11s %-5s %-20s %-10s " "$st" "$pr" "$id" "$ow"
    lock_status "$id"
    printf "\n"
  done
  echo ""
fi

bold "══════════════════════════════════════════════════════════════"
green " $TOTAL active task(s).  (read-only)"
bold "══════════════════════════════════════════════════════════════"
