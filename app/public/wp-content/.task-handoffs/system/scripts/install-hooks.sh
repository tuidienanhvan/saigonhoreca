#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# install-hooks.sh — Install git pre-commit hook running lint --strict (v4.3)
# ──────────────────────────────────────────────────────────────────
# Walks up from .task-handoffs/ to find .git/ (workspace root, not submodule).
# Installs .git/hooks/pre-commit that runs lint-handoffs.sh --strict.
# Existing pre-commit backed up to .bak.YYYYMMDD.
#
# Usage:
#   bash system/scripts/install-hooks.sh             # install
#   bash system/scripts/install-hooks.sh --uninstall # remove (restore .bak)
#   bash system/scripts/install-hooks.sh --status    # show install state
#
# Exit codes: 0=ok, 1=runtime fail, 2=usage error
# ──────────────────────────────────────────────────────────────────

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"

red()    { printf '\033[31m%s\033[0m\n' "$*"; }
green()  { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
blue()   { printf '\033[34m%s\033[0m\n' "$*"; }

die() { red "❌ $*" >&2; exit 1; }
usage_err() { red "❌ $*" >&2; print_usage; exit 2; }

print_usage() {
  cat <<EOF
Usage: $0 [--install | --uninstall | --status]

Default: --install
  --install     Write .git/hooks/pre-commit (backs up existing as .bak.YYYYMMDD)
  --uninstall   Remove hook, restore most recent .bak.* if present
  --status      Show whether hook is installed

The hook runs: bash .task-handoffs/system/scripts/lint-handoffs.sh --strict
EOF
}

# ─── Parse args ──────────────────────────────────────────────────
ACTION="install"
case "${1:-}" in
  --install|"")  ACTION="install" ;;
  --uninstall)   ACTION="uninstall" ;;
  --status)      ACTION="status" ;;
  --help|-h)     print_usage; exit 0 ;;
  *)             usage_err "Unknown flag: $1" ;;
esac

# ─── Locate .git directory ───────────────────────────────────────
find_git_dir() {
  local dir="$1"
  while [[ "$dir" != "/" && "$dir" != "" ]]; do
    if [[ -d "$dir/.git" ]]; then
      echo "$dir/.git"
      return 0
    elif [[ -f "$dir/.git" ]]; then
      # Git submodule or worktree — read gitdir
      gitdir=$(sed -n 's/^gitdir: //p' "$dir/.git")
      [[ -n "$gitdir" ]] && { echo "$gitdir"; return 0; }
    fi
    dir=$(dirname "$dir")
  done
  return 1
}

GIT_DIR=$(find_git_dir "$HANDOFFS_DIR" || true)
[[ -n "$GIT_DIR" ]] || die "No .git/ found walking up from $HANDOFFS_DIR. Is this a git workspace?"

HOOK_DIR="$GIT_DIR/hooks"
HOOK_FILE="$HOOK_DIR/pre-commit"

# Compute relative path from git workspace root → handoffs dir
GIT_ROOT="$(dirname "$GIT_DIR")"
# Strip GIT_ROOT prefix; if HANDOFFS_DIR starts with GIT_ROOT/, use suffix
case "$HANDOFFS_DIR" in
  "$GIT_ROOT"/*)
    RELATIVE_HANDOFFS="${HANDOFFS_DIR#$GIT_ROOT/}"
    ;;
  "$GIT_ROOT")
    RELATIVE_HANDOFFS="."
    ;;
  *)
    # Different volume / outside git root — use absolute
    RELATIVE_HANDOFFS="$HANDOFFS_DIR"
    ;;
esac

blue "Git workspace:   $GIT_ROOT"
blue "Handoffs path:   $RELATIVE_HANDOFFS"
blue "Hook target:     $HOOK_FILE"
echo ""

# ─── Status ──────────────────────────────────────────────────────
if [[ "$ACTION" == "status" ]]; then
  if [[ -f "$HOOK_FILE" ]]; then
    if grep -q "lint-handoffs.sh" "$HOOK_FILE" 2>/dev/null; then
      green "✓ Hook installed (lint-handoffs reference found)"
      exit 0
    else
      yellow "⚠ pre-commit exists but does not reference lint-handoffs.sh"
      exit 0
    fi
  else
    yellow "⚠ Not installed"
    exit 0
  fi
fi

# ─── Uninstall ───────────────────────────────────────────────────
if [[ "$ACTION" == "uninstall" ]]; then
  if [[ ! -f "$HOOK_FILE" ]]; then
    yellow "⚠ No hook to uninstall"
    exit 0
  fi
  # Try restore from latest .bak.*
  shopt -s nullglob
  BAKS=("$HOOK_FILE".bak.*)
  shopt -u nullglob
  if [[ ${#BAKS[@]} -gt 0 ]]; then
    LATEST="${BAKS[${#BAKS[@]}-1]}"
    mv "$LATEST" "$HOOK_FILE"
    green "✓ Restored from $(basename "$LATEST")"
  else
    rm -f "$HOOK_FILE"
    green "✓ Removed hook (no backup found)"
  fi
  exit 0
fi

# ─── Install ─────────────────────────────────────────────────────
mkdir -p "$HOOK_DIR"

# Backup existing
if [[ -f "$HOOK_FILE" ]]; then
  if grep -q "lint-handoffs.sh" "$HOOK_FILE" 2>/dev/null; then
    green "✓ Hook already installed (lint-handoffs reference present)"
    exit 0
  fi
  BAK="$HOOK_FILE.bak.$(date +%Y%m%d-%H%M%S)"
  cp "$HOOK_FILE" "$BAK"
  yellow "  Backed up existing pre-commit to: $(basename "$BAK")"
fi

# Write hook
cat > "$HOOK_FILE" <<EOF
#!/usr/bin/env bash
# Auto-installed by .task-handoffs/system/scripts/install-hooks.sh
# Runs lint-handoffs --strict before allowing commit

set -e

HANDOFFS="$RELATIVE_HANDOFFS"

if [[ -d "\$HANDOFFS" ]]; then
  if ! bash "\$HANDOFFS/system/scripts/lint-handoffs.sh" --strict; then
    echo ""
    echo "❌ task-handoffs drift detected. Fix with:"
    echo "   bash \$HANDOFFS/system/scripts/lint-handoffs.sh --fix"
    echo ""
    echo "Or bypass (NOT RECOMMENDED): git commit --no-verify"
    exit 1
  fi
fi

exit 0
EOF

chmod +x "$HOOK_FILE"

green "✅ Installed pre-commit hook"
echo ""
echo "  File:    $HOOK_FILE"
echo "  Runs:    bash $RELATIVE_HANDOFFS/system/scripts/lint-handoffs.sh --strict"
echo ""
yellow "To uninstall:  $0 --uninstall"
yellow "To bypass:     git commit --no-verify  (NOT recommended)"
