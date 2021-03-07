# Composer
FROM composer:latest as build

WORKDIR /app

COPY . /app

RUN composer install

# Application
FROM php:8-apache

COPY --from=build /app /var/www
WORKDIR /var/www

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

# Configuring Apache
ENV APACHE_DOCUMENT_ROOT /var/www/public

RUN sed -ri -e 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    a2enmod rewrite

RUN chmod -R 777 ./storage ./public ./bootstrap
