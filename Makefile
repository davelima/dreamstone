#!/usr/bin/env bash

all: prepare-database load-fixtures create-directories update-permissions

prepare-database:
	docker exec -i dreamstone-php7 php /var/www/bin/console doctrine:schema:update --force

load-fixtures:
	docker exec -i dreamstone-php7 php /var/www/bin/console doctrine:fixtures:load

create-directories:
	docker exec -i dreamstone-php7 mkdir -p /var/www/public/uploads
	docker exec -i dreamstone-php7 mkdir -p /var/www/public/uploads/pages
	docker exec -i dreamstone-php7 mkdir -p /var/www/public/uploads/posts
	docker exec -i dreamstone-php7 mkdir -p /var/www/public/uploads/usr
	docker exec -i dreamstone-php7 mkdir -p /var/www/public/uploads/usr-thumbs

update-permissions:
	docker exec -i dreamstone-php7 chown www-data:www-data -R /var/www/var/
	docker exec -i dreamstone-php7 chown www-data:www-data -R /var/www/public/uploads/
