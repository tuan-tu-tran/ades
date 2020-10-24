FROM php:5.6-apache-stretch

RUN docker-php-ext-install mysqli mysql
RUN apt-get update && apt-get install -y mysql-client
RUN a2enmod rewrite

ENV TZ Europe/Brussels
RUN echo "date.timezone=Europe/Brussels" > /usr/local/etc/php/conf.d/tzinfo.ini
COPY 000-default.conf /etc/apache2/sites-available