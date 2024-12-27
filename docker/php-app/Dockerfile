FROM php:8.1.27-apache-bullseye AS prod

RUN apt-get update -y && apt-get install -y \
    libicu-dev \
    libmariadb-dev \
    unzip zip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev

RUN a2enmod rewrite

WORKDIR /var/www/html

# Install the latest version of Composer.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install intl pdo_mysql gd

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
&& docker-php-ext-install -j$(nproc) \
    gd \
    intl \
    pdo_mysql

RUN pecl install redis-5.3.7 \
        && rm -rf /tmp/pear \
        && docker-php-ext-enable redis

# start new build stage
FROM prod
# run as root user
USER root

# install Xdebug and enable 'xdebug' extension
RUN pecl install xdebug-3.3.2 \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable xdebug
