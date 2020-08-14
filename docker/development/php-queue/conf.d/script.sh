#!/bin/sh

php /var/www/app/artisan queue:listen --verbose --queue=default --sleep=1 --tries=3 --timeout=90
