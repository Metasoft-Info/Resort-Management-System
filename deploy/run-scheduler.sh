#!/usr/bin/env bash
# Run Laravel scheduler with correct PHP path
set -euo pipefail

APP_DIR="/home/tufanconx/public_html"
LOG_FILE="/home/tufanconx/scheduler.log"

# Find PHP binary
PHP_BIN=""
for p in /usr/local/bin/php /usr/bin/php /opt/cpanel/ea-php83/root/usr/bin/php /opt/cpanel/ea-php84/root/usr/bin/php; do
    if [ -x "$p" ]; then
        PHP_BIN="$p"
        break
    fi
done

if [ -z "$PHP_BIN" ]; then
    echo "$(date): ERROR: PHP not found" >> "$LOG_FILE"
    exit 1
fi

cd "$APP_DIR"
$PHP_BIN artisan schedule:run >> "$LOG_FILE" 2>&1 || true

echo "$(date): Scheduler ran" >> "$LOG_FILE"
