FROM php:7.4-cli-alpine

RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /var/www/app

COPY ./ ./

RUN mkdir /var/log/cron \
    && touch /var/log/cron/cron.log \
    && chown -R www-data:www-data /var/log/cron \
    && touch crontab.tmp \
    && echo '* * * * * cd /var/www/app && php artisan schedule:run >> /var/log/cron/cron.log 2>&1' > crontab.tmp \
    && crontab crontab.tmp \
    && rm -rf crontab.tmp

CMD ["/usr/sbin/crond", "-f", "-d", "0"]

