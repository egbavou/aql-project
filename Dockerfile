FROM php:8.2-apache

RUN mkdir /project && chmod 777 -R /project && \
    apt update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    git && \
    docker-php-ext-install pdo pdo_mysql gd zip

COPY wait-for-it.sh /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/wait-for-it.sh

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /project
COPY . /project

EXPOSE 8000
EXPOSE 5173

RUN cp .env.docker .env && \
    composer install && \
    php artisan key:generate && \
    curl -fsSL https://deb.nodesource.com/setup_23.x | bash - && \
    apt-get install -y nodejs && \
    npm install
