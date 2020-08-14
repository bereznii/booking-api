FROM php:7.4-fpm-alpine

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/app

COPY ./ ./
