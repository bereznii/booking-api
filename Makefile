docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-build:
	docker-compose build

docker-pull:
	docker-compose pull

docker-push-rebuilt-php-queue:
	docker build --file ./docker/production/php-queue/Dockerfile --tag '<<RESISTRY_NAME>>'/booking_api:php-queue_master-1 .
	docker push '<<RESISTRY_NAME>>'/booking_api:php-queue_master-1

docker-push-rebuilt-php:
	docker build --file ./docker/production/php-fpm/Dockerfile --tag '<<RESISTRY_NAME>>'/booking_api:php-fpm_master-1 .
	docker build --file ./docker/production/php-cli/Dockerfile --tag '<<RESISTRY_NAME>>'/booking_api:php-cli_master-1 .
	docker build --file ./docker/production/php-queue/Dockerfile --tag '<<RESISTRY_NAME>>'/booking_api:php-queue_master-1 .
	docker push '<<RESISTRY_NAME>>'/booking_api:php-fpm_master-1
	docker push '<<RESISTRY_NAME>>'/booking_api:php-cli_master-1
	docker push '<<RESISTRY_NAME>>'/booking_api:php-queue_master-1

docker-pull-rebuilt-php:
	docker pull '<<RESISTRY_NAME>>'/booking_api:php-fpm_master-1
	docker pull '<<RESISTRY_NAME>>'/booking_api:php-cli_master-1
	docker pull '<<RESISTRY_NAME>>'/booking_api:php-queue_master-1
	docker-compose -f docker-compose_production.yml up -d --force-recreate --no-deps --build php-fpm
	docker-compose -f docker-compose_production.yml up -d --force-recreate --no-deps --build php-cli
	docker-compose -f docker-compose_production.yml up -d --force-recreate --no-deps --build php-queue
	sh copy_env_to_lumen_containers.sh
	docker exec -it booking_php-fpm_1 sh -c "chown www-data:www-data -R storage";
	docker exec -it booking_php-cli_1 sh -c "chown www-data:www-data -R storage";
	docker exec -it booking_php-queue_1 sh -c "chown www-data:www-data -R storage";
	sh run_tests.sh
