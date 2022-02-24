FROM php:8.0-fpm

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get update && apt-get install -yqq --no-install-recommends \
    curl \
    libzip-dev \
    libpq-dev \
    unzip \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug \
    && docker-php-ext-install zip \
    && docker-php-ext-enable xdebug

WORKDIR /var/www/app
