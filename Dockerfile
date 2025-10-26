# Gunakan base image PHP 8.2 dengan Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# 1. Install semua dependencies sistem
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 2. Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 3. Optimasi layer caching
COPY composer.json composer.lock ./

# 4. Install dependencies TAPI JANGAN JALANKAN SCRIPT (seperti artisan)
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# 5. Salin sisa file aplikasi
COPY . .

# 7. Atur Apache agar menunjuk ke folder /public Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

# 8. Atur kepemilikan file
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Expose port 80 (Apache)
EXPOSE 80

# --- PERUBAHAN DI SINI ---
# 10. Salin entrypoint.sh ke dalam image
COPY entrypoint.sh .

# 11. Buat agar bisa dieksekusi
RUN chmod +x ./entrypoint.sh

# 12. Hapus CMD lama dan setel entrypoint baru
# CMD ["apache2-foreground"] # <-- HAPUS/Komentari baris ini
ENTRYPOINT ["./entrypoint.sh"]