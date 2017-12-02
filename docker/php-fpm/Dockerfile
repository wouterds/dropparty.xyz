FROM php:7.1-fpm-alpine
MAINTAINER Wouter De Schuyter <wouter.de.schuyter@gmail.com>

# Install extensions
RUN NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
    && docker-php-ext-install -j${NPROC} pdo pdo_mysql mysqli \
    && apk add --update --no-cache gmp gmp-dev \
    && docker-php-ext-install -j${NPROC} gmp \
    && apk del gmp-dev

# PHP configuration file
COPY ./docker/php-fpm/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy project
COPY ./ /code

# Change working directory
WORKDIR /code
