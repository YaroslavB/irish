FROM php:8.1-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip openssl \
    librabbitmq-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg-dev \
    libpq-dev \
    libz-dev \
    && docker-php-ext-install intl opcache pdo pdo_mysql exif pcntl bcmath \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd \
     --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /var/www/irish

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Fix permissions
RUN usermod -u 1000 www-data

WORKDIR /var/www/irish