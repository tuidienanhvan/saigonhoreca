#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# prune-changes.sh — Drop changes/T-*/ folders for tasks archived >Nd (v4.3)
# ──────────────────────────────────────────────────────────────────
# Policy: changes/ folders are audit trail. Once parent task is archived
# >30 days, the rollback window is closed → safe to prune.
# Configurable: CHANGES_RETENTION_DAYS env var (default 30).
#
# Usage:
#   bash system/scripts/prune-changes.sh --dry-run        # list candidates
#   bash system/scripts/prune-changes.sh --fix            # prompt y/N per folder
#   bash system/scripts/prune-changes.sh --fix --force    # no prompts
#
# Exit codes: 0=ok, 1=runtime fail, 2=usage error
# ──────────────────────────────────────────────────────────────────

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
CHANGES_DIR="$HANDOFFS_DIR/changes"
ARCHIVE_DIR="$HANDOFFS_DIR/archive"

RETENTION_DAYS="${CHANGES_RETENTION_DAYS:-30}"

red()    { printf '\033[31m%s\033[0m\n' "$*"; }
green()  { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
blue()   { printf '\033[34m%s\033[0m\n' "$*"; }

die() { red "❌ $*" >&2; exit 1; }
usage_err() { red "❌ $*" >&2; print_usage; exit 2; }

print_usage() {
  cat <<EOF
Usage: $0 [--dry-run | --fix [--force]]

Default mode: --dry-run (list candidates only)
Retention:    $RETENTION_DAYS days (env: CHANGES_RETENTION_DAYS)

Examples:
  $0 --dry-run
  $0 --fix
  $0 --fix --force                  # no per-folder prompt
  CHANGES_RETENTION_DAYS=60 $0 --dry-run
EOF
}

# ─── Parse args ──────────────────────────────────────────────────
MODE="dry-run"
FORCE=0
while [[ $# -gt 0 ]]; do
  case "$1" in
    --dry-run) MODE="dry-run"; shift ;;
    --fix)     MODE="fix"; shift ;;
    --force)   FORCE=1; shift ;;
    --help|-h) print_usage; exit 0 ;;
    *)         usage_err "Unknown flag: $1" ;;
  esac
done

[[ -d "$CHANGES_DIR" ]] || die "Missing changes/ folder at $CHANGES_DIR"

# Cross-platform "now epoch"
NOW_EPOCH=$(date +%s)
CUTOFF_EPOCH=$((NOW_EPOCH - RETENTION_DAYS * 86400))

blue "🔍 Scanning changes/T-*/ folders..."
blue "    Retention: $RETENTION_DAYS days  →  cutoff: $(date -d @$CUTOFF_EPOCH 2>/dev/null || date -r $CUTOFF_EPOCH 2>/dev/null || echo $CUTOFF_EPOCH)"
echo ""

# ─── Collect candidates ──────────────────────────────────────────
CANDIDATES=()
KEPT=()
ORPHANS=()

shopt -s nullglob
for folder in "$CHANGES_DIR"/T-*/; do
  base="$(basename "$folder")"
  # Extract task ID (T-YYYYMMDD-NNN[A-Z]?) — anchored, ignore -owner-slug suffix
  task_id=$(echo "$base" | sed -n 's/^\(T-[0-9]\{8\}-[0-9]\+[A-Z]\?\).*/\1/p')
  [[ -n "$task_id" ]] || { yellow "  ⚠ Unparseable folder: $base"; continue; }

  # Find archive dossier for this task
  archive_files=("$ARCHIVE_DIR"/*/${task_id}-*.md)
  archive_files_exist=0
  for f in "${archive_files[@]}"; do [[ -f "$f" ]] && archive_files_exist=1 && break; done

  if [[ $archive_files_exist -eq 0 ]]; then
    ORPHANS+=("$base (no archive dossier — task may still be active)")
    continue
  fi

  # Use file mtime of archive dossier as proxy for "archived date"
  dossier="${archive_files[0]}"
  if command -v stat >/dev/null 2>&1; then
    mtime=$(stat -c '%Y' "$dossier" 2>/dev/null || stat -f '%m' "$dossier" 2>/dev/null || echo 0)
  else
    mtime=0
  fi

  age_days=$(( (NOW_EPOCH - mtime) / 86400 ))

  if (( mtime > 0 && mtime < CUTOFF_EPOCH )); then
    CANDIDATES+=("$base|$age_days|$dossier")
  else
    KEPT+=("$base ($age_days days old, retention $RETENTION_DAYS)")
  fi
done
shopt -u nullglob

# ─── Report ──────────────────────────────────────────────────────
if [[ ${#CANDIDATES[@]} -eq 0 ]]; then
  green "✓ No prune candidates (all changes/ folders <$RETENTION_DAYS days)"
else
  yellow "🗑️  Prune candidates (archived >$RETENTION_DAYS days):"
  for c in "${CANDIDATES[@]}"; do
    folder="${c%%|*}"
    rest="${c#*|}"
    age="${rest%%|*}"
    echo "    - $folder  ($age days)"
  done
fi

if [[ ${#KEPT[@]} -gt 0 ]]; then
  echo ""
  green "✓ Kept (within retention):"
  for k in "${KEPT[@]}"; do echo "    - $k"; done
fi

if [[ ${#ORPHANS[@]} -gt 0 ]]; then
  echo ""
  yellow "⚠ Orphans (no archive dossier — skipped):"
  for o in "${ORPHANS[@]}"; do echo "    - $o"; done
fi

# ─── Dry-run exit ────────────────────────────────────────────────
if [[ "$MODE" == "dry-run" ]]; then
  echo ""
  blue "Dry-run complete. To delete: $0 --fix"
  exit 0
fi

# ─── Fix mode: delete with optional prompt ───────────────────────
if [[ ${#CANDIDATES[@]} -eq 0 ]]; then
  exit 0
fi

echo ""
DELETED=0
SKIPPED=0
for c in "${CANDIDATES[@]}"; do
  folder="${c%%|*}"
  path="$CHANGES_DIR/$folder"

  if [[ $FORCE -eq 0 ]]; then
    read -p "Delete $folder? [y/N] " yn
    case "$yn" in
      [Yy]*) ;;
      *) yellow "  ⊘ Skipped $folder"; SKIPPED=$((SKIPPED+1)); continue ;;
    esac
  fi

  rm -rf "$path"
  green "  ✓ Deleted $folder"
  DELETED=$((DELETED+1))
done

echo ""
green "✅ Pruned: $DELETED  Skipped: $SKIPPED  Kept: ${#KEPT[@]}"
