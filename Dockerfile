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
# Salin HANYA file composer dulu
COPY composer.json composer.lock ./

# 4. Install dependencies TAPI JANGAN JALANKAN SCRIPT (seperti artisan)
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# 5. Salin sisa file aplikasi (SEKARANG artisan SUDAH ADA)
COPY . .

# 6. Jalankan artisan cache (ini aman dilakukan di dalam build image)
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# 7. Atur Apache agar menunjuk ke folder /public Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

# 8. Atur kepemilikan file agar Apache (www-data) bisa menulis ke folder storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Expose port 80 (Apache)
EXPOSE 80

# 10. Perintah untuk menjalankan server
CMD ["apache2-foreground"]