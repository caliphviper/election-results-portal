FROM tangramor/nginx-php8-fpm:php8.4.7_withoutNodejs

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV WEBROOT="/var/www/html/public"

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["/var/www/html/docker-entrypoint.sh"]