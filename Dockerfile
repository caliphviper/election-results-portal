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

# --prefer-dist is deliberately absent. It pulls zips from codeload.github.com,
# which is the endpoint being rate limited, and it also switches off Composer's
# ability to fall back to cloning the package from git ("Source fallback is
# disabled" in the failing logs). Without it, a throttled zip download retries
# as a git clone, which has its own far more generous limits.
RUN set -e; \
    for attempt in 1 2 3 4; do \
        composer install --no-dev --no-scripts --no-autoloader --no-interaction && break; \
        echo "composer install failed (attempt $attempt of 4)"; \
        [ "$attempt" = 4 ] && exit 1; \
        echo "waiting $((attempt * 45))s for the rate limit window to clear..."; \
        sleep $((attempt * 45)); \
    done

COPY . .

RUN composer dump-autoload --no-dev --optimize --no-interaction \
 && php artisan package:discover --ansi

COPY docker/entrypoint.d/migrate.sh /opt/docker/provision/entrypoint.d/migrate.sh
RUN chmod +x /opt/docker/provision/entrypoint.d/migrate.sh

RUN chown -R application:application /app
