printf "Copying .env to php containers ... "
docker cp .env.lumen booking_api_php-cli_1:/var/www/app/.env
docker cp .env.lumen booking_api_php-fpm_1:/var/www/app/.env
docker cp .env.lumen booking_api_php-queue_1:/var/www/app/.env
GREEN='\033[0;32m'
NC='\033[0m' # No Color
printf "${GREEN}done!${NC} \n"
