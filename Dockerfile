# Use an official PHP runtime as the base image with Apache
FROM php:8.2-apache

# Install system dependencies and PHP extensions required by Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

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
