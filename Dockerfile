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
    curl \
    ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 2. Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# --- BLOK BARU: Install Node.js (LTS) ---
RUN curl -sL https://deb.nodesource.com/setup_lts.x | bash -
RUN apt-get install -y nodejs
# --- AKHIR BLOK BARU ---

# 3. Optimasi layer caching
COPY composer.json composer.lock ./

# 4. Install dependencies PHP
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# 5. Salin sisa file aplikasi
COPY . .

# --- BLOK BARU: Install dan Build Aset Frontend ---
RUN npm install
RUN npm run build
# --- AKHIR BLOK BARU ---

# 6. Atur Apache agar menunjuk ke folder /public Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

# 7. Atur kepemilikan file
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Expose port 80 (Apache)
EXPOSE 80

# 9. Salin entrypoint.sh ke dalam image
COPY entrypoint.sh .

# 10. Buat agar bisa dieksekusi
RUN chmod +x ./entrypoint.sh

# 11. Setel entrypoint
ENTRYPOINT ["./entrypoint.sh"]