# Gunakan image PHP 8.2.12 FPM (alpine lebih ringan)
FROM php:8.2.12-fpm-alpine

# Instal dependensi
RUN apk add --no-cache git curl build-base

# Install ekstensi PHP
RUN docker-php-ext-install pdo_mysql opcache

# Set direktori kerja
WORKDIR /app

# Salin seluruh file aplikasi
COPY . /app

# Instal Composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Expose port yang digunakan
EXPOSE 8000

# Gunakan built-in server PHP dan biarkan Railway set $PORT
CMD php -S 0.0.0.0:${PORT:-8000} -t public
