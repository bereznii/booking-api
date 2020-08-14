#!/bin/sh

php /var/www/app/artisan queue:work --verbose --queue=default --sleep=1 --tries=1 --timeout=15
