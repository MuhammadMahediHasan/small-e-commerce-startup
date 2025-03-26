FROM php:8.3-fpm
ARG user
ARG uid

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libz-dev \
        libpq-dev \
        libjpeg-dev \
        libpng-dev \
        libssl-dev \
        libzip-dev \
        unzip \
        zip \
    && apt-get clean \
    && docker-php-ext-install \
        gd \
        zip \
    && rm -rf /var/lib/apt/lists/*;

# Copy Composer binary from the official Composer image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy PHP configuration
COPY .docker/app/php-fpm.ini /usr/local/etc/php/conf.d/php-fpm.ini

# Create a system user for running Composer and Artisan commands

#set work directory
WORKDIR /var/www
RUN chown -R www-data:www-data /var/www;
USER $user
