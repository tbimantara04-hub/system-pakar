# Gunakan base image PHP 8.2 dengan Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# 1. Install semua dependencies sistem
# - ekstensi GD (libpng, libjpeg, libfreetype)
# - ekstensi PostgreSQL (libpq-dev)
# - utility (zip, unzip, curl)
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

# 2. Install Composer (Manajemen package PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 3. Optimasi layer caching Docker
# Salin HANYA file composer dulu
COPY composer.json composer.lock ./

# Install dependencies. Jika file composer tidak berubah, Docker akan menggunakan cache
RUN composer install --no-dev --no-interaction --optimize-autoloader

# 4. Salin sisa file aplikasi
COPY . .

# 5. Atur Apache agar menunjuk ke folder /public Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

# 6. Atur kepemilikan file agar Apache (www-data) bisa menulis ke folder storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 7. Expose port 80 (Apache)
EXPOSE 80

# 8. Perintah untuk menjalankan server
CMD ["apache2-foreground"]