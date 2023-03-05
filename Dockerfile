FROM php:8.2-cli
RUN apt-get update && apt-get install -y git libzip-dev
RUN docker-php-ext-install zip


COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY ./ /home/project/app

WORKDIR /home/project/app

RUN composer install
RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install \
  pcntl