docker-all: install run

php-all: php-build php-run

install:
	docker-compose build

run:
	docker-compose up -d
	# FIXME find a better way to find out if rabbitmq has started
	sleep 10
	php app/helper/QueueConsumerProcess.php

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
	php app/helper/QueueConsumerProcess.php &
	php -S localhost:8000 -t app/public app/.htrouter.php

rabbit-start:
	docker-compose up -d rabbitmq3
	# FIXME find a better way to find out if rabbitmq has started
	sleep 10

php-stop:
	docker stop rabbitmq

clean: php-stop stop