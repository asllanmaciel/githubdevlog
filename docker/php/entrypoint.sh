set -e

cd /app

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -d vendor ]; then
    composer install
fi

if [ -f artisan ]; then
    php artisan key:generate --ansi --force --no-interaction >/dev/null 2>&1 || true

    if [ "${DB_CONNECTION:-}" = "mysql" ]; then
        until mysqladmin ping -h"${DB_HOST:-mysql}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME:-devlog}" -p"${DB_PASSWORD:-devlog}" --ssl=0 --silent; do
            sleep 2
        done
    fi

    php artisan optimize:clear --no-interaction >/dev/null 2>&1 || true
    php artisan migrate --force --no-interaction
fi

exec "$@"
