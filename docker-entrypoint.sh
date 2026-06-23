#!/usr/bin/env bash
#
# First-run setup + dev server for the containerised MH4U Database API.
# Idempotent: safe to run on every `docker compose up`. The database is only
# seeded the first time (when the SQLite file is empty), so restarts are fast
# and never duplicate the imported data.
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

# `php artisan serve` forwards only a whitelist of env vars to its built-in PHP
# server, so APP_URL from the container environment never reaches request
# handling. Write it into .env (which the server does read) so absolute
# icon_url / map_url keep the :8088 port.
if [ -n "${APP_URL:-}" ]; then
    sed -i "s#^APP_URL=.*#APP_URL=${APP_URL}#" .env
fi

# Seed only when the database file is empty (-s is false for a 0-byte file),
# so first boot imports the data and later boots skip straight to migrating.
FRESH=0
if [ ! -s database/database.sqlite ]; then
    FRESH=1
fi
touch database/database.sqlite

echo "[mh4u] Running migrations"
php artisan migrate --force

if [ "$FRESH" = "1" ]; then
    echo "[mh4u] Fresh database -> importing data (database/source/mh4u.db)"
    php artisan db:seed --force
    echo "[mh4u] Exporting API docs (Scribe + OpenAPI)"
    php artisan mh4u:export-docs || echo "[mh4u] export-docs failed (non-fatal); continuing"
else
    echo "[mh4u] Existing database -> skipping seed"
fi

echo "[mh4u] Syncing game assets into public/assets"
php artisan mh4u:sync-assets

echo "[mh4u] Serving API on http://0.0.0.0:8088 (host: http://localhost:8088)"
exec php artisan serve --host=0.0.0.0 --port=8088
