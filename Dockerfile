# --- Stage 1: build frontend assets (Vite) ---
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --legacy-peer-deps
COPY resources resources
COPY public public
COPY vite.config.js vite.icons.plugin.js ./
RUN npm run build

# --- Stage 2: PHP app ---
FROM php:8.3-cli AS app

RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql pgsql zip gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
COPY --from=assets /app/public/build public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/testing storage/framework/views storage/logs storage/app/public storage/app/private bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
