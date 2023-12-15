FROM php:7.4-apache

RUN apt-get update \
    && apt-get install -y \
    && apt-get autoremove -y \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && apt-get install curl -y \
    && apt-get install git -y\
    && apt-get install zip -y\
    && curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin/symfony

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/trade_cube/ .

EXPOSE 8050