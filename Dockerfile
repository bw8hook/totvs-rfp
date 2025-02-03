FROM node:18 AS builder

WORKDIR /app

COPY . .
RUN npm install
RUN npm run build

FROM php:8.2-fpm AS base

RUN usermod -u 1000 www-data
WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libpng-dev \
    zlib1g-dev \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install pdo_mysql pdo mbstring exif pcntl bcmath gd zip \
    && docker-php-ext-enable exif \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=builder /app/public/build /var/www/public/build

COPY --chown=www-data . .
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

RUN chmod -R 755 /var/www/storage /var/www/bootstrap

EXPOSE 8080

ENTRYPOINT ["docker/entrypoint.sh"]
