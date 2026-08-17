FROM webdevops/php-nginx:8.4

ENV WEB_DOCUMENT_ROOT=/app/public
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

WORKDIR /app

COPY . .

# vendor/ is committed to the repo (see the deploy-with-vendor commit), so this
# resolves entirely from disk and never contacts GitHub -- which is the point,
# while their archive downloads are failing. It still prunes the dev-only
# packages, regenerates the optimised autoloader, and re-runs package discovery
# against what is actually installed.
#
# This has to happen after COPY, not before: copying the repo afterwards would
# put the dev packages straight back and leave installed.json disagreeing with
# the autoloader.
RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY docker/entrypoint.d/migrate.sh /opt/docker/provision/entrypoint.d/migrate.sh
RUN chmod +x /opt/docker/provision/entrypoint.d/migrate.sh

RUN chown -R application:application /app
