FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    libzip-dev \
    unzip \
    curl \
    bash \
    git

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    zip \
    opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN mkdir -p /var/lib/nginx/tmp /var/lib/nginx/logs /var/log/nginx \
    && chown -R www-data:www-data /var/www/html /var/lib/nginx /var/log/nginx \
    && chmod -R 775 storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 8080

CMD ["sh", "-c", "php artisan package:discover --ansi --no-interaction && php artisan migrate --force && (php artisan storage:link || true) && php artisan config:cache && php artisan route:cache && exec supervisord -c /etc/supervisord.conf"]
