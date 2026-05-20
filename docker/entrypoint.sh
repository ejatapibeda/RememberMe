#!/bin/sh
set -e

cd /var/www/html

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "WARNING: APP_KEY is not set. Set it in CapRover App Configs."
fi

php artisan storage:link --force 2>/dev/null || true

if [ "$RUN_MIGRATIONS" != "false" ]; then
    php artisan migrate --force --no-interaction || echo "Migration skipped or failed (check DB connection)."
fi

php artisan config:cache --no-interaction 2>/dev/null || true
php artisan route:cache --no-interaction 2>/dev/null || true
php artisan view:cache --no-interaction 2>/dev/null || true

exec "$@"
