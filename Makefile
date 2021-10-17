docker-all: install run

php-all: php-build php-run

install:
	docker-compose build

run:
	docker-compose up

stop:
	docker-compose down

php-build: php-download-composer
	php composer.phar update

php-download-composer:
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"

php-run: rabbit-start
	docker logs --since 5s -f rabbitmq | sed '/Server startup complete/ q'
	php app/helper/QueueHelper.php &
	php -S localhost:8000 -t public .htrouter.php

NOW := $(shell date "+%Y-%m-%dT%H:%M:%S")

rabbit-start:
	docker-compose run -d rabbitmq3
	docker logs --since ${NOW} rabbitmq | grep "Server startup complete" | wc -l

n ?= 10
loop:
	n=$(n)
	echo ${n}

php-stop:
	docker stop rabbitmq