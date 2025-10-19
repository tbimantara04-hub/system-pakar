# --- STAGE 1: BUILDER STAGE ---
FROM php:8.2-fpm-alpine AS builder

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="/root/.composer/vendor/bin:$PATH"

# Update dan instal paket, termasuk oniguruma-dev untuk mbstring
RUN apk update && apk add --no-cache \
    git \
    curl \
    build-base \
    libzip-dev \
    libpng-dev \
    mariadb-dev \
    libxml2-dev \
    nodejs \
    npm \
    oniguruma-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo \
    opcache \
    mbstring \
    tokenizer \
    xml \
    zip \
    gd

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY package.json package-lock.json* ./
RUN if [ -f package.json ]; then npm install && npm run build; fi

COPY . .

# --- STAGE 2: PRODUCTION STAGE ---
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    mariadb-client \
    git \
    tzdata

RUN ln -sf /usr/share/zoneinfo/Asia/Jakarta /etc/localtime

RUN addgroup -g 1000 laravel && adduser -u 1000 -G laravel -s /bin/sh -D laravel

WORKDIR /app

COPY --from=builder --chown=laravel:laravel /app /app

RUN mkdir -p public/storage && chmod -R 775 public/storage && chown -R laravel:laravel public/storage
RUN chmod -R 775 storage && chown -R laravel:laravel storage
RUN chmod -R 775 bootstrap/cache && chown -R laravel:laravel bootstrap/cache

USER laravel

EXPOSE 8000

ENTRYPOINT ["/app/entrypoint.sh"]

CMD []