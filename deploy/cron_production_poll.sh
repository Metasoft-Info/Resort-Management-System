#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-/home/tufanconx/laravel}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-production}"
LOCK_DIR="${LOCK_DIR:-/tmp/lakeview-deploy-lock}"

if [ ! -d "$APP_DIR" ]; then
  echo "[cron-deploy] APP_DIR does not exist: $APP_DIR"
  exit 1
fi

# If APP_DIR points to public/public_html, try to auto-resolve Laravel root.
if [ ! -f "$APP_DIR/artisan" ]; then
  if [ -f "$APP_DIR/../artisan" ]; then
    APP_DIR="$(cd "$APP_DIR/.." && pwd)"
  elif [ -L "$APP_DIR" ]; then
    LINK_TARGET="$(readlink -f "$APP_DIR" || true)"
    if [ -n "$LINK_TARGET" ] && [ -f "$LINK_TARGET/../artisan" ]; then
      APP_DIR="$(cd "$LINK_TARGET/.." && pwd)"
    fi
  fi
fi

if [ ! -f "$APP_DIR/artisan" ]; then
  echo "[cron-deploy] artisan not found under APP_DIR: $APP_DIR"
  exit 1
fi

if [ ! -d "$APP_DIR/.git" ]; then
  echo "[cron-deploy] Git repository not found under APP_DIR: $APP_DIR"
  exit 1
fi

# Prevent overlapping deployments when cron runs every minute.
if ! mkdir "$LOCK_DIR" 2>/dev/null; then
  echo "[cron-deploy] Another deploy process is running. Skipping."
  exit 0
fi

cleanup_lock() {
  rmdir "$LOCK_DIR" 2>/dev/null || true
}
trap cleanup_lock EXIT

cd "$APP_DIR"
git config --global --add safe.directory "$APP_DIR" || true

git fetch origin "$DEPLOY_BRANCH" --prune

LOCAL_SHA="$(git rev-parse HEAD)"
REMOTE_SHA="$(git rev-parse "origin/$DEPLOY_BRANCH")"

if [ "$LOCAL_SHA" = "$REMOTE_SHA" ]; then
  echo "[cron-deploy] No new commits on $DEPLOY_BRANCH."
  exit 0
fi

echo "[cron-deploy] New commit detected on $DEPLOY_BRANCH. Running deploy."
bash "$APP_DIR/deploy/production_deploy.sh"
