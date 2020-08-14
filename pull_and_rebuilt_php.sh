GREEN='\033[0;32m'
NC='\033[0m' # No Color

docker pull '<<RESISTRY_NAME>>'/booking_api:php-fpm_master-1
docker pull '<<RESISTRY_NAME>>'/booking_api:php-cli_master-1
docker pull '<<RESISTRY_NAME>>'/booking_api:php-queue_master-1
docker-compose -f docker-compose_production.yml up -d --force-recreate --no-deps --build php-fpm
docker-compose -f docker-compose_production.yml up -d --force-recreate --no-deps --build php-cli
docker-compose -f docker-compose_production.yml up -d --force-recreate --no-deps --build php-queue
sh copy_env_to_lumen_containers.sh

printf "Setting chown in php containers ... "
docker exec -it booking_api_php-fpm_1 sh -c "chown www-data:www-data -R storage";
docker exec -it booking_api_php-cli_1 sh -c "chown www-data:www-data -R storage";
docker exec -it booking_api_php-queue_1 sh -c "chown www-data:www-data -R storage";
printf "${GREEN}done!${NC} \n"

sh run_tests.sh
