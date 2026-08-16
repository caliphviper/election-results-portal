FROM webdevops/php-nginx:8.4

ENV WEB_DOCUMENT_ROOT=/app/public
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY docker/entrypoint.d/migrate.sh /opt/docker/provision/entrypoint.d/migrate.sh
RUN chmod +x /opt/docker/provision/entrypoint.d/migrate.sh

RUN chown -R application:application /app