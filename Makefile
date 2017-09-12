#!/usr/bin/env bash

all: docker-prepare

docker-prepare:
	docker exec -i dreamstone-php7 chown www-data:www-data -R /var/www/var/
	docker exec -i dreamstone-php7 php /var/www/bin/console doctrine:schema:update --force

load-fixtures:
	docker exec -i dreamstone-php7 php /var/www/bin/console doctrine:fixtures:load
