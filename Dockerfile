# Gunakan base image PHP dengan Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Copy semua file proyek ke container
COPY . .

# Install dependencies sistem (jika diperlukan, misalnya untuk ekstensi PHP)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies PHP via Composer
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Cache config, route, view, dan migrate
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force

# Expose port (Render akan menggunakan $PORT)
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]