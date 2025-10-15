# --- STAGE 1: BUILDER STAGE ---
# Menggunakan base image 8.2-fpm-alpine yang lebih stabil.
FROM php:8.2-fpm-alpine AS builder

# Variabel Lingkungan
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PATH="/root/.composer/vendor/bin:$PATH"

# Instal dependensi sistem yang diperlukan untuk kompilasi PHP extensions, Git, dan Node.
# Pertama, update repository untuk menghindari error paket tidak ditemukan.
RUN apk update && apk add --no-cache \
    git \
    curl \
    build-base \
    libzip-dev \
    libpng-dev \
    libxml2-dev  # Paket ini seharusnya baik

# Instal paket dev untuk MariaDB (ganti mariadb-client-dev dengan mariadb-dev)
RUN apk add --no-cache mariadb-dev

# Instal Node/NPM secara terpisah (disarankan untuk Alpine)
# Ini diperlukan jika Anda menjalankan 'npm run build' untuk aset front-end (Vite/Mix).
RUN apk add --no-cache nodejs npm

# Instal Composer secara global
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instal PHP extensions yang diperlukan oleh Laravel
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo \
    opcache \
    mbstring \
    tokenizer \
    xml \
    zip \
    gd

# Set working directory
WORKDIR /app

# Salin composer.json dan composer.lock untuk layer cache Composer
COPY composer.json composer.lock ./

# Instal Composer Dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Salin sisa kode aplikasi
COPY . .

# Run NPM build (Hanya jika Anda menggunakan Vite/Mix/Tailwind)
RUN if [ -f package.json ]; then \
    npm install && npm run build; \
    fi


# --- STAGE 2: PRODUCTION STAGE ---
# Image runtime yang ringan untuk produksi
FROM php:8.2-fpm-alpine

# Instal hanya dependensi runtime yang diperlukan
RUN apk add --no-cache \
    mariadb-client \  # Ini untuk runtime, bukan dev
    git \
    tzdata

# Set timezone ke Asia/Jakarta
RUN ln -sf /usr/share/zoneinfo/Asia/Jakarta /etc/localtime

# Buat user non-root untuk keamanan
RUN addgroup -g 1000 laravel && \
    adduser -u 1000 -G laravel -s /bin/sh -D laravel

# Set working directory
WORKDIR /app

# Salin semua file dari builder stage
# --chown=laravel:laravel memastikan file dimiliki oleh user non-root
COPY --from=builder --chown=laravel:laravel /app /app

# Pastikan Laravel dapat menulis ke storage dan cache
RUN chmod -R 775 /app/storage \
    && chown -R laravel:laravel /app/storage \
    && chmod -R 775 /app/bootstrap/cache \
    && chown -R laravel:laravel /app/bootstrap/cache

# Menggunakan user non-root (laravel) untuk menjalankan aplikasi
USER laravel

# EXPOSE port yang akan didengarkan oleh php artisan serve (port default Laravel)
EXPOSE 8000

# Entrypoint mengacu pada script yang Anda buat
ENTRYPOINT ["/app/entrypoint.sh"]

# CMD default hanya menjalankan entrypoint
CMD []