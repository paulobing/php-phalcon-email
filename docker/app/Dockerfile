FROM composer as builder
WORKDIR /app/
COPY composer.docker.json ./composer.json
RUN docker-php-ext-install sockets
RUN composer install

FROM php:7.2-apache

COPY --from=builder /app/vendor /var/www/vendor

# PSR for phalcon 4.x
ARG PSR_VERSION=0.6.1

ARG PHALCON_VERSION=4.1.2
ARG PHALCON_EXT_PATH=php7/64bits

RUN set -xe && \
        # Compile PSR   
        curl -LO https://github.com/jbboehr/php-psr/archive/v${PSR_VERSION}.tar.gz && \
        tar xzf ${PWD}/v${PSR_VERSION}.tar.gz && \
        cd ${PWD}/php-psr-${PSR_VERSION} && \
        phpize && \
        ./configure && \
        make && \
        make test && \
        make install 

RUN set -xe && \
        # Compile Phalcon
        curl -LO https://github.com/phalcon/cphalcon/archive/v${PHALCON_VERSION}.tar.gz && \
        tar xzf ${PWD}/v${PHALCON_VERSION}.tar.gz && \
        cd ${PWD}/cphalcon-${PHALCON_VERSION}/build/ && \
        ./install

# Install packages
RUN apt-get update && \
        apt-get install -y zlib1g-dev && \ 
        apt-get install -y libpng-dev && \
        apt-get install nano

RUN docker-php-ext-install zip
RUN docker-php-ext-install gd
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install sockets
RUN docker-php-ext-enable sockets

# Enable mod rewrite
RUN a2enmod rewrite

# /usr/local/bin/php -S 0.0.0.0:80 -t /var/www/html /var/www/html/.htrouter.php
ENTRYPOINT ["/usr/local/bin/php", "-S", "0.0.0.0:80", "-t", "/var/www/html", "/var/www/html/.htrouter.php"]
