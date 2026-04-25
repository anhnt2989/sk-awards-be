FROM php:8.4-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    libzip-dev \
    unzip \
    curl \
    bash

# PHP extensions for Laravel + MySQL
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    zip \
    opcache

# Opcache tuning
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.interned_strings_buffer=16'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies first
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# Copy app code
COPY . .

# Laravel writable dirs
RUN chmod -R 775 storage bootstrap/cache

# Config files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 10000

CMD ["/start.sh"]
