#!/bin/bash
set -euo pipefail

cd /app

# Caching happens here rather than in the Dockerfile because Render only
# exposes the service's environment variables at runtime.
echo "Caching config, routes and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running migrations..."
php artisan deploy:migrate

# The commands above run as root; hand the files they wrote back to the user
# php-fpm and nginx run as.
chown -R application:application /app/bootstrap/cache /app/storage
