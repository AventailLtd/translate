FROM php:8.2-fpm-bullseye

# Install composer in image
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# git - Required for composer
# zip - Required for composer
# libzip-dev - Required for zip extenstion
RUN apt-get update
RUN apt-get install -y -q --no-install-recommends \
    git \
    zip \
    libzip-dev

# zip - Required for composer
RUN docker-php-ext-install \
    zip
