#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-/home/tufanconx/laravel}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-production}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"

if [ ! -d "$APP_DIR" ]; then
  echo "ERROR: APP_DIR does not exist: $APP_DIR"
  exit 1
fi

cd "$APP_DIR"

if [ ! -f artisan ]; then
  echo "ERROR: artisan file not found in APP_DIR: $APP_DIR"
  echo "Set PROD_APP_DIR to your Laravel root (folder that contains artisan)."
  echo "Possible artisan locations under /home/tufanconx:"
  find /home/tufanconx -maxdepth 4 -type f -name artisan 2>/dev/null || true
  exit 1
fi

git config --global --add safe.directory "$APP_DIR" || true

# Always restore application availability on exit.
cleanup() {
  "$PHP_BIN" artisan up || true
}
trap cleanup EXIT

"$PHP_BIN" artisan down || true

git fetch --all --prune
git checkout "$DEPLOY_BRANCH"
git reset --hard "origin/$DEPLOY_BRANCH"

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