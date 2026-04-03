FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    libpng-dev libzip-dev zip unzip git curl icu-dev \
    && docker-php-ext-install pdo_mysql bcmath zip intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-scripts

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
