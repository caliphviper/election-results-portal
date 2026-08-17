FROM webdevops/php-nginx:8.4

ENV WEB_DOCUMENT_ROOT=/app/public
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

# Composer opens a dozen connections at once by default, which is what trips
# GitHub's anonymous rate limit from Render's shared build IPs.
ENV COMPOSER_MAX_PARALLEL_HTTP=6

WORKDIR /app

# Dependencies are installed before the application code is copied, so editing
# a view or a controller reuses this layer instead of re-downloading vendor/ on
# every single deploy. Scripts and the autoloader are deferred because both
# need artisan, which arrives with the code below.
COPY composer.json composer.lock ./

# Optional: set COMPOSER_AUTH in Render to a GitHub token to lift the anonymous
# rate limit entirely. The build works without it.
ARG COMPOSER_AUTH

RUN set -e; \
    for attempt in 1 2 3; do \
        composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction && break; \
        echo "composer install failed (attempt $attempt), retrying..."; \
        [ "$attempt" = 3 ] && exit 1; \
        sleep $((attempt * 20)); \
    done

COPY . .

RUN composer dump-autoload --no-dev --optimize --no-interaction \
 && php artisan package:discover --ansi

COPY docker/entrypoint.d/migrate.sh /opt/docker/provision/entrypoint.d/migrate.sh
RUN chmod +x /opt/docker/provision/entrypoint.d/migrate.sh

RUN chown -R application:application /app
