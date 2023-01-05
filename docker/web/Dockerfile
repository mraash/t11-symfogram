FROM php:8.0-apache

RUN a2enmod rewrite

RUN apt-get update \
    && apt-get install -y libpq-dev

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable pdo_mysql \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-enable pdo_pgsql
