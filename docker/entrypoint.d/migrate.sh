#!/bin/bash
cd /app

echo "Running migrations..."
php artisan migrate --force

COUNT=$(php artisan tinker --execute="echo App\Models\State::count();" 2>/dev/null | tail -1)
if [ "$COUNT" = "0" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
else
    echo "Database already seeded, skipping."
fi