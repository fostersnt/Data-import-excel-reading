FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /usr/local/apache2/htdocs

COPY . /usr/local/apache2/htdocs

RUN chown -R www-data:www-data /usr/local/apache2/htdocs/storage && chmod -R 777 /usr/local/apache2/htdocs/storage

# RUN composer install --no-dev --optimize-autoloader --no-interaction

EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
