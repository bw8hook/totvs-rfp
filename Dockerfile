FROM dunglas/frankenphp:1.0-php8.2 AS base

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    zlib1g-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY frankenphp.yaml /etc/frankenphp.yaml
COPY composer.json composer.lock ./

RUN docker-php-ext-install exif gd zip pdo pdo_mysql \
    && docker-php-ext-enable exif

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

