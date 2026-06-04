#!/bin/bash
export APP_DIR="/home/tufanconx/public_html"
export REPO_DIR="/home/tufanconx/repo-production"
export DEPLOY_BRANCH="production"
export PHP_BIN="/usr/local/bin/php"
# Use CLI PHP explicitly so composer does not run under cgi-fcgi SAPI
export COMPOSER_BIN="/usr/local/bin/php /opt/cpanel/composer/bin/composer"

exec /bin/bash -l "$REPO_DIR/deploy/cron_production_poll.sh"
