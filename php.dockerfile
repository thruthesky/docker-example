FROM php:8.0.7-fpm-alpine
RUN docker-php-ext-install mysqli
