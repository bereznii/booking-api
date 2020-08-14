FROM php:7.4-cli-alpine

ENV QUEUE_CONNECTION=database
ENV QUEUE_NAME=default

RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /var/www/app

ENTRYPOINT ["sh", "docker/development/php-queue/conf.d/script.sh"]
