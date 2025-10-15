# --- STAGE 1: BUILDER STAGE ---
FROM php:8.2-fpm-alpine AS builder

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PATH="/root/.composer/vendor/bin:$PATH"

# Update repository dan instal paket
RUN apk update && apk add --no-cache \
    git \
    curl \
    build-base \
    libzip-dev \
    libpng-dev \
    mariadb-dev \  # Sudah diganti dari mariadb-client-dev
    libxml2-dev

RUN apk add --no-cache nodejs npm

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

COPY . .

RUN if [ -f package.json ]; then \
    npm install && npm run build; \
    fi

# --- STAGE 2: PRODUCTION STAGE ---
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    mariadb-client \
    git \
    tzdata

RUN ln -sf /usr/share/zoneinfo/Asia/Jakarta /etc/localtime

RUN addgroup -g 1000 laravel && \
    adduser -u 1000 -G laravel -s /bin/sh -D laravel

WORKDIR /app

COPY --from=builder --chown=laravel:laravel /app /app

RUN chmod -R 775 /app/storage && chown -R laravel:laravel /app/storage \
    && chmod -R 775 /app/bootstrap/cache && chown -R laravel:laravel /app/bootstrap/cache

USER laravel

EXPOSE 8000

ENTRYPOINT ["/app/entrypoint.sh"]

CMD []