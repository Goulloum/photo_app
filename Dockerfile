FROM php:8.1-cli

RUN apt-get update -y && apt-get install -y libmcrypt-dev git zip unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
RUN docker-php-ext-install pdo_mysql

WORKDIR /app
COPY . /app

# ENV COMPOSER_ALLOW_SUPERUSER 1

RUN composer install

EXPOSE 8000
