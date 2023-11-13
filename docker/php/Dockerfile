FROM php:7.4-fpm

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




WORKDIR /var/www/irish

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN git config --global user.email "yaroslav.bailo@gmail.com" \
    && git config --global user.name "Yaroslav"