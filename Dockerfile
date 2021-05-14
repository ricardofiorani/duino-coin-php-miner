FROM php:8-cli-alpine

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer --version && php -v

WORKDIR /code

COPY . .

RUN composer install

ENTRYPOINT php miner.php
