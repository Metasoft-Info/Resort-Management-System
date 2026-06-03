#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-/home/tufanconx/laravel}"
REPO_DIR="${REPO_DIR:-$APP_DIR}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-production}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"

resolve_app_dir() {
  local candidate="$1"

  if [ -z "$candidate" ] || [ ! -d "$candidate" ]; then
    return 1
  fi

  # Direct Laravel root.
  if [ -f "$candidate/artisan" ] && [ -e "$candidate/.git" ]; then
    printf '%s\n' "$(cd "$candidate" && pwd)"
    return 0
  fi

  # If candidate is public/public_html, parent may be Laravel root.
  if [ -f "$candidate/../artisan" ] && [ -e "$candidate/../.git" ]; then
    printf '%s\n' "$(cd "$candidate/.." && pwd)"
    return 0
  fi

  # If candidate is symlink, check link target and target parent.
  if [ -L "$candidate" ]; then
    local link_target
    link_target="$(readlink -f "$candidate" || true)"

    if [ -n "$link_target" ] && [ -f "$link_target/../artisan" ] && [ -e "$link_target/../.git" ]; then
      printf '%s\n' "$(cd "$link_target/.." && pwd)"
      return 0
    fi

    if [ -n "$link_target" ] && [ -f "$link_target/artisan" ] && [ -e "$link_target/.git" ]; then
      printf '%s\n' "$(cd "$link_target" && pwd)"
      return 0
    fi
  fi

  return 1
}

resolve_composer_cmd() {
  local composer_cmd="$1"

  if command -v "$composer_cmd" >/dev/null 2>&1; then
    printf '%s\n' "$composer_cmd"
    return 0
  fi

  for candidate in \
    /opt/cpanel/composer/bin/composer \
    /usr/local/bin/composer \
    /usr/bin/composer \
    /home/tufanconx/composer.phar; do
    if [ -x "$candidate" ]; then
      printf '%s\n' "$candidate"
      return 0
    fi
    if [ -f "$candidate" ]; then
      printf '%s %s\n' "$PHP_BIN" "$candidate"
      return 0
    fi
  done

  return 1
}

if resolved="$(resolve_app_dir "$APP_DIR")"; then
  APP_DIR="$resolved"
else
  # Fallback scan for shared hosting layouts.
  for probe in \
    /home/tufanconx \
    /home/tufanconx/tufanconventionresort.com \
    /home/tufanconx/laravel \
    /home/tufanconx/public_html; do
    if resolved="$(resolve_app_dir "$probe")"; then
      APP_DIR="$resolved"
      break
    fi
  done
fi

if [ -d "$REPO_DIR" ]; then
  REPO_DIR="$(cd "$REPO_DIR" && pwd)"
fi

if [ ! -d "$APP_DIR" ]; then
  echo "ERROR: APP_DIR does not exist: $APP_DIR"
  exit 1
fi

cd "$APP_DIR"

if [ ! -f artisan ]; then
  echo "ERROR: artisan file not found in APP_DIR: $APP_DIR"
  echo "Set PROD_APP_DIR to your Laravel root (folder that contains both artisan and .git)."
  echo "Possible Laravel roots under /home/tufanconx:"
  find /home/tufanconx -maxdepth 5 -type f -name artisan 2>/dev/null | sed 's#/artisan$##' | while read -r p; do
    if [ -d "$p/.git" ]; then
      echo "$p"
    fi
  done
  exit 1
fi

if [ ! -e "$REPO_DIR/.git" ]; then
  echo "ERROR: git repository not found in REPO_DIR: $REPO_DIR"
  exit 1
fi

if resolved_composer="$(resolve_composer_cmd "$COMPOSER_BIN")"; then
  COMPOSER_BIN="$resolved_composer"
else
  echo "ERROR: composer binary not found. Set COMPOSER_BIN to the correct path."
  exit 1
fi

git config --global --add safe.directory "$APP_DIR" || true
git config --global --add safe.directory "$REPO_DIR" || true

# Always restore application availability on exit.
cleanup() {
  "$PHP_BIN" artisan up || true
}
trap cleanup EXIT

"$PHP_BIN" artisan down || true

if [ "$REPO_DIR" != "$APP_DIR" ]; then
  cd "$REPO_DIR"
  git fetch --all --prune
  git checkout "$DEPLOY_BRANCH"
  git reset --hard "origin/$DEPLOY_BRANCH"

  if command -v rsync >/dev/null 2>&1; then
    rsync -a --delete \
      --exclude '.git' \
      --exclude 'storage/logs' \
      --exclude 'storage/framework/cache' \
      --exclude 'storage/framework/sessions' \
      --exclude 'storage/framework/views' \
      "$REPO_DIR/" "$APP_DIR/"
  else
    echo "ERROR: rsync is required when REPO_DIR and APP_DIR are different."
    exit 1
  fi

  cd "$APP_DIR"
else
  git fetch --all --prune
  git checkout "$DEPLOY_BRANCH"
  git reset --hard "origin/$DEPLOY_BRANCH"
fi

"$COMPOSER_BIN" install --no-interaction --prefer-dist --no-dev --optimize-autoloader

"$PHP_BIN" artisan migrate --force
"$PHP_BIN" artisan storage:link || true

"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache
"$PHP_BIN" artisan queue:restart || true

chmod -R 775 storage bootstrap/cache || true

echo "Deployment finished successfully."