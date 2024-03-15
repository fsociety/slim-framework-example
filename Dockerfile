FROM php:8.3-alpine3.19

RUN docker-php-ext-install pdo pdo_mysql