FROM php:8.2 as php

RUN apt-get update -y
RUN apt-get install -y unzip libpq-dev libcurl4-gnutls-dev libsodium-dev libzip-dev
RUN docker-php-ext-install pdo pdo_mysql bcmath sodium zip exif

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

WORKDIR /var/www

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chmod +x docker/entrypoint.sh

ENTRYPOINT [ "docker/entrypoint.sh" ]