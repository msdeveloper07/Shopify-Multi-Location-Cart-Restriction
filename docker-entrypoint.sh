#!/bin/bash
set -euo pipefail
 
# Require stable APP_KEY
if [[ -z "${APP_KEY:-}" ]]; then
  echo "ERROR: APP_KEY is not set. Set a permanent APP_KEY in the environment."
  exit 1
fi
 
# Prepare Laravel storage early
mkdir -p storage/app/private storage/logs storage/framework/{cache,sessions,testing,views}
chmod -R 755 storage || true
chmod -R 755 bootstrap/cache || true
 
# Prepare persistent shop storage (mounted volume recommended)
# SHOP_STORAGE_DIR should point to your persistent path (e.g., /app/storage_pv/shops)
SHOP_DIR="${SHOP_STORAGE_DIR:-/app/storage_pv/shops}"
mkdir -p "$SHOP_DIR"
chmod -R 700 "$SHOP_DIR" || true
 
# DO NOT rotate APP_KEY in prod
# php artisan key:generate --force   # <-- REMOVE
 
# Caches after env + storage are ready
php artisan config:cache
php artisan route:cache
php artisan view:cache
 
# Optional: migrations
php artisan migrate --force || true
 
# Start queue worker (for real prod use Horizon or a supervisor)
php artisan queue:work --tries=3 --sleep=3 --timeout=120 &
 
# Dev server (for prod use nginx + php-fpm)
php -S 0.0.0.0:${PORT:-8080} -t public