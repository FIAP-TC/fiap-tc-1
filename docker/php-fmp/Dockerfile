FROM php:8.3-fpm-alpine AS builder

RUN apk add --no-cache \
    curl \
    unzip \
    libzip-dev \
    icu-dev

RUN docker-php-ext-install bcmath intl zip pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --optimize-autoloader --no-interaction

FROM php:8.3-fpm-alpine

RUN apk add --no-cache libzip icu-libs

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=builder /var/www /var/www

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

COPY docker/php-fmp/zz-tuning.conf /usr/local/etc/php-fpm.d/zz-tuning.conf

USER www-data

CMD ["php-fpm"]

EXPOSE 9000
