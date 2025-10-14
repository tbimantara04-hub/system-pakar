# --- STAGE 1: BUILDER STAGE ---
# Tahap ini digunakan untuk menginstal composer dependencies dan meng-compile aset front-end (jika ada)
FROM php:8.2.12-fpm-alpine AS builder

# Variabel Lingkungan
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PATH="/root/.composer/vendor/bin:\$PATH"

# Instal dependensi sistem yang diperlukan untuk PHP extensions dan Git
# mariadb-client-dev diperlukan untuk pdo_mysql
RUN apk add --no-cache \
    git \
    curl \
    build-base \
    libzip-dev \
    libpng-dev \
    mariadb-client-dev \
    npm

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

# Set working directory ke /app
WORKDIR /app

# Salin composer.json dan composer.lock untuk layer cache Composer
COPY composer.json composer.lock ./

# Instal Composer Dependencies
RUN composer install --no-dev --optimize-autoloader

# Salin sisa kode aplikasi
COPY . .

# Run NPM build (Hanya jika Anda menggunakan aset front-end seperti Vue/React/Tailwind)
# Jika tidak menggunakan aset front-end, baris ini bisa dihilangkan
RUN if [ -f package.json ]; then \
    npm install && npm run build; \
    fi


# --- STAGE 2: PRODUCTION STAGE ---
# Tahap ini adalah image yang akan benar-benar dijalankan (runtime)
# Kami hanya menyalin apa yang dibutuhkan dari builder stage (Kode, Vendor, Aset)
FROM php:8.2.12-fpm-alpine

# Instal hanya dependensi runtime yang diperlukan
RUN apk add --no-cache \
    mariadb-client \
    git \
    tzdata

# Set timezone untuk PHP
RUN ln -sf /usr/share/zoneinfo/Asia/Jakarta /etc/localtime

# Buat user non-root untuk alasan keamanan
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

# EXPOSE port yang akan didengarkan oleh php artisan serve
EXPOSE 8000

# Entrypoint mengacu pada script yang Anda buat
ENTRYPOINT ["/app/entrypoint.sh"]

# CMD default hanya menjalankan entrypoint
CMD []
