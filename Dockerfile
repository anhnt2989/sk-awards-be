FROM php:8.4-cli-alpine

RUN apk add --no-cache \
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

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

#CMD sh -c "php artisan migrate --force && php artisan storage:link || true && php artisan config:cache && php artisan route:cache && php -S 0.0.0.0:${PORT:-8080} -t public"

CMD sh -c "php artisan migrate --force && php artisan db:seed --class=AwardSystemSeeder --force && php artisan storage:link || true && php artisan config:cache && php artisan route:cache && php -S 0.0.0.0:${PORT:-8080} -t public"
