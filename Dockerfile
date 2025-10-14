# Gunakan image PHP 8.2.12
FROM php:8.2.12-alpine

# Install dependencies
RUN apk add --no-cache git curl build-base

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql opcache

# Set workdir
WORKDIR /app

# Copy aplikasi
COPY . /app

# Copy composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port Railway
EXPOSE 8080

# Jalankan PHP built-in server langsung
CMD ["php", "-S", "0.0.0.0:${PORT}", "-t", "public"]
