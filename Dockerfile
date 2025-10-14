# Gunakan base image yang lebih stabil dan sesuai dengan versi PHP Anda
FROM php:8.2.12-fpm-alpine

# --- 1. INSTALASI DEPENDENSI SISTEM ---
# Instal dependensi sistem yang diperlukan untuk PHP extensions dan composer
# Juga instal 'supervisor' jika Anda membutuhkan queue worker (opsional, dihapus untuk kesederhanaan)
RUN apk update && apk add --no-cache \
    git \
    curl \
    build-base \
    # Tambahkan dependensi untuk ekstensi yang umum digunakan Laravel:
    mysql-client \
    libxml2-dev \
    oniguruma-dev \
    $PHPIZE_DEPS

# --- 2. INSTALASI EKSTENSI PHP ---
# Instal ekstensi yang disyaratkan oleh Laravel dan proyek Anda
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    opcache \
    mbstring \
    tokenizer \
    xml

# Bersihkan cache APK
RUN rm -rf /var/cache/apk/*

# --- 3. KONFIGURASI DAN COPY FILE ---
WORKDIR /app

# Salin script entrypoint (Entrypoint ini yang akan menjalankan migrasi!)
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Salin composer, menggunakan multi-stage build untuk menghemat ukuran image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin seluruh kode aplikasi
COPY . /app

# --- 4. INSTALASI DEPENDENSI KOMPOSER ---
# Instal dependensi PHP. Gunakan 'prefer-dist' untuk kecepatan.
RUN composer install --no-dev --optimize-autoloader

# --- 5. PERMISSION DAN USER NON-ROOT ---
# Buat user non-root (www-data) dan ubah permission storage/bootstrap
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage \
    && chmod -R 775 /app/bootstrap/cache

USER www-data

# --- 6. COMMAND DOCKER ---
# Expose port (meskipun Railway seringkali mengabaikan ini dan menggunakan $PORT)
EXPOSE 8000

# ENTRYPOINT menjalankan script setup (migrasi, key generate)
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# CMD menyediakan default port jika variabel lingkungan tidak disetel, tetapi entrypoint.sh menanganinya.
# Kami biarkan CMD kosong karena entrypoint.sh sudah menggunakan 'exec php artisan serve'
CMD []
