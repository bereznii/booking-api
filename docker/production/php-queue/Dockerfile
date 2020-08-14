FROM php:7.4-cli-alpine

ENV QUEUE_CONNECTION=database
ENV QUEUE_NAME=default

RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /var/www/app

COPY ./ ./
COPY ./docker/production/php-queue/conf.d/php-queue.sh /var/www/app

ENTRYPOINT ["sh", "/var/www/app/php-queue.sh"]
