#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# prime-context.sh — session bootstrap primer (read-only) (v1.5)
# (reconstructed 2026-06-03 after data-loss incident — verify against usage)
# ──────────────────────────────────────────────────────────────────
# Print a session briefing at the start of any Pi-task work:
#   • Active tasks count by state (scan active/)
#   • Memory summary (count pitfalls/patterns/agent-quirks/playbooks in
#     _brain/memory/, list high/critical pitfalls)
#   • Skills list (from _brain/skills/)
#   • Pending review count (_brain/memory/_pending/)
# NEVER mutates anything. Safe to run repeatedly.
#
# Usage:
#   bash system/scripts/prime-context.sh
# ──────────────────────────────────────────────────────────────────

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
ACTIVE_DIR="$HANDOFFS_DIR/active"
BRAIN_DIR="$HANDOFFS_DIR/_brain"
MEMORY_DIR="$BRAIN_DIR/memory"
SKILLS_DIR="$BRAIN_DIR/skills"
PENDING_DIR="$MEMORY_DIR/_pending"

# (v1.3) dual-mode dossier resolver (folder model + legacy file model)
source "$SCRIPT_DIR/lib/dossier-lib.sh"

red()    { printf '\033[31m%s\033[0m\n' "$*"; }
green()  { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
blue()   { printf '\033[34m%s\033[0m\n' "$*"; }
bold()   { printf '\033[1m%s\033[0m\n' "$*"; }
dim()    { printf '\033[2m%s\033[0m\n' "$*"; }

# Read a frontmatter field from a dossier (first match, comment-stripped).
field() {
  awk '/^---$/{c++; next} c==1' "$2" 2>/dev/null \
    | grep -m1 "^$1:" \
    | sed "s/^$1:[[:space:]]*//; s/[[:space:]]*#.*$//; s/[[:space:]]*$//" || true
}

# ─── Header ───────────────────────────────────────────────────────
bold "══════════════════════════════════════════════════════════════"
bold " PI TASK HANDOFFS — SESSION CONTEXT PRIMER (v1.5)"
bold "══════════════════════════════════════════════════════════════"
echo "  Workspace : $(basename "$(cd "$HANDOFFS_DIR/.." && pwd)")"
echo "  Root      : $HANDOFFS_DIR"
echo "  Generated : $(date +'%Y-%m-%d %H:%M:%S')"
echo ""

# ─── 1. Active tasks by state ─────────────────────────────────────
blue "── 1. Active Tasks (by state) ─────────────────────────────────"
declare -A STATE_COUNT
TOTAL_ACTIVE=0
while IFS= read -r dossier; do
  [[ -n "$dossier" ]] || continue
  st="$(field state "$dossier")"
  [[ -n "$st" ]] || st="(unknown)"
  STATE_COUNT["$st"]=$(( ${STATE_COUNT["$st"]:-0} + 1 ))
  TOTAL_ACTIVE=$((TOTAL_ACTIVE + 1))
done < <(list_dossiers "$ACTIVE_DIR")

if [[ $TOTAL_ACTIVE -eq 0 ]]; then
  dim "  (no active tasks)"
else
  for st in $(printf '%s\n' "${!STATE_COUNT[@]}" | sort); do
    printf "  %-14s %s\n" "$st" "${STATE_COUNT[$st]}"
  done
  echo "  ──────────────"
  printf "  %-14s %s\n" "TOTAL" "$TOTAL_ACTIVE"
fi
echo ""

# ─── 2. Memory summary ────────────────────────────────────────────
blue "── 2. Memory (_brain/memory/) ─────────────────────────────────"
count_md() { local d="$1"; if [[ -d "$d" ]]; then find "$d" -maxdepth 1 -name '*.md' -type f 2>/dev/null | wc -l | tr -d ' '; else echo 0; fi; }
N_PIT=$(count_md "$MEMORY_DIR/pitfalls")
N_PAT=$(count_md "$MEMORY_DIR/patterns")
N_QRK=$(count_md "$MEMORY_DIR/agent-quirks")
N_PLY=$(count_md "$MEMORY_DIR/playbooks")
printf "  pitfalls     %s\n" "$N_PIT"
printf "  patterns     %s\n" "$N_PAT"
printf "  agent-quirks %s\n" "$N_QRK"
printf "  playbooks    %s\n" "$N_PLY"
echo ""

# High/critical severity pitfalls (worth surfacing every session)
if [[ -d "$MEMORY_DIR/pitfalls" ]]; then
  CRIT_FOUND=0
  while IFS= read -r pf; do
    [[ -n "$pf" ]] || continue
    sev="$(grep -m1 '^severity:' "$pf" 2>/dev/null | sed 's/^severity:[[:space:]]*//; s/[[:space:]]*$//' | tr 'A-Z' 'a-z')"
    case "$sev" in
      high|critical)
        if [[ $CRIT_FOUND -eq 0 ]]; then
          yellow "  ⚠ High/critical pitfalls:"
          CRIT_FOUND=1
        fi
        title="$(grep -m1 '^title:' "$pf" 2>/dev/null | sed 's/^title:[[:space:]]*//')"
        [[ -n "$title" ]] || title="$(basename "$pf" .md)"
        red "    • [$sev] $title"
        ;;
    esac
  done < <(find "$MEMORY_DIR/pitfalls" -maxdepth 1 -name '*.md' -type f 2>/dev/null | sort)
  [[ $CRIT_FOUND -eq 0 ]] && dim "  (no high/critical pitfalls)"
fi
echo ""

# ─── 3. Skills ────────────────────────────────────────────────────
blue "── 3. Skills (_brain/skills/) ─────────────────────────────────"
if [[ -d "$SKILLS_DIR" ]]; then
  FOUND_SKILL=0
  while IFS= read -r sk; do
    [[ -n "$sk" ]] || continue
    name="$(basename "$sk" .md)"
    case "$name" in README|_template) continue ;; esac
    echo "  • $name"
    FOUND_SKILL=1
  done < <(find "$SKILLS_DIR" -maxdepth 1 -name '*.md' -type f 2>/dev/null | sort)
  [[ $FOUND_SKILL -eq 0 ]] && dim "  (no skills)"
else
  dim "  (_brain/skills/ missing)"
fi
echo ""

# ─── 4. Pending review ────────────────────────────────────────────
blue "── 4. Pending Memory Review (_brain/memory/_pending/) ─────────"
N_PEND=0
[[ -d "$PENDING_DIR" ]] && N_PEND=$(find "$PENDING_DIR" -maxdepth 1 -name '*.md' -type f 2>/dev/null | wc -l | tr -d ' ')
if [[ "${N_PEND:-0}" -gt 0 ]]; then
  yellow "  $N_PEND suggestion(s) awaiting review:"
  find "$PENDING_DIR" -maxdepth 1 -name '*.md' -type f 2>/dev/null | sort | while read -r p; do
    echo "    • $(basename "$p")"
  done
  echo "    → review: bash system/scripts/harvest-lessons.sh  (and promote via _brain/skills/)"
else
  dim "  (none pending)"
fi
echo ""

bold "══════════════════════════════════════════════════════════════"
green " Primer complete — read-only. You are ready to work."
bold "══════════════════════════════════════════════════════════════"
