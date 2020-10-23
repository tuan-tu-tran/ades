FROM php:5.6-apache-stretch

RUN docker-php-ext-install mysqli
RUN a2enmod rewrite

RUN apt-get update && apt-get install -y mysql-client
RUN docker-php-ext-install mysql
