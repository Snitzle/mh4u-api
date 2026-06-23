#!/usr/bin/env bash
#
# First-run setup + Apache for the containerised MH4U Database API.
# Idempotent: safe to run on every `docker compose up`. The database is seeded
# only when empty, so restarts are fast and never duplicate the imported data.
set -euo pipefail

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    echo "[mh4u] Installing PHP dependencies (composer install)..."
    composer install --no-interaction --prefer-dist --no-progress
fi

if [ ! -f .env ]; then
    echo "[mh4u] Creating .env from .env.example"
    cp .env.example .env
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    echo "[mh4u] Generating application key"
    php artisan key:generate --force
fi

# Wait for MySQL to accept connections (compose also gates on its healthcheck).
export MYSQL_PWD="${DB_PASSWORD:-secret}"
echo "[mh4u] Waiting for MySQL at ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
until mysql -h"${DB_HOST:-mysql}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME:-mh4u}" -e 'SELECT 1;' >/dev/null 2>&1; do
    sleep 2
done

echo "[mh4u] Running migrations"
php artisan migrate --force

# Seed only when the database is empty so restarts don't re-import.
COUNT=$(mysql -h"${DB_HOST:-mysql}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME:-mh4u}" "${DB_DATABASE:-mh4u_api}" \
    -N -B -e 'SELECT COUNT(*) FROM monsters;' 2>/dev/null || echo 0)
if [ "${COUNT:-0}" = "0" ]; then
    echo "[mh4u] Empty database -> importing data (mh4u.db + Kiranico)"
    php artisan db:seed --force
    php artisan mh4u:export-docs || echo "[mh4u] export-docs failed (non-fatal); continuing"
else
    echo "[mh4u] Existing data (${COUNT} monsters) -> skipping seed"
fi

echo "[mh4u] Syncing game assets into public/assets"
php artisan mh4u:sync-assets

echo "[mh4u] Starting Apache (host: http://localhost:8088)"
exec apache2-foreground
