FROM php:8.2-fpm AS base

WORKDIR /app

RUN apt-get update && apt-get install -y \
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

COPY . .
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["php", "-a"] # Permite passar comandos arbitrários
CMD ["php-fpm"]
