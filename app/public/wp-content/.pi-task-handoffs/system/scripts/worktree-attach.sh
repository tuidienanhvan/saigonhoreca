#!/usr/bin/env bash
# worktree-attach.sh — Create/attach git worktree for isolated agent execution.
# Inspired by ccswarm git worktree isolation pattern.
#
# Usage:
#   bash system/scripts/worktree-attach.sh T-20260528-001            # create worktree
#   bash system/scripts/worktree-attach.sh T-20260528-001 --remove   # remove after task done
#   bash system/scripts/worktree-attach.sh --list                    # list active worktrees

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HANDOFFS_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
ACTIVE_DIR="$HANDOFFS_DIR/active"
LOCKS_DIR="$HANDOFFS_DIR/system/locks"
WORKTREE_ROOT="$(cd "$HANDOFFS_DIR/.." && pwd)/.task-worktrees"

# (v1.3) dual-mode dossier resolver (folder model + legacy file model)
source "$SCRIPT_DIR/lib/dossier-lib.sh"

mkdir -p "$WORKTREE_ROOT"

# Color helpers
green() { printf '\033[32m%s\033[0m\n' "$*"; }
red()   { printf '\033[31m%s\033[0m\n' "$*"; }
blue()  { printf '\033[34m%s\033[0m\n' "$*"; }

# ─── Parse args ───────────────────────────────────────────────────
TASK_ID=""; ACTION="create"
for arg in "$@"; do
    case "$arg" in
        --remove) ACTION="remove" ;;
        --list)   ACTION="list" ;;
        --help|-h)
            grep '^#' "$0" | head -10
            exit 0 ;;
        T-*) TASK_ID="$arg" ;;
    esac
done

# ─── List mode ────────────────────────────────────────────────────
if [[ "$ACTION" == "list" ]]; then
    blue "Active worktrees in $WORKTREE_ROOT:"
    if [[ -d "$WORKTREE_ROOT" ]]; then
        find "$WORKTREE_ROOT" -maxdepth 1 -type d -name "T-*" | while read -r wt; do
            local id branch
            id=$(basename "$wt")
            branch=$(cd "$(dirname "$HANDOFFS_DIR")" && git -C "$wt" branch --show-current 2>/dev/null || echo "?")
            echo "  • $id → branch: $branch"
        done
    fi
    cd "$(dirname "$HANDOFFS_DIR")"
    echo ""
    blue "Git worktree status:"
    git worktree list 2>/dev/null | grep -v "(bare)" || red "  (no git repo at $(dirname "$HANDOFFS_DIR"))"
    exit 0
fi

[[ -z "$TASK_ID" ]] && { red "Task ID required (T-YYYYMMDD-NNN)"; exit 1; }

WORKTREE_PATH="$WORKTREE_ROOT/$TASK_ID"
BRANCH_NAME="task/$TASK_ID"
REPO_ROOT="$(cd "$HANDOFFS_DIR/.." && pwd)"

# ─── Remove mode ──────────────────────────────────────────────────
if [[ "$ACTION" == "remove" ]]; then
    if [[ ! -d "$WORKTREE_PATH" ]]; then
        red "✗ Worktree not found: $WORKTREE_PATH"
        exit 1
    fi
    blue " Removing worktree for $TASK_ID..."
    cd "$REPO_ROOT"
    git worktree remove --force "$WORKTREE_PATH" 2>&1 || {
        red "Worktree remove failed; manual: rm -rf $WORKTREE_PATH && git worktree prune"
        exit 1
    }
    git branch -D "$BRANCH_NAME" 2>/dev/null || true
    green "✓ Worktree removed: $WORKTREE_PATH"
    green "✓ Branch deleted: $BRANCH_NAME"
    exit 0
fi

# ─── Create mode ──────────────────────────────────────────────────
# (v1.3) dual-mode: folder (T-*/dossier.md) or legacy (T-*.md)
DOSSIER="$(resolve_dossier "$TASK_ID" "$ACTIVE_DIR" 2>/dev/null || true)"
[[ -z "$DOSSIER" ]] && { red "✗ Dossier not found for $TASK_ID in active/"; exit 1; }

if [[ -d "$WORKTREE_PATH" ]]; then
    blue "ℹ Worktree already exists: $WORKTREE_PATH"
    exit 0
fi

blue "Creating worktree for $TASK_ID..."
cd "$REPO_ROOT"

# Verify clean state on current branch
if [[ -n "$(git status --short 2>/dev/null)" ]]; then
    red "Working tree dirty. Commit/stash changes first or worktree may conflict."
    exit 1
fi

# Create worktree on new branch from HEAD
git worktree add -b "$BRANCH_NAME" "$WORKTREE_PATH" HEAD 2>&1 || {
    red "✗ git worktree add failed"
    exit 1
}

# Update lock file with worktree_path
LOCK="$LOCKS_DIR/${TASK_ID}.lock"
if [[ -f "$LOCK" ]]; then
    if grep -q "^worktree_path:" "$LOCK"; then
        sed -i "s|^worktree_path:.*|worktree_path: $WORKTREE_PATH|" "$LOCK"
    else
        echo "worktree_path: $WORKTREE_PATH" >> "$LOCK"
    fi
    green "✓ Lock updated: worktree_path → $WORKTREE_PATH"
fi

green "✓ Worktree created: $WORKTREE_PATH"
green "✓ Branch: $BRANCH_NAME"
echo ""
blue "Next steps for worker:"
echo "  1. cd $WORKTREE_PATH"
echo "  2. Edit files within this isolated tree"
echo "  3. Test + verify locally"
echo "  4. Return to main, merge branch: git merge $BRANCH_NAME"
echo "  5. Cleanup: bash system/scripts/worktree-attach.sh $TASK_ID --remove"
