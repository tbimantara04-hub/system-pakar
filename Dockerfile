FROM php:8.2.12-fpm-alpine

RUN apk add --no-cache git curl build-base
RUN docker-php-ext-install pdo_mysql opcache

WORKDIR /app
COPY . /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

EXPOSE 8000

ENTRYPOINT []
CMD php -S 0.0.0.0:${PORT:-8000} -t public
