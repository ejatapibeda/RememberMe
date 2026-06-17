#!/bin/sh
set -e

cd /var/www/html

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "WARNING: APP_KEY is not set. Set it in CapRover App Configs."
fi

php artisan storage:link --force 2>/dev/null || true

if [ "$DB_CONNECTION" = "sqlite" ]; then
    # Default SQLite path used by config/database.php when DB_DATABASE is unset
    SQLITE_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    echo "Using SQLite database at: $SQLITE_PATH"
    mkdir -p "$(dirname "$SQLITE_PATH")"
    [ -f "$SQLITE_PATH" ] || touch "$SQLITE_PATH"
    chown -R www-data:www-data "$(dirname "$SQLITE_PATH")" 2>/dev/null || true
    chmod -R ug+rwx "$(dirname "$SQLITE_PATH")" 2>/dev/null || true
fi

if [ "$RUN_MIGRATIONS" != "false" ]; then
    if [ "$DB_CONNECTION" != "sqlite" ]; then
        echo "Ensuring MySQL database exists..."
        php -r '
            $host = getenv("DB_HOST") ?: "127.0.0.1";
            $port = getenv("DB_PORT") ?: "3306";
            $db   = getenv("DB_DATABASE") ?: "rememberme";
            $user = getenv("DB_USERNAME") ?: "root";
            $pass = getenv("DB_PASSWORD") ?: "";
            try {
                $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $db . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                echo "Database $db is ready.\n";
            } catch (Exception $e) {
                echo "DB create skipped: " . $e->getMessage() . "\n";
            }
        ' || true
    fi
    php artisan migrate --force --no-interaction || echo "Migration skipped or failed (check DB connection)."
fi

php artisan config:cache --no-interaction 2>/dev/null || true
php artisan route:cache --no-interaction 2>/dev/null || true
php artisan view:cache --no-interaction 2>/dev/null || true

exec "$@"
