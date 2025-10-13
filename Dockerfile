# Gunakan image PHP 8.2.12 FPM (Pastikan image ini ada di Docker Hub)
FROM php:8.2.12-fpm-alpine 

# Instal dependensi dan extensions
RUN apk add --no-cache git curl build-base
RUN docker-php-ext-install pdo_mysql opcache

# Set direktori kerja
WORKDIR /app

# Salin aplikasi
COPY . /app

# Instal Composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# (Lanjutkan dengan konfigurasi Web Server seperti Nginx atau Caddy jika perlu)
# Railway biasanya memerlukan instruksi entrypoint/CMD yang menjalankan server Anda
# ...