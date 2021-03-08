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

# Installing XDebug
RUN pecl install xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && docker-php-ext-enable xdebug

# Configuring Apache
ENV APACHE_DOCUMENT_ROOT /var/www/public

RUN sed -ri -e 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    a2enmod rewrite

RUN chmod -R 777 ./storage ./public ./bootstrap
