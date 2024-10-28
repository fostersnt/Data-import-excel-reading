# Use an official PHP runtime as the base image with Apache
# FROM ubuntu
FROM php:8.1-apache

# Install system dependencies and PHP extensions required by Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Install GD extensions
# RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
# RUN docker-php-ext-install gd

# Install Composer (PHP dependency manager)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory in the container
WORKDIR /var/www/html

# Copy the Laravel application files to the container
COPY . /var/www/html

# Give necessary permissions to the storage and cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Run Composer install to set up dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Expose port 80 to make the app accessible on this port
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
