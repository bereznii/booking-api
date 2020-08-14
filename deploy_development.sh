php artisan key:generate
chown -R www-data:www-data storage
make docker-down
make docker-build
make docker-up
