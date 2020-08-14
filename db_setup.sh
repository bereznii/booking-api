#DB_PASSWD="root"
#docker exec -it booking_mysql_1 mysql -u root -p${DB_PASSWD} -e "CREATE DATABASE booking CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;"

docker exec -it booking_php-fpm_1 sh -c "php artisan migrate";
docker exec -it booking_php-fpm_1 sh -c "php artisan db:seed --class=UniqueTestsSeeder";
docker exec -it booking_php-fpm_1 sh -c "php artisan syncTable:centers";

