# Composer
FROM composer:latest as build

WORKDIR /app

COPY . /app

RUN composer install

# Application
FROM php:8-fpm

COPY --from=build /app /app
WORKDIR /app

# Installing and enabling packages php mods
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql pdo mysqli opcache \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN chmod -R 777 ./storage ./public ./bootstrap
