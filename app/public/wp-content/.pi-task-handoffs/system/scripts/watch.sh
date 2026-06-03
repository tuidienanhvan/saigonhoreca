#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# watch.sh — watch active/ for changes and re-print the dashboard (v1.2)
# (reconstructed 2026-06-03 after data-loss incident — verify against usage)
# ──────────────────────────────────────────────────────────────────
# Polls active/ (+ locks) for modifications and re-renders dashboard.sh
# whenever something changes (or every --interval seconds regardless).
# Read-only. Ctrl-C to quit.
#
# Usage:
#   bash system/scripts/watch.sh                 # poll every 5s
#   bash system/scripts/watch.sh --interval 10   # poll every 10s
#   bash system/scripts/watch.sh --once          # render once, no loop
# ──────────────────────────────────────────────────────────────────

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
ACTIVE_DIR="$HANDOFFS_DIR/active"
LOCKS_DIR="$HANDOFFS_DIR/system/locks"
DASHBOARD="$SCRIPT_DIR/dashboard.sh"

# (v1.3) dual-mode dossier resolver (folder model + legacy file model)
source "$SCRIPT_DIR/lib/dossier-lib.sh"

dim()  { printf '\033[2m%s\033[0m\n' "$*"; }
red()  { printf '\033[31m%s\033[0m\n' "$*"; }

INTERVAL=5
ONCE=0
while [[ $# -gt 0 ]]; do
  case "$1" in
    --interval) INTERVAL="${2:-5}"; shift 2 ;;
    --once)     ONCE=1; shift ;;
    --help|-h)  sed -n '2,18p' "${BASH_SOURCE[0]}" | sed 's/^# \?//'; exit 0 ;;
    *)          red "Unknown arg: $1" >&2; exit 1 ;;
  esac
done
[[ "$INTERVAL" =~ ^[0-9]+$ ]] || { red "--interval must be an integer"; exit 1; }
[[ -f "$DASHBOARD" ]] || { red "dashboard.sh not found at $DASHBOARD"; exit 1; }

# Signature of current active/ + locks state (mtimes + names).
state_sig() {
  {
    [[ -d "$ACTIVE_DIR" ]] && find "$ACTIVE_DIR" -type f \( -name '*.md' \) -printf '%p %T@\n' 2>/dev/null
    [[ -d "$LOCKS_DIR" ]]  && find "$LOCKS_DIR"  -type f -name '*.lock' -printf '%p %T@\n' 2>/dev/null
  } | sort | cksum | awk '{print $1}'
}

render() {
  clear 2>/dev/null || true
  bash "$DASHBOARD" --no-clear
  echo ""
  dim "  watching active/ every ${INTERVAL}s — Ctrl-C to quit  (last refresh: $(date +%H:%M:%S))"
}

# ─── Once mode ────────────────────────────────────────────────────
if [[ $ONCE -eq 1 ]]; then
  render
  exit 0
fi

# ─── Watch loop ───────────────────────────────────────────────────
LAST_SIG=""
trap 'echo ""; dim "watch.sh stopped."; exit 0' INT TERM

while true; do
  SIG="$(state_sig)"
  if [[ "$SIG" != "$LAST_SIG" ]]; then
    render
    LAST_SIG="$SIG"
  fi
  sleep "$INTERVAL"
done
