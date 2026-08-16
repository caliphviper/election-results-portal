#!/bin/sh
set -e

cd /var/www/html

echo "Running migrations..."
php artisan migrate --force

# Only seed if the states table is empty (prevents duplicate rows on every restart)
COUNT=$(php artisan tinker --execute="echo App\Models\State::count();" 2>/dev/null | tail -1)
if [ "$COUNT" = "0" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
else
    echo "Database already seeded, skipping."
fi

echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf