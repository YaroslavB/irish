FROM php:8.1-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies, PHP extensions, Composer and Symfony CLI
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    g++ \
    git \
    libicu-dev \
    zip \
    libzip-dev \
    openssl \
    librabbitmq-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg-dev \
    libpq-dev \
    libz-dev \
    # Install PHP extensions \
    && docker-php-ext-install intl opcache pdo pdo_mysql exif pcntl bcmath \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    # Xdebug \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    # Install Composer \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    # Install Symfony CLI \
    && curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony \
    # Cleanup \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy PHP-FPM pool configuration for monitoring
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/irish

# Copy application files with correct ownership
COPY --chown=www-data:www-data . /var/www/irish

# Switch to non-root user
USER www-data
