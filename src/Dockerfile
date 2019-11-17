FROM webdevops/php-apache:ubuntu-16.04

RUN apt update
RUN apt install php7.0-dev -y
RUN pecl install mongodb
RUN php --ini | grep php.ini | sed -ne 's/^[a-zA-Z ]*:\s*//p'
RUN echo "extension=mongodb.so" >> $(php --ini | grep php.ini | sed -ne 's/^[a-zA-Z ]*:\s*//p')

