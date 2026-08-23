#!/bin/bash
# SessionStart hook for Claude Code on the web.
# Installs PHP (Composer) and JS (pnpm) dependencies so tests, static
# analysis, and linters work without manual setup in a fresh container.
set -euo pipefail

if [ "${CLAUDE_CODE_REMOTE:-}" != "true" ]; then
  exit 0
fi

cd "$CLAUDE_PROJECT_DIR"

# --- PHP / Composer -------------------------------------------------------
# Some sandboxed network policies block api.github.com/codeload.github.com
# (used for GitHub-hosted dist zipballs) while plain `git clone` over
# github.com still works. Fall back progressively so the hook still leaves a
# usable app even when dist downloads are unreachable:
#   1) normal install (prefer-dist, fastest when the network allows it)
#   2) --prefer-source (forces git clone for every package that has a VCS
#      source, which is everything except dist-only releases)
#   3) --prefer-source --no-dev (skips the rare dist-only-with-no-source dev
#      package, e.g. phpstan/phpstan pulled in by larastan) so the app still
#      boots and migrates even though quality-gate tooling is unavailable.
if [ -f composer.json ]; then
  export COMPOSER_ALLOW_SUPERUSER=1
  if ! composer install --no-interaction --no-progress; then
    echo "composer install (prefer-dist) failed, retrying with --prefer-source..." >&2
    if ! composer install --no-interaction --no-progress --prefer-source; then
      echo "composer install --prefer-source failed (likely a dist-only dev package unreachable), falling back to production dependencies only..." >&2
      composer install --no-interaction --no-progress --prefer-source --no-dev
    fi
  fi
fi

# --- Node / pnpm ------------------------------------------------------------
if [ -f package.json ]; then
  corepack enable >/dev/null 2>&1 || true
  pnpm install --frozen-lockfile
fi

# --- Laravel app bootstrap --------------------------------------------------
if [ -f artisan ]; then
  [ -f .env ] || cp .env.example .env
  php artisan key:generate --ansi --force

  # Local/sandbox DB: sqlite, no external Neon/cloud credentials required.
  if grep -q '^DB_CONNECTION=sqlite' .env 2>/dev/null; then
    mkdir -p database
    touch database/database.sqlite
  fi

  php artisan migrate --graceful --ansi --force
fi
