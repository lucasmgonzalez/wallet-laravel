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
RUN apt-get update && \
    apt-get install -y --no-install-recommends\
        mariadb-client \
        zlib1g-dev \
        libzip-dev && \
    docker-php-ext-install \
        pdo_mysql \
        opcache \
        zip

RUN chmod -R 777 ./storage ./public ./bootstrap
