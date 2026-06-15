FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql zip

WORKDIR /var/www/html
COPY . /var/www/html

EXPOSE 8000

CMD php artisan serve \
    --host=0.0.0.0 \
    --port=8000